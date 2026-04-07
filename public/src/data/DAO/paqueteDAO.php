<?php
require_once "./../conexion.php";

class paqueteDAO{

    private $id;
    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function getPaquetesPorAgencia($idAgencia){
        try{
            $sql = "CALL getPaquetesPorAgencia(:id)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(":id", $idAgencia, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch(PDOException $e){
            throw new Exception("Error al obtener paquetes: " . $e->getMessage());
        }
    }

    public function getPaquetePorID($idPaquete){
        try{
            $sql = "CALL getPaquetePorID(:id)";
            $stmt = $this->conexion->prepare($sql);
            
            $stmt->bindParam(":id", $idPaquete, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch(PDOException $e){
            throw new Exception("Error al obtener paquete: " . $e->getMessage());
        }
    }

    public function getPaquetesPorLugar($id_lugar){
        try{
            $sql = "CALL getPaquetesPorLugar(:id_lugar)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt -> bindParam(':id_lugar', $id_lugar, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e){
            throw new Exception("Error al realizar la consulta en la base de datos: " . $e->getMessage());
        }

    }

    public function getNumeroPaquetesEsteMes($idAgencia){
        try{
            $sql = "CALL getNumeroPaquetesPorAgencia(:id)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(":id", $idAgencia, PDO::PARAM_INT);
            $stmt->execute();

            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $res['total'];
        } catch(PDOException $e) {
            throw new Exception("Error al contar paquetes: " . $e->getMessage());
        }
    }

    public function filtrarPaquetesPorLugar($idAgencia, $lugar){
        try{
            $sql = "CALL filtrarPaquetesPorLugar(:id, :lugar)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(":id", $idAgencia, PDO::PARAM_INT);
            $stmt->bindValue(":lugar", $lugar);

            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch(PDOException $e){
            throw new Exception("Error al filtrar por lugar: " . $e->getMessage());
        }
    }

    public function ordenarPaquetesPorPrecio($idAgencia, $asc = true){
        try{
            $sql = "CALL getPaquetesOrdenadosPorPrecio(:id, :orden)";
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
        }
    }

    public function crearPaquete($nombre_paquete, $descripcion_paquete, $precio, $imagen_url, $id_lugar, $id_agencia) {
        try {
            $sql = "CALL crearPaquete(:nombre, :descripcion, :precio, :imagen, :id_lugar, :id_agencia)";
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
        }
    }

    public function actualizarPaquete($id_paquete, $nombre_paquete, $descripcion_paquete, $precio, $id_lugar) {
        try {
            $sql = "CALL actualizarPaquete(:id, :nombre, :descripcion, :precio, :id_lugar)";
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
        }
    }

    public function actualizarImagen($id_paquete, $nuevoNombreImagen) {
        if (empty($nuevoNombreImagen)) {
            throw new Exception("El nombre de la imagen no puede estar vacío.");
        }

        $sql = "CALL actualizarImagenPaquete(:id, :imagen)";
        $stmt = $this->conexion->prepare($sql);

        $stmt->bindParam(':id', $id_paquete);
        $stmt->bindParam(':imagen', $nuevoNombreImagen);

        $result = $stmt->execute();
        $stmt->closeCursor();

        return $result;
    }

    public function eliminarPaquete($id_paquete) {
        $sql = "CALL eliminarPaquete(:id)";
        $stmt = $this->conexion->prepare($sql);

        $stmt->bindParam(':id', $id_paquete);

        $result = $stmt->execute();
        $stmt->closeCursor();

        return $result;
    }

}
?>

