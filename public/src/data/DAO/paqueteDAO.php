<?php
require_once __DIR__ ."/../conexion.php";

class paqueteDAO{

    private $id;
    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function getPaquetesPorAgencia($idAgencia){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_paquetes.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try{
                $sql = "CALL sp_get_paquetes_por_agencia(:id)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(":id", $idAgencia, PDO::PARAM_INT);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch(PDOException $e){
                throw new Exception("Error al obtener paquetes: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function getPaquetePorID($idPaquete){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_paquetes.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try{
                $sql = "CALL sp_get_paquete_por_id(:id)";
                $stmt = $this->conexion->prepare($sql);
                
                $stmt->bindParam(":id", $idPaquete, PDO::PARAM_INT);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch(PDOException $e){
                throw new Exception("Error al obtener paquete: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function getPaquetesPorLugar($id_lugar){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_paquetes.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try{
                $sql = "CALL sp_get_paquetes_por_lugar(:id_lugar)";
                
                $stmt = $this->conexion->prepare($sql);
                $stmt -> bindParam(':id_lugar', $id_lugar, PDO::PARAM_INT);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e){
                throw new Exception("Error al realizar la consulta en la base de datos: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function getNumeroPaquetesEsteMes($idAgencia){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_paquetes.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try{
                $sql = "CALL sp_get_numero_paquetes_por_agencia(:id)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(":id", $idAgencia, PDO::PARAM_INT);
                $stmt->execute();

                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $res['total'];
            } catch(PDOException $e) {
                throw new Exception("Error al contar paquetes: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function filtrarPaquetesPorLugar($idAgencia, $lugar){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_paquetes.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try{
                $sql = "CALL sp_filtrar_paquetes_por_lugar(:id, :lugar)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(":id", $idAgencia, PDO::PARAM_INT);
                $stmt->bindValue(":lugar", $lugar);

                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch(PDOException $e){
                throw new Exception("Error al filtrar por lugar: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function ordenarPaquetesPorPrecio($idAgencia, $asc = true){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_paquetes.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try{
                $sql = "CALL sp_get_paquetes_ordenados_por_precio(:id, :orden)";
                $stmt = $this->conexion->prepare($sql);

                $orden = $asc ? 'ASC' : 'DESC';

                $stmt->bindParam(":id", $idAgencia, PDO::PARAM_INT);
                $stmt->bindParam(":orden", $orden);

                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch(PDOException $e){
                throw new Exception("Error al ordenar paquetes: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function crearPaquete($nombre_paquete, $descripcion_paquete, $precio, $imagen_url, $id_lugar, $id_agencia) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_paquetes.txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_crear_paquete(:nombre, :descripcion, :precio, :imagen, :id_lugar, :id_agencia)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(":nombre", $nombre_paquete);
                $stmt->bindParam(":descripcion", $descripcion_paquete);
                $stmt->bindParam(":precio", $precio);
                $stmt->bindParam(":imagen", $imagen_url);
                $stmt->bindParam(":id_lugar", $id_lugar);
                $stmt->bindParam(":id_agencia", $id_agencia);

                $stmt->execute();

                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $res['id_paquete'];
            } catch (PDOException $e) {
                error_log("Error en PaqueteDAO::crearPaquete: " . $e->getMessage());
                return false;
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            return false;
        }
    }

    public function actualizarPaquete($id_paquete, $nombre_paquete, $descripcion_paquete, $precio, $id_lugar) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_paquete_" . $id_paquete . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_actualizar_paquete(:id, :nombre, :descripcion, :precio, :id_lugar)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':id', $id_paquete);
                $stmt->bindParam(':nombre', $nombre_paquete);
                $stmt->bindParam(':descripcion', $descripcion_paquete);
                $stmt->bindParam(':precio', $precio);
                $stmt->bindParam(':id_lugar', $id_lugar);

                $result = $stmt->execute();
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al actualizar paquete: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El paquete está siendo editado por alguien más.");
        }
    }

    public function actualizarImagen($id_paquete, $nuevoNombreImagen) {
        if (empty($nuevoNombreImagen)) {
            throw new Exception("El nombre de la imagen no puede estar vacío.");
        }

        $lockFile = fopen(sys_get_temp_dir() . "/lock_paquete_" . $id_paquete . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_actualizar_imagen_paquete(:id, :imagen)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':id', $id_paquete);
                $stmt->bindParam(':imagen', $nuevoNombreImagen);

                $result = $stmt->execute();
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al actualizar la imagen: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El paquete está siendo editado por alguien más.");
        }
    }

    public function eliminarPaquete($id_paquete) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_paquete_" . $id_paquete . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_eliminar_paquete(:id)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':id', $id_paquete);

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
}
?>
