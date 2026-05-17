<?php
require_once __DIR__ . "/../conexion.php";

class paqueteDAO
{
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    // ==========================================
    // LECTURA (SELECT) - SIN TRANSACCIONES
    // ==========================================

    public function getPaquetesPorAgencia($idAgencia)
    {
        try {
            $sql  = "CALL sp_get_paquetes_por_agencia(:id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idAgencia, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener paquetes: " . $e->getMessage());
        }
    }

    public function getPaquetePorID($idPaquete)
    {
        try {
            $sql  = "CALL sp_get_paquete_por_id(:id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idPaquete, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener paquete: " . $e->getMessage());
        }
    }

    public function getPaquetesPorLugar($id_lugar)
    {
        try {
            $sql  = "CALL sp_get_paquetes_por_lugar(:id_lugar)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_lugar', $id_lugar, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al realizar la consulta: " . $e->getMessage());
        }
    }

    public function getNumeroPaquetesEsteMes($idAgencia)
    {
        try {
            $sql  = "CALL sp_get_numero_paquetes_por_agencia(:id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $idAgencia, PDO::PARAM_INT);
            $stmt->execute();

            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $res['total'];
        } catch (PDOException $e) {
            throw new Exception("Error al contar paquetes: " . $e->getMessage());
        }
    }

    public function filtrarPaquetesPorLugar($idAgencia, $lugar)
    {
        try {
            $sql  = "CALL sp_filtrar_paquetes_por_lugar(:id, :lugar)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id",    $idAgencia, PDO::PARAM_INT);
            $stmt->bindValue(":lugar", $lugar);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al filtrar por lugar: " . $e->getMessage());
        }
    }

    public function ordenarPaquetesPorPrecio($idAgencia, $asc = true)
    {
        try {
            $orden = $asc ? 'ASC' : 'DESC';
            $sql   = "CALL sp_get_paquetes_ordenados_por_precio(:id, :orden)";
            $stmt  = $this->conexion->prepare($sql);
            $stmt->bindParam(":id",    $idAgencia, PDO::PARAM_INT);
            $stmt->bindParam(":orden", $orden);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al ordenar paquetes: " . $e->getMessage());
        }
    }

    // ==========================================
    // ESCRITURA — CON TRANSACCIONES Y BLOQUEO
    // ==========================================

    /**
     * Crea un paquete nuevo. No requiere bloqueo previo (insercion nueva).
     */
    public function crearPaquete($nombre_paquete, $descripcion_paquete, $precio, $imagen_url, $id_lugar, $id_agencia)
    {
        try {
            $this->conexion->beginTransaction();

            $sql  = "CALL sp_crear_paquete(:nombre, :descripcion, :precio, :imagen, :id_lugar, :id_agencia)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":nombre",      $nombre_paquete);
            $stmt->bindParam(":descripcion", $descripcion_paquete);
            $stmt->bindParam(":precio",      $precio);
            $stmt->bindParam(":imagen",      $imagen_url);
            $stmt->bindParam(":id_lugar",    $id_lugar);
            $stmt->bindParam(":id_agencia",  $id_agencia);
            $stmt->execute();

            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $this->conexion->commit();

            return $res['id_paquete'];
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en paqueteDAO::crearPaquete: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un paquete validando el bloqueo de concurrencia.
     * Llama a sp_actualizar_paquete que verifica con SELECT FOR UPDATE
     * que el $id_admin sea el dueno del bloqueo activo.
     *
     * @throws Exception Si el bloqueo no existe o pertenece a otro admin.
     */
    public function actualizarPaquete($id_admin, $id_paquete, $nombre_paquete, $descripcion_paquete, $precio, $id_lugar)
    {
        try {
            $sql  = "CALL sp_actualizar_paquete(:id_admin, :id, :nombre, :descripcion, :precio, :id_lugar, NULL, NULL)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_admin',    $id_admin,           PDO::PARAM_INT);
            $stmt->bindParam(':id',          $id_paquete,         PDO::PARAM_INT);
            $stmt->bindParam(':nombre',      $nombre_paquete);
            $stmt->bindParam(':descripcion', $descripcion_paquete);
            $stmt->bindParam(':precio',      $precio);
            $stmt->bindParam(':id_lugar',    $id_lugar,           PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function actualizarImagen($id_paquete, $nuevoNombreImagen)
    {
        if (empty($nuevoNombreImagen)) {
            throw new Exception("El nombre de la imagen no puede estar vacio.");
        }

        try {
            $this->conexion->beginTransaction();

            $sql  = "CALL sp_actualizar_imagen_paquete(:id, :imagen)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id',     $id_paquete);
            $stmt->bindParam(':imagen', $nuevoNombreImagen);
            $result = $stmt->execute();
            $stmt->closeCursor();

            $this->conexion->commit();

            return $result;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            throw new Exception("Error al actualizar la imagen: " . $e->getMessage());
        }
    }

    /**
     * Elimina un paquete validando el bloqueo de concurrencia.
     * sp_eliminar_paquete verifica el bloqueo internamente con SELECT FOR UPDATE.
     *
     * @throws Exception Si el bloqueo no existe o pertenece a otro admin.
     */
    public function eliminarPaquete($id_admin, $id_paquete)
    {
        try {
            $sql  = "CALL sp_eliminar_paquete(:id_admin, :id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_admin', $id_admin,   PDO::PARAM_INT);
            $stmt->bindParam(':id',       $id_paquete, PDO::PARAM_INT);
            $result = $stmt->execute();
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
}