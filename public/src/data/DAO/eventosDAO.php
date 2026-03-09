<?php
require_once "./../conexion.php";

class eventosDAO{

    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function getEventosPorOrganizadora($idOrganizadora){
        try{
            $sql = "CALL getEventosPorOrganizadora(:id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idOrganizadora, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception("Error al obtener eventos: " . $e->getMessage());
        }
    }
    
    public function getEventosParaCalendario($idOrganizadora){
        try{
            $sql = "CALL getEventosParaCalendario(:id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idOrganizadora, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception("Error al obtener eventos: " . $e->getMessage());
        }
    }

    public function getNumeroEventosEsteMes($idOrganizadora){
        try{
            $sql = "CALL getNumeroEventosEsteMes(:id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idOrganizadora, PDO::PARAM_INT);
            $stmt->execute();

            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res['total'];
        } catch(PDOException $e) {
            throw new Exception("Error al contar eventos del mes: " . $e->getMessage());
        }
    }

    public function getTipoActividadPorID($idTipoActividad){
        try{
            $sql = "CALL getTipoActividadPorID(:id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idTipoActividad, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception("Error al obtener tipo de actividad: " . $e->getMessage());
        }
    }

    public function getTodosTiposActividad(){
        try{
            $sql = "CALL getTodosTiposActividad()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception("Error al obtener tipos de actividad: " . $e->getMessage());
        }
    }

    public function getEventoPorID($idEvento){
        try{
            $sql = "CALL getEventoPorID(:id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idEvento, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception("Error al obtener evento: " . $e->getMessage());
        }
    }

    public function filtrarEventosPorLugar($idOrganizadora, $lugar){
        try{
            $sql = "CALL filtrarEventosPorLugar(:id, :lugar)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idOrganizadora, PDO::PARAM_INT);
            $stmt->bindParam(":lugar", $lugar, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception("Error al filtrar por lugar: " . $e->getMessage());
        }
    }

    public function filtrarEventosPorFecha($idOrganizadora, $inicio, $fin){
        try{
            $sql = "CALL filtrarEventosPorFecha(:id, :inicio, :fin)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idOrganizadora, PDO::PARAM_INT);
            $stmt->bindParam(":inicio", $inicio);
            $stmt->bindParam(":fin", $fin);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception("Error al filtrar por fecha: " . $e->getMessage());
        }
    }

    public function ordenarEventosPorFecha($idOrganizadora, $asc = true){
        try{
            $sql = "CALL ordenarEventosPorFecha(:id, :asc_flag)";
            $stmt = $this->conexion->prepare($sql);
            
            $asc_flag = $asc ? 1 : 0;
            
            $stmt->bindParam(":id", $idOrganizadora, PDO::PARAM_INT);
            $stmt->bindParam(":asc_flag", $asc_flag, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception("Error al ordenar eventos: " . $e->getMessage());
        }
    }

    public function actualizarFechaEvento($idEvento, $nuevaFecha) {
        try {
            $sql = "CALL actualizarFechaEvento(:id, :fecha)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idEvento, PDO::PARAM_INT);
            $stmt->bindParam(":fecha", $nuevaFecha, PDO::PARAM_STR);

            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Error al actualizar fecha del evento: " . $e->getMessage());
        }
    }

    public function actualizarEvento($id_evento, $nombre, $descripcion, $hora_evento, $precio_boleto, $id_lugar, $id_tipo_actividad) {
        $sql = "CALL actualizarEvento(:id_evento, :nombre, :descripcion, :hora_evento, :precio_boleto, :id_lugar, :id_tipo_actividad)";
        $stmt = $this->conexion->prepare($sql);
        
        $stmt->bindParam(':id_evento', $id_evento, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $stmt->bindParam(':hora_evento', $hora_evento, PDO::PARAM_STR);
        $stmt->bindParam(':precio_boleto', $precio_boleto, PDO::PARAM_STR); 
        $stmt->bindParam(':id_lugar', $id_lugar, PDO::PARAM_INT);
        $stmt->bindParam(':id_tipo_actividad', $id_tipo_actividad, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function actualizarImagen($id_evento, $nuevoNombreImagen) {
        if (empty($nuevoNombreImagen)) {
            throw new Exception("El nombre de la imagen no puede estar vacío.");
        }

        $sql = "CALL actualizarImagen(:id_evento, :imagen)";
        $stmt = $this->conexion->prepare($sql);

        $stmt->bindParam(':imagen', $nuevoNombreImagen, PDO::PARAM_STR);
        $stmt->bindParam(':id_evento', $id_evento, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function crearEvento($nombre_evento, $descripcion, $fecha_evento, $hora_evento, $fecha_registro, $precio_boleto, $imagen_url, $id_lugar, $id_tipo_actividad, $id_organizadora) {
        try {
            $sql = "CALL crearEvento(:nombre_evento, :descripcion, :fecha_evento, :hora_evento, :fecha_registro, :precio_boleto, :imagen_url, :id_lugar, :id_tipo_actividad, :id_organizadora)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(":nombre_evento", $nombre_evento);
            $stmt->bindParam(":descripcion", $descripcion);
            $stmt->bindParam(":fecha_evento", $fecha_evento);
            $stmt->bindParam(":hora_evento", $hora_evento);
            $stmt->bindParam(":fecha_registro", $fecha_registro);
            $stmt->bindValue(":precio_boleto", (float)$precio_boleto, PDO::PARAM_STR);
            $stmt->bindParam(":imagen_url", $imagen_url);
            $stmt->bindParam(":id_lugar", $id_lugar);
            $stmt->bindParam(":id_tipo_actividad", $id_tipo_actividad);
            $stmt->bindParam(":id_organizadora", $id_organizadora); 

            if ($stmt->execute()) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['id_generado'] ?? false;
            } else {
                return false;
            }

        } catch (PDOException $e) {
            error_log("Error en EventosDAO::crearEvento: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarEvento($id_evento) {
        $sql = "CALL eliminarEvento(:id_evento)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id_evento', $id_evento, PDO::PARAM_INT);

        return $stmt->execute();
    }
}