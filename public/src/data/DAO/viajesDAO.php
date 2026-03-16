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
        // Llamada al procedimiento almacenado
        $sql = "CALL getAllViajes()";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getViajePorID($id_viaje) {
        $sql = "CALL getViajePorID(?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_viaje]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getViajesPorCliente($id_cliente) {
        $sql = "CALL getViajesPorCliente(?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_cliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==========================================
    // INSERCIÓN (INSERT)
    // ==========================================

    public function crearViaje($id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje) {
        $sql = "CALL crearViaje(?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje]);
        
        // El procedimiento devuelve un SELECT LAST_INSERT_ID() AS id_generado
        // Lo capturamos y lo retornamos para poder usarlo en el Controlador
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['id_generado']; 
    }

    // ==========================================
    // ACTUALIZACIÓN (UPDATE)
    // ==========================================

    public function actualizarViaje($id_viaje, $id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje) {
        $sql = "CALL actualizarViaje(?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id_viaje, $id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje]);
    }

    public function actualizarEstadoViaje($id_viaje, $nuevo_estado) {
        $sql = "CALL actualizarEstadoViaje(?, ?)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id_viaje, $nuevo_estado]);
    }

    // ==========================================
    // ELIMINACIÓN (DELETE)
    // ==========================================

    public function eliminarViaje($id_viaje) {
        $sql = "CALL eliminarViaje(?)";
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$id_viaje]);
    }
}
?>