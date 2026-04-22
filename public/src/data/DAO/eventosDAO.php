<?php
require_once "./../conexion.php";

class eventosDAO{

    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    // ==========================================
    // LECTURA (SELECT) - SIN CANDADOS
    // ==========================================

    public function getEventosPorOrganizadora($idOrganizadora){
        try{
            $sql = "CALL sp_get_eventos_por_organizadora(:id)";
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
            $sql = "CALL sp_get_eventos_para_calendario(:id)";
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
            $sql = "CALL sp_get_numero_eventos_este_mes(:id)";
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
            $sql = "CALL sp_get_tipo_actividad_por_id(:id)";
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
            $sql = "CALL sp_get_tipos_actividad()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            throw new Exception("Error al obtener tipos de actividad: " . $e->getMessage());
        }
    }

    public function getEventoPorID($idEvento){
        try{
            $sql = "CALL sp_get_evento_por_id(:id)";
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
            $sql = "CALL sp_filtrar_eventos_por_lugar(:id, :lugar)";
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
            $sql = "CALL sp_filtrar_eventos_por_fecha(:id, :inicio, :fin)";
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
            $sql = "CALL sp_ordenar_eventos_por_fecha(:id, :asc_flag)";
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

    // ==========================================
    // ESCRITURA (INSERT/UPDATE/DELETE) - CON CANDADOS (FLOCK)
    // ==========================================

    public function actualizarFechaEvento($idEvento, $nuevaFecha) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_evento_" . $idEvento . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_actualizar_fecha_evento(:id, :fecha)";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(":id", $idEvento, PDO::PARAM_INT);
                $stmt->bindParam(":fecha", $nuevaFecha, PDO::PARAM_STR);

                return $stmt->execute();
            } catch(PDOException $e) {
                throw new Exception("Error al actualizar fecha del evento: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El evento está siendo modificado. Intenta nuevamente.");
        }
    }

    public function actualizarEvento($id_evento, $nombre, $descripcion, $hora_evento, $precio_boleto, $id_lugar, $id_tipo_actividad) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_evento_" . $id_evento . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_actualizar_evento(:id_evento, :nombre, :descripcion, :hora_evento, :precio_boleto, :id_lugar, :id_tipo_actividad)";
                $stmt = $this->conexion->prepare($sql);
                
                $stmt->bindParam(':id_evento', $id_evento, PDO::PARAM_INT);
                $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
                $stmt->bindParam(':hora_evento', $hora_evento, PDO::PARAM_STR);
                $stmt->bindParam(':precio_boleto', $precio_boleto, PDO::PARAM_STR); 
                $stmt->bindParam(':id_lugar', $id_lugar, PDO::PARAM_INT);
                $stmt->bindParam(':id_tipo_actividad', $id_tipo_actividad, PDO::PARAM_INT);

                return $stmt->execute();
            } catch(PDOException $e) {
                 throw new Exception("Error al actualizar el evento: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El evento está siendo modificado. Intenta nuevamente.");
        }
    }

    public function actualizarImagen($id_evento, $nuevoNombreImagen) {
        if (empty($nuevoNombreImagen)) {
            throw new Exception("El nombre de la imagen no puede estar vacío.");
        }

        $lockFile = fopen(sys_get_temp_dir() . "/lock_evento_" . $id_evento . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_actualizar_imagen_evento(:id_evento, :imagen)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':imagen', $nuevoNombreImagen, PDO::PARAM_STR);
                $stmt->bindParam(':id_evento', $id_evento, PDO::PARAM_INT);

                return $stmt->execute();
            } catch(PDOException $e) {
                 throw new Exception("Error al actualizar la imagen: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El evento está siendo modificado. Intenta nuevamente.");
        }
    }

    public function crearEvento($nombre_evento, $descripcion, $fecha_evento, $hora_evento, $fecha_registro, $precio_boleto, $imagen_url, $id_lugar, $id_tipo_actividad, $id_organizadora) {
        // Bloqueamos por el id_lugar para evitar que dos organizadores aparten el mismo lugar a la vez
        $lockFile = fopen(sys_get_temp_dir() . "/lock_lugar_" . $id_lugar . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_crear_evento(:nombre_evento, :descripcion, :fecha_evento, :hora_evento, :fecha_registro, :precio_boleto, :imagen_url, :id_lugar, :id_tipo_actividad, :id_organizadora)";
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
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            return false; // El sistema está ocupado
        }
    }

    public function eliminarEvento($id_evento) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_evento_" . $id_evento . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_eliminar_evento(:id_evento)";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(':id_evento', $id_evento, PDO::PARAM_INT);

                return $stmt->execute();
            } catch(PDOException $e) {
                 throw new Exception("Error al eliminar el evento: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El evento está siendo modificado. Intenta nuevamente.");
        }
    }
}
?>