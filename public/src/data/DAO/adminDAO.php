<?php
require_once "./../conexion.php";

class adminDAO{

    private $id;
    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function validarAdmin(string $user, string $password){
        try {
            $sql = "CALL getAdminPorUsuario(:user)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':user', $user);
            $stmt->execute();

            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && $admin['password'] === $password) {
                $stmt->closeCursor(); // importante
                return $admin;
            }

            $stmt->closeCursor();
            return null;

        } catch (PDOException $e) {
            throw new Exception("Error al realizar la consulta: " . $e->getMessage());
        }
    }
}
?>