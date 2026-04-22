<?php
require_once "../../config/conexion.php";

if (isset($_POST['id_rol']) && !empty($_POST['id_rol'])) {
    $id_rol = $_POST['id_rol'];

    try {
        $sql = "SELECT id_permiso FROM roles_permisos WHERE id_rol = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_rol]);
        
        $permisos = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        echo json_encode([
            "status" => true,
            "permisos" => $permisos
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "status" => false,
            "msg" => "Error al obtener permisos"
        ]);
    }
} else {
    echo json_encode([
        "status" => false,
        "msg" => "ID de rol no proporcionado"
    ]);
}