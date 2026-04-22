<?php
require_once "./../conexion.php";

class ViajesDAO {
    private $conexion;

    public function __construct() {
        // Instanciamos la conexión a la base de datos
        $con = new Conexion(); 
        $this->conexion = $con->getConexion();
    }

    // ==========================================
    // LECTURA (SELECT)
    // ==========================================

    public function getAllViajes() {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_viajes.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                // Llamada al procedimiento almacenado
                $sql = "CALL sp_get_all_viajes()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error al obtener viajes: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado consultando viajes. Intenta de nuevo.");
        }
    }

    public function getViajePorID($id_viaje) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_viajes.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_viaje_por_id(?)";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute([$id_viaje]);
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error al obtener viaje: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado consultando viajes. Intenta de nuevo.");
        }
    }

    public function getViajesPorCliente($id_cliente) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_viajes.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_viajes_por_cliente(?)";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute([$id_cliente]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                throw new Exception("Error al obtener viajes del cliente: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está ocupado consultando viajes. Intenta de nuevo.");
        }
    }

    // ==========================================
    // INSERCIÓN (INSERT)
    // ==========================================

    public function crearViaje($id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_paquete_" . $id_paquete . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_crear_viaje(?, ?, ?, ?, ?)";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute([$id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje]);
                
                // El procedimiento devuelve un SELECT LAST_INSERT_ID() AS id_generado
                // Lo capturamos y lo retornamos para poder usarlo en el Controlador
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['id_generado']; 
            } catch (PDOException $e) {
                throw new Exception("Error al crear viaje: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El paquete está siendo procesado por otra reservación. Intenta de nuevo.");
        }
    }

    // ==========================================
    // ACTUALIZACIÓN (UPDATE)
    // ==========================================

    public function actualizarViaje($id_viaje, $id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_viaje_" . $id_viaje . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_actualizar_viaje(?, ?, ?, ?, ?, ?)";
                $stmt = $this->conexion->prepare($sql);
                return $stmt->execute([$id_viaje, $id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje]);
            } catch (PDOException $e) {
                throw new Exception("Error al actualizar viaje: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El viaje está siendo procesado en este momento. Intenta de nuevo.");
        }
    }

    public function actualizarEstadoViaje($id_viaje, $nuevo_estado) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_viaje_" . $id_viaje . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_actualizar_estado_viaje(?, ?)";
                $stmt = $this->conexion->prepare($sql);
                return $stmt->execute([$id_viaje, $nuevo_estado]);
            } catch (PDOException $e) {
                throw new Exception("Error al actualizar estado del viaje: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El viaje está siendo procesado en este momento. Intenta de nuevo.");
        }
    }

    public function cancelarViaje($id_viaje) {
        // Llama directamente a la función de arriba, el candado se aplica automáticamente ahí.
        return $this->actualizarEstadoViaje($id_viaje, 'cancelado');
    }

    // ==========================================
    // ELIMINACIÓN (DELETE)
    // ==========================================

    public function eliminarViaje($id_viaje) {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_viaje_" . $id_viaje . ".txt", "w+");
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_eliminar_viaje(?)";
                $stmt = $this->conexion->prepare($sql);
                return $stmt->execute([$id_viaje]);
            } catch (PDOException $e) {
                throw new Exception("Error al eliminar viaje: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El viaje está siendo procesado en este momento. Intenta de nuevo.");
        }
    }
}
?>