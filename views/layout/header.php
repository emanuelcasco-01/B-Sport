<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../helpers/seguridad.php";
require_once __DIR__ . "/../../config/conexion.php";

verificarSesion($conexion); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B-Sport</title>
    <link rel="icon" type="image/png" href="<?= URL_BASE ?>assets/img/miniB1.png">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>assets/bt/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>assets/bt-icons/bootstrap-icons.min.css">
    <!-- Alertify CSS -->
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>assets/alertify/alertify.min.css">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>assets/alertify/themes/default.min.css">

    <!-- Estilo del header CSS -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/header.css">
</head>
<body>
    <!-- Navbar Superior - Rojo y Negro -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <button class="btn" id="menu-toggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="navbar-brand mb-0 ms-3">
                B-SPORT
            </span>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: var(--color-red);">
                <span class="navbar-toggler-icon" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 30 30\'%3E%3Cpath stroke=\'rgba(234, 179, 8, 1)\' stroke-linecap=\'round\' stroke-miterlimit=\'10\' stroke-width=\'2\' d=\'M4 7h22M4 15h22M4 23h22\'/%3E%3C/svg%3E');"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> 
                            <?php echo htmlspecialchars($_SESSION['nombre'] , ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                            <li>
                                <a class="dropdown-item" href="<?php echo URL_BASE; ?>views/perfil.php">
                                    <i class="bi bi-person"></i> Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?php echo URL_BASE; ?>views/configuracion.php">
                                    <i class="bi bi-gear"></i> Configuración
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?php echo URL_BASE; ?>modules/auth/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <?php 
            // Incluir el menú de navegación
            $navFile = __DIR__ . '/nav.php';
            if (file_exists($navFile)) {
                include $navFile;
            } else {
                // Menú por defecto
                ?>
                <div class="sidebar-heading">
                    <i class="bi bi-grid-3x3-gap-fill"></i> MENÚ PRINCIPAL
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?php echo URL_BASE; ?>views/dashboard.php" class="list-group-item list-group-item-action">
                        <i class="bi bi-speedometer2"></i> Inicio
                    </a>
                </div>
                <?php
            }
            ?>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <!-- Contenido dinámico aquí -->