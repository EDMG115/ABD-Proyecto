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
            $sql = "CALL sp_getMasPopulares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los lugares más populares: " . $e->getMessage());
        }
    }

    public function getMenosPopulares()
    {
        try {
            $sql = "CALL sp_getMenosPopulares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los lugares menos populares: " . $e->getMessage());
        }
    }
    public function getEventosMasPopulares()
    {
        try {
            $sql = "CALL sp_getEventosMasPopulares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los eventos más populares: " . $e->getMessage());
        }
    }
    public function getEventosMenosPopulares()
    {
        try {
            $sql = "CALL sp_getEventosMenosPopulares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los eventos menos populares: " . $e->getMessage());
        }
    }

    public function getAsistenciasCompletadas()
    {
        try {
            $sql = "CALL sp_getAsistenciasCompletadas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener asistencias completadas: " . $e->getMessage());
        }
    }

    public function getReservacionesPendientes()
    {
        try {
            $sql = "CALL sp_getReservacionesPendientes()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reservaciones pendientes: " . $e->getMessage());
        }
    }

    public function getReservacionesCanceladas()
    {
        try {
            $sql = "CALL sp_getReservacionesCanceladas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reservaciones canceladas: " . $e->getMessage());
        }
    }

    public function getReservacionesTotales()
    {
        try {
            $sql = "CALL sp_getReservacionesTotales()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            throw new Exception("Error al obtener reservaciones totales: " . $e->getMessage());
        }
    }
    public function getViajesMasPopulares()
    {
        try {
            $sql = "CALL sp_getViajesMasPopulares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los viajes más populares: " . $e->getMessage());
        }
    }

    public function getViajesMenosPopulares()
    {
        try {
            $sql = "CALL sp_getViajesMenosPopulares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los viajes menos populares: " . $e->getMessage());
        }
    }
    public function getViajesMejorRemunerados()
    {
        try {
            $sql = "CALL sp_getViajesMejorRemunerados()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los viajes mejor remunerados: " . $e->getMessage());
        }
    }

    public function getViajesPeorRemunerados()
    {
        try {
            $sql = "CALL sp_getViajesPeorRemunerados()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los viajes peor remunerados: " . $e->getMessage());
        }
    }
    public function getAgenciasMejorRemuneradas()
    {
        try {
            $sql = "CALL sp_getAgenciasMejorRemuneradas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las agencias mejor remuneradas: " . $e->getMessage());
        }
    }

    public function getAgenciasPeorRemuneradas()
    {
        try {
            $sql = "CALL sp_getAgenciasPeorRemuneradas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las agencias peor remuneradas: " . $e->getMessage());
        }
    }
    public function getOrganizadorasMejorRemuneradas()
    {
        try {
            $sql = "CALL sp_getOrganizadorasMejorRemuneradas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las organizadoras mejor remuneradas por eventos: " . $e->getMessage());
        }
    }
    public function getOrganizadorasPeorRemuneradas()
    {
        try {
            $sql = "CALL sp_getOrganizadorasPeorRemuneradas()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las organizadoras peor remuneradas por eventos: " . $e->getMessage());
        }
    }
    public function getEventosMejorRemunerados()
    {
        try {
            $sql = "CALL sp_getEventosMejorRemunerados()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los eventos mejor remunerados: " . $e->getMessage());
        }
    }

    public function getEventosPeorRemunerados()
    {
        try {
            $sql = "CALL sp_getEventosPeorRemunerados()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los eventos peor remunerados: " . $e->getMessage());
        }
    }

    public function getLugares()
    {
        try {
            $sql = "CALL sp_getLugares()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la lista de lugares: " . $e->getMessage());
        }
    }

    public function getEventosPorLugar($id_lugar)
    {
        try {
            $sql = "CALL sp_getEventosPorLugar(:id_lugar)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_lugar', $id_lugar, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener eventos por lugar: " . $e->getMessage());
        }
    }

    public function getOrganizadoraPorEvento($id_evento)
    {
        try {
            $sql = "CALL sp_getOrganizadoraPorEvento(:id_evento)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_evento', $id_evento, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener organizadora por evento: " . $e->getMessage());
        }
    }

    public function getOrganizadoraFiltrada($id_organizadora)
    {
        try {
            $sql = "CALL sp_getOrganizadoraFiltrada(:id_organizadora)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_organizadora', $id_organizadora, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la organizadora filtrada: " . $e->getMessage());
        }
    }

    public function getDetalleOrganizadora($id_organizadora)
    {
        try {
            $sql = "CALL sp_getDetalleOrganizadora(:id_organizadora)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id_organizadora', $id_organizadora, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener el detalle de la organizadora: " . $e->getMessage());
        }
    }
}
