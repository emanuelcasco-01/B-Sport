<?php
if (!function_exists('tienePermiso')) {
    require_once __DIR__ . "/../../helpers/seguridad.php";
}

if (!isset($menu)) {
    require_once __DIR__ . "/../../helpers/menu.php";
}


// Definir URL_BASE si no está definida
if (!defined('URL_BASE')) {
    define('URL_BASE', '../../');
}
?>

<!-- Contenido del Sidebar -->
<div class="sidebar-menu-wrapper">
    <div class="sidebar-heading text-center fw-bold">
        B-SPORT
        <small class="d-block text-white-50 mt-1" style="font-size: 0.7rem; letter-spacing: 2px;">
            SISTEMA DE FACTURACIÓN Y PRODUCCIÓN
        </small>
    </div>
    
    <div class="sidebar-menu py-2">
        <!-- Dashboard -->
        <a href="<?= URL_BASE ?>views/dashboard.php" class="sidebar-item" data-link="dashboard">
            <i class="bi bi-speedometer2 sidebar-icon"></i> 
            <span class="text-lg-start">Inicio</span>
        </a>

        <?php foreach ($menu as $modulo => $data): ?>
            <?php if (tienePermiso($conexion, $data['permiso'])): ?>
                <?php 
                    // Limpiar nombre del módulo para ID
                    $id_modulo = 'menu-' . md5($modulo);
                ?>
                
                <!-- Encabezado del módulo -->
                <div class="module-header">
                    <i class="bi bi-diamond-fill"></i> 
                    <?= htmlspecialchars($modulo) ?>
                </div>

                <!-- Item principal del módulo con colapso -->
                <a href="#<?= $id_modulo ?>" 
                data-bs-toggle="collapse" 
                class="sidebar-item has-submenu"
                data-module="<?= $id_modulo ?>"
                aria-expanded="false">
                    <span class="sidebar-item-content">
                        <i class="bi <?= $data['icono'] ?> sidebar-icon"></i> 
                        <span class="sidebar-text"><?= htmlspecialchars($modulo) ?></span>
                    </span>
                    <i class="bi bi-chevron-down collapse-icon"></i>
                </a>

                <!-- Submenú colapsable -->
                <div class="collapse" id="<?= $id_modulo ?>">
                    <div class="submenu-container">
                        <?php foreach ($data['items'] as $item): ?>
                            <?php if (isset($item['tipo']) && $item['tipo'] == "titulo"): ?>
                                <div class="submenu-title">
                                    <i class="bi bi-folder-fill"></i>
                                    <?= strtoupper(htmlspecialchars($item['nombre'])) ?>
                                </div>
                            <?php else: ?>
                                <a href="<?= URL_BASE . $item['ruta'] ?>" 
                                class="submenu-item"
                                data-link="<?= basename($item['ruta']) ?>">
                                    <span class="submenu-dot"></span>
                                    <?= htmlspecialchars($item['nombre']) ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Separador -->
        <div class="sidebar-divider"></div>
        
    </div>
</div>
<hr>
<hr>


<!-- Estilos específicos para el menú -->
<link rel="stylesheet" href="<?= URL_BASE ?>assets/css/nav.css">

<!-- JavaScript para funcionalidades del menú -->
<script src="<?= URL_BASE ?>assets/js/nav.js"></script>