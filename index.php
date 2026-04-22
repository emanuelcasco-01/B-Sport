<?php
session_start();
// Verificamos si existe la sesión activa en el navegador
if (isset($_SESSION['id_usuario']) && isset($_SESSION['token'])) {
    // Si ya está logueado, lo mandamos directo al Dashboard
    header("Location: views/dashboard.php");
} else {
    // Si no, al Login
    header("Location: views/auth/login.php");
}
exit();
?>
