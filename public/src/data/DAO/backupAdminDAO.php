<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "./../conexion.php";

$conexion = new Conexion();

if (
    isset($_POST['accion']) &&
    $_POST['accion'] === 'historial'
) {

    $carpeta =
        __DIR__ . "/../DB/backups/";

    $archivos =
        glob($carpeta . "*.sql");

    usort($archivos, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    $resultado = [];

    foreach ($archivos as $archivo) {

        $resultado[] = [

            "fecha" => date(
                "Y-m-d H:i:s",
                filemtime($archivo)
            ),

            "tipo" => "Completo",

            "archivo" =>
            basename($archivo)

        ];
    }

    header('Content-Type: application/json');

    echo json_encode($resultado);

    exit;
}

try {

    $cred = $conexion->getCredencialesActuales();
    $user = $cred['username'];
    //$pass = escapeshellarg($cred['password']);
    $pass = $cred['password'];
    $fecha = date("Ymd_His");
    $db = "abdarcproyecto5";
    $host = "localhost";

    $mysqldump = encontrarMysqldump();
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $ruta = __DIR__ . "\\..\\DB\\backups\\respaldo_$fecha.sql";

        // $comando =  "\"$mysqldump\" --single-transaction --skip-lock-tables --triggers --events --result-file=\"$ruta\" -u $user -p\"$pass\"  $db ";
        $comando =  "\"$mysqldump\" --single-transaction --skip-lock-tables --result-file=\"$ruta\" -u $user -p\"$pass\"  $db ";
        exec($comando . " 2>&1", $output, $resultado);
    } else {
        $ruta = __DIR__ . "/../DB/backups/respaldo_$fecha.sql";
        $pass = escapeshellarg($pass);
        // $comando = "$mysqldump --single-transaction --skip-lock-tables --triggers --events  -u $user -p$pass $db > $ruta";
        $comando = "$mysqldump --single-transaction --skip-lock-tables --triggers --events  -u $user -p$pass $db > $ruta";
        exec($comando, $output, $resultado);
    }

    if ($resultado === 0) {

        $contenido =
            file_get_contents($ruta);

        // quitar DEFINER
        $contenido = preg_replace(
            '/DEFINER[ ]*=[ ]*`[^`]+`@`[^`]+`/i',
            '',
            $contenido
        );

        // quitar SQL SECURITY DEFINER
        $contenido = preg_replace(
            '/SQL SECURITY DEFINER/i',
            '',
            $contenido
        );

        // algunos formatos especiales
        $contenido = preg_replace(
            '/DEFINER=[^*]*\*/i',
            '*',
            $contenido
        );

        file_put_contents(
            $ruta,
            $contenido
        );

        echo "✅ Respaldo generado correctamente";
    } else {
        echo implode("\n", $output);
    }
} catch (Exception $e) {
    echo "⛔ " . $e->getMessage();
}

function encontrarMysqldump(): ?string
{
    $posibles = [];

    // Windows (XAMPP / WAMP / MySQL típicos)
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $posibles = [
            "C:\\xampp\\mysql\\bin\\mysqldump.exe",
            "C:\\xampp\\mysql\\bin\\mysqldump",
            "C:\\wamp64\\bin\\mysql\\mysql8.0\\bin\\mysqldump.exe",
            "C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe",
            "C:\\Program Files (x86)\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe",
        ];
    }
    //Linux / servidor
    else {
        $output = trim(shell_exec("which mysqldump"));
        if (!empty($output)) {
            $posibles[] = $output;
        }

        $posibles[] = "/usr/bin/mysqldump";
        $posibles[] = "/usr/local/bin/mysqldump";
    }


    foreach ($posibles as $path) {
        if ($path && file_exists($path)) {
            return $path;
        }
    }

    return null;
}
