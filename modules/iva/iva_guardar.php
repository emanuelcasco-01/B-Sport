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
$porcentaje = filter_var($_POST['porcentaje'] ?? 0, FILTER_VALIDATE_FLOAT);
$estado      = filter_var($_POST['estado'] ?? 1, FILTER_VALIDATE_INT);
$creado_por  = $_SESSION['id_usuario'];

// 3. Validaciones de Lógica de Negocio
if (empty($nombre) || $porcentaje === false) {
    echo json_encode(['status' => false, 'msg' => 'Iva y Porcentaje son obligatorios']);
    exit;
}

if ($porcentaje < 0 || $porcentaje > 100) {
    echo json_encode(['status' => false, 'msg' => 'El porcentaje debe estar entre 0 y 100']);
    exit;
}


try {
    // 4. Búsqueda de duplicados (Case Insensitive gracias a mb_strtoupper)
    $sql_check = "SELECT id_iva FROM iva WHERE nombre = ? OR porcentaje = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$nombre, $porcentaje]);

    if ($stmt_check->fetch()) {
        echo json_encode(['status' => false, 'msg' => 'El Iva o porcentaje ya existen']);
        exit;
    }

    // 5. Inserción Segura
    $sql_insert = "INSERT INTO iva (nombre, porcentaje, estado, creado_por) 
                   VALUES (?, ?, ?, ?)";
    
    $stmt_insert = $conexion->prepare($sql_insert);
    $ok = $stmt_insert->execute([$nombre, $porcentaje, $estado, $creado_por]);

    if ($ok) {
        $response['status'] = true;
        $response['msg'] = 'Iva registrado correctamente';
    } else {
        $response['msg'] = 'Error interno al intentar guardar';
    }

} catch (PDOException $e) {
    // En producción, es mejor no mostrar $e->getMessage() al usuario, sino guardarlo en un log.
    $response['msg'] = 'Error de base de datos: ' . $e->getCode();
}

echo json_encode($response);