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
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_lugares_top_mas_populares()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener los lugares más populares: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getMenosPopulares()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_lugares_top_menos_populares()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener los lugares menos populares: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getEventosMasPopulares()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_eventos_mas_populares()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener los eventos más populares: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getEventosMenosPopulares()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_eventos_menos_populares()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener los eventos menos populares: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getAsistenciasCompletadas()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_asistencias_completadas()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result['count'] ?? 0;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener asistencias completadas: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getReservacionesPendientes()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_reservaciones_pendientes()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result['count'] ?? 0;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener reservaciones pendientes: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getReservacionesCanceladas()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_reservaciones_canceladas()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result['count'] ?? 0;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener reservaciones canceladas: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getReservacionesTotales()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_reservaciones_totales()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result['count'] ?? 0;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener reservaciones totales: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getViajesMasPopulares()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_viajes_mas_populares()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener los viajes más populares: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getViajesMenosPopulares()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_viajes_menos_populares()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener los viajes menos populares: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getViajesMejorRemunerados()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_viajes_mejor_remunerados()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener los viajes mejor remunerados: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getViajesPeorRemunerados()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_viajes_peor_remunerados()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener los viajes peor remunerados: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getAgenciasMejorRemuneradas()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_agencias_mejor_remuneradas()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener las agencias mejor remuneradas: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getAgenciasPeorRemuneradas()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_agencias_peor_remuneradas()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener las agencias peor remuneradas: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getOrganizadorasMejorRemuneradas()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_organizadoras_mejor_remuneradas()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener las organizadoras mejor remuneradas por eventos: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getOrganizadorasPeorRemuneradas()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_organizadoras_peor_remuneradas()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener las organizadoras peor remuneradas por eventos: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getEventosMejorRemunerados()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_eventos_mejor_remunerados()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener los eventos mejor remunerados: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getEventosPeorRemunerados()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_eventos_peor_remunerados()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener los eventos peor remunerados: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getLugares()
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
            try {
                $sql = "CALL sp_get_lugares_resumen()";
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return $result;
            } catch (PDOException $e) {
                throw new Exception("Error al obtener la lista de lugares: " . $e->getMessage());
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getEventosPorLugar($id_lugar)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
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
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getOrganizadoraPorEvento($id_evento)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
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
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getOrganizadoraFiltrada($id_organizadora)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
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
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }

    public function getDetalleOrganizadora($id_organizadora)
    {
        $lockFile = fopen(sys_get_temp_dir() . "/lock_info.txt", "w+");
        if (flock($lockFile, LOCK_SH)) {
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
            } finally {
                flock($lockFile, LOCK_UN);
                fclose($lockFile);
            }
        } else {
            throw new Exception("El sistema está procesando reportes. Intenta de nuevo.");
        }
    }
}
?>