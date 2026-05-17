<?php
require_once "./../DAO/CrearViajeDAO.php";

header('Content-Type: application/json');
$viajeDAO = new CrearViajeDAO();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_cliente  = $_POST["id_cliente"]  ?? null;
    $id_paquete  = $_POST["id_paquete"]  ?? null;
    $estado      = $_POST["estado"]      ?? 'pendiente';
    $fecha_viaje = $_POST["fecha_viaje"] ?? null;
    $hora_viaje  = $_POST["hora_viaje"]  ?? '08:00:00';

    if (!$id_cliente || !$id_paquete || !$fecha_viaje) {
        echo json_encode(['correcto' => false, 'mensaje' => 'Faltan datos para registrar el viaje.']);
        exit;
    }

    try { 
        $viaje = $viajeDAO->crearViaje($id_cliente, $id_paquete, $estado, $fecha_viaje, $hora_viaje);
        echo json_encode(['correcto' => true, 'paquetes' => $viaje]);

    } catch (Exception $e) {
        // El mensaje ya viene limpio desde el DAO
        echo json_encode(['correcto' => false, 'mensaje' => $e->getMessage()]);
    }
}