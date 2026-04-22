<?php
session_start();
require_once __DIR__ . "/../../config/conexion.php";

if (!defined('URL_BASE')) {
    define('URL_BASE', '../../');
}

// Si ya tiene sesión, lo redirigimos al dashboard
if (isset($_SESSION['id_usuario'])) {
    header("Location: " . URL_BASE . "views/dashboard.php");
    exit();
}

$ok = isset($_GET['ok']) ? true : false;
$error = isset($_GET['error']) ? true : false;
$token_expirado = isset($_GET['token_expirado']) ? true : false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Recuperar acceso a B-Sport - Sistema de Gestión de Producción">
    <meta name="theme-color" content="#dc2626">
    <title>B-Sport | Recuperar Cuenta</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= URL_BASE ?>assets/img/favicon.ico">
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/bt/bootstrap.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/bt-icons/bootstrap-icons.min.css">
    
    <!-- Alertify CSS -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/alertify/alertify.min.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/alertify/themes/default.min.css">
    <!-- Estilo de recuperar CSS -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/recuperar.css">
</head>
<body>
    <!-- Contenedor principal -->
    <div class="recovery-wrapper">
        <div class="recovery-container">
            <div class="recovery-card">
                <!-- Icono y título -->
                <div class="recovery-icon-container">
                    <div class="recovery-icon">
                        <i class="bi bi-lock-fill"></i>
                    </div>
                    <h1 class="recovery-title">RECUPERAR CUENTA</h1>
                    <div class="recovery-subtitle">
                        <i class="bi bi-envelope-paper"></i> 
                        Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
                    </div>
                </div>

                <!-- Formulario -->
                <form class="recovery-form" method="POST" action="<?= URL_BASE ?>modules/auth/enviar_token.php" id="recoveryForm">
                    <div class="form-group">
                        <label>
                            <i class="bi bi-envelope-at"></i> Correo Electrónico
                        </label>
                        <div class="input-group-custom">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" 
                                name="email" 
                                class="form-control" 
                                placeholder="ejemplo@b-sport.com"
                                required 
                                autocomplete="email"
                                autofocus>
                        </div>
                    </div>

                    <!-- Botón de envío -->
                    <button type="submit" class="btn-recovery" id="submitBtn">
                        <i class="bi bi-send"></i> Enviar enlace de recuperación
                    </button>
                </form>

                <!-- Nota de ayuda -->
                <div class="help-note">
                    <p>
                        <i class="bi bi-info-circle-fill"></i>
                        <span>Revisa tu bandeja de entrada y la carpeta de spam. El enlace expirará en 15 minutos por seguridad.</span>
                    </p>
                </div>

                <!-- Link de regreso -->
                <div class="back-link">
                    <a href="login.php">
                        <i class="bi bi-arrow-left"></i> Volver al inicio de sesión
                    </a>
                </div>

                <!-- Info de seguridad -->
                <div class="security-info">
                    <span>
                        <i class="bi bi-shield-check"></i> Conexión segura
                    </span>
                    <span>
                        <i class="bi bi-dot"></i>
                    </span>
                    <span>
                        <i class="bi bi-clock-history"></i> 
                        <span class="highlight">B-Sport v0.0.2</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- SCRIPTS                                      -->
    <!-- ============================================ -->
    <script src="<?= URL_BASE ?>assets/bt/bootstrap.bundle.min.js"></script>
    <script src="<?= URL_BASE ?>assets/alertify/alertify.min.js"></script>
    
    <script>
        // ============================================
        // CONFIGURACIÓN DE ALERTIFY
        // ============================================
        if (typeof alertify !== 'undefined') {
            alertify.set('notifier', 'position', 'top-right');
            alertify.set('notifier', 'delay', 5);
        }

        // ============================================
        // NOTIFICACIONES SEGÚN PARÁMETROS URL
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($ok): ?>
            setTimeout(() => {
                alertify.success('<i class="bi bi-check-circle"></i> ¡Enlace enviado con éxito! Revisa tu correo electrónico.');
            }, 300);
            <?php endif; ?>
            
            <?php if ($error): ?>
            setTimeout(() => {
                alertify.error('<i class="bi bi-exclamation-triangle"></i> El correo electrónico no está registrado en el sistema.');
            }, 300);
            <?php endif; ?>
            
            <?php if ($token_expirado): ?>
            setTimeout(() => {
                alertify.warning('<i class="bi bi-clock-history"></i> El enlace de recuperación ha expirado. Solicita uno nuevo.');
            }, 300);
            <?php endif; ?>
        });

        // ============================================
        // PREVENIR ENVÍO MÚLTIPLE DEL FORMULARIO
        // ============================================
        const form = document.getElementById('recoveryForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                const emailInput = form.querySelector('input[name="email"]');
                
                // Validación adicional de email
                if (emailInput && !emailInput.value.includes('@')) {
                    e.preventDefault();
                    alertify.error('<i class="bi bi-exclamation-triangle"></i> Por favor, ingresa un correo electrónico válido.');
                    return;
                }
                
                const btnText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Enviando...';
                submitBtn.disabled = true;
                
                // Restaurar después de 3 segundos si no hay redirección
                setTimeout(() => {
                    if (submitBtn.disabled) {
                        submitBtn.innerHTML = btnText;
                        submitBtn.disabled = false;
                    }
                }, 3000);
            });
        }

        // ============================================
        // TECLA ENTER PARA ENVIAR
        // ============================================
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !submitBtn.disabled) {
                const activeElement = document.activeElement;
                if (activeElement && activeElement.tagName !== 'BUTTON') {
                    form.dispatchEvent(new Event('submit', { cancelable: true }));
                }
            }
        });

        // ============================================
        // MENSAJE EN CONSOLA
        // ============================================
        console.log('%c B-SPORT  Recuperación de Cuenta', 
                    'font-size: 16px; font-weight: bold; color: #eab308; background: #0a0a0a; padding: 6px 12px; border-left: 4px solid #dc2626; border-radius: 4px;');
        console.log('%cRecuperación segura • Versión 0.0.2', 
                    'font-size: 12px; color: #dc2626; font-weight: bold;');
    </script>
</body>
</html>