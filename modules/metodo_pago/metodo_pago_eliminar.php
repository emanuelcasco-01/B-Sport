<?php
session_start();

header('Content-Type: application/json');
require_once "../../config/conexion.php";

$response = ['status' => false, 'msg' => ''];

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    echo json_encode(['status' => false, 'msg' => 'Acceso denegado. No tiene permisos para eliminar registros.']);
    exit;
}

$id_metodo_pago = isset($_POST['id_metodo_pago']) ? intval($_POST['id_metodo_pago']) : 0;

if ($id_metodo_pago <= 0) {
    echo json_encode(['status' => false, 'msg' => 'ID de método de pago no válido']);
    exit;
}

try {
    // verificar si el método de pago está siendo usado en ventas activas
    
    $sql = "UPDATE metodo_pago SET estado = 0 WHERE id_metodo_pago = ?";
    $stmt = $conexion->prepare($sql);
    
    if ($stmt->execute([$id_metodo_pago])) {
        if ($stmt->rowCount() > 0) {
            $response['status'] = true;
            $response['msg'] = 'El método de pago ha sido desactivado correctamente';
        } else {
            $response['msg'] = 'No se encontró el registro o ya se encontraba desactivado';
        }
    } else {
        $response['msg'] = 'Error al intentar procesar la solicitud en el servidor';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error de base de datos: ' . $e->getMessage();
}

echo json_encode($response);