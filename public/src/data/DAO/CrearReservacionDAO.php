<?php
require_once "./../conexion.php";
class CrearReservacionDAO{

    private $id;
    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function crearReservacion($id_evento, $id_cliente, $estado)
    {
        try {
            $sql = "CALL crearReservacion(:id_evento, :id_cliente, :estado)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_evento', $id_evento);
            $stmt->bindParam(':id_cliente', $id_cliente);
            $stmt->bindParam(':estado', $estado);

            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $resultado['id_reservacion'];

        } catch (PDOException $e) {
            throw new Exception("Error en la creación de la reservación: " . $e->getMessage());
        }
    }
}