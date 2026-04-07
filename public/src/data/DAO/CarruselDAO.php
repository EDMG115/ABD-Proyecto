<?php
require_once "./../conexion.php";

class CarruselDAO
{
    private $conexion;

    public function __construct()
    {
        $conn = new conexion();
        $this->conexion = $conn->getConexion();
    }

    public function getLugaresMasPopulares($limite = 30)
    {
        try {
            $sql = "SELECT 
                        l.id_lugar, l.nombre_lugar, l.descripcion, l.direccion, l.ciudad, l.zona, l.imagen_url, 
                        (COUNT(r.id_reservacion) + COUNT(v.id_viaje)) AS total_asistencias 
                    FROM lugares l 
                    LEFT JOIN reservaciones r ON r.estado = 'completado' 
                    LEFT JOIN paquetes p ON l.id_lugar = p.id_lugar 
                    LEFT JOIN viajes v ON p.id_paquete = v.id_paquete AND v.estado = 'completado' 
                    GROUP BY l.id_lugar
                    ORDER BY total_asistencias DESC, l.nombre_lugar ASC 
                    LIMIT :limite";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los lugares más populares para el carrusel: " . $e->getMessage());
        }
    }

    public function getEventosDisponibles($limite = 20)
    {
        try {
            $sql = "SELECT e.nombre_evento, e.id_lugar, l.nombre_lugar, e.descripcion, l.direccion, l.ciudad, l.zona, e.imagen_url, e.precio_boleto, e.fecha_evento,
                COUNT(r.id_reservacion) AS total_asistencias
                
                FROM eventos e 
                LEFT JOIN lugares l ON e.id_lugar = l.id_lugar 
                LEFT JOIN tipoactividad t ON t.id_tipo_actividad = e.id_tipo_actividad
                LEFT JOIN reservaciones r ON r.id_evento = e.id_evento 
                WHERE e.fecha_evento > NOW()
                
                GROUP BY e.id_evento, e.nombre_evento, e.id_lugar, l.nombre_lugar, e.descripcion, l.direccion, l.ciudad, l.zona, e.imagen_url, e.precio_boleto, e.fecha_evento
                ORDER BY total_asistencias DESC, e.nombre_evento ASC
                LIMIT :limite";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener los eventos disponibles para el carrusel: " . $e->getMessage());
        }
    }
}

