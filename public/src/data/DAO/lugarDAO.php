<?php
require_once "./../conexion.php";

class lugarDAO
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

    public function getLugares()
    {
        try {
            $sql = "CALL sp_get_lugares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al realizar la consulta en la base de datos: " . $e->getMessage());
        }
    }

    public function getLugarPorID($idLugar)
    {
        try {
            $sql = "CALL sp_get_lugar_por_id(:id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $idLugar, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener lugar: " . $e->getMessage());
        }
    }

    // ==========================================
    // ESCRITURA — CON TRANSACCIONES Y BLOQUEO
    // Todos los metodos de escritura delegan al
    // stored procedure que valida el bloqueo.
    // ==========================================

    public function crearLugar($nombre, $descripcion, $direccion, $ciudad, $zona, $id_admin)
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "CALL sp_crear_lugar(:nombre, :descripcion, :direccion, :ciudad, :zona, :id_admin)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':nombre',      $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':direccion',   $direccion);
            $stmt->bindParam(':ciudad',      $ciudad);
            $stmt->bindParam(':zona',        $zona);
            $stmt->bindParam(':id_admin',    $id_admin);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $this->conexion->commit();

            return $result['id_lugar'];
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            throw new Exception("Error en la creacion del lugar: " . $e->getMessage());
        }
    }

    public function updateImagen($id, $imagen_nombre)
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "CALL sp_actualizar_imagen_lugar(:id, :imagen)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id',     $id);
            $stmt->bindParam(':imagen', $imagen_nombre);
            $result = $stmt->execute();
            $stmt->closeCursor();

            $this->conexion->commit();

            return $result;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            throw new Exception("Error al actualizar la imagen del lugar: " . $e->getMessage());
        }
    }

    /**
     * Actualiza un lugar validando el bloqueo de concurrencia.
     * Llama a sp_actualizar_lugar que verifica internamente que
     * el $id_admin sea el dueno del bloqueo activo.
     *
     * @throws Exception Si el bloqueo no existe o pertenece a otro admin.
     */
    public function updateLugar($id_admin, $id, $nombre, $descripcion, $direccion, $ciudad, $zona)
    {
        try {
            $sql = "CALL sp_actualizar_lugar(:id_admin, :id, :nombre, :descripcion, :direccion, :ciudad, :zona, NULL)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_admin',    $id_admin,    PDO::PARAM_INT);
            $stmt->bindParam(':id',          $id,          PDO::PARAM_INT);
            $stmt->bindParam(':nombre',      $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':direccion',   $direccion);
            $stmt->bindParam(':ciudad',      $ciudad);
            $stmt->bindParam(':zona',        $zona);
            $stmt->execute();
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Elimina un lugar validando el bloqueo de concurrencia.
     * sp_eliminar_lugar verifica el bloqueo y lo libera tras el borrado.
     *
     * @throws Exception Si el bloqueo no existe o pertenece a otro admin.
     */
    public function eliminarLugarPorId($id_admin, $id)
    {
        try {
            $sql = "CALL sp_eliminar_lugar(:id_admin, :id)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_admin', $id_admin, PDO::PARAM_INT);
            $stmt->bindParam(':id',       $id,       PDO::PARAM_INT);
            $result = $stmt->execute();
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Elimina un lugar SIN validar bloqueo.
     * Solo para rollback interno al fallar la carga de imagen en creacion.
     */
    public function eliminarLugarSinBloqueo($id)
    {
        try {
            $this->conexion->beginTransaction();

            // Asume que existe un SP simple que no requiere bloqueo (o usa DELETE directo)
            $sql = "DELETE FROM lugares WHERE id_lugar = :id";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            $stmt->closeCursor();

            $this->conexion->commit();

            return $result;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            return false;
        }
    }
}