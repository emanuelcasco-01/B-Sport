<?php
session_start();
header('Content-Type: application/json');
require_once "../../config/conexion.php";

$response = ['status' => false, 'msg' => ''];

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 1) {
    echo json_encode(['status' => false, 'msg' => 'Acceso denegado.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'msg' => 'Método no permitido']);
    exit;
}

$id_talla = isset($_POST['id_talla']) ? intval($_POST['id_talla']) : 0;
$nombre_talla = isset($_POST['nombre_talla']) ? trim($_POST['nombre_talla']) : '';
$sigla = isset($_POST['sigla']) ? trim($_POST['sigla']) : '';
$estado = isset($_POST['estado']) ? intval($_POST['estado']) : 1;
$id_usuario = $_SESSION['id_usuario']; // Obtener el ID del usuario logueado

if ($id_talla <= 0 || empty($nombre_talla) || empty($sigla)) {
    echo json_encode(['status' => false, 'msg' => 'Todos los campos son obligatorios']);
    exit;
}
// Validar longitud máxima
if (strlen($nombre_talla) > 20) {
    echo json_encode(['status' => false, 'msg' => 'El nombre de la talla es demasiado largo']);
    exit;
}

if (strlen($sigla) > 10) {
    echo json_encode(['status' => false, 'msg' => 'La sigla es demasiado larga ']);
    exit;
}

try {
 // Verificar duplicados
    $sql_check = "SELECT id_talla FROM talla WHERE (nombre_talla = ? OR sigla = ?) AND id_talla != ? LIMIT 1";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->execute([$nombre_talla, $sigla, $id_talla]);

    if ($stmt_check->fetch()) {
        // Determinar cuál campo está duplicado
        $sql_verify = "SELECT 
                        CASE 
                            WHEN nombre_talla = ? THEN 'nombre'
                            WHEN sigla = ? THEN 'sigla'
                        END as campo_duplicado
                        FROM talla 
                        WHERE (nombre_talla = ? OR sigla = ?) AND id_talla != ? 
                        LIMIT 1";
        $stmt_verify = $conexion->prepare($sql_verify);
        $stmt_verify->execute([$nombre_talla, $sigla, $nombre_talla, $sigla, $id_talla]);
        $result = $stmt_verify->fetch();
        
        if ($result['campo_duplicado'] == 'nombre') {
            echo json_encode(['status' => false, 'msg' => 'Ya existe otra talla con ese nombre']);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Ya existe otra talla con esa sigla']);
        }
        exit;
    }

    // ACTUALIZACIÓN CON AUDITORÍA (actualizado_por y fecha_actualizacion)
    $sql_update = "UPDATE talla SET nombre_talla = ?, sigla = ?, estado = ?, actualizado_por = ?, fecha_actualizacion = NOW() WHERE id_talla = ?";
    $stmt_update = $conexion->prepare($sql_update);
    
    if ($stmt_update->execute([$nombre_talla, $sigla, $estado, $id_usuario, $id_talla])) {
        $response['status'] = true;
        $response['msg'] = 'Talla actualizada correctamente';
    } else {
        $response['msg'] = 'Error al actualizar la talla';
    }

} catch (PDOException $e) {
    $response['msg'] = 'Error técnico: ' . $e->getMessage();
}

echo json_encode($response);
?>