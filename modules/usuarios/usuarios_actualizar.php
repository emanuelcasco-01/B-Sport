<?php
session_start();
require_once "../../config/conexion.php";
require_once "../../helpers/seguridad.php";

header('Content-Type: application/json');

$response = ['status' => false, 'msg' => ''];

// VALIDACIÓN DE SESIÓN Y PERMISOS
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    echo json_encode(['status' => false, 'msg' => 'Acceso no autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'msg' => 'Método no permitido']);
    exit;
}

// CAPTURA DE DATOS
$id_usuario = $_POST['id_usuario'] ?? '';
$nombre     = trim($_POST['nombre'] ?? '');
$apellido   = trim($_POST['apellido'] ?? '');
$username   = trim($_POST['username'] ?? '');
$email      = trim($_POST['email'] ?? '');
$clave      = $_POST['clave'] ?? ''; // Puede venir vacío
$id_rol     = $_POST['id_rol'] ?? '';
$estado     = $_POST['estado'] ?? 1;

if (empty($id_usuario) || empty($nombre) || empty($apellido) || empty($username) || empty($email) || empty($id_rol)) {
    echo json_encode(['status' => false, 'msg' => 'Todos los campos excepto la clave son obligatorios']);
    exit;
}

try {
    // VALIDAR QUE EL USERNAME O EMAIL NO PERTENEZCAN A OTRO USUARIO
    $sql_check = "SELECT id_usuario FROM usuario WHERE (username = ? OR email = ?) AND id_usuario != ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$username, $email, $id_usuario]);

    if ($stmt_check->fetch()) {
        echo json_encode(['status' => false, 'msg' => 'El username o email ya están en uso por otro usuario']);
        exit;
    }
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
    // CONSTRUIR LA CONSULTA DINÁMICA (Para la contraseña)
    if (!empty($clave)) {
        // Si el usuario escribió una nueva clave, la validamos y hasheamos
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $clave)) {
            echo json_encode(['status' => false, 'msg' => 'La nueva contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un caracter especial']);
            exit;
        }
        $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
        
        $sql = "UPDATE usuario SET nombre=?, apellido=?, username=?, email=?, password_hash=?, id_rol=?, estado=? WHERE id_usuario=?";
        $params = [$nombre, $apellido, $username, $email, $clave_hash, $id_rol, $estado, $id_usuario];
    } else {
        // Si la clave está vacía, no tocamos la columna password_hash
        $sql = "UPDATE usuario SET nombre=?, apellido=?, username=?, email=?, id_rol=?, estado=? WHERE id_usuario=?";
        $params = [$nombre, $apellido, $username, $email, $id_rol, $estado, $id_usuario];
    }

    $stmt = $conexion->prepare($sql);
    if ($stmt->execute($params)) {
        $response['status'] = true;
        $response['msg'] = 'Usuario actualizado correctamente';
    } else {
        $response['msg'] = 'Error al actualizar en la base de datos';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error de sistema: ' . $e->getMessage();
}

echo json_encode($response);