<?php
session_start();

require_once "../../config/conexion.php";
require_once "../../helpers/seguridad.php";

header('Content-Type: application/json');

$response = ['status' => false, 'msg' => ''];

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    echo json_encode(['status' => false, 'msg' => 'Acceso no autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'msg' => 'Método no permitido']);
    exit;
}
$nombre          = trim($_POST['nombre'] ?? '');
$apellido        = trim($_POST['apellido'] ?? '');
$username        = trim($_POST['username'] ?? '');
$email           = trim($_POST['email'] ?? '');
$clave           = $_POST['clave'] ?? '';
$confirmar_clave = $_POST['confirmar_clave'] ?? '';
$id_rol          = $_POST['id_rol'] ?? '';
$estado          = $_POST['estado'] ?? 1;

if (empty($nombre) || empty($apellido) || empty($username) || empty($email) || empty($clave) || empty($id_rol)) {
    echo json_encode(['status' => false, 'msg' => 'Todos los campos son obligatorios']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => false, 'msg' => 'El formato de email no es válido']);
    exit;
}

if ($clave !== $confirmar_clave) {
    echo json_encode(['status' => false, 'msg' => 'Las contraseñas no coinciden']);
    exit;
}

// Validación de seguridad de contraseña
if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $clave)) {
    echo json_encode([
        'status' => false, 
        'msg' => 'La contraseña es muy débil (mínimo 8 caracteres, una mayúscula, un número y un carácter especial)'
    ]);
    exit;
}
// 
// Valida que sean alfanuméricos y tengan entre 3 y 20 caracteres
// nombre
if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $nombre)) {
    echo json_encode(['status' => false, 'msg' => 'El formato del nombre no es válido']);
    exit;
}
// apellido
if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $apellido)) {
    echo json_encode(['status' => false, 'msg' => 'El formato del apellido no es válido']);
    exit;
}// usuario
if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    echo json_encode(['status' => false, 'msg' => 'El formato del usuario no es válido']);
    exit;
}
try {
    // BUSCAR DUPLICADOS 
    $sql_check = "SELECT id_usuario FROM usuario WHERE username = ? OR email = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$username, $email]);

    if ($stmt_check->fetch()) {
        echo json_encode(['status' => false, 'msg' => 'El nombre de usuario o email ya está registrado']);
        exit;
    }

    $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

    $sql_insert = "INSERT INTO usuario (nombre, apellido, username, email, password_hash, id_rol, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conexion->prepare($sql_insert);
    
    $ok = $stmt_insert->execute([
        $nombre, 
        $apellido, 
        $username, 
        $email, 
        $clave_hash, 
        $id_rol, 
        $estado
    ]);

    if ($ok) {
        $response['status'] = true;
        $response['msg'] = 'Usuario creado exitosamente para Bianca Sport';
    } else {
        $response['msg'] = 'No se pudo completar el registro en la base de datos';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error en el sistema: ' . $e->getMessage();
}

echo json_encode($response);