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
        try {
            $sql = "CALL obtenerHistorialDetallado(:id_cliente)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            // Se lanza una excepción para que ReservacionLogic.php la maneje.
            throw new Exception("Error al consultar historial de reservaciones: " . $e->getMessage());
        }
    }
    
    /**
     * Actualiza el estado de una reservación a 'cancelado'.
     */
    public function cancelarReservacion($id_reservacion)
    {
        try {
            $sql = "CALL cancelarReservacion(:id_reservacion)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_reservacion', $id_reservacion, PDO::PARAM_INT);

            $result = $stmt->execute();
            $stmt->closeCursor();

            return $result; // true si se actualizó, false si no
        } catch (PDOException $e) {
            throw new Exception("Error al cancelar reservación: " . $e->getMessage());
        }
    }
}
?>