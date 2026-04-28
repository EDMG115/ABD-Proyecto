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
        $lockFile = fopen(sys_get_temp_dir() . "/lock_lugares.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_lugares()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al realizar la consulta en la base de datos: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function crearLugar($nombre, $descripcion, $direccion, $ciudad, $zona, $id_admin)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_lugares.txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_crear_lugar(:nombre, :descripcion, :direccion, :ciudad, :zona, :id_admin)";
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
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function updateImagen($id, $imagen_nombre)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_lugar_" . $id . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_actualizar_imagen_lugar(:id, :imagen)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':imagen', $imagen_nombre);

                $result = $stmt->execute();
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al actualizar la imagen del lugar en la base de datos: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function eliminarLugarPorId($id)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_lugar_" . $id . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_eliminar_lugar(:id)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':id', $id);

                $result = $stmt->execute();
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                return false;
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            return false;
        }
    }

    public function getLugarPorID($idLugar)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_lugares.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_lugar_por_id(:id)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':id', $idLugar, PDO::PARAM_INT);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener lugar: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function updateLugar($id, $nombre, $descripcion, $direccion, $ciudad, $zona)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_lugar_" . $id . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_actualizar_lugar(:id, :nombre, :descripcion, :direccion, :ciudad, :zona)";
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
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }
}
?>