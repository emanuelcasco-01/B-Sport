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
$id_categoria = $_POST['id_categoria'] ?? '';

if (empty($id_categoria)) {
    echo json_encode(['status' => false, 'msg' => 'ID de categoría no proporcionado']);
    exit;
}

try {
    $sql = "UPDATE categoria SET estado = 0 WHERE id_categoria = ?";
    
    $stmt = $conexion->prepare($sql);
    
    if ($stmt->execute([$id_categoria])) {
        $response['status'] = true;
        $response['msg'] = 'Categoría desactivada correctamente';
    } else {
        $response['msg'] = 'No se pudo completar la operación en la base de datos';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error de integridad: Esta categoría tiene registros asociados y no puede ser eliminada.';
}

echo json_encode($response);