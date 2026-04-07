<?php
require_once "./../conexion.php";

class lugarDAO
{

    private $id;
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function getLugares()
    {
        try {
            $sql = "CALL getLugares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al realizar la consulta en la base de datos: " . $e->getMessage());
        }
    }

    public function crearLugar($nombre, $descripcion, $direccion, $ciudad, $zona, $id_admin)
    {
        try {
            $sql = "CALL crearLugar(:nombre, :descripcion, :direccion, :ciudad, :zona, :id_admin)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':ciudad', $ciudad);
            $stmt->bindParam(':zona', $zona);
            $stmt->bindParam(':id_admin', $id_admin);

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result['id_lugar'];
        } catch (PDOException $e) {
            throw new Exception("Error en la creacion del lugar en la base de datos: " . $e->getMessage());
        }
    }

    public function updateImagen($id, $imagen_nombre)
    {
        try {
            $sql = "CALL actualizarImagenLugar(:id, :imagen)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':imagen', $imagen_nombre);

            $result = $stmt->execute();
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar la imagen del lugar en la base de datos: " . $e->getMessage());
        }
    }

    public function eliminarLugarPorId($id)
    {
        try {
            $sql = "CALL eliminarLugar(:id)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id', $id);

            $result = $stmt->execute();
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getLugarPorID($idLugar)
    {
        try {
            $sql = "CALL getLugarPorId(:id)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id', $idLugar, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener lugar: " . $e->getMessage());
        }
    }

    public function updateLugar($id, $nombre, $descripcion, $direccion, $ciudad, $zona)
    {
        try {
            $sql = "CALL actualizarLugar(:id, :nombre, :descripcion, :direccion, :ciudad, :zona)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':ciudad', $ciudad);
            $stmt->bindParam(':zona', $zona);

            $result = $stmt->execute();
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar el lugar en la base de datos: " . $e->getMessage());
        }
    }
}
