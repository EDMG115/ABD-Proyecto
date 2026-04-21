<?php
require_once "./../conexion.php";

class eventoDAO
{
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    // LECTURA (SELECT) - NO REQUIERE CANDADO (FLOCK)
    public function getEvento()
    {
        try {
            $sql = "CALL sp_get_all_eventos()";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al realizar la consulta en la base de datos: " . $e->getMessage());
        }
    }
}
?>