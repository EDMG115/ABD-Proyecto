<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . "/../DAO/CarruselDAO.php";
require_once __DIR__ . "/../DAO/paqueteDAO.php";
header('Content-Type: application/json');

$indexCarruselDAO = new CarruselDAO();
$paqueteDAO = new paqueteDAO();

// Rutas de imágenes (relativas al index en public/)
$RUTA_IMG_LUGARES = "./src/media/images/lugares/";
$RUTA_IMG_EVENTOS = "./src/media/images/events/";
$RUTA_IMG_PAQUETES = "./src/media/images/paquetes/";

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

            // Listado amplio para poder filtrar en cliente (nombre, ciudad) sin perder variedad
            $limite = 80;

            $eventos = $indexCarruselDAO->getEventosDisponibles($limite);

            $q = isset($_GET['q']) ? trim($_GET['q']) : '';
            $ciudad = isset($_GET['ciudad']) ? trim($_GET['ciudad']) : '';

            if ($q !== '') {
                $eventos = array_values(array_filter($eventos, function ($e) use ($q) {
                    $nombre = $e['nombre_evento'] ?? '';
                    $desc = $e['descripcion'] ?? '';
                    return (stripos($nombre, $q) !== false || stripos($desc, $q) !== false);
                }));
            }

            if ($ciudad !== '') {
                $eventos = array_values(array_filter($eventos, function ($e) use ($ciudad) {
                    return isset($e['ciudad']) && strcasecmp(trim($e['ciudad']), $ciudad) === 0;
                }));
            }

            if (!empty($eventos)) {

                foreach ($eventos as &$evento) {
                    if (!empty($evento['imagen_url']) && $evento['imagen_url'] !== 'nourl') {
                        $evento['imagen_url'] = $RUTA_IMG_EVENTOS . $evento['imagen_url'];
                    } else {
                        $evento['imagen_url'] = $RUTA_IMG_EVENTOS . 'default.jpg';
                    }
                }
                unset($evento);

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

        } elseif ($tipo === 'paquetes') {

            $id_lugar = isset($_GET['id_lugar']) ? (int)$_GET['id_lugar'] : 0;

            if ($id_lugar > 0) {
                $paquetes = $paqueteDAO->getPaquetesPorLugar($id_lugar);
            } else {
                $lugaresTop = $indexCarruselDAO->getLugaresMasPopulares(25);
                $vistos = [];
                $paquetes = [];
                foreach ($lugaresTop as $lug) {
                    $lid = (int)($lug['id_lugar'] ?? 0);
                    if ($lid <= 0) {
                        continue;
                    }
                    $rows = $paqueteDAO->getPaquetesPorLugar($lid);
                    foreach ($rows as $row) {
                        $pid = (int)($row['id_paquete'] ?? 0);
                        if ($pid > 0 && !isset($vistos[$pid])) {
                            $vistos[$pid] = true;
                            $paquetes[] = $row;
                        }
                    }
                }
            }

            $orden = isset($_GET['orden']) ? strtolower(trim($_GET['orden'])) : '';
            if ($orden === 'desc') {
                usort($paquetes, function ($a, $b) {
                    return (float)($b['precio'] ?? 0) <=> (float)($a['precio'] ?? 0);
                });
            } elseif ($orden === 'asc') {
                usort($paquetes, function ($a, $b) {
                    return (float)($a['precio'] ?? 0) <=> (float)($b['precio'] ?? 0);
                });
            }

            if (!empty($paquetes)) {
                foreach ($paquetes as &$p) {
                    if (!empty($p['imagen_url']) && $p['imagen_url'] !== 'nourl') {
                        $p['imagen_url'] = $RUTA_IMG_PAQUETES . $p['imagen_url'];
                    } else {
                        $p['imagen_url'] = $RUTA_IMG_LUGARES . 'default.jpg';
                    }
                    $precio = $p['precio'] ?? '';
                    $desc = $p['descripcion_paquete'] ?? '';
                    if ($precio !== '' && $precio !== null) {
                        $p['descripcion_paquete'] = $desc . ' · Desde $' . $precio;
                    }
                }
                unset($p);

                $respuesta = [
                    'correcto' => true,
                    'tipo' => 'paquetes',
                    'data' => $paquetes
                ];
            } else {
                $respuesta = [
                    'correcto' => false,
                    'mensaje' => 'No se encontraron paquetes',
                    'data' => []
                ];
            }

        } else {
            $respuesta = [
                'correcto' => false,
                'mensaje' => 'Tipo no válido (usa: lugares, eventos o paquetes)',
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



