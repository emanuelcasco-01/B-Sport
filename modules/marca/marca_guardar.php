<?php 
session_start();
require_once "../../config/conexion.php";

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => false, 'msg' => 'Sesión no válida o expirada']);
    exit();
}

if ($_POST) {

    $nombre = isset($_POST['nombre']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre'])) : '';
    $estado = isset($_POST['estado']) ? intval($_POST['estado']) : 1;
    $id_usuario = $_SESSION['id_usuario'];

    if (empty($nombre)) {
        echo json_encode(['status' => false, 'msg' => 'El nombre de la marca es obligatorio']);
        exit();
    }

    if (strlen($nombre) < 2) {
        echo json_encode(['status' => false, 'msg' => 'El nombre es demasiado corto']);
        exit();
    }

    if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ&\-. ]+$/", $nombre)) {
        echo json_encode(['status' => false, 'msg' => 'El nombre contiene caracteres no permitidos']);
        exit();
    }

    $nombre = strtoupper($nombre);

    try {

        // VALIDAR DUPLICADO
        $sql = "SELECT id_marca FROM marca WHERE UPPER(nombre) = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$nombre]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['status' => false, 'msg' => 'La marca ya existe']);
            exit();
        }

        // INSERT
        $sql = "INSERT INTO marca (nombre, estado, creado_por) VALUES (?, ?, ?)";
        $ok = $conexion->prepare($sql)->execute([$nombre, $estado, $id_usuario]);

        echo json_encode([
            'status' => $ok,
            'msg' => $ok ? 'Marca registrada correctamente' : 'Error al registrar'
        ]);

    } catch (PDOException $e) {
        echo json_encode(['status' => false, 'msg' => 'Error técnico en el servidor']);
    }

} else {
    echo json_encode(['status' => false, 'msg' => 'Acceso no permitido']);
}