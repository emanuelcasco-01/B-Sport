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
$id_proveedor = $_POST['id_proveedor'] ?? '';

if (empty($id_proveedor)) {
    echo json_encode(['status' => false, 'msg' => 'ID de proveedor no proporcionado']);
    exit;
}

try {
    $sql = "UPDATE proveedor SET estado = 0 WHERE id_proveedor = ?";
    
    $stmt = $conexion->prepare($sql);
    
    if ($stmt->execute([$id_proveedor])) {
        $response['status'] = true;
        $response['msg'] = 'Proveedor desactivado correctamente';
    } else {
        $response['msg'] = 'No se pudo completar la operación en la base de datos';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error de integridad: Este proveedor tiene registros asociados y no puede ser eliminado.';
}

echo json_encode($response);