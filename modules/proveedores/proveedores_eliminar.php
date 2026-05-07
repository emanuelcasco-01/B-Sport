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
  
    $sql = "DELETE FROM proveedor WHERE id_proveedor = ?";
    
    $stmt = $conexion->prepare($sql);
    
    if ($stmt->execute([$id_proveedor])) {
        // Verificamos si realmente se eliminó algún registro
        if ($stmt->rowCount() > 0) {
            $response['status'] = true;
            $response['msg'] = 'Proveedor eliminado correctamente';
        } else {
            $response['msg'] = 'No se encontró el proveedor especificado';
        }
    } else {
        $response['msg'] = 'No se pudo completar la eliminación en la base de datos';
    }

} catch (PDOException $e) {
    // Error de integridad referencial (claves foráneas)
    if ($e->getCode() == '23000') {
        $response['msg'] = 'Error de integridad: Este proveedor tiene registros asociados y no puede ser eliminado. Elimine primero los registros relacionados.';
    } else {
        $response['msg'] = 'Error en la base de datos: ' . $e->getMessage();
    }
}

echo json_encode($response);
