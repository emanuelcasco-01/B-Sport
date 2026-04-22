<?php
session_start();

require_once "../../config/conexion.php";
require_once "../../helpers/seguridad.php";

header('Content-Type: application/json');

/**
 * Función auxiliar para respuestas estandarizadas
 */
function sendResponse($status, $msg, $code = 200) {
    http_response_code($code);
    echo json_encode(['status' => $status, 'msg' => $msg]);
    exit;
}

// 1. Seguridad de Acceso y Método
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    sendResponse(false, 'Acceso denegado: Sesión no válida', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Método no permitido', 405);
}

// 2. Captura y Limpieza
$ruc          = trim($_POST['ruc'] ?? '');
$razon_social = mb_strtoupper(trim($_POST['razon_social'] ?? ''), 'UTF-8');
$direccion    = mb_strtoupper(trim($_POST['direccion'] ?? ''), 'UTF-8');
$telefono     = trim($_POST['telefono'] ?? '');
$email        = mb_strtolower(trim($_POST['email'] ?? ''), 'UTF-8');
$estado       = filter_var($_POST['estado'] ?? 1, FILTER_VALIDATE_INT);
$creado_por   = $_SESSION['id_usuario'];

// 3. Validaciones de Lógica de Negocio (Fail Fast)

// Campos vacíos
if (empty($ruc) || empty($razon_social) || empty($direccion) || empty($email)) {
    sendResponse(false, 'Por favor, complete todos los campos obligatorios');
}

// Validación específica: RUC (Solo números y longitud exacta)
if (!ctype_digit($ruc) || strlen($ruc) !== 11) {
    sendResponse(false, 'El RUC debe contener exactamente 11 dígitos numéricos');
}

// Validación específica: Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(false, 'El formato del correo electrónico no es válido');
}

// Validación específica: Teléfono (Regex básico para números y caracteres de contacto)
if (!preg_match('/^[0-9\-\+\s\(\)]{7,20}$/', $telefono)) {
    sendResponse(false, 'El formato del teléfono es inválido');
}

// Longitud de dirección
if (mb_strlen($direccion) < 5 || mb_strlen($direccion) > 150) {
    sendResponse(false, 'La dirección debe tener entre 5 y 150 caracteres');
}
// Validación específica: Email (Stricter Regex)
// Explicación: Verifica formato estándar y que el TLD (el final) tenga al menos 2 caracteres.
$email_regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match($email_regex, $email)) {
    sendResponse(false, 'La dirección de correo electrónico no es válida (ejemplo@dominio.com)');
}
try {
    // 4. Verificación de Duplicados
    $stmt_check = $conexion->prepare("SELECT id_proveedor FROM proveedor WHERE ruc = ? LIMIT 1");
    $stmt_check->execute([$ruc]);

    if ($stmt_check->fetch()) {
        sendResponse(false, 'Error: Ya existe un proveedor registrado con este RUC');
    }

    // 5. Inserción con Transacción
    $conexion->beginTransaction();

    $sql_insert = "INSERT INTO proveedor (ruc, razon_social, direccion, telefono, email, estado, creado_por) 
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_insert = $conexion->prepare($sql_insert);
    $stmt_insert->execute([$ruc, $razon_social, $direccion, $telefono, $email, $estado, $creado_por]);

    $conexion->commit();
    sendResponse(true, 'Proveedor registrado exitosamente');

} catch (PDOException $e) {
    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }
    // Log interno del error para el desarrollador
    error_log("Error en Registro Proveedor: " . $e->getMessage());
    
    // Mensaje amigable para el usuario
    sendResponse(false, 'Hubo un problema interno en el servidor. Intente más tarde.', 500);
}