<?php
require_once "../../config/conexion.php";

// Verificamos que los datos existan
if (isset($_POST['id_rol']) && !empty($_POST['nombre'])) {
    
    // Validación de Permisos 
    if (!isset($_POST['permisos']) || empty($_POST['permisos'])) {
        echo json_encode([
            "status" => false, 
            "msg" => "No puedes dejar el rol sin permisos. Selecciona al menos uno."
        ]);
        exit;
    }

    $id_rol      = $_POST['id_rol'];
    $nombre      = trim($_POST['nombre']);
    $descripcion = $_POST['descripcion'];
    $estado      = $_POST['estado'];
    $permisos    = $_POST['permisos']; 

    try {
        //Validación de Nombre Duplicado
        $check = $conexion->prepare("SELECT COUNT(*) FROM rol WHERE nombre = ? AND id_rol != ?");
        $check->execute([$nombre, $id_rol]);
        
        if ($check->fetchColumn() > 0) {
            echo json_encode([
                "status" => false, 
                "msg" => "Ya existe otro rol con ese nombre. Intenta con uno diferente."
            ]);
            exit;
        }

        $conexion->beginTransaction();

        // 4. Actualizar datos principales
        $sqlRol = "UPDATE rol SET nombre = ?, descripcion = ?, estado = ? WHERE id_rol = ?";
        $stmtRol = $conexion->prepare($sqlRol);
        $stmtRol->execute([$nombre, $descripcion, $estado, $id_rol]);

        // 5. Sincronizar Permisos
        $stmtDelete = $conexion->prepare("DELETE FROM roles_permisos WHERE id_rol = ?");
        $stmtDelete->execute([$id_rol]);

        $sqlInsert = "INSERT INTO roles_permisos (id_rol, id_permiso) VALUES (?, ?)";
        $stmtInsert = $conexion->prepare($sqlInsert);

        foreach ($permisos as $id_p) {
            $stmtInsert->execute([$id_rol, $id_p]);
        }

        $conexion->commit();

        echo json_encode([
            "status" => true,
            "msg" => "¡Los cambios se guardaron correctamente!"
        ]);

    } catch (Exception $e) {
        $conexion->rollBack();
        echo json_encode([
            "status" => false,
            "msg" => "Hubo un problema al actualizar. Intenta de nuevo."
        ]);
    }

} else {
    echo json_encode([
        "status" => false,
        "msg" => "Faltan datos importantes para completar la operación."
    ]);
}