<?php
require_once "./../DAO/CrearReservacionDAO.php";
header('Content-Type: application/json');

$ReservacionDAO = new CrearReservacionDAO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id_evento  = isset($_POST["id_evento"])  ? (int)$_POST["id_evento"]  : null;
    $id_cliente = isset($_POST["id_cliente"]) ? (int)$_POST["id_cliente"] : null;

    if (!$id_evento || !$id_cliente) {
        echo json_encode(['correcto' => false, 'mensaje' => 'Faltan datos para la reservación.']);
        exit;
    }

    try {
        // El DAO llama a sp_intentar_reservar que verifica el bloqueo del admin
        // y lanza Exception con mensaje legible si el evento no esta disponible.
        $id_reservacion = $ReservacionDAO->crearReservacion($id_evento, $id_cliente);

        echo json_encode([
            'correcto'       => true,
            'id_reservacion' => $id_reservacion,
            'mensaje'        => 'Reservación exitosa.'
        ]);

    } catch (Exception $e) {
        // Todos los mensajes (bloqueo, duplicado, etc.) llegan aqui listos para mostrar
        echo json_encode([
            'correcto' => false,
            'mensaje'  => $e->getMessage()
        ]);
    }
}