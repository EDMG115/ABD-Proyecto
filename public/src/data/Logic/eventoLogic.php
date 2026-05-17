<?php
require_once "./../DAO/eventoDAO.php";
header('Content-Type: application/json');

$eventoDAO            = new eventoDAO();
$RUTA_IMG_ESTANDAR    = "./../../media/images/lugares/";
$RUTA_FISICA_GUARDADO = __DIR__ . "/../../media/images/lugares/";

// ============================================================
// GET — Listar eventos (lectura libre, sin bloqueo)
// ============================================================
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $eventos = $eventoDAO->getEvento();
        if ($eventos != null) {
            foreach ($eventos as &$evento) {
                $evento['imagen_url'] = $RUTA_IMG_ESTANDAR . $evento['imagen_url'];
            }
            $respuesta = ['correcto' => true, 'eventos' => $eventos];
        } else {
            $respuesta = ['correcto' => false];
        }
        echo json_encode($respuesta);
    } catch (Exception $e) {
        echo json_encode(['correcto' => false, 'mensaje' => 'Error - ' . $e->getMessage()]);
    }

// ============================================================
// POST — Actualizar o eliminar evento (requiere bloqueo previo)
// ============================================================
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $accion    = $_POST['accion']    ?? '';
    $id_evento = (int)($_POST['idEvento'] ?? 0);
    $id_admin  = (int)($_POST['id_admin'] ?? 0);

    if (!$id_evento || !$id_admin) {
        echo json_encode(['correcto' => false, 'mensaje' => 'Faltan idEvento o id_admin.']);
        exit;
    }

    try {
        // ---- ELIMINAR EVENTO ----
        if ($accion === 'eliminar') {
            // sp_cancelar_evento_y_reservaciones valida el bloqueo internamente
            $resultado = $eventoDAO->eliminarEvento($id_admin, $id_evento);
            echo json_encode([
                'correcto'            => true,
                'mensaje'             => 'Evento eliminado correctamente.',
                'reservaciones_canceladas' => $resultado['canceladas']
            ]);
            exit;
        }

        // ---- ACTUALIZAR EVENTO ----
        if ($accion === 'actualizar') {
            $nombre_evento     = $_POST['nombre_evento']     ?? '';
            $descripcion       = $_POST['descripcion']       ?? '';
            $fecha_evento      = $_POST['fecha_evento']      ?? '';
            $hora_evento       = $_POST['hora_evento']       ?? '';
            $precio_boleto     = $_POST['precio_boleto']     ?? 0;
            $id_lugar          = (int)($_POST['id_lugar']          ?? 0);
            $id_tipo_actividad = (int)($_POST['id_tipo_actividad'] ?? 0);
            $id_organizadora   = (int)($_POST['id_organizadora']   ?? 0);
            $imagen_url        = null;  // Se actualiza solo si sube archivo

            // Manejo de imagen
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $imagen     = $_FILES['imagen'];
                $tipo       = $imagen['type'];

                if ($tipo !== 'image/jpeg' && $tipo !== 'image/jpg' && $tipo !== 'image/png') {
                    echo json_encode(['correcto' => false, 'mensaje' => 'Solo se permiten JPG y PNG.']);
                    exit;
                }

                $ext           = ($tipo === 'image/png') ? 'png' : 'jpg';
                $nombre_img_db = 'eimg' . $id_evento . '.' . $ext;
                $ruta_fisica   = $RUTA_FISICA_GUARDADO . $nombre_img_db;

                if (!move_uploaded_file($imagen['tmp_name'], $ruta_fisica)) {
                    echo json_encode(['correcto' => false, 'mensaje' => 'No se pudo guardar la imagen.']);
                    exit;
                }

                $imagen_url = $nombre_img_db;
            }

            // El SP valida el bloqueo internamente
            $eventoDAO->actualizarEvento(
                $id_admin, $id_evento,
                $nombre_evento, $descripcion,
                $fecha_evento, $hora_evento,
                $precio_boleto, $imagen_url,
                $id_lugar, $id_tipo_actividad, $id_organizadora
            );

            echo json_encode(['correcto' => true, 'mensaje' => 'Evento actualizado correctamente.']);
            exit;
        }

        echo json_encode(['correcto' => false, 'mensaje' => "Accion '$accion' desconocida."]);

    } catch (Exception $e) {
        // Errores de bloqueo (SQLSTATE 45000) llegan aqui con mensaje claro
        echo json_encode(['correcto' => false, 'mensaje' => $e->getMessage()]);
    }
}