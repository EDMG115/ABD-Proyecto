<?php
require_once "./../conexion.php";

class CrearReservacionDAO
{

    private $id;
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function crearReservacion($id_evento, $id_cliente, $estado)
    {
        try {

            $this->conexion->beginTransaction();

            $sql = "CALL sp_crear_reservacion(:id_evento, :id_cliente, :estado)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_evento', $id_evento);
            $stmt->bindParam(':id_cliente', $id_cliente);
            $stmt->bindParam(':estado', $estado);

            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $this->conexion->commit();

            return $resultado['id_reservacion'];
        } catch (PDOException $e) {

            $this->conexion->rollBack();

            throw new Exception("Error en la creación de la reservación: " . $e->getMessage());
        }
    }
}
