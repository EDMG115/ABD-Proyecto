<?php
header("Content-Type: application/json");

require_once "./../util/seguridad.php";
require_once "./../dao/paqueteDAO.php";
require_once "./../dao/agenciaDAO.php";
require_once "./../dao/lugarDAO.php";

// INSTANCIAS DE CADA DAO
$paqueteDAO = new paqueteDAO(); 
$agenciaDAO = new agenciaDAO(); 
$lugarDAO = new lugarDAO();



if ($_SERVER["REQUEST_METHOD"] === "GET") {

    if (!isset($_GET["accion"])) {
        echo json_encode(["correcto" => false, "mensaje" => "Accion no especificada"]);
        exit;
    }

    $accion = $_GET["accion"];

    try {
        switch ($accion) {

            case "paquete":
                $id = $_GET["idPaquete"] ?? null;
                if (!$id) throw new Exception("Falta id_paquete");

                $data = $paqueteDAO->getPaquetePorID($id);
                echo json_encode(["correcto" => true, "data" => $data]);
                break;
            case "lugar":
                $id = $_GET["idLugar"] ?? null;
                if (!$id) throw new Exception("Falta id_lugar");

                $data = $lugarDAO->getLugarPorID($id);
                echo json_encode(["correcto" => true, "data" => $data]);
                break;
            case "lugares":
                $data = $lugarDAO->getLugares();
                echo json_encode(["correcto" => true, "data" => $data]);
                break;
            case "agencia":
                $id = $_GET["idAgencia"] ?? null;
                if (!$id) throw new Exception("Falta id_agencia");

                $data = $agenciaDAO->getAgenciaPorID($id);
                echo json_encode(["correcto" => true, "data" => $data]);
                break;

           
            // Accion no encontrada
            default:
                echo json_encode(["correcto" => false, "mensaje" => "Accion no valida"]);
                break;
        }
    } catch (Exception $e) {
        echo json_encode(["correcto" => false, "mensaje" => $e->getMessage()]);
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($_POST["accion"] === "eliminar") {
        // Validar permisos
        if (!verificarPermisos("eliminar_paquete")) {
            redirigirAlIndex();
        }
        
        $id_paquete = sanitizarEntero($_POST["idPaquete"]);
        if (!$id_paquete) {
            echo json_encode(["correcto" => false, "mensaje" => "Datos incompletos"]);
            exit;
        }

        try {
            $eliminar = $paqueteDAO->eliminarPaquete($id_paquete);
            echo json_encode([
                "correcto" => $eliminar,
                "mensaje" => $eliminar ? "Paquete eliminado correctamente" : "Error al eliminar paquete"
            ]);
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "foreign key constraint") !== false) {
                echo json_encode(["correcto" => false, "mensaje" => "No se puede eliminar el paquete porque tiene viajes asociados."]);
            } else {
                echo json_encode(["correcto" => false, "mensaje" => "Error al eliminar paquete: " . $e->getMessage()]);
            }
        }
        exit;

    } else if ($_POST["accion"] === "actualizar") {
        // Validar permisos
        if (!verificarPermisos("editar_paquete")) {
            redirigirAlIndex();
        }
        
        $id_paquete = sanitizarEntero($_POST["idPaquete"]);
        $nombre = sanitizarTexto($_POST["nombre_paquete"]);
        $descripcion = sanitizarTexto($_POST["descripcion_paquete"]);
        $precio = sanitizarDecimal($_POST["precio"]);
        $id_lugar = sanitizarEntero($_POST["id_lugar"] ?? null);

        if (!$id_paquete || !$nombre || !$descripcion || !$precio) {
            echo json_encode(["correcto" => false, "mensaje" => "Datos incompletos"]);
            exit;
        }

        // 1. Actualizar datos (sin tocar imagen_url) para que el trigger de bitácora no registre "modificación imagen"
        $actualizado = $paqueteDAO->actualizarPaquete($id_paquete, $nombre, $descripcion, $precio, $id_lugar);

        // 2. Solo si llegó una nueva imagen: guardar archivo y actualizar BD (evita falsos positivos en bitácora)
        $nuevaImagen = isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK;
        if ($nuevaImagen) {
            $RUTA_FISICA_GUARDADO = __DIR__ . "/../../media/images/paquetes/";
            if (!is_dir($RUTA_FISICA_GUARDADO)) {
                mkdir($RUTA_FISICA_GUARDADO, 0755, true);
            }
            $tipo = $_FILES["imagen"]["type"];
            $ext = ($tipo === "image/png") ? "png" : (($tipo === "image/jpeg" || $tipo === "image/jpg") ? "jpg" : null);
            if (!$ext) {
                echo json_encode(["correcto" => false, "mensaje" => "La imagen debe ser PNG o JPEG."]);
                exit;
            }
            $nuevoNombre = "pimg" . $id_paquete . "." . $ext;
            $rutaDestino = $RUTA_FISICA_GUARDADO . $nuevoNombre;
            $imgAnterior = $paqueteDAO->getPaquetePorID($id_paquete)["imagen_url"] ?? "";
            if ($imgAnterior && file_exists($RUTA_FISICA_GUARDADO . $imgAnterior)) {
                unlink($RUTA_FISICA_GUARDADO . $imgAnterior);
            }
            if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
                echo json_encode(["correcto" => false, "mensaje" => "Error al guardar imagen"]);
                exit;
            }
            $paqueteDAO->actualizarImagen($id_paquete, $nuevoNombre);
        }

        echo json_encode([
            "correcto" => $actualizado,
            "mensaje" => $actualizado ? "Paquete actualizado correctamente" : "Error al actualizar paquete"
        ]);

        exit;

    } else if ($_POST["accion"] === "crear") {
        // Validar permisos
        if (!verificarPermisos("crear_paquete")) {
            redirigirAlIndex();
        }
        
        // Recibir datos
        $nombre = sanitizarTexto($_POST["nombre_paquete"] ?? null);
        $descripcion = sanitizarTexto($_POST["descripcion_paquete"] ?? null);
        $precio = sanitizarDecimal($_POST["precio"] ?? null); 
        $id_lugar = sanitizarEntero($_POST["id_lugar"] ?? null);
        $id_agencia = sanitizarEntero($_POST["id_agencia"] ?? null); 

        // 1. VALIDACIÓN: Datos incompletos
        if (!$nombre || !$descripcion || !$precio || !$id_lugar || !$id_agencia) {
            echo json_encode(["correcto" => false, "mensaje" => "Faltan datos obligatorios para crear el paquete."]);
            exit;
        }

        // 2. VALIDACIÓN: Precio debe ser mayor a 0
        $precioFloat = (float)$precio; 
        if ($precioFloat <= 0) {
            echo json_encode(["correcto" => false, "mensaje" => "El precio debe ser mayor a 0."]);
            exit;
        }

        // === INSERTAR PRIMERO EN BD ===
        $id_nuevo_paquete = $paqueteDAO->crearPaquete($nombre, $descripcion, $precio, "", $id_lugar, $id_agencia);

        if (!$id_nuevo_paquete) {
            echo json_encode(["correcto" => false, "mensaje" => "Error al insertar el paquete en la base de datos."]);
            exit;
        }

        // === PROCESAMIENTO DE IMAGEN ===
        $RUTA_FISICA_GUARDADO = __DIR__ . "/../../media/images/paquetes/";
        if (!is_dir($RUTA_FISICA_GUARDADO)) {
            mkdir($RUTA_FISICA_GUARDADO, 0755, true);
        }
        $nuevaImagen = isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK;
        $nombreImagenFinal = null;

        if ($nuevaImagen) {
            $tipo = $_FILES["imagen"]["type"];
            $ext = ($tipo === "image/png") ? "png" : (($tipo === "image/jpeg" || $tipo === "image/jpg") ? "jpg" : null);
            if (!$ext) {
                echo json_encode(["correcto" => true, "mensaje" => "Paquete creado, pero la imagen fue rechazada (solo PNG o JPEG).", "nueva_imagen_url" => null]);
                exit;
            }

            // Crear nombre basado en el ID recién generado
            $nombreImagenFinal = "pimg" . $id_nuevo_paquete . "." . $ext;
            $rutaDestino = $RUTA_FISICA_GUARDADO . $nombreImagenFinal;

            // Mover archivo
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
                // Actualizar el registro creado con el nombre de la imagen
                $paqueteDAO->actualizarImagen($id_nuevo_paquete, $nombreImagenFinal);
            } else {
                echo json_encode(["correcto" => true, "mensaje" => "Paquete creado, pero hubo un error al mover la imagen al servidor."]);
                exit;
            }
        }

        echo json_encode([
            "correcto" => true, 
            "mensaje" => "Paquete creado exitosamente con ID: " . $id_nuevo_paquete,
        ]);
        exit;
    } else {
        echo json_encode(["correcto" => false, "mensaje" => "Acción no válida"]);
        exit;
    }
    
}

