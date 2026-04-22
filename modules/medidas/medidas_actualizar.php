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
$id_unidad   = filter_var($_POST['id_unidad'] ?? 0, FILTER_VALIDATE_INT);
$nombre      = mb_strtoupper(trim($_POST['nombre'] ?? ''), 'UTF-8');
$abreviatura = mb_strtoupper(trim($_POST['abreviatura'] ?? ''), 'UTF-8');
$estado      = filter_var($_POST['estado'] ?? 1, FILTER_VALIDATE_INT);
$actualizado_por = $_SESSION['id_usuario']; 

// 3. VALIDACIONES DE LÓGICA DE NEGOCIO
if (!$id_unidad) {
    echo json_encode(['status' => false, 'msg' => 'ID de unidad no válido']);
    exit;
}

if (empty($nombre) || empty($abreviatura)) {
    echo json_encode(['status' => false, 'msg' => 'Todos los campos son obligatorios']);
    exit;
}

if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 50) {
    echo json_encode(['status' => false, 'msg' => 'El nombre debe tener entre 3 y 50 caracteres']);
    exit;
}

if (mb_strlen($abreviatura) > 10) {
    echo json_encode(['status' => false, 'msg' => 'La abreviatura no puede exceder los 10 caracteres']);
    exit;
}

// Validar formato de abreviatura (letras, números y puntos únicamente)
if (!preg_match('/^[A-Z0-9.]+$/', $abreviatura)) {
    echo json_encode(['status' => false, 'msg' => 'La abreviatura contiene caracteres no permitidos']);
    exit;
}

try {
    // VALIDAR QUE LA MEDIDA NO EXISTA EN OTRA FILA
    $sql_check = "SELECT id_unidad FROM unidad_medida WHERE (nombre = ? OR abreviatura = ?) AND id_unidad != ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$nombre, $abreviatura, $id_unidad]);

    if ($stmt_check->fetch()) {
        echo json_encode(['status' => false, 'msg' => 'El nombre o abreviatura ya están en uso por otra medida']);
        exit;
    }

    $sql = "UPDATE unidad_medida SET nombre = ?, abreviatura = ?, estado = ?, actualizado_por = ? WHERE id_unidad = ?";
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([$nombre, $abreviatura, $estado, $actualizado_por, $id_unidad])) {
        $response['status'] = true;
        $response['msg'] = 'Medida actualizada correctamente';
    } else {
        $response['msg'] = 'Error al actualizar en la base de datos';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error de sistema: ' . $e->getMessage();
}

echo json_encode($response);