<?php
require_once "./../conexion.php";

class registroDAO
{
    private $id;
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function existeRegistro(string $user): bool
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_registros.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $stmt = $this->conexion->prepare("CALL sp_existe_usuario(:user)");
                $stmt->bindParam(':user', $user);
                $stmt->execute();

                $total = $stmt->fetchColumn();
                $stmt->closeCursor();

                return $total > 0;
            } catch (PDOException $e) {
                throw new Exception("Error al verificar existencia de usuario: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function registroUsuario($nombre, $apellido, $correo, $telefono, $usuario, $contra)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_registros.txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $stmt = $this->conexion->prepare("CALL sp_registro_cliente(:usuario, :contra, :nombre, :apellido, :correo, :telefono)");
                
                $stmt->bindParam(':usuario', $usuario);
                $stmt->bindParam(':contra', $contra);
                $stmt->bindParam(':nombre', $nombre);
                $stmt->bindParam(':apellido', $apellido);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':telefono', $telefono);

                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result['id'] ?? null;

            } catch (PDOException $e) {
                throw new Exception("Error en la creación del usuario: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado procesando registros. Intenta de nuevo.");
        }
    }

    public function registroOrganizadora($nombre_agencia, $descripcion, $correo, $telefono, $usuario, $contra)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_registros.txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $fecha = date("Y-m-d");
                $direccion_default = "Sin especificar";
                $imagen_default = "default.png";

                $stmt = $this->conexion->prepare(
                    "CALL sp_registro_organizadora(
                        :usuario, :contra, :descripcion, :nombre_agencia, 
                        :fecha, :direccion, :imagen, :telefono, :correo
                    )"
                );

                $stmt->bindParam(':usuario', $usuario);
                $stmt->bindParam(':contra', $contra);
                $stmt->bindParam(':descripcion', $descripcion);
                $stmt->bindParam(':nombre_agencia', $nombre_agencia);
                $stmt->bindParam(':fecha', $fecha);
                $stmt->bindParam(':direccion', $direccion_default);
                $stmt->bindParam(':imagen', $imagen_default);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':correo', $correo);

                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result['id'] ?? null;

            } catch (PDOException $e) {
                throw new Exception("Error en el registro de organizadora: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado procesando registros. Intenta de nuevo.");
        }
    }

    public function registroAgencia($nombre_agencia, $descripcion, $correo, $telefono, $usuario, $contra)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_registros.txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $fecha = date("Y-m-d");
                $direccion_default = "Sin especificar";
                $imagen_default = "default.png";

                $stmt = $this->conexion->prepare(
                    "CALL sp_registro_agencia(
                        :usuario, :contra, :descripcion, :nombre_agencia, 
                        :fecha, :direccion, :imagen, :telefono, :correo
                    )"
                );

                $stmt->bindParam(':usuario', $usuario);
                $stmt->bindParam(':contra', $contra);
                $stmt->bindParam(':descripcion', $descripcion);
                $stmt->bindParam(':nombre_agencia', $nombre_agencia);
                $stmt->bindParam(':fecha', $fecha);
                $stmt->bindParam(':direccion', $direccion_default);
                $stmt->bindParam(':imagen', $imagen_default);
                $stmt->bindParam(':telefono', $telefono);
                $stmt->bindParam(':correo', $correo);

                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result['id'] ?? null;

            } catch (PDOException $e) {
                throw new Exception("Error en el registro de agencia: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado procesando registros. Intenta de nuevo.");
        }
    }
}
?>