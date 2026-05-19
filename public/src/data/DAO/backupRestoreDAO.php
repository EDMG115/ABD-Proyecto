<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "./../conexion.php";

$conexion = new Conexion();

if (!isset($_POST['backup_file'])) {
    die("No se recibió archivo");
}
try {
    $cred = $conexion->getCredencialesActuales();

    $archivo = basename($_POST['backup_file']);

    $user = $cred['username'];
    //$pass = escapeshellarg($cred['password']);
    $pass = $cred['password'];
    $fecha = date("Ymd_His");
    $db = "abdarcproyecto4";
    $host = "localhost";

    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $ruta = __DIR__ . "\\..\\DB\\backups\\" . $archivo;
        if (!file_exists($ruta)) {
            die("El respaldo no existe");
        }
        $mysql =
            "\"C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe\"";
    } else {
        $ruta = __DIR__ . "/../DB/backups/" . $archivo;
        if (!file_exists($ruta)) {
            die("El respaldo no existe");
        }
        $mysql = "/usr/bin/mysql";
    }

    $comando =
        "$mysql -u $user -p\"$pass\" $db < \"$ruta\"";

    exec($comando . " 2>&1", $output, $resultado);

    if ($resultado === 0) {
        echo "✅ Base restaurada correctamente";
    } else {
        echo "❌ Error al restaurar\n";
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
