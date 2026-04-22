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
$id_usuario = $_POST['id_usuario'] ?? '';

if (empty($id_usuario)) {
    echo json_encode(['status' => false, 'msg' => 'ID de usuario no proporcionado']);
    exit;
}

// Permite que el admin no se elimine así mismo
if ($id_usuario == $_SESSION['id_usuario']) {
    echo json_encode(['status' => false, 'msg' => 'No puedes eliminar tu propia cuenta mientras estás en sesión']);
    exit;
}

try {
    $sql = "UPDATE usuario SET estado = 0 WHERE id_usuario = ?";
    
    $stmt = $conexion->prepare($sql);
    
    if ($stmt->execute([$id_usuario])) {
        $response['status'] = true;
        $response['msg'] = 'Usuario desactivado correctamente';
    } else {
        $response['msg'] = 'No se pudo completar la operación en la base de datos';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error de integridad: Este usuario tiene registros asociados y no puede ser eliminado.';
}

echo json_encode($response);