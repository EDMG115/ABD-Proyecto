<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "./../conexion.php";

    $conexion = new Conexion();
try {

    $cred = $conexion->getCredencialesActuales();

    $user = $cred['username'];
    $pass = escapeshellarg($cred['password']);

    $fecha = date("Ymd_His");
    $ruta = __DIR__ . "/../DB/backups/respaldo_$fecha.sql";

    $db = "abdarcproyecto1";
    $host = "localhost";

    $comando = "mysqldump --no-tablespaces -h $host -u $user -p$pass $db > $ruta";

    $cred = $conexion->getCredencialesActuales();

    $user = $cred['username'];
    $pass = escapeshellarg($cred['password']);

    $fecha = date("Ymd_His");
    $ruta = __DIR__ . "/../DB/backups/respaldo_$fecha.sql";

    $db = "abdarcproyecto1";
    $host = "localhost";

    $comando = "mysqldump --no-tablespaces -h $host -u $user -p$pass $db > $ruta";

    exec($comando, $output, $resultado);

    if ($resultado === 0) {
        echo "✅ Respaldo generado correctamente";
    } else {
        echo "❌ Error al generar respaldo";
        echo $comando;
    }

} catch (Exception $e) {
    echo "⛔ " . $e->getMessage();
}
?>