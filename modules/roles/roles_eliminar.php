<?php
require_once "../../config/conexion.php";

if (isset($_POST['id_rol']) && !empty($_POST['id_rol'])) {
    
    $id_rol = $_POST['id_rol'];

    try {
        //No permitir desactivar al Administrador 
        if ($id_rol == 1) {
            echo json_encode([
                "status" => false, 
                "msg" => "Este es el rol principal del sistema y no puede ser desactivado."
            ]);
            exit;
        }

        //Verificar si hay usuarios vinculados a este rol
        $checkUsuarios = $conexion->prepare("SELECT COUNT(*) FROM usuario WHERE id_rol = ?");
        $checkUsuarios->execute([$id_rol]);
        $totalUsuarios = $checkUsuarios->fetchColumn();

        if ($totalUsuarios > 0) {
            echo json_encode([
                "status" => false, 
                "msg" => "No puedes desactivar este rol porque tiene $totalUsuarios usuario(s) asignado(s). cámbialos de rol primero."
            ]);
            exit;
        }
        $sql = "UPDATE rol SET estado = 0 WHERE id_rol = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_rol]);

        if ($stmt->rowCount() > 0) {
            echo json_encode([
                "status" => true,
                "msg" => "El rol ha sido desactivado correctamente."
            ]);
        } else {
            echo json_encode([
                "status" => false, 
                "msg" => "El rol ya se encuentra desactivado o no existe."
            ]);
        }

    } catch (Exception $e) {
        echo json_encode([
            "status" => false, 
            "msg" => "Lo sentimos, hubo un problema al procesar la solicitud."
        ]);
    }
} else {
    echo json_encode([
        "status" => false, 
        "msg" => "No se pudo identificar el rol a eliminar."
    ]);
}