<?php
require_once "./../conexion.php";

class eventoDAO
{
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    // ==========================================
    // LECTURA (SELECT) - SIN BLOQUEO
    // ==========================================

    public function getEvento()
    {
        try {
            $sql  = "CALL sp_get_all_eventos()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al realizar la consulta: " . $e->getMessage());
        }
    }

    // ==========================================
    // ESCRITURA — CON TRANSACCIONES Y BLOQUEO
    // ==========================================

    /**
     * Actualiza un evento validando el bloqueo de concurrencia.
     * Llama a sp_actualizar_evento que verifica con SELECT FOR UPDATE
     * que el $id_admin sea el dueno del bloqueo activo.
     *
     * @throws Exception Si el bloqueo no existe o pertenece a otro admin.
     */
    public function actualizarEvento(
        $id_admin,
        $id_evento,
        $nombre_evento,
        $descripcion,
        $fecha_evento,
        $hora_evento,
        $precio_boleto,
        $imagen_url,
        $id_lugar,
        $id_tipo_actividad,
        $id_organizadora
    ) {
        try {
            $sql = "CALL sp_actualizar_evento(
                        :id_admin, :id_evento,
                        :nombre, :descripcion,
                        :fecha, :hora,
                        :precio, :imagen,
                        :id_lugar, :id_tipo, :id_org
                    )";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_admin',  $id_admin,          PDO::PARAM_INT);
            $stmt->bindParam(':id_evento', $id_evento,         PDO::PARAM_INT);
            $stmt->bindParam(':nombre',    $nombre_evento);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':fecha',     $fecha_evento);
            $stmt->bindParam(':hora',      $hora_evento);
            $stmt->bindParam(':precio',    $precio_boleto);
            $stmt->bindParam(':imagen',    $imagen_url);
            $stmt->bindParam(':id_lugar',  $id_lugar,          PDO::PARAM_INT);
            $stmt->bindParam(':id_tipo',   $id_tipo_actividad, PDO::PARAM_INT);
            $stmt->bindParam(':id_org',    $id_organizadora,   PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            return true;
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Elimina un evento (y sus reservaciones pendientes) validando el bloqueo.
     * Usa sp_cancelar_evento_y_reservaciones que libera el bloqueo al terminar.
     *
     * @throws Exception Si el bloqueo no existe o pertenece a otro admin.
     */
    public function eliminarEvento($id_admin, $id_evento)
    {
        try {
            $stmt = $this->conexion->prepare(
                "CALL sp_cancelar_evento_y_reservaciones(:id_admin, :id_evento, @canceladas)"
            );
            $stmt->bindParam(':id_admin',  $id_admin,  PDO::PARAM_INT);
            $stmt->bindParam(':id_evento', $id_evento, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            $res = $this->conexion->query("SELECT @canceladas AS canceladas")->fetch(PDO::FETCH_ASSOC);

            return [
                'eliminado'  => true,
                'canceladas' => (int)($res['canceladas'] ?? 0)
            ];
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }
}