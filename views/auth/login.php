<?php
session_start();

// Si ya tiene sesión, redirigir al dashboard
if (isset($_SESSION['id_usuario']) && isset($_SESSION['token'])) {
    header("Location: ../../views/dashboard.php");
    exit();
}

if (!defined('URL_BASE')) {
    define('URL_BASE', '../../');
}

// Obtener mensajes de la URL
$error = isset($_GET['error']) ? true : false;
$expirado = isset($_GET['expirado']) ? true : false;
$logout = isset($_GET['logout']) ? true : false;
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Gestión de Producción B-Sport">
    <meta name="theme-color" content="#dc2626">
    <title>B-Sport</title>
    <link rel="icon" type="image/png" href="<?= URL_BASE ?>assets/img/miniB1.png">
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= URL_BASE ?>assets/img/favicon.ico">
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/bt/bootstrap.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/bt-icons/bootstrap-icons.min.css">
    
    <!-- Alertify CSS -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/alertify/alertify.min.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/alertify/themes/default.min.css">
    <!-- Estilo del login CSS -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/login.css">
</head>
<body>
    <!-- Contenedor principal -->
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-card">
                <!-- Logo y título -->
                <div class="login-logo-container">
                    <img src="<?= URL_BASE ?>assets/img/IconoB.jfif" 
                        alt="B-Sport Logo" 
                        class="login-logo"
                        onerror="this.src='<?= URL_BASE ?>assets/img/default-logo.png'">
                    <h1 class="login-title">B-SPORT</h1>
                    
                </div>

                <!-- Formulario -->
                <form class="login-form" method="POST" action="<?= URL_BASE ?>modules/auth/validar.php" id="loginForm">
                    <!-- Usuario -->
                    <div class="form-group">
                        <label>
                            <i class="bi bi-person-badge"></i> Usuario o Correo
                        </label>
                        <div class="input-group-custom">
                            <i class="bi bi-person input-icon"></i>
                            <input type="text" 
                                name="login" 
                                class="form-control" 
                                placeholder="Ingresa tu usuario o email"
                                required 
                                autocomplete="username"
                                autofocus>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group">
                        <label>
                            <i class="bi bi-shield-lock"></i> Contraseña
                        </label>
                        <div class="input-group-custom">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" 
                                name="password" 
                                class="form-control" 
                                id="password"
                                placeholder="••••••••"
                                required 
                                autocomplete="current-password">
                        </div>
                    </div>

                    <!-- Mostrar contraseña -->
                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="showPass" onclick="togglePassword()">
                        <label for="showPass">
                            <i class="bi bi-eye"></i> Mostrar contraseña
                        </label>
                    </div>

                    <!-- Botón de ingreso -->
                    <button type="submit" class="btn-login" id="submitBtn">
                        <i class="bi bi-box-arrow-in-right"></i> Ingresar
                    </button>

                    <!-- Link de recuperación -->
                    <div class="forgot-link">
                        <a href="recuperar.php">
                            <i class="bi bi-question-circle"></i> ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </form>

                <!-- Footer -->
                <div class="login-footer">
                    <i class="bi bi-c-circle"></i> 2026 B-Sport 
                    <i class="bi bi-dot"></i> 
                    <span class="version">v0.0.2</span>
                    <br>
                    <span style="color: #555;">
                        <i class="bi bi-shield-check"></i> Acceso Seguro
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
            alertify.set('notifier', 'delay', 4);
        }

        // ============================================
        // TOGGLE PASSWORD
        // ============================================
        function togglePassword() {
            const passInput = document.getElementById("password");
            const checkbox = document.getElementById("showPass");
            
            if (passInput && checkbox) {
                passInput.type = checkbox.checked ? "text" : "password";
            }
        }

        // ============================================
        // NOTIFICACIONES SEGÚN PARÁMETROS URL
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($error): ?>
            setTimeout(() => {
                alertify.error('<i class="bi bi-exclamation-triangle"></i> Credenciales incorrectas o usuario inactivo.');
            }, 300);
            <?php endif; ?>
            
            <?php if ($expirado): ?>
            setTimeout(() => {
                alertify.warning('<i class="bi bi-clock-history"></i> Su sesión ha caducado. Por favor, ingrese nuevamente.');
            }, 300);
            <?php endif; ?>
            
            <?php if ($logout): ?>
            setTimeout(() => {
                alertify.success('<i class="bi bi-check-circle"></i> Sesión cerrada correctamente.');
            }, 300);
            <?php endif; ?>
            
            <?php if (!empty($message)): ?>
            setTimeout(() => {
                alertify.message('<i class="bi bi-info-circle"></i> <?= $message ?>');
            }, 300);
            <?php endif; ?>
        });

        // ============================================
        // PREVENIR ENVÍO MÚLTIPLE DEL FORMULARIO
        // ============================================
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Detiene la recarga de página lenta

                const btnOriginalHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="loading-spinner"></span> Verificando...';
                submitBtn.disabled = true;

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alertify.success('¡Bienvenido! Redirigiendo...');
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    } else {
                        alertify.error('<i class="bi bi-exclamation-triangle"></i> ' + data.msg);
                        submitBtn.innerHTML = btnOriginalHtml;
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    alertify.error('Error en el servidor');
                    submitBtn.innerHTML = btnOriginalHtml;
                    submitBtn.disabled = false;
                });
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
        // MENSAJE DE BIENVENIDA EN CONSOLA
        // ============================================
        console.log('%c B-SPORT  Sistema de Producción', 
                    'font-size: 16px; font-weight: bold; color: #eab308; background: #0a0a0a; padding: 6px 12px; border-left: 4px solid #dc2626; border-radius: 4px;');
        console.log('%cAcceso seguro • Versión 0.0.2', 
                    'font-size: 12px; color: #dc2626; font-weight: bold;');
    </script>
</body>
</html>