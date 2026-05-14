<?php
require_once "./../conexion.php";

class ViajesDAO
{
    private $conexion;

    public function __construct()
    {
        $con = new Conexion();
        $this->conexion = $con->getConexion();
    }

    // ==========================================
    // LECTURA (SELECT)
    // ==========================================

    public function getAllViajes()
    {
        try {

            $sql = "CALL sp_get_all_viajes()";

            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {

            throw new Exception("Error al obtener viajes: " . $e->getMessage());
        }
    }

    public function getViajePorID($id_viaje)
    {
        try {

            $sql = "CALL sp_get_viaje_por_id(:id_viaje)";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_viaje', $id_viaje, PDO::PARAM_INT);

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {

            throw new Exception("Error al obtener viaje: " . $e->getMessage());
        }
    }

    public function getViajesPorCliente($id_cliente)
    {
        try {

            $sql = "CALL sp_get_viajes_por_cliente(:id_cliente)";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);

            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {

            throw new Exception("Error al obtener viajes del cliente: " . $e->getMessage());
        }
    }

    // ==========================================
    // INSERCIÓN
    // ==========================================

    public function crearViaje($id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje)
    {
        try {

            $this->conexion->beginTransaction();

            $sql = "CALL sp_crear_viaje(
                :id_cliente,
                :id_paquete,
                :estado,
                :fecha_viaje,
                :hora_viaje
            )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
            $stmt->bindParam(':id_paquete', $id_paquete, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_viaje', $fecha_viaje);
            $stmt->bindParam(':hora_viaje', $hora_viaje);

            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            $this->conexion->commit();

            return $resultado['id_generado'] ?? null;
        } catch (PDOException $e) {

            $this->conexion->rollBack();

            throw new Exception("Error al crear viaje: " . $e->getMessage());
        }
    }

    // ==========================================
    // ACTUALIZACIÓN
    // ==========================================

    public function actualizarViaje(
        $id_viaje,
        $id_cliente,
        $id_paquete,
        $estado,
        $fecha_viaje,
        $hora_viaje
    ) {
        try {

            $this->conexion->beginTransaction();

            $sql = "CALL sp_actualizar_viaje(
                :id_viaje,
                :id_cliente,
                :id_paquete,
                :estado,
                :fecha_viaje,
                :hora_viaje
            )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_viaje', $id_viaje, PDO::PARAM_INT);
            $stmt->bindParam(':id_cliente', $id_cliente, PDO::PARAM_INT);
            $stmt->bindParam(':id_paquete', $id_paquete, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_viaje', $fecha_viaje);
            $stmt->bindParam(':hora_viaje', $hora_viaje);

            $result = $stmt->execute();

            $stmt->closeCursor();

            $this->conexion->commit();

            return $result;
        } catch (PDOException $e) {

            $this->conexion->rollBack();

            throw new Exception("Error al actualizar viaje: " . $e->getMessage());
        }
    }

    public function actualizarEstadoViaje($id_viaje, $nuevo_estado)
    {
        try {

            $this->conexion->beginTransaction();

            $sql = "CALL sp_actualizar_estado_viaje(
                :id_viaje,
                :nuevo_estado
            )";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_viaje', $id_viaje, PDO::PARAM_INT);
            $stmt->bindParam(':nuevo_estado', $nuevo_estado, PDO::PARAM_STR);

            $result = $stmt->execute();

            $stmt->closeCursor();

            $this->conexion->commit();

            return $result;
        } catch (PDOException $e) {

            $this->conexion->rollBack();

            throw new Exception("Error al actualizar estado del viaje: " . $e->getMessage());
        }
    }

    public function cancelarViaje($id_viaje)
    {
        return $this->actualizarEstadoViaje($id_viaje, 'cancelado');
    }

    // ==========================================
    // ELIMINACIÓN
    // ==========================================

    public function eliminarViaje($id_viaje)
    {
        try {

            $this->conexion->beginTransaction();

            $sql = "CALL sp_eliminar_viaje(:id_viaje)";

            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_viaje', $id_viaje, PDO::PARAM_INT);

            $result = $stmt->execute();

            $stmt->closeCursor();

            $this->conexion->commit();

            return $result;
        } catch (PDOException $e) {

            $this->conexion->rollBack();

            throw new Exception("Error al eliminar viaje: " . $e->getMessage());
        }
    }
}
