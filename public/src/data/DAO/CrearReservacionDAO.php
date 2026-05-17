<?php
require_once "./../conexion.php";

class CrearReservacionDAO
{
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    /**
     * Intenta crear una reservacion validando que el evento NO este
     * bloqueado por un administrador en este momento.
     *
     * Llama a sp_intentar_reservar que internamente:
     *   1. Verifica bloqueo con FOR SHARE
     *   2. Verifica duplicado pendiente
     *   3. Inserta si todo esta libre
     *
     * Retorna el id_reservacion si fue exitoso.
     *
     * @throws Exception con mensaje legible si el evento esta bloqueado
     *                   o si ocurre otro error.
     */
    public function crearReservacion($id_evento, $id_cliente)
    {
        try {
            // El SP maneja su propia transaccion internamente
            $stmt = $this->conexion->prepare(
                "CALL sp_intentar_reservar(:id_evento, :id_cliente, @resultado)"
            );
            $stmt->bindParam(':id_evento',  $id_evento,  PDO::PARAM_INT);
            $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            $res = $this->conexion->query("SELECT @resultado AS resultado")
                                  ->fetch(PDO::FETCH_ASSOC);
            $resultado = $res['resultado'] ?? '';

            // Formato OK: 'OK|id_reservacion'
            if (str_starts_with($resultado, 'OK')) {
                $partes = explode('|', $resultado);
                return (int)($partes[1] ?? 0);
            }

            // Formato BLOQUEADO: 'EVENTO_EN_EDICION|NombreAdmin|HH:MM del DD/MM/AAAA'
            if (str_starts_with($resultado, 'EVENTO_EN_EDICION')) {
                $partes = explode('|', $resultado);
                $admin  = $partes[1] ?? 'un administrador';
                $hora   = $partes[2] ?? 'unos minutos';
                throw new Exception(
                    "Este evento no está disponible en este momento. " .
                    "$admin lo está actualizando (hasta las $hora). " .
                    "Intenta de nuevo más tarde."
                );
            }

            // Otros casos
            $mensajes = [
                'YA_RESERVADO'      => 'Ya tienes una reservación pendiente para este evento.',
                'EVENTO_NO_EXISTE'  => 'El evento seleccionado ya no está disponible.',
                'ERROR_INTERNO'     => 'Ocurrió un error interno. Intenta de nuevo.',
            ];

            throw new Exception($mensajes[$resultado] ?? "No se pudo completar la reservación ($resultado).");

        } catch (PDOException $e) {
            throw new Exception("Error en la base de datos: " . $e->getMessage());
        }
    }

    /**
     * Cancela una reservacion de un cliente validando que el evento
     * NO este bloqueado por un administrador.
     *
     * @throws Exception con mensaje legible si el evento esta bloqueado.
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

            throw new Exception($mensajes[$resultado] ?? "No se pudo cancelar la reservación ($resultado).");

        } catch (PDOException $e) {
            throw new Exception("Error en la base de datos: " . $e->getMessage());
        }
    }
}