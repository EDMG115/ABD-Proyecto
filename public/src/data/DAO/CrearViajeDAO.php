<?php
require_once "./../conexion.php";

class CrearViajeDAO{

    private $id;
    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function crearViaje($id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje)
    {
        // 1. Crear un archivo de bloqueo único para este paquete
        $lockPath = sys_get_temp_dir() . "/lock_paquete_" . $id_paquete . ".txt";
        $lockFile = fopen($lockPath, "w+");

        // 2. Intentar bloquear el archivo
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_crear_viaje(:id_cliente, :id_paquete, :estado, :fecha_viaje, :hora_viaje)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':id_cliente', $id_cliente);
                $stmt->bindParam(':id_paquete', $id_paquete);
                $stmt->bindParam(':estado', $estado);
                $stmt->bindParam(':fecha_viaje', $fecha_viaje);
                $stmt->bindParam(':hora_viaje', $hora_viaje);

                $stmt->execute();

                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $resultado['id_generado'];

            } catch (PDOException $e) {
                throw new Exception("Error en la creación del viaje: " . $e->getMessage());
            } finally {
                // 3. Liberar el candado siempre
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando otra reservación para este paquete. Intenta nuevamente en unos segundos.");
        }
    }
}
?>