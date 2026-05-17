<?php
require_once "./../conexion.php";

class ReservacionesDAO
{
    private $conexion;

    public function __construct()
    {
        $conn = new Conexion();
        $this->conexion = $conn->getConexion();
    }

    // ==========================================
    // LECTURA (SELECT) - SIN TRANSACCIONES
    // ==========================================

    /**
     * Obtiene el historial detallado de reservaciones de un cliente.
     */
    public function obtenerHistorialDetallado($id_cliente)
    {
        try {
            $sql  = "CALL sp_obtener_historial_reservaciones_cliente(:id_cliente)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al consultar historial de reservaciones: " . $e->getMessage());
        }
    }

    // ==========================================
    // ESCRITURA (UPDATE) - CON CONTROL DE BLOQUEO
    // ==========================================

    /**
     * Cancela una reservacion del cliente validando que el evento
     * NO este bloqueado por un administrador en este momento.
     *
     * Delega a sp_intentar_cancelar_reservacion que internamente:
     *   1. Verifica que la reservacion pertenezca al cliente (FOR UPDATE)
     *   2. Verifica bloqueo del admin sobre el evento (FOR SHARE)
     *   3. Cancela si todo esta libre
     *
     * @throws Exception con mensaje legible si hay bloqueo de admin u otro error.
     */
    public function cancelarReservacion($id_reservacion, $id_cliente)
    {
        try {
            $stmt = $this->conexion->prepare(
                "CALL sp_intentar_cancelar_reservacion(:id_reservacion, :id_cliente, @resultado)"
            );
            $stmt->bindParam(':id_reservacion', $id_reservacion, PDO::PARAM_INT);
            $stmt->bindParam(':id_cliente',     $id_cliente,     PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            $res = $this->conexion->query("SELECT @resultado AS resultado")
                                  ->fetch(PDO::FETCH_ASSOC);
            $resultado = $res['resultado'] ?? '';

            if ($resultado === 'OK') {
                return true;
            }

            if (str_starts_with($resultado, 'EVENTO_EN_EDICION')) {
                $partes = explode('|', $resultado);
                $admin  = $partes[1] ?? 'un administrador';
                $hora   = $partes[2] ?? 'unos minutos';
                throw new Exception(
                    "No puedes cancelar esta reservación ahora. " .
                    "$admin está modificando el evento (hasta las $hora). " .
                    "Intenta de nuevo más tarde."
                );
            }

            $mensajes = [
                'YA_CANCELADA'             => 'Esta reservación ya estaba cancelada.',
                'COMPLETADA_NO_CANCELABLE' => 'No puedes cancelar una reservación completada.',
                'RESERVACION_NO_ENCONTRADA'=> 'La reservación no fue encontrada.',
                'ERROR_INTERNO'            => 'Ocurrió un error interno. Intenta de nuevo.',
            ];

            throw new Exception($mensajes[$resultado] ?? "No se pudo cancelar la reservación.");

        } catch (PDOException $e) {
            throw new Exception("Error en la base de datos: " . $e->getMessage());
        }
    }
}