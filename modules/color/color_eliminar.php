<?php
session_start();
require_once "../../config/conexion.php";

header('Content-Type: application/json');

// 1. Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => false, 'msg' => 'Sesión no válida']);
    exit();
}

// 2. Recibir ID y ID del usuario para auditoría
$id_color = isset($_POST['id_color']) ? intval($_POST['id_color']) : 0;
$id_usuario = $_SESSION['id_usuario'];

if ($id_color <= 0) {
    echo json_encode(['status' => false, 'msg' => 'ID de color no válido']);
    exit();
}

try {
    // 3. Primero consultamos el estado actual del color
    $sql_check = "SELECT estado FROM color WHERE id_color = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$id_color]);
    $color = $stmt_check->fetch(PDO::FETCH_ASSOC);

    // Verificamos si existe el registro y su estado
    if (!$color) {
        echo json_encode(['status' => false, 'msg' => 'El color no existe']);
        exit();
    }

    if ($color['estado'] == 0) {
        echo json_encode(['status' => false, 'msg' => 'Este color ya está desactivado']);
        exit();
    }

    // 4. Proceder con la desactivación (Borrado Lógico)
    $sql_update = "UPDATE color SET 
                    estado = 0, 
                    actualizado_por = ?, 
                    fecha_actualizacion = CURRENT_TIMESTAMP 
                WHERE id_color = ?";
    
    $stmt_update = $conexion->prepare($sql_update);
    $ok = $stmt_update->execute([$id_usuario, $id_color]);

    if ($ok && $stmt_update->rowCount() > 0) {
        echo json_encode([
            'status' => true, 
            'msg' => 'Color desactivado correctamente'
        ]);
    } else {
        echo json_encode([
            'status' => false, 
            'msg' => 'No se pudo realizar la desactivación'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'status' => false, 
        'msg' => 'Error técnico al procesar la solicitud'
    ]);
}