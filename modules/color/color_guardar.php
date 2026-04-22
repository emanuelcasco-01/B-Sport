<?php
session_start();
require_once "../../config/conexion.php";

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => false, 'msg' => 'Sesión no válida']);
    exit();
}

$nombre_color = trim($_POST['nombre_color'] ?? '');
$estado = intval($_POST['estado'] ?? 1);
$id_usuario = $_SESSION['id_usuario'];

// VALIDACIONES
if (empty($nombre_color)) {
    echo json_encode(['status' => false, 'msg' => 'El nombre del color es obligatorio']);
    exit();
}

if (strlen($nombre_color) < 3) {
    echo json_encode(['status' => false, 'msg' => 'El nombre del color es muy corto']);
    exit();
}

if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre_color)) {
    echo json_encode(['status' => false, 'msg' => 'Solo letras son permitidas']);
    exit();
}

try {

    // VALIDAR DUPLICADO
    $sql = "SELECT id_color FROM color WHERE nombre_color = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$nombre_color]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => false, 'msg' => 'El color ya existe']);
        exit();
    }

    // INSERT
    $sql = "INSERT INTO color (nombre_color, estado, creado_por) VALUES (?, ?, ?)";
    $ok = $conexion->prepare($sql)->execute([$nombre_color, $estado, $id_usuario]);

    echo json_encode([
        'status' => $ok,
        'msg' => $ok ? 'Color registrado correctamente' : 'Error al registrar'
    ]);

} catch (PDOException $e) {
    echo json_encode(['status' => false, 'msg' => 'Error en el servidor']);
}