<?php
require_once "../../config/conexion.php";

if (isset($_POST['nombre']) && !empty($_POST['nombre'])) {
    
    if (!isset($_POST['permisos']) || empty($_POST['permisos'])) {
        echo json_encode(["status" => false, "msg" => "Debes seleccionar al menos un permiso."]);
        exit;
    }

    $nombre      = trim($_POST['nombre']);
    $descripcion = $_POST['descripcion'];
    $estado      = $_POST['estado'];
    $permisos    = $_POST['permisos'];

    try {
        $check = $conexion->prepare("SELECT COUNT(*) FROM rol WHERE nombre = ?");
        $check->execute([$nombre]);
        if ($check->fetchColumn() > 0) {
            echo json_encode(["status" => false, "msg" => "El nombre de este rol ya existe."]);
            exit;
        }

        $conexion->beginTransaction();

        $sqlRol = "INSERT INTO rol (nombre, descripcion, estado) VALUES (?, ?, ?)";
        $stmtRol = $conexion->prepare($sqlRol);
        $stmtRol->execute([$nombre, $descripcion, $estado]);

        $id_nuevo_rol = $conexion->lastInsertId();

        $sqlPermiso = "INSERT INTO roles_permisos (id_rol, id_permiso) VALUES (?, ?)";
        $stmtPermiso = $conexion->prepare($sqlPermiso);
        foreach ($permisos as $id_p) {
            $stmtPermiso->execute([$id_nuevo_rol, $id_p]);
        }

        $conexion->commit();
        echo json_encode(["status" => true, "msg" => "¡Rol creado correctamente!"]);

    } catch (Exception $e) {
        $conexion->rollBack();
        echo json_encode(["status" => false, "msg" => "Hubo un problema al guardar, intenta de nuevo."]);
    }
}