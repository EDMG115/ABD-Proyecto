<?php
require_once "./../conexion.php";

class usuarioDAO{

    private $id;
    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function validarUsuario(string $user, string $password){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_usuario.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try{
                $sql = "CALL sp_validar_cliente_por_usuario(:user)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':user', $user, PDO::PARAM_STR);
                $stmt->execute();

                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                // Validación de contraseña en PHP
                if ($usuario && $usuario['password'] === $password) {
                    return $usuario;
                }
                return null;
            } catch (PDOException $e){
                throw new Exception("Error al realizar la consulta: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado validando usuarios. Intenta de nuevo.");
        }
    }
}
?>