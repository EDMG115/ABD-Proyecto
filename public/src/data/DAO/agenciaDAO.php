<?php
require_once "./../conexion.php";

class agenciaDAO{

    private $id;
    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function altaAgencia($descripcion, $nombre, $fecha, $direccion, $imagen, $usuario, $clave, $cel, $correo){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_agencias.txt", "w+");
        
        if (flock($lockFile, LOCK_EX)) {
            try{
                $sql = "CALL sp_alta_agencia(:descripcion, :nombre, :fecha, :direccion, :imagen, :usuario, :clave, :cel, :correo)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(":descripcion", $descripcion);
                $stmt->bindParam(":nombre", $nombre);
                $stmt->bindParam(":fecha", $fecha);
                $stmt->bindParam(":direccion", $direccion);
                $stmt->bindParam(":imagen", $imagen);
                $stmt->bindParam(":usuario", $usuario);
                $stmt->bindParam(":clave", $clave);
                $stmt->bindParam(":cel", $cel);
                $stmt->bindParam(":correo", $correo);

                return $stmt->execute();
            } catch(PDOException $e){
                throw new Exception("Error al insertar agencia: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado gestionando agencias. Intenta en un momento.");
        }
    }

    public function actualizarAgencia($id, $descripcion, $nombre, $direccion, $imagen, $usuario, $clave, $cel, $correo){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_agencias.txt", "w+");

        if (flock($lockFile, LOCK_EX)) {
            try{
                $sql = "CALL sp_actualizar_agencia(:id, :descripcion, :nombre, :direccion, :imagen, :usuario, :clave, :cel, :correo)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                $stmt->bindParam(":descripcion", $descripcion);
                $stmt->bindParam(":nombre", $nombre);
                $stmt->bindParam(":direccion", $direccion);
                $stmt->bindParam(":imagen", $imagen);
                $stmt->bindParam(":usuario", $usuario);
                $stmt->bindParam(":clave", $clave);
                $stmt->bindParam(":cel", $cel);
                $stmt->bindParam(":correo", $correo);

                return $stmt->execute();
            } catch(PDOException $e){
                throw new Exception("Error al actualizar agencia: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado gestionando agencias. Intenta en un momento.");
        }
    }

    public function eliminarAgencia($id){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_agencias.txt", "w+");

        if (flock($lockFile, LOCK_EX)) {
            try{
                $sql = "CALL sp_baja_agencia(:id)";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(":id", $id, PDO::PARAM_INT);

                return $stmt->execute();
            } catch(PDOException $e){
                throw new Exception("Error al eliminar agencia: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado gestionando agencias. Intenta en un momento.");
        }
    }

    // LAS FUNCIONES DE LECTURA (SELECT) SE QUEDAN SIN LOCK PARA NO AFECTAR RENDIMIENTO
    public function validarAgencia(string $user, string $password){
        try{
            $sql = "CALL sp_consultar_agencia_por_usuario(:user)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':user', $user); 
            $stmt->execute();

            $agencia = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($agencia && $agencia['password'] === $password) {
                return $agencia;
            }
            return null;
        }catch (PDOException $e){
            throw new Exception("Error al realizar la consulta: " . $e->getMessage());
        }
    }

    public function getAgenciaPorID($idAgencia){
        try{
            $sql = "CALL sp_consultar_agencia_por_id(:id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idAgencia, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception("Error al obtener agencia: " . $e->getMessage());
        }
    }
}
?>