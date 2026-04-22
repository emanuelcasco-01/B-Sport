<?php
session_start();
require_once "../../config/conexion.php";
require_once "../../helpers/seguridad.php";

header('Content-Type: application/json');

$response = ['status' => false, 'msg' => ''];

// SEGURIDAD: Solo Admin (Rol 1) puede eliminar
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    echo json_encode(['status' => false, 'msg' => 'Acceso denegado']);
    exit;
}

// VALIDAR DATOS RECIBIDOS
$id_iva = $_POST['id_iva'] ?? '';

if (empty($id_iva)) {
    echo json_encode(['status' => false, 'msg' => 'ID de iva no proporcionado']);
    exit;
}

try {
    $sql = "UPDATE iva SET estado = 0 WHERE id_iva = ?";
    
    $stmt = $conexion->prepare($sql);
    
    if ($stmt->execute([$id_iva])) {
        $response['status'] = true;
        $response['msg'] = 'Iva desactivado correctamente';
    } else {
        $response['msg'] = 'No se pudo completar la operación en la base de datos';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error de integridad: Este iva tiene registros asociados y no puede ser eliminado.';
}

echo json_encode($response);