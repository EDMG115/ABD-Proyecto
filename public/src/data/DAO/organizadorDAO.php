<?php
require_once "./../conexion.php";

class organizadorDAO{

    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function validarOrganizador(string $user, string $password){
        try{
            $sql = "CALL validarOrganizador(:user)";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':user', $user); 
            $stmt->execute();

            $organizador = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($organizador && $organizador['password'] === $password) {
                return $organizador;
            }
            return null;
        }catch (PDOException $e){
            throw new Exception("Error al realizar la consulta: " . $e->getMessage());
        }
    }

    public function getOrganizadorPorID($idOrganizadora){
        try{
            $sql = "CALL getOrganizadorPorID(:id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idOrganizadora, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception("Error al obtener organizador: " . $e->getMessage());
        }
    }
}