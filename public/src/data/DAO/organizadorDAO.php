<?php
require_once "./../conexion.php";

class organizadorDAO{

    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function validarOrganizador(string $user, string $password){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_organizador.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try{
                $sql = "CALL sp_validar_organizador(:user)";

                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(':user', $user); 
                $stmt->execute();

                $organizador = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                if ($organizador && $organizador['password'] === $password) {
                    return $organizador;
                }
                return null;
            } catch (PDOException $e){
                throw new Exception("Error al realizar la consulta: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado. Intenta de nuevo.");
        }
    }

    public function getOrganizadorPorID($idOrganizadora){
        $lockFile = fopen(sys_get_temp_dir() . "/lock_organizador.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try{
                $sql = "CALL sp_get_organizadora_por_id(:id)";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(":id", $idOrganizadora, PDO::PARAM_INT);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch(PDOException $e){
                throw new Exception("Error al obtener organizador: " . $e->getMessage());
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