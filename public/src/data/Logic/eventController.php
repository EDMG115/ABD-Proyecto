<?php
header("Content-Type: application/json");

require_once "./../util/seguridad.php";
require_once "./../dao/eventosDAO.php";
require_once "./../dao/organizadorDAO.php";
require_once "./../dao/lugarDAO.php";
require_once "./../dao/actividadDAO.php";

// INSTANCIAS DE CADA DAO
$eventosDAO = new eventosDAO(); 
$organizadorDAO = new organizadorDAO(); 
$lugarDAO = new lugarDAO();
$actividadDAO = new actividadDAO(); 



if ($_SERVER["REQUEST_METHOD"] === "GET") {

    if (!isset($_GET["accion"])) {
        echo json_encode(["correcto" => false, "mensaje" => "Accion no especificada"]);
        exit;
    }

    $accion = $_GET["accion"];

    try {
        switch ($accion) {

            case "evento":
                $id = $_GET["idEvento"] ?? null;
                if (!$id) throw new Exception("Falta id_evento");

                $data = $eventosDAO->getEventoPorID($id);
                echo json_encode(["correcto" => true, "data" => $data]);
                break;
            case "lugar":
                $id = $_GET["idLugar"] ?? null;
                if (!$id) throw new Exception("Falta id_evento");

                $data = $lugarDAO->getLugarPorID($id);
                echo json_encode(["correcto" => true, "data" => $data]);
                break;
            case "lugares":
                $data = $lugarDAO->getLugares();
                echo json_encode(["correcto" => true, "data" => $data]);
                break;
            case "tipos":
                $data = $actividadDAO->getActividades();
                echo json_encode(["correcto" => true, "data" => $data]);
                break;
            case "organizadora":
                $id = $_GET["idOrganizadora"] ?? null;
                if (!$id) throw new Exception("Falta id_evento");

                $data = $organizadorDAO->getOrganizadorPorID($id);
                echo json_encode(["correcto" => true, "data" => $data]);
                break;
            case "actividad":
                $id = $_GET["idActividad"] ?? null;
                if (!$id) throw new Exception("Falta id_actividad");

                $data = $eventosDAO->getTipoActividadPorID($id);
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
        if (!verificarPermisos("eliminar_evento")) {
            redirigirAlIndex();
        }
        
        $id_evento = sanitizarEntero($_POST["idEvento"]);
        if (!$id_evento) {
            echo json_encode(["correcto" => false, "mensaje" => "Datos incompletos"]);
            exit;
        }

        try {
            $eliminar = $eventosDAO->eliminarEvento($id_evento);
            echo json_encode([
                "correcto" => $eliminar,
                "mensaje" => $eliminar ? "Evento eliminado correctamente" : "Error al eliminar evento"
            ]);
        } catch (PDOException $e) {
            $msg = $e->getMessage();
            if (strpos($msg, "foreign key") !== false || strpos($msg, "foreign key constraint") !== false) {
                echo json_encode(["correcto" => false, "mensaje" => "No se puede eliminar el evento porque tiene reservaciones asociadas."]);
            } else {
                echo json_encode(["correcto" => false, "mensaje" => "Error al eliminar evento. Verifica que exista el procedimiento eliminarEvento en la BD."]);
            }
        }
        exit;

    } else if ($_POST["accion"] === "actualizar") {
        // Validar permisos
        if (!verificarPermisos("editar_evento")) {
            redirigirAlIndex();
        }
        
        //$id_evento, $nombre, $descripcion, $hora_evento, $precio_boleto, $id_lugar, $id_tipo_actividad
        $id_evento = sanitizarEntero($_POST["idEvento"]);
        $nombre = sanitizarTexto($_POST["nombre_evento"]);
        $descripcion = sanitizarTexto($_POST["descripcion"]);
        $hora_evento = sanitizarTexto($_POST["hora_evento"]);
        $precio_boleto = sanitizarDecimal($_POST["precio_boleto"]);
        $id_lugar = sanitizarEntero($_POST["id_lugar"] ?? null);
        $id_tipo_actividad = sanitizarEntero($_POST["id_tipo_actividad"] ?? null);

        if (!$id_evento || !$nombre || !$descripcion || !$hora_evento || !$precio_boleto) {
            echo json_encode(["correcto" => false, "mensaje" => "Datos incompletos"]);
            exit;
        }

        // 1. Actualizar datos (sin tocar imagen_url) para que el trigger de bitácora no registre "modificación imagen"
        $actualizado = $eventosDAO->actualizarEvento($id_evento, $nombre, $descripcion, $hora_evento, $precio_boleto, $id_lugar, $id_tipo_actividad);

        // 2. Solo si llegó una nueva imagen: guardar archivo y actualizar BD (evita falsos positivos en bitácora)
        $nuevaImagen = isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK;
        if ($nuevaImagen) {
            $RUTA_FISICA_GUARDADO = __DIR__ . "/../../media/images/events/";
            if (!is_dir($RUTA_FISICA_GUARDADO)) {
                mkdir($RUTA_FISICA_GUARDADO, 0755, true);
            }
            $tipo = $_FILES["imagen"]["type"];
            $ext = ($tipo === "image/png") ? "png" : (($tipo === "image/jpeg" || $tipo === "image/jpg") ? "jpg" : null);
            if (!$ext) {
                echo json_encode(["correcto" => false, "mensaje" => "La imagen debe ser PNG o JPEG."]);
                exit;
            }
            $nuevoNombre = "eimg" . $id_evento . "." . $ext;
            $rutaDestino = $RUTA_FISICA_GUARDADO . $nuevoNombre;
            $imgAnterior = $eventosDAO->getEventoPorID($id_evento)["imagen_url"] ?? "";
            if ($imgAnterior && file_exists($RUTA_FISICA_GUARDADO . $imgAnterior)) {
                unlink($RUTA_FISICA_GUARDADO . $imgAnterior);
            }
            if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
                echo json_encode(["correcto" => false, "mensaje" => "Error al guardar imagen"]);
                exit;
            }
            $eventosDAO->actualizarImagen($id_evento, $nuevoNombre);
        }

        echo json_encode([
            "correcto" => $actualizado,
            "mensaje" => $actualizado ? "Evento actualizado correctamente" : "Error al actualizar evento"
        ]);

        exit;

    } else if ($_POST["accion"] === "crear") {
        // Validar permisos
        if (!verificarPermisos("crear_evento")) {
            redirigirAlIndex();
        }
        
        // Recibir datos, no recibeo idevento porque es AI
        $nombre = sanitizarTexto($_POST["nombre_evento"] ?? null);
        $descripcion = sanitizarTexto($_POST["descripcion"] ?? null);
        $hora_evento = sanitizarTexto($_POST["hora_evento"] ?? null);
        $precio_boleto = sanitizarDecimal($_POST["precio_boleto"] ?? null); 
        $fecha_evento = sanitizarTexto($_POST["fecha_evento"] ?? null); // Necesario para la validación
        $id_lugar = sanitizarEntero($_POST["id_lugar"] ?? null);
        $id_tipo_actividad = sanitizarEntero($_POST["id_tipo_actividad"] ?? null);
        $id_organizador = sanitizarEntero($_POST["id_organizador"] ?? null); 

        // 1. VALIDACIÓN: Datos incompletos
        if (!$nombre || !$descripcion || !$hora_evento || !$precio_boleto || !$fecha_evento || !$id_lugar || !$id_tipo_actividad) {
            echo json_encode(["correcto" => false, "mensaje" => "Faltan datos obligatorios para crear el evento."]);
            exit;
        }

        // 2. VALIDACIÓN: Cantidad de entradas (Entre 10 y 2000)
        // Se castea a int para asegurar la comparación numérica
        $cantidad = (int)$precio_boleto; 
        if ($cantidad < 10 || $cantidad > 2000) {
            echo json_encode(["correcto" => false, "mensaje" => "La cantidad de entradas debe ser mínimo 10 y máximo 2000."]);
            exit;
        }

        // 3. VALIDACIÓN: La fecha no puede ser anterior a la fecha actual
        // Asegúrate de tener la zona horaria correcta configurada, ej: date_default_timezone_set('America/Mexico_City');
        $fechaActual = date("Y-m-d");
        if ($fecha_evento <= $fechaActual) {
            echo json_encode(["correcto" => false, "mensaje" => "No puedes crear eventos con fechas pasadas."]);
            exit;
        }

        // === INSERTAR PRIMERO EN BD ===
        // Necesitamos insertar primero para obtener el ID y poder nombrar la imagen como 'eimgID.png'
        // Asumimos que tu DAO tiene un método 'crearEvento' que retorna el ID del último insertado
        // Pasamos NULL o cadena vacía en la imagen temporalmente.
        
        $id_nuevo_evento = $eventosDAO->crearEvento($nombre, $descripcion, $fecha_evento, $hora_evento, $fechaActual, $precio_boleto, "", $id_lugar, $id_tipo_actividad, $id_organizador);

        if (!$id_nuevo_evento) {
            echo json_encode(["correcto" => false, "mensaje" => "Error al insertar el evento en la base de datos."]);
            exit;
        }

        // === PROCESAMIENTO DE IMAGEN ===
        $RUTA_FISICA_GUARDADO = __DIR__ . "/../../media/images/events/";
        if (!is_dir($RUTA_FISICA_GUARDADO)) {
            mkdir($RUTA_FISICA_GUARDADO, 0755, true);
        }
        $nuevaImagen = isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK;
        $nombreImagenFinal = null;

        if ($nuevaImagen) {
            $tipo = $_FILES["imagen"]["type"];
            $ext = ($tipo === "image/png") ? "png" : (($tipo === "image/jpeg" || $tipo === "image/jpg") ? "jpg" : null);
            if (!$ext) {
                echo json_encode(["correcto" => true, "mensaje" => "Evento creado, pero la imagen fue rechazada (solo PNG o JPEG).", "nueva_imagen_url" => null]);
                exit;
            }

            // Crear nombre basado en el ID recién generado
            $nombreImagenFinal = "eimg" . $id_nuevo_evento . "." . $ext;
            $rutaDestino = $RUTA_FISICA_GUARDADO . $nombreImagenFinal;

            // Mover archivo
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $rutaDestino)) {
                // Actualizar el registro creado con el nombre de la imagen
                $eventosDAO->actualizarImagen($id_nuevo_evento, $nombreImagenFinal);
            } else {
                echo json_encode(["correcto" => true, "mensaje" => "Evento creado, pero hubo un error al mover la imagen al servidor."]);
                exit;
            }
        } else {
            // Opcional: Si la imagen es obligatoria, podrías retornar error aquí, 
            // pero como ya insertamos el evento, usualmente se permite crear sin imagen o con una por defecto.
        }

        echo json_encode([
            "correcto" => true, 
            "mensaje" => "Evento creado exitosamente con ID: " . $id_nuevo_evento,
        ]);
        exit;
    } else {
        echo json_encode(["correcto" => false, "mensaje" => "Acción no válida"]);
        exit;
    }
    
}