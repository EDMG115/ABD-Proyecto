<?php
require_once "./../conexion.php";

class CarruselDAO
{
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    // LECTURA (SELECT) - NO REQUIERE CANDADO (FLOCK)
    public function getLugaresMasPopulares($limite = 30)
    {
        try {
            $sql = "CALL sp_get_lugares_mas_populares(:limite)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;

        } catch (PDOException $e) {
            throw new Exception("Error al obtener los lugares más populares: " . $e->getMessage());
        }
    }

    // LECTURA (SELECT) - NO REQUIERE CANDADO (FLOCK)
    public function getEventosDisponibles($limite = 20)
    {
        try {
            $sql = "CALL sp_get_eventos_disponibles(:limite)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;

        } catch (PDOException $e) {
            throw new Exception("Error al obtener los eventos disponibles: " . $e->getMessage());
        }
    }
}
?>