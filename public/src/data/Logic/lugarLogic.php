<?php
require_once "./../Util/seguridad.php";
require_once "./../DAO/lugarDAO.php";
header('Content-Type: application/json');

$lugarDAO = new lugarDAO();
$RUTA_IMG_ESTANDAR    = "./../../media/images/lugares/";
$RUTA_FISICA_GUARDADO = __DIR__ . "/../../media/images/lugares/";

// ============================================================
// GET — Listar lugares (lectura libre, sin bloqueo)
// ============================================================
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (!verificarPermisos("ver_lugar")) {
        redirigirAlIndex();
    }

    try {
        $lugares = $lugarDAO->getLugares();
        if ($lugares != null) {
            foreach ($lugares as &$lugar) {
                $lugar['imagen_url'] = $RUTA_IMG_ESTANDAR . $lugar['imagen_url'];
            }
            $respuesta = ['correcto' => true, 'lugares' => $lugares];
        } else {
            $respuesta = ['correcto' => false];
        }
        echo json_encode($respuesta);
    } catch (Exception $e) {
        echo json_encode(['correcto' => false, 'mensaje' => 'Error - ' . $e->getMessage()]);
    }

// ============================================================
// POST — Crear o actualizar lugar
// ============================================================
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $respuesta = ['correcto' => false];

    if (empty($_POST) && empty($_FILES)) {
        echo json_encode($respuesta);
        exit;
    }

    $id_lugar_update = sanitizarEntero($_POST["id_lugar_update"] ?? null);
    $id_admin        = sanitizarEntero($_POST["id_admin"]        ?? null);
    $imagen          = $_FILES["imagen"] ?? null;

    // Permisos
    if ($id_lugar_update) {
        if (!verificarPermisos("editar_lugar")) redirigirAlIndex();
    } else {
        if (!verificarPermisos("crear_lugar"))  redirigirAlIndex();
    }

    $nombre      = sanitizarTexto($_POST["nombre"]      ?? '');
    $descripcion = sanitizarTexto($_POST["descripcion"] ?? '');
    $direccion   = sanitizarTexto($_POST["direccion"]   ?? '');
    $ciudad      = sanitizarTexto($_POST["ciudad"]      ?? '');
    $zona        = sanitizarTexto($_POST["zona"]        ?? '');

    if (empty($nombre) || empty($descripcion) || empty($direccion) || empty($ciudad) || empty($zona) || empty($id_admin)) {
        echo json_encode(['correcto' => false, 'mensaje' => 'Datos incompletos.']);
        exit;
    }

    $id_lugar = 0;

    try {
        // ---- ACTUALIZACIÓN ----
        if ($id_lugar_update) {
            $id_actual = $id_lugar_update;

            // El SP verifica el bloqueo internamente; si no existe lanza excepción
            $lugarDAO->updateLugar($id_admin, $id_actual, $nombre, $descripcion, $direccion, $ciudad, $zona);

            if ($imagen && $imagen["error"] === UPLOAD_ERR_OK) {
                $tipoArchivo = $imagen["type"];
                if ($tipoArchivo !== 'image/jpeg' && $tipoArchivo !== 'image/jpg') {
                    echo json_encode(['correcto' => false, 'mensaje' => 'Solo se permiten archivos JPG y JPEG.']);
                    exit;
                }

                $lugar_antiguo          = $lugarDAO->getLugarPorID($id_actual);
                $nombre_imagen_anterior = $lugar_antiguo['imagen_url'] ?? '';
                $nombre_img_db          = 'limg' . $id_actual . '.jpg';
                $ruta_fisica            = $RUTA_FISICA_GUARDADO . $nombre_img_db;

                if (move_uploaded_file($imagen["tmp_name"], $ruta_fisica)) {
                    $lugarDAO->updateImagen($id_actual, $nombre_img_db);

                    if (!empty($nombre_imagen_anterior) && $nombre_imagen_anterior !== $nombre_img_db) {
                        $ruta_anterior = $RUTA_FISICA_GUARDADO . $nombre_imagen_anterior;
                        if (file_exists($ruta_anterior)) @unlink($ruta_anterior);
                    }
                } else {
                    echo json_encode(['correcto' => false, 'mensaje' => 'No se pudo guardar la nueva imagen.']);
                    exit;
                }
            }

            echo json_encode(['correcto' => true, 'mensaje' => 'Lugar modificado exitosamente.']);
            exit;
        }

        // ---- CREACIÓN ----
        if (!$imagen || $imagen["error"] !== UPLOAD_ERR_OK) {
            echo json_encode(['correcto' => false, 'mensaje' => 'Imagen requerida para crear el lugar.']);
            exit;
        }

        $tipoArchivo = $imagen["type"];
        if ($tipoArchivo !== 'image/jpeg' && $tipoArchivo !== 'image/jpg') {
            echo json_encode(['correcto' => false, 'mensaje' => 'Solo se permiten archivos JPG y JPEG.']);
            exit;
        }

        // Crear lugar (no necesita bloqueo para creación)
        $id_lugar = $lugarDAO->crearLugar($nombre, $descripcion, $direccion, $ciudad, $zona, $id_admin);

        if ($id_lugar > 0) {
            $nombre_img_db = 'limg' . $id_lugar . '.jpg';
            $ruta_fisica   = $RUTA_FISICA_GUARDADO . $nombre_img_db;

            if (move_uploaded_file($imagen["tmp_name"], $ruta_fisica)) {
                if ($lugarDAO->updateImagen($id_lugar, $nombre_img_db)) {
                    echo json_encode(['correcto' => true, 'mensaje' => 'Lugar creado exitosamente.']);
                } else {
                    @unlink($ruta_fisica);
                    $lugarDAO->eliminarLugarSinBloqueo($id_lugar);
                    echo json_encode(['correcto' => false, 'mensaje' => 'No se pudo actualizar la imagen en la base de datos.']);
                }
            } else {
                $lugarDAO->eliminarLugarSinBloqueo($id_lugar);
                echo json_encode(['correcto' => false, 'mensaje' => 'No se pudo guardar fisicamente la imagen.']);
            }
        } else {
            echo json_encode(['correcto' => false, 'mensaje' => 'No se pudo crear el lugar.']);
        }

    } catch (Exception $e) {
        // Si la excepcion viene del SP de bloqueo, el mensaje es claro para el usuario
        if ($id_lugar > 0) {
            $lugarDAO->eliminarLugarSinBloqueo($id_lugar);
        }
        echo json_encode(['correcto' => false, 'mensaje' => $e->getMessage()]);
    }

// ============================================================
// DELETE — Eliminar lugar (requiere bloqueo previo)
// ============================================================
} else if ($_SERVER["REQUEST_METHOD"] == "DELETE") {

    if (!verificarPermisos("eliminar_lugar")) {
        redirigirAlIndex();
    }

    try {
        $json_data = file_get_contents("php://input");
        $data      = json_decode($json_data, true);

        $id_lugar = sanitizarEntero($data['id_lugar'] ?? null);
        $id_admin = sanitizarEntero($data['id_admin'] ?? null);

        if (!$id_lugar || !$id_admin) {
            echo json_encode(['correcto' => false, 'mensaje' => 'Faltan id_lugar o id_admin.']);
            exit;
        }

        $lugar = $lugarDAO->getLugarPorID($id_lugar);

        if (!$lugar) {
            echo json_encode(['correcto' => true, 'mensaje' => 'El lugar no existe o ya fue eliminado.']);
            exit;
        }

        $nombre_imagen = $lugar['imagen_url'];

        // El SP verifica el bloqueo y lo libera automaticamente al eliminar
        $lugarDAO->eliminarLugarPorId($id_admin, $id_lugar);

        if (!empty($nombre_imagen)) {
            $ruta_fisica = $RUTA_FISICA_GUARDADO . $nombre_imagen;
            if (file_exists($ruta_fisica)) @unlink($ruta_fisica);
        }

        echo json_encode(['correcto' => true, 'mensaje' => 'Lugar eliminado exitosamente.']);

    } catch (Exception $e) {
        echo json_encode(['correcto' => false, 'mensaje' => $e->getMessage()]);
    }
}