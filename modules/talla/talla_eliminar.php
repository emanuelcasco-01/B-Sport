<?php
session_start();
header('Content-Type: application/json');
require_once "../../config/conexion.php";

$response = ['status' => false, 'msg' => ''];

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    echo json_encode(['status' => false, 'msg' => 'Acceso denegado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'msg' => 'Método no permitido']);
    exit;
}

$id_talla = isset($_POST['id_talla']) ? intval($_POST['id_talla']) : 0;
$id_usuario = $_SESSION['id_usuario'];

if ($id_talla <= 0) {
    echo json_encode(['status' => false, 'msg' => 'ID de talla inválido']);
    exit;
}

try {
    // Verificar si la talla está siendo usada en alguna variante
    $sql_check = "SELECT COUNT(*) FROM articulo_variante WHERE id_talla = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$id_talla]);
    $usado = $stmt_check->fetchColumn();

    if ($usado > 0) {
        echo json_encode(['status' => false, 'msg' => 'No se puede desactivar la talla porque está siendo utilizada en productos']);
        exit;
    }

    // Desactivación lógica (cambiar estado a 0) con auditoría
    $sql_delete = "UPDATE talla SET estado = 0, actualizado_por = ?, fecha_actualizacion = NOW() WHERE id_talla = ?";
    $stmt_delete = $conexion->prepare($sql_delete);
    
    if ($stmt_delete->execute([$id_usuario, $id_talla])) {
        $response['status'] = true;
        $response['msg'] = 'Talla desactivada correctamente';
    } else {
        $response['msg'] = 'Error al desactivar la talla';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error técnico: ' . $e->getMessage();
}

echo json_encode($response);
?>