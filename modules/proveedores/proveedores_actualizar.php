<?php
session_start();
require_once "../../config/conexion.php";
require_once "../../helpers/seguridad.php";

header('Content-Type: application/json');

/**
 * Envía una respuesta JSON y termina la ejecución
 */
function sendResponse($status, $msg, $code = 200) {
    http_response_code($code);
    echo json_encode(['status' => $status, 'msg' => $msg]);
    exit;
}

// 1. SEGURIDAD Y ACCESO
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    sendResponse(false, 'Sesión no autorizada', 403);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Método no permitido', 405);
}

// 2. CAPTURA Y SANITIZACIÓN
$id_proveedor    = filter_var($_POST['id_proveedor'] ?? 0, FILTER_VALIDATE_INT);
$ruc             = trim($_POST['ruc'] ?? '');
$razon_social    = mb_strtoupper(trim($_POST['razon_social'] ?? ''), 'UTF-8');
$direccion       = mb_strtoupper(trim($_POST['direccion'] ?? ''), 'UTF-8');
$telefono        = trim($_POST['telefono'] ?? '');
$email           = mb_strtolower(trim($_POST['email'] ?? ''), 'UTF-8');
$estado          = filter_var($_POST['estado'] ?? 1, FILTER_VALIDATE_INT);
$actualizado_por = $_SESSION['id_usuario'];

// 3. VALIDACIONES DE LÓGICA
if (!$id_proveedor || $id_proveedor <= 0) {
    sendResponse(false, 'El ID del proveedor es inválido');
}

if (empty($ruc) || empty($razon_social) || empty($direccion) || empty($email)) {
    sendResponse(false, 'Todos los campos marcados como obligatorios son necesarios');
}

// Validación de RUC (11 dígitos numéricos)
if (!ctype_digit($ruc) || strlen($ruc) !== 11) {
    sendResponse(false, 'El RUC debe ser una cadena de 11 dígitos numéricos');
}

// Validación de Email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendResponse(false, 'La dirección de correo electrónico no tiene un formato válido');
}

// Validación de longitud (Razón Social y Dirección)
if (mb_strlen($razon_social) > 100 || mb_strlen($direccion) > 150) {
    sendResponse(false, 'Razón social o dirección exceden el límite permitido');
}
// Validación específica: Email (Stricter Regex)
// Explicación: Verifica formato estándar y que el TLD (el final) tenga al menos 2 caracteres.
$email_regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match($email_regex, $email)) {
    sendResponse(false, 'La dirección de correo electrónico no es válida (ejemplo@dominio.com)');
}

try {
    // 4. VERIFICACIÓN DE DUPLICADOS (Excluyendo al ID actual)
    $sql_check = "SELECT id_proveedor FROM proveedor WHERE (ruc = ? OR email = ?) AND id_proveedor != ? LIMIT 1";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$ruc, $email, $id_proveedor]);

    if ($stmt_check->fetch()) {
        sendResponse(false, 'El RUC o el Email ya pertenecen a otro proveedor registrado');
    }

    // 5. ACTUALIZACIÓN CON TRANSACCIÓN
    $conexion->beginTransaction();

    $sql_update = "UPDATE proveedor SET 
                    ruc = ?, 
                    razon_social = ?, 
                    direccion = ?, 
                    telefono = ?, 
                    email = ?, 
                    estado = ?, 
                    actualizado_por = ?
                   WHERE id_proveedor = ?";
    
    $stmt = $conexion->prepare($sql_update);
    $resultado = $stmt->execute([
        $ruc, 
        $razon_social, 
        $direccion, 
        $telefono, 
        $email, 
        $estado, 
        $actualizado_por, 
        $id_proveedor
    ]);

    if ($resultado && $stmt->rowCount() > 0) {
        $conexion->commit();
        sendResponse(true, 'Proveedor actualizado con éxito');
    } else {
        // En caso de que no haya cambios (rowCount == 0) o error
        $conexion->rollBack();
        sendResponse(false, 'No se realizaron cambios o el proveedor no existe');
    }

} catch (PDOException $e) {
    if ($conexion->inTransaction()) $conexion->rollBack();
    error_log("Error Update Proveedor [ID $id_proveedor]: " . $e->getMessage());
    sendResponse(false, 'Error interno del sistema', 500);
}