<?php
require_once "./../conexion.php";

class infoEventosDAO
{
    private $id;
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function getMasPopulares()
    {
        try {
            $sql = "CALL sp_get_lugares_top_mas_populares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los lugares más populares: " . $e->getMessage());
        }
    }

    public function getMenosPopulares()
    {
        try {
            $sql = "CALL sp_get_lugares_top_menos_populares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los lugares menos populares: " . $e->getMessage());
        }
    }

    public function getEventosMasPopulares()
    {
        try {
            $sql = "CALL sp_get_eventos_mas_populares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los eventos más populares: " . $e->getMessage());
        }
    }

    public function getEventosMenosPopulares()
    {
        try {
            $sql = "CALL sp_get_eventos_menos_populares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los eventos menos populares: " . $e->getMessage());
        }
    }

    public function getAsistenciasCompletadas()
    {
        try {
            $sql = "CALL sp_get_asistencias_completadas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener asistencias completadas: " . $e->getMessage());
        }
    }

    public function getReservacionesPendientes()
    {
        try {
            $sql = "CALL sp_get_reservaciones_pendientes()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reservaciones pendientes: " . $e->getMessage());
        }
    }

    public function getReservacionesCanceladas()
    {
        try {
            $sql = "CALL sp_get_reservaciones_canceladas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reservaciones canceladas: " . $e->getMessage());
        }
    }

    public function getReservacionesTotales()
    {
        try {
            $sql = "CALL sp_get_reservaciones_totales()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reservaciones totales: " . $e->getMessage());
        }
    }

    public function getViajesMasPopulares()
    {
        try {
            $sql = "CALL sp_get_viajes_mas_populares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los viajes más populares: " . $e->getMessage());
        }
    }

    public function getViajesMenosPopulares()
    {
        try {
            $sql = "CALL sp_get_viajes_menos_populares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los viajes menos populares: " . $e->getMessage());
        }
    }

    public function getViajesMejorRemunerados()
    {
        try {
            $sql = "CALL sp_get_viajes_mejor_remunerados()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los viajes mejor remunerados: " . $e->getMessage());
        }
    }

    public function getViajesPeorRemunerados()
    {
        try {
            $sql = "CALL sp_get_viajes_peor_remunerados()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los viajes peor remunerados: " . $e->getMessage());
        }
    }

    public function getAgenciasMejorRemuneradas()
    {
        try {
            $sql = "CALL sp_get_agencias_mejor_remuneradas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las agencias mejor remuneradas: " . $e->getMessage());
        }
    }

    public function getAgenciasPeorRemuneradas()
    {
        try {
            $sql = "CALL sp_get_agencias_peor_remuneradas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las agencias peor remuneradas: " . $e->getMessage());
        }
    }

    public function getOrganizadorasMejorRemuneradas()
    {
        try {
            $sql = "CALL sp_get_organizadoras_mejor_remuneradas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las organizadoras mejor remuneradas por eventos: " . $e->getMessage());
        }
    }

    public function getOrganizadorasPeorRemuneradas()
    {
        try {
            $sql = "CALL sp_get_organizadoras_peor_remuneradas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las organizadoras peor remuneradas por eventos: " . $e->getMessage());
        }
    }

    public function getEventosMejorRemunerados()
    {
        try {
            $sql = "CALL sp_get_eventos_mejor_remunerados()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los eventos mejor remunerados: " . $e->getMessage());
        }
    }

    public function getEventosPeorRemunerados()
    {
        try {
            $sql = "CALL sp_get_eventos_peor_remunerados()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los eventos peor remunerados: " . $e->getMessage());
        }
    }

    public function getLugares()
    {
        try {
            $sql = "CALL sp_get_lugares_resumen()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la lista de lugares: " . $e->getMessage());
        }
    }

    public function getEventosPorLugar($id_lugar)
    {
        try {
            $sql = "CALL sp_get_eventos_por_lugar(:id_lugar)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_lugar', $id_lugar, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener eventos por lugar: " . $e->getMessage());
        }
    }

    public function getOrganizadoraPorEvento($id_evento)
    {
        try {
            $sql = "CALL sp_get_organizadora_por_evento(:id_evento)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_evento', $id_evento, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener organizadora por evento: " . $e->getMessage());
        }
    }

    public function getOrganizadoraFiltrada($id_organizadora)
    {
        try {
            $sql = "CALL sp_get_organizadora_filtrada(:id_organizadora)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_organizadora', $id_organizadora, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la organizadora filtrada: " . $e->getMessage());
        }
    }

    public function getDetalleOrganizadora($id_organizadora)
    {
        try {
            $sql = "CALL sp_get_detalle_organizadora(:id_organizadora)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_organizadora', $id_organizadora, PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $result;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el detalle de la organizadora: " . $e->getMessage());
        }
    }
}
