<?php
require_once "./../conexion.php";

class CrearReservacionDAO{

    private $id;
    private $conexion;

    public function __construct(){
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function crearReservacion($id_evento, $id_cliente, $estado)
    {
        // 1. Crear un archivo de bloqueo único para este evento en específico
        $lockPath = sys_get_temp_dir() . "/lock_evento_" . $id_evento . ".txt";
        $lockFile = fopen($lockPath, "w+");

        // 2. Intentar bloquear el archivo (espera si alguien más lo está usando)
        if (flock($lockFile, LOCK_EX)) {
            try {
                $sql = "CALL sp_crear_reservacion(:id_evento, :id_cliente, :estado)";
                $stmt = $this->conexion->prepare($sql);

                $stmt->bindParam(':id_evento', $id_evento);
                $stmt->bindParam(':id_cliente', $id_cliente);
                $stmt->bindParam(':estado', $estado);

                $stmt->execute();

                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $resultado['id_reservacion'];

            } catch (PDOException $e) {
                throw new Exception("Error en la creación de la reservación: " . $e->getMessage());
            } finally {
                // 3. Siempre liberar el candado, falle o sea exitoso
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando otra reserva para este evento. Intenta nuevamente en unos segundos.");
        }
    }
}
?>