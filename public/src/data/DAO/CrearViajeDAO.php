<?php
require_once "./../conexion.php";

class CrearViajeDAO
{
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function crearViaje($id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje)
    {
        try {
            $this->conexion->beginTransaction();

            $sql = "CALL sp_crear_viaje(:id_cliente, :id_paquete, :estado, :fecha_viaje, :hora_viaje)";
            $stmt = $this->conexion->prepare($sql);

            $stmt->bindParam(':id_cliente',  $id_cliente,  PDO::PARAM_INT);
            $stmt->bindParam(':id_paquete',  $id_paquete,  PDO::PARAM_INT);
            $stmt->bindParam(':estado',      $estado);
            $stmt->bindParam(':fecha_viaje', $fecha_viaje);
            $stmt->bindParam(':hora_viaje',  $hora_viaje);

            $stmt->execute();

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            $this->conexion->commit();

            return $resultado;

        } catch (PDOException $e) {
            $this->conexion->rollBack();

            $mensaje = $e->getMessage();

            // Extraer mensaje limpio del SIGNAL
            if (strpos($mensaje, 'LUGAR_BLOQUEADO:') !== false) {
                preg_match('/LUGAR_BLOQUEADO:(.+)/', $mensaje, $matches);
                throw new Exception(trim($matches[1] ?? 'Este paquete no esta disponible en este momento. Intenta de nuevo mas tarde.'));
            }

            if (strpos($mensaje, '1644') !== false) {
                $partes = explode('1644', $mensaje);
                throw new Exception(trim($partes[1]));
            }

            throw new Exception("Error al registrar el viaje: " . $mensaje);
        }
    }
}