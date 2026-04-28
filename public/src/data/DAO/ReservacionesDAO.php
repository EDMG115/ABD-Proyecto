<?php
require_once "./../conexion.php";

class ReservacionesDAO
{
    private $conexion;

    public function __construct()
    {
        // Se asume que 'Conexion' y 'getConexion()' están correctamente definidos.
        $conn = new Conexion();
        $this->conexion = $conn->getConexion();
    }

    /**
     * Obtiene el historial detallado de reservaciones de eventos para un cliente.
     * Realiza INNER JOIN con 'eventos', 'lugares', 'tipoactividad' y 'organizadoras'
     * para obtener todos los detalles solicitados.
     * Se corrigió 't.tipo' a 't.nombre_tipo_actividad'.
     */
    public function obtenerHistorialDetallado($id_cliente)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_reservaciones.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_obtener_historial_reservaciones_cliente(:id_cliente)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                // Se lanza una excepción para que ReservacionLogic.php la maneje.
                throw new Exception("Error al consultar historial de reservaciones: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado consultando información. Intenta de nuevo.");
        }
    }
    
    /**
     * Actualiza el estado de una reservación a 'cancelado'.
     */
    public function cancelarReservacion($id_reservacion)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_reservacion_" . $id_reservacion . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_cancelar_reservacion(:id_reservacion)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':id_reservacion', $id_reservacion, PDO::PARAM_INT);

                $result = $stmt->execute();
                $stmt->closeCursor();

                return $result; // true si se actualizó, false si no
            } catch (PDOException $e) {
                throw new Exception("Error al cancelar reservación: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("La reservación está siendo procesada por otro medio. Intenta de nuevo.");
        }
    }
}
?>