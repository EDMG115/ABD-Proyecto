<?php id="logic1"

require_once "./../dao/backupDAO.php";
require_once "./../Util/seguridad.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['tipo_usuario'])) {
    redirigirAlIndex();
}

$accion = $_POST['accion'] ?? null;

sanitizar($_POST);

$dao = new BackupDAO();

switch ($accion) {

    case "backup_full":
        if ($_SESSION['tipo_usuario'] !== "administrador") {
            die("Acceso denegado");
        }

        $resultado = $dao->generarBackupCompleto();
        echo json_encode($resultado);
        break;

    case "backup_fecha":
        $inicio = $_POST['fecha_inicio'];
        $fin = $_POST['fecha_fin'];

        $tipo = $_SESSION['tipo_usuario'];

        if ($tipo === "administrador") {
            $resultado = $dao->backupPorFechaGlobal($inicio, $fin);

        } elseif ($tipo === "organizadora") {

            $id = sanitizarEntero($_POST['id_organizadora']);
            $resultado = $dao->backupEventosPorOrganizadora($id, $inicio, $fin);

        } elseif ($tipo === "agencia") {

            $id = sanitizarEntero($_POST['id_agencia']);
            $resultado = $dao->backupPaquetesPorAgencia($id, $inicio, $fin);
        }

        echo json_encode($resultado);
        break;

    case "historial":
        $tipo = $_SESSION['tipo_usuario'];

        if ($tipo === "administrador") {
            $data = $dao->obtenerHistorial();
        } elseif ($tipo === "organizadora") {
            $data = $dao->historialPorOrganizadora($_SESSION['id_organizadora']);
        } elseif ($tipo === "agencia") {
            $data = $dao->historialPorAgencia($_SESSION['id_agencia']);
        }

        echo json_encode($data);
        break;

    case "restore":

        if ($_SESSION['tipo_usuario'] !== "administrador") {
            die("Acceso denegado");
        }

        $archivo = sanitizarTexto($_POST['archivo']);
        $ruta = __DIR__ . "/backups/" . $archivo;

        if (!file_exists($ruta)) {
            echo json_encode(["msg" => "Archivo no encontrado"]);
            exit;
        }

        // Aquí falta el backup mediante comandos

        echo json_encode(["msg" => "Restauración completada"]);
        break;

    default:
        echo json_encode(["error" => "Acción no válida"]);
}