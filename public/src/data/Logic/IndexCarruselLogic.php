<?php
require_once "./../dao/CarruselDAO.php";
header('Content-Type: application/json');

$indexCarruselDAO = new CarruselDAO();

// Rutas de imágenes
$RUTA_IMG_LUGARES = "./src/media/images/lugares/";
$RUTA_IMG_EVENTOS = "./src/media/images/eventos/";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        // Obtener el tipo desde GET (lugares o eventos)
        $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'lugares';

        if ($tipo === 'lugares') {

            $lugares = $indexCarruselDAO->getLugaresMasPopulares(30);

            if (!empty($lugares)) {

                foreach ($lugares as &$lugar) {
                    if (!empty($lugar['imagen_url']) && $lugar['imagen_url'] !== 'nourl') {
                        $lugar['imagen_url'] = $RUTA_IMG_LUGARES . $lugar['imagen_url'];
                    } else {
                        $lugar['imagen_url'] = $RUTA_IMG_LUGARES . 'default.jpg';
                    }
                }

                $respuesta = [
                    'correcto' => true,
                    'tipo' => 'lugares',
                    'data' => $lugares
                ];
            } else {
                $respuesta = [
                    'correcto' => false,
                    'mensaje' => 'No se encontraron lugares populares',
                    'data' => []
                ];
            }

        } elseif ($tipo === 'eventos') {

            $eventos = $indexCarruselDAO->getEventosDisponibles(20);

            if (!empty($eventos)) {

                foreach ($eventos as &$evento) {
                    if (!empty($evento['imagen_url']) && $evento['imagen_url'] !== 'nourl') {
                        $evento['imagen_url'] = $RUTA_IMG_EVENTOS . $evento['imagen_url'];
                    } else {
                        $evento['imagen_url'] = $RUTA_IMG_EVENTOS . 'default.jpg';
                    }
                }

                $respuesta = [
                    'correcto' => true,
                    'tipo' => 'eventos',
                    'data' => $eventos
                ];
            } else {
                $respuesta = [
                    'correcto' => false,
                    'mensaje' => 'No se encontraron eventos disponibles',
                    'data' => []
                ];
            }

        } else {
            $respuesta = [
                'correcto' => false,
                'mensaje' => 'Tipo no válido (usa: lugares o eventos)',
                'data' => []
            ];
        }

        echo json_encode($respuesta);

    } catch (Exception $e) {
        echo json_encode([
            'correcto' => false,
            'mensaje' => 'Error - ' . $e->getMessage(),
            'data' => []
        ]);
    }

} else {
    echo json_encode([
        'correcto' => false,
        'mensaje' => 'Método no permitido',
        'data' => []
    ]);
}



