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
$id_categoria   = filter_var($_POST['id_categoria'] ?? 0, FILTER_VALIDATE_INT);
$nombre      = mb_strtoupper(trim($_POST['nombre'] ?? ''), 'UTF-8');
$descripcion = mb_strtoupper(trim($_POST['descripcion'] ?? ''), 'UTF-8');
$estado      = filter_var($_POST['estado'] ?? 1, FILTER_VALIDATE_INT);
$actualizado_por = $_SESSION['id_usuario']; 

// 3. VALIDACIONES DE LÓGICA DE NEGOCIO
if (!$id_categoria) {
    echo json_encode(['status' => false, 'msg' => 'ID de categoría no válido']);
    exit;
}

if (empty($nombre) || empty($descripcion)) {
    echo json_encode(['status' => false, 'msg' => 'Todos los campos son obligatorios']);
    exit;
}

if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 50) {
    echo json_encode(['status' => false, 'msg' => 'El nombre debe tener entre 3 y 50 caracteres']);
    exit;
}

if (mb_strlen($descripcion) > 200) {
    echo json_encode(['status' => false, 'msg' => 'La descripción no puede exceder los 200 caracteres']);
    exit;
}



try {
    // VALIDAR QUE LA CATEGORÍA NO EXISTA EN OTRA FILA
    $sql_check = "SELECT id_categoria FROM categoria WHERE (nombre = ? OR descripcion = ?) AND id_categoria != ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$nombre, $descripcion, $id_categoria]);

    if ($stmt_check->fetch()) {
        echo json_encode(['status' => false, 'msg' => 'El nombre o descripción ya están en uso por otra categoría']);
        exit;
    }

    $sql = "UPDATE categoria SET nombre = ?, descripcion = ?, estado = ?, actualizado_por = ? WHERE id_categoria = ?";
    $stmt = $conexion->prepare($sql);

    if ($stmt->execute([$nombre, $descripcion, $estado, $actualizado_por, $id_categoria])) {
        $response['status'] = true;
        $response['msg'] = 'Categoría actualizada correctamente';
    } else {
        $response['msg'] = 'Error al actualizar en la base de datos';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error de sistema: ' . $e->getMessage();
}

echo json_encode($response);