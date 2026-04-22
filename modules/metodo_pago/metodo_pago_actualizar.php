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

$id_metodo_pago = isset($_POST['id_metodo_pago']) ? intval($_POST['id_metodo_pago']) : 0;
$nombre         = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$estado         = isset($_POST['estado']) ? intval($_POST['estado']) : 1;

// Validar que el ID sea válido
if ($id_metodo_pago <= 0) {
    echo json_encode(['status' => false, 'msg' => 'ID de registro no válido']);
    exit;
}


if (empty($nombre)) {
    echo json_encode(['status' => false, 'msg' => 'El nombre del método de pago es obligatorio']);
    exit;
}

if (strlen($nombre) < 3 || strlen($nombre) > 50) {
    echo json_encode(['status' => false, 'msg' => 'El nombre debe tener entre 3 y 50 caracteres']);
    exit;
}

if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre)) {
    echo json_encode(['status' => false, 'msg' => 'El nombre contiene caracteres no permitidos']);
    exit;
}

try {
    $sql_check = "SELECT id_metodo_pago FROM metodo_pago WHERE UPPER(nombre) = UPPER(?) AND id_metodo_pago != ? LIMIT 1";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$nombre, $id_metodo_pago]);

    if ($stmt_check->fetch()) {
        echo json_encode(['status' => false, 'msg' => 'Ya existe otro método de pago con ese nombre']);
        exit;
    }

    $sql_update = "UPDATE metodo_pago SET nombre = ?, estado = ? WHERE id_metodo_pago = ?";
    $stmt_update = $conexion->prepare($sql_update);
    
    if ($stmt_update->execute([$nombre, $estado, $id_metodo_pago])) {

        if ($stmt_update->rowCount() >= 0) {
            $response['status'] = true;
            $response['msg'] = 'Método de pago actualizado correctamente';
        } else {
            $response['msg'] = 'No se encontraron cambios para actualizar';
        }
    } else {
        $response['msg'] = 'Error al procesar la actualización en la base de datos';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error técnico: ' . $e->getMessage();
}

echo json_encode($response);