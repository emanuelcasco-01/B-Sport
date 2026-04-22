<?php
function verificarSesion($conexion) {
    if (!isset($_SESSION['id_usuario']) || !isset($_SESSION['token'])) {
        header("Location: " . URL_BASE . "views/auth/login.php");
        exit();
    }

    try {
        $token = $_SESSION['token'];
        // Verificamos si la sesión aún no ha vencido en la BD
        $sql = "SELECT id_usuario FROM sesion 
                WHERE token = ? AND activa = 1 
                AND fecha_expiracion > NOW() LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$token]);
        
        if ($stmt->fetch()) {
            $sqlUpdate = "UPDATE sesion SET fecha_expiracion = DATE_ADD(NOW(), INTERVAL 30 MINUTE) 
                        WHERE token = ?";
            $stmtUpdate = $conexion->prepare($sqlUpdate);
            $stmtUpdate->execute([$token]);
        } else {
            // Sesión expirada por tiempo
            session_destroy();
            header("Location: " . URL_BASE . "views/auth/login.php?expirado=1");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: " . URL_BASE . "views/auth/login.php?error_db=1");
        exit();
    }
}
function tienePermiso($conexion, $id_permiso) {
    if (!isset($_SESSION['id_rol'])) {
        return false;
    }
    if ($_SESSION['id_rol'] == 1) {
        return true;
    }
    try {
        $id_rol = $_SESSION['id_rol'];
        $sql = "SELECT id_rol FROM roles_permisos WHERE id_rol = ? AND id_permiso = ? LIMIT 1";    
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$id_rol, $id_permiso]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);  
        return $resultado !== false;
    } catch (PDOException $e) {
        return false;
    }
}