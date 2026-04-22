<?php
require_once "../../config/conexion.php";
session_start();
if (isset($_SESSION['token'])) {
    try {
        $token = $_SESSION['token'];
        $sql = "UPDATE sesion SET activa = 0 WHERE token = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$token]);
    } catch (PDOException $e) {
        // Si falla la DB, igual procedemos a borrar la sesión local
    }
}
// Limpiar y destruir la sesión en el servidor
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
//Redirigir al login
header("Location: " . URL_BASE . "views/auth/login.php");
exit();