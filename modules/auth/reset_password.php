<?php
require_once "../../config/conexion.php";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = isset($_POST['token']) ? $_POST['token'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // 1. Validaciones básicas de seguridad
    if (empty($token) || empty($password)) {
        header("Location: ../../views/auth/login.php?error=data_missing");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: ../../views/auth/reset.php?token=$token&error=match");
        exit();
    }

    try {
        // Iniciar una transacción para asegurar que ambos cambios ocurran o ninguno
        $conexion->beginTransaction();

        //Verificar que el token sea válido y no haya expirado
        $sql = "SELECT id_usuario FROM recuperacion_password 
                WHERE token = ? AND usado = 0 AND fecha_expiracion > NOW() 
                LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $id_usuario = $row['id_usuario'];

            // Encriptar la nueva contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Actualizar la contraseña en la tabla usuario
            $sqlUpdate = "UPDATE usuario SET password_hash = ? WHERE id_usuario = ?";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            $stmtUpdate->execute([$password_hash, $id_usuario]);

            // Marcar el token como usado
            $sqlUsed = "UPDATE recuperacion_password SET usado = 1 WHERE token = ?";
            $stmtUsed = $conexion->prepare($sqlUsed);
            $stmtUsed->execute([$token]);

            // Todo salió bien, confirmamos los cambios
            $conexion->commit();

            // Redirigir al login con un mensaje de éxito
            header("Location: ../../views/auth/login.php?reset=success");
            exit();

        } else {
            $conexion->rollBack();
            header("Location: ../../views/auth/login.php?error=token_invalid");
            exit();
        }

    } catch (PDOException $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        die("Error de sistema al actualizar la contraseña.");
    }
} else {
    header("Location: ../../views/auth/login.php");
    exit();
}