<?php
session_start();
require_once "../../config/conexion.php";

header('Content-Type: application/json');

// 1. Validar sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['status' => false, 'msg' => 'Sesión no válida']);
    exit();
}

// 2. Recibir y limpiar datos
$id_color = isset($_POST['id_color']) ? intval($_POST['id_color']) : 0;
$nombre_color = isset($_POST['nombre_color']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre_color'])) : '';
$estado = isset($_POST['estado']) ? intval($_POST['estado']) : 1;
$id_usuario = $_SESSION['id_usuario'];

// 3. Validaciones de entrada
if ($id_color <= 0) {
    echo json_encode(['status' => false, 'msg' => 'ID de color no válido']);
    exit();
}

if (empty($nombre_color)) {
    echo json_encode(['status' => false, 'msg' => 'El nombre es obligatorio']);
    exit();
}

if (strlen($nombre_color) < 3) {
    echo json_encode(['status' => false, 'msg' => 'El nombre es demasiado corto']);
    exit();
}

// Validación de caracteres (letras y espacios)
if (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ ]+$/", $nombre_color)) {
    echo json_encode(['status' => false, 'msg' => 'Solo se permiten letras en el nombre']);
    exit();
}

try {
    // 4. Validar duplicados (Pero excluyendo el ID actual)
    $sql_check = "SELECT id_color FROM color WHERE nombre_color = ? AND id_color != ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$nombre_color, $id_color]);

    if ($stmt_check->rowCount() > 0) {
        echo json_encode(['status' => false, 'msg' => 'Ya existe otro color con ese nombre']);
        exit();
    }

    // 5. Ejecutar la actualización
    $sql_update = "UPDATE color SET 
                    nombre_color = ?, 
                    estado = ?, 
                    actualizado_por = ?, 
                    fecha_actualizacion = CURRENT_TIMESTAMP 
                    WHERE id_color = ?";
    
    $stmt_update = $conexion->prepare($sql_update);
    $ok = $stmt_update->execute([
        $nombre_color, 
        $estado, 
        $id_usuario, 
        $id_color
    ]);

    if ($ok) {
        echo json_encode(['status' => true, 'msg' => 'Color actualizado correctamente']);
    } else {
        echo json_encode(['status' => false, 'msg' => 'No se realizaron cambios o error en la base de datos']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => false, 'msg' => 'Error técnico: ' . $e->getMessage()]);
}