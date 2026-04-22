<?php
session_start();
require_once "../../config/conexion.php";
require_once "../../helpers/seguridad.php";

header('Content-Type: application/json');

$response = ['status' => false, 'msg' => ''];

// 1. VALIDACIÓN DE SESIÓN Y PERMISOS
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    http_response_code(403);
    echo json_encode(['status' => false, 'msg' => 'Acceso no autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'msg' => 'Método no permitido']);
    exit;
}

// 2. CAPTURA, NORMALIZACIÓN Y SANITIZACIÓN
$id_iva   = filter_var($_POST['id_iva'] ?? 0, FILTER_VALIDATE_INT);
$nombre      = mb_strtoupper(trim($_POST['nombre'] ?? ''), 'UTF-8');
$porcentaje = filter_var($_POST['porcentaje'] ?? 0, FILTER_VALIDATE_FLOAT);
$estado      = filter_var($_POST['estado'] ?? 1, FILTER_VALIDATE_INT);
$actualizado_por = $_SESSION['id_usuario']; 

// 3. VALIDACIONES DE LÓGICA DE NEGOCIO
if (!$id_iva) {
    echo json_encode(['status' => false, 'msg' => 'ID de iva no válido']);
    exit;
}

if (empty($nombre) || $porcentaje === false) {
    echo json_encode(['status' => false, 'msg' => 'Todos los campos son obligatorios']);
    exit;
}

if ($porcentaje < 0 || $porcentaje > 100) {
    echo json_encode(['status' => false, 'msg' => 'El porcentaje debe estar entre 0 y 100']);
    exit;
}



try {
    // VALIDAR QUE EL IVA NO EXISTA EN OTRA FILA
    $sql_check = "SELECT id_iva FROM iva WHERE (nombre = ? OR porcentaje = ?) AND id_iva != ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$nombre, $porcentaje, $id_iva]);

    if ($stmt_check->fetch()) {
        echo json_encode(['status' => false, 'msg' => 'El nombre o porcentaje ya están en uso por otro iva']);
        exit;
    }

    $sql = "UPDATE iva SET nombre = ?, porcentaje = ?, estado = ?, actualizado_por = ? WHERE id_iva = ?";
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([$nombre, $porcentaje, $estado, $actualizado_por, $id_iva])) {
        $response['status'] = true;
        $response['msg'] = 'Iva actualizado correctamente';
    } else {
        $response['msg'] = 'Error al actualizar en la base de datos';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error de sistema: ' . $e->getMessage();
}

echo json_encode($response);