<?php
require_once "./../conexion.php";

class CrearViajeDAO
{

    private $id;
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function crearViaje($id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje)
    {
        try {

            $this->conexion->beginTransaction();

            $sql = "CALL sp_crear_viaje(:id_cliente, :id_paquete, :estado, :fecha_viaje, :hora_viaje)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_cliente', $id_cliente);
            $stmt->bindParam(':id_paquete', $id_paquete);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':fecha_viaje', $fecha_viaje);
            $stmt->bindParam(':hora_viaje', $hora_viaje);

            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $this->conexion->commit();

            return $resultado['id_generado'];
        } catch (PDOException $e) {

            $this->conexion->rollBack();

            throw new Exception("Error en la creación del viaje: " . $e->getMessage());
        }
    }
}
