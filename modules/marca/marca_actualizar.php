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
$id_marca = isset($_POST['id_marca']) ? intval($_POST['id_marca']) : 0;
$nombre = isset($_POST['nombre']) ? preg_replace('/\s+/', ' ', trim($_POST['nombre'])) : '';
$estado = isset($_POST['estado']) ? intval($_POST['estado']) : 1;
$id_usuario = $_SESSION['id_usuario'];

// 3. Validaciones de entrada
if ($id_marca <= 0) {
    echo json_encode(['status' => false, 'msg' => 'ID de marca no válido']);
    exit();
}

if (empty($nombre)) {
    echo json_encode(['status' => false, 'msg' => 'El nombre de la marca es obligatorio']);
    exit();
}

if (strlen($nombre) < 3) {
    echo json_encode(['status' => false, 'msg' => 'El nombre de la marca es demasiado corto']);
    exit();
}

if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ&\-. ]+$/", $nombre)) {
        echo json_encode(['status' => false, 'msg' => 'El nombre de la marca contiene caracteres no permitidos']);
        exit();
    }

try {
    // 4. Validar duplicados (Pero excluyendo el ID actual)
    $sql_check = "SELECT id_marca FROM marca WHERE nombre = ? AND id_marca != ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$nombre, $id_marca]);

    if ($stmt_check->rowCount() > 0) {
        echo json_encode(['status' => false, 'msg' => 'Ya existe otra marca con ese nombre']);
        exit();
    }

    // 5. Ejecutar la actualización
    $sql_update = "UPDATE marca SET 
                    nombre = ?, 
                    estado = ?, 
                    actualizado_por = ?, 
                    fecha_actualizacion = CURRENT_TIMESTAMP 
                    WHERE id_marca = ?";
    
    $stmt_update = $conexion->prepare($sql_update);
    $ok = $stmt_update->execute([
        $nombre, 
        $estado, 
        $id_usuario, 
        $id_marca
    ]);

    if ($ok) {
        echo json_encode(['status' => true, 'msg' => 'Marca actualizada correctamente']);
    } else {
        echo json_encode(['status' => false, 'msg' => 'No se realizaron cambios o error en la base de datos']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => false, 'msg' => 'Error técnico: ' . $e->getMessage()]);
}