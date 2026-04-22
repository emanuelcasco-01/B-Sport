<?php
session_start();

require_once "../../config/conexion.php";
require_once "../../helpers/seguridad.php";

header('Content-Type: application/json');

$response = ['status' => false, 'msg' => ''];

// 1. Verificación de sesión y rol
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    http_response_code(403); // Proporciona un código de estado HTTP adecuado
    echo json_encode(['status' => false, 'msg' => 'Acceso no autorizado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'msg' => 'Método no permitido']);
    exit;
}

// 2. Captura y Limpieza (Sanitización)
$nombre      = mb_strtoupper(trim($_POST['nombre'] ?? ''), 'UTF-8'); // Normalizamos a mayúsculas
$abreviatura = mb_strtoupper(trim($_POST['abreviatura'] ?? ''), 'UTF-8');
$estado      = filter_var($_POST['estado'] ?? 1, FILTER_VALIDATE_INT);
$creado_por  = $_SESSION['id_usuario'];

// 3. Validaciones de Lógica de Negocio
if (empty($nombre) || empty($abreviatura)) {
    echo json_encode(['status' => false, 'msg' => 'Nombre y Abreviatura son obligatorios']);
    exit;
}

if (mb_strlen($nombre) < 3 || mb_strlen($nombre) > 50) {
    echo json_encode(['status' => false, 'msg' => 'El nombre debe tener entre 3 y 50 caracteres']);
    exit;
}

if (mb_strlen($abreviatura) > 10) {
    echo json_encode(['status' => false, 'msg' => 'La abreviatura es demasiado larga (máx 10)']);
    exit;
}

// Validar que la abreviatura no contenga caracteres extraños (solo letras, números y quizás un punto)
if (!preg_match('/^[A-Z0-9.]+$/', $abreviatura)) {
    echo json_encode(['status' => false, 'msg' => 'La abreviatura solo permite letras, números y puntos']);
    exit;
}

try {
    // 4. Búsqueda de duplicados (Case Insensitive gracias a mb_strtoupper)
    $sql_check = "SELECT id_unidad FROM unidad_medida WHERE nombre = ? OR abreviatura = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$nombre, $abreviatura]);

    if ($stmt_check->fetch()) {
        echo json_encode(['status' => false, 'msg' => 'El nombre o la abreviatura ya existen']);
        exit;
    }

    // 5. Inserción Segura
    $sql_insert = "INSERT INTO unidad_medida (nombre, abreviatura, estado, creado_por) 
                   VALUES (?, ?, ?, ?)";
    
    $stmt_insert = $conexion->prepare($sql_insert);
    $ok = $stmt_insert->execute([$nombre, $abreviatura, $estado, $creado_por]);

    if ($ok) {
        $response['status'] = true;
        $response['msg'] = 'Medida registrada correctamente';
    } else {
        $response['msg'] = 'Error interno al intentar guardar';
    }

} catch (PDOException $e) {
    // En producción, es mejor no mostrar $e->getMessage() al usuario, sino guardarlo en un log.
    $response['msg'] = 'Error de base de datos: ' . $e->getCode();
}

echo json_encode($response);