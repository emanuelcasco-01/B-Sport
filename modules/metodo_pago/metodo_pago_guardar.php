<?php
session_start();

header('Content-Type: application/json');
require_once "../../config/conexion.php";
// Descomenta la siguiente línea si usas el helper de seguridad de usuarios
// require_once "../../helpers/seguridad.php"; 

$response = ['status' => false, 'msg' => ''];

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    echo json_encode(['status' => false, 'msg' => 'Acceso denegado. No tiene permisos suficientes.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'msg' => 'Método de petición no válido']);
    exit;
}

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$estado = isset($_POST['estado']) ? intval($_POST['estado']) : 1;

if (empty($nombre)) {
    echo json_encode(['status' => false, 'msg' => 'El nombre del método de pago es obligatorio']);
    exit;
}

if (strlen($nombre) < 3 || strlen($nombre) > 50) {
    echo json_encode(['status' => false, 'msg' => 'El nombre debe tener entre 3 y 50 caracteres']);
    exit;
}


if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre)) {
    echo json_encode(['status' => false, 'msg' => 'El nombre contiene caracteres no permitidos (solo letras)']);
    exit;
}

try {

    $sql_check = "SELECT id_metodo_pago FROM metodo_pago WHERE UPPER(nombre) = UPPER(?) LIMIT 1";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$nombre]);

    if ($stmt_check->fetch()) {
        echo json_encode(['status' => false, 'msg' => 'Ya existe un método de pago registrado con ese nombre']);
        exit;
    }

    $sql_insert = "INSERT INTO metodo_pago (nombre, estado) VALUES (?, ?)";
    $stmt_insert = $conexion->prepare($sql_insert);
    
    if ($stmt_insert->execute([$nombre, $estado])) {
        $response['status'] = true;
        $response['msg'] = 'Método de pago registrado exitosamente';
    } else {
        $response['msg'] = 'No se pudo completar el registro en el servidor';
    }

} catch (PDOException $e) {

    $response['msg'] = 'Error técnico: ' . $e->getMessage();
}

echo json_encode($response);