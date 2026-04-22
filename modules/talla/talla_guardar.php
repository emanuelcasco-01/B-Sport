<?php
session_start();
header('Content-Type: application/json');
require_once "../../config/conexion.php";

$response = ['status' => false, 'msg' => ''];

// 1. SEGURIDAD: Solo Admin (Rol 1)
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    echo json_encode(['status' => false, 'msg' => 'Acceso denegado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'msg' => 'Método no permitido']);
    exit;
}

// 2. RECOLECCIÓN Y LIMPIEZA
$nombre_talla = isset($_POST['nombre_talla']) ? trim($_POST['nombre_talla']) : '';
$sigla = isset($_POST['sigla']) ? trim($_POST['sigla']) : '';
$estado = isset($_POST['estado']) ? intval($_POST['estado']) : 1;
$id_usuario = $_SESSION['id_usuario']; 

// VALIDACIONES DE CAMPOS
if (empty($nombre_talla) || empty($sigla)) {
    echo json_encode(['status' => false, 'msg' => 'Todos los campos son obligatorios']);
    exit;
}

// Validar longitud máxima (la tabla acepta hasta 10 caracteres)
if (strlen($nombre_talla) > 20) {
    echo json_encode(['status' => false, 'msg' => 'El nombre de la talla es demasiado largo']);
    exit;
}

if (strlen($sigla) > 10) {
    echo json_encode(['status' => false, 'msg' => 'La sigla es demasiado larga ']);
    exit;
}

try {
    // 4. VALIDAR DUPLICADOS
    $sql_check = "SELECT id_talla FROM talla WHERE nombre_talla = ?  LIMIT 1";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$nombre_talla]);

    if ($stmt_check->fetch()) {
        echo json_encode(['status' => false, 'msg' => 'Ya existe una talla con ese nombre']);
        exit;
    }
    $sql_check_sigla = "SELECT id_talla FROM talla WHERE sigla = ? LIMIT 1";
    $stmt_check_sigla = $conexion->prepare($sql_check_sigla);
    $stmt_check_sigla->execute([$sigla]);

    if ($stmt_check_sigla->fetch()) {
        echo json_encode(['status' => false, 'msg' => 'Ya existe una talla con esa sigla']);
        exit;
    }

    // 5. INSERCIÓN CON AUDITORÍA
    $sql_insert = "INSERT INTO talla (nombre_talla, sigla, estado, creado_por) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conexion->prepare($sql_insert);
    
    if ($stmt_insert->execute([$nombre_talla, $sigla, $estado, $id_usuario])) {
        $response['status'] = true;
        $response['msg'] = 'Talla registrada correctamente';
    } else {
        $response['msg'] = 'Error al registrar la talla en la base de datos';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error técnico: ' . $e->getMessage();
}

echo json_encode($response);
?>