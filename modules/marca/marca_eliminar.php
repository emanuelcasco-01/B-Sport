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
$id_marca = isset($_POST['id_marca']) ? intval($_POST['id_marca']) : 0;
$id_usuario = $_SESSION['id_usuario'];

if ($id_marca <= 0) {
    echo json_encode(['status' => false, 'msg' => 'ID de marca no válido']);
    exit();
}

try {
    // 3. Primero consultamos el estado actual del color
    $sql_check = "SELECT estado FROM marca WHERE id_marca = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$id_marca]);
    $marca = $stmt_check->fetch(PDO::FETCH_ASSOC);

    // Verificamos si existe el registro y su estado
    if (!$marca) {
        echo json_encode(['status' => false, 'msg' => 'La marca no existe']);
        exit();
    }

    if ($marca['estado'] == 0) {
        echo json_encode(['status' => false, 'msg' => 'Esta marca ya está desactivada']);
        exit();
    }

    // 4. Proceder con la desactivación (Borrado Lógico)
    $sql_update = "UPDATE marca SET 
                    estado = 0, 
                    actualizado_por = ?, 
                    fecha_actualizacion = CURRENT_TIMESTAMP 
                WHERE id_marca = ?";
    
    $stmt_update = $conexion->prepare($sql_update);
    $ok = $stmt_update->execute([$id_usuario, $id_marca]);

    if ($ok && $stmt_update->rowCount() > 0) {
        echo json_encode([
            'status' => true, 
            'msg' => 'Marca desactivada correctamente'
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