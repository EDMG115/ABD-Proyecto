<?php
require_once "./../conexion.php";
require_once "./../Util/seguridad.php";
header('Content-Type: application/json');

// ============================================================
//  bloqueoLogic.php — Endpoint para control de concurrencia
//  Acciones: adquirir | liberar | estado
// ============================================================

$conn    = new conexion();
$pdo     = $conn->getConexion();

$accion     = sanitizarTexto($_GET['accion']     ?? $_POST['accion']     ?? '');
$entidad    = sanitizarTexto($_GET['entidad']    ?? $_POST['entidad']    ?? '');
$idRegistro = sanitizarEntero($_GET['id']        ?? $_POST['id']         ?? null);
$idAdmin    = sanitizarEntero($_GET['id_admin']  ?? $_POST['id_admin']   ?? null);
$minutos    = sanitizarEntero($_GET['minutos']   ?? $_POST['minutos']    ?? 30);

// Entidades válidas que puede manejar el sistema
$ENTIDADES_VALIDAS = ['lugar', 'evento', 'paquete', 'viaje', 'reservacion', 'agencia', 'administrador'];

// ----------------------------------------------------------
// Validaciones comunes
// ----------------------------------------------------------
if (empty($accion)) {
    echo json_encode(['correcto' => false, 'mensaje' => 'Acción no especificada.']);
    exit;
}

if (!in_array($entidad, $ENTIDADES_VALIDAS, true)) {
    echo json_encode(['correcto' => false, 'mensaje' => "Entidad '$entidad' no válida."]);
    exit;
}

if ($idRegistro === null || $idAdmin === null) {
    echo json_encode(['correcto' => false, 'mensaje' => 'Faltan parámetros: id o id_admin.']);
    exit;
}

// ----------------------------------------------------------
// Dispatcher
// ----------------------------------------------------------
try {
    switch ($accion) { 

        // ---- ADQUIRIR BLOQUEO ----
        case 'adquirir':
            $stmt = $pdo->prepare("CALL sp_adquirir_bloqueo(:entidad, :id, :admin, :min, @res)");
            $stmt->bindParam(':entidad',  $entidad);
            $stmt->bindParam(':id',       $idRegistro, PDO::PARAM_INT);
            $stmt->bindParam(':admin',    $idAdmin,    PDO::PARAM_INT);
            $stmt->bindParam(':min',      $minutos,    PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            $res = $pdo->query("SELECT @res AS resultado")->fetch(PDO::FETCH_ASSOC);
            $resultado = $res['resultado'] ?? '';

            if (str_starts_with($resultado, 'OK')) {
                echo json_encode([
                    'correcto'   => true,
                    'estado'     => $resultado,          // OK | OK_RENOVADO | OK_EXPIRADO_REASIGNADO
                    'mensaje'    => 'Bloqueo adquirido.'
                ]);
            } else {
                // Formato: BLOQUEADO|NombreAdmin|HH:MM del DD/MM/AAAA
                $partes = explode('|', $resultado);
                echo json_encode([
                    'correcto'        => false,
                    'estado'          => 'BLOQUEADO',
                    'admin_bloqueador'=> $partes[1] ?? 'Otro administrador',
                    'expira_en'       => $partes[2] ?? '',
                    'mensaje'         => "Este registro está siendo editado por " . ($partes[1] ?? 'otro administrador') . ". Intenta de nuevo después de las " . ($partes[2] ?? '...')
                ]);
            }
            break;

        // ---- LIBERAR BLOQUEO ----
        case 'liberar':
            $stmt = $pdo->prepare("CALL sp_liberar_bloqueo(:entidad, :id, :admin, @res)");
            $stmt->bindParam(':entidad', $entidad);
            $stmt->bindParam(':id',      $idRegistro, PDO::PARAM_INT);
            $stmt->bindParam(':admin',   $idAdmin,    PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            $res = $pdo->query("SELECT @res AS resultado")->fetch(PDO::FETCH_ASSOC);
            echo json_encode([
                'correcto' => true,
                'estado'   => $res['resultado'] ?? 'LIBERADO',
                'mensaje'  => 'Bloqueo liberado.'
            ]);
            break;

        // ---- CONSULTAR ESTADO ----
        case 'estado':
            $stmt = $pdo->prepare("
                SELECT b.id_admin, b.expira_en, b.fecha_bloqueo,
                       CONCAT(a.nombre, ' ', a.ap1) AS nombre_admin
                  FROM bloqueos_edicion b
                  JOIN administradores  a ON a.id_admin = b.id_admin
                 WHERE b.entidad     = :entidad
                   AND b.id_registro = :id
                   AND b.expira_en   > NOW()
                 LIMIT 1
            ");
            $stmt->bindParam(':entidad', $entidad);
            $stmt->bindParam(':id',      $idRegistro, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($fila) {
                $esDelAdmin = ((int)$fila['id_admin'] === (int)$idAdmin);
                echo json_encode([
                    'correcto'    => true,
                    'bloqueado'   => true,
                    'es_tuyo'     => $esDelAdmin,
                    'id_admin'    => (int)$fila['id_admin'],
                    'nombre_admin'=> $fila['nombre_admin'],
                    'expira_en'   => $fila['expira_en'],
                    'mensaje'     => $esDelAdmin
                                        ? 'Tienes el bloqueo de este registro.'
                                        : "Bloqueado por {$fila['nombre_admin']}."
                ]);
            } else {
                echo json_encode([
                    'correcto'  => true,
                    'bloqueado' => false,
                    'mensaje'   => 'Registro libre.'
                ]);
            }
            break;

        default:
            echo json_encode(['correcto' => false, 'mensaje' => "Acción '$accion' desconocida."]);
    }

} catch (PDOException $e) {
    echo json_encode(['correcto' => false, 'mensaje' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['correcto' => false, 'mensaje' => $e->getMessage()]);
}