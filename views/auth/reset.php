<?php
require_once "../../config/conexion.php";

if (!defined('URL_BASE')) {
    define('URL_BASE', '../../');
}

// Verificar que el token venga en la URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: login.php?error_token=1");
    exit();
}

$token = $_GET['token'];
$token_valido = false;

try {
    $sql = "SELECT id_usuario FROM recuperacion_password 
            WHERE token = ? AND usado = 0 AND fecha_expiracion > NOW() 
            LIMIT 1";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$token]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$registro) {
        // Token inválido o expirado
        header("Location: recuperar.php?token_expirado=1");
        exit();
    }
    
    $token_valido = true;

} catch (PDOException $e) {
    die("Error de conexión al validar el acceso.");
}

// Obtener mensajes de error de la URL (si los hay)
$error_match = isset($_GET['error_match']) ? true : false;
$error_length = isset($_GET['error_length']) ? true : false;
$success = isset($_GET['success']) ? true : false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Restablecer contraseña - B-Sport Sistema de Producción">
    <meta name="theme-color" content="#dc2626">
    <title>B-Sport | Nueva Contraseña</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= URL_BASE ?>assets/img/favicon.ico">
    
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/bt/bootstrap.min.css">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/bt-icons/bootstrap-icons.min.css">
    
    <!-- Alertify CSS -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/alertify/alertify.min.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/alertify/themes/default.min.css">
    
    <!-- Estilo de reset CSS -->
    <link rel="stylesheet" href="<?= URL_BASE ?>assets/css/reset.css">
</head>
<body>
    <!-- Contenedor principal -->
    <div class="reset-wrapper">
        <div class="reset-container">
            <div class="reset-card">
                <!-- Icono y título -->
                <div class="reset-icon-container">
                    <div class="reset-icon">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h1 class="reset-title">NUEVA CONTRASEÑA</h1>
                    <div class="reset-subtitle">
                        <i class="bi bi-key"></i> 
                        Por seguridad, elige una contraseña fuerte que no hayas usado antes.
                    </div>
                </div>

                <!-- Formulario -->
                <form class="reset-form" method="POST" action="<?= URL_BASE ?>modules/auth/reset_password.php" id="resetForm">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <!-- Nueva Contraseña -->
                    <div class="form-group">
                        <label>
                            <i class="bi bi-lock"></i> Nueva Contraseña
                        </label>
                        <div class="input-group-custom">
                            <i class="bi bi-key input-icon"></i>
                            <input type="password" 
                                name="password" 
                                id="pass1" 
                                class="form-control" 
                                placeholder="Mínimo 8 caracteres"
                                required 
                                autocomplete="new-password"
                                autofocus>
                            <button type="button" class="toggle-password" onclick="togglePassword('pass1')" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <!-- Indicador de fortaleza -->
                        <div class="password-strength" id="strengthMeter">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <div class="strength-text" id="strengthText">Ingresa una contraseña</div>
                        </div>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="form-group">
                        <label>
                            <i class="bi bi-check-circle"></i> Confirmar Contraseña
                        </label>
                        <div class="input-group-custom">
                            <i class="bi bi-shield-check input-icon"></i>
                            <input type="password" 
                                name="confirm_password" 
                                id="pass2" 
                                class="form-control" 
                                placeholder="Repite la contraseña"
                                required 
                                autocomplete="new-password">
                            <button type="button" class="toggle-password" onclick="togglePassword('pass2')" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="strength-text" id="matchMessage" style="margin-top: 5px;"></div>
                    </div>

                    <!-- Requisitos de contraseña -->
                    <div class="password-requirements">
                        <p><i class="bi bi-shield-shaded me-1"></i> Requisitos de seguridad:</p>
                        <ul class="requirement-list">
                            <li id="req-length"><i class="bi bi-x-circle"></i> Mínimo 8 caracteres</li>
                            <li id="req-uppercase"><i class="bi bi-x-circle"></i> Al menos una mayúscula</li>
                            <li id="req-lowercase"><i class="bi bi-x-circle"></i> Al menos una minúscula</li>
                            <li id="req-number"><i class="bi bi-x-circle"></i> Al menos un número</li>
                            <li id="req-special"><i class="bi bi-x-circle"></i> Al menos un carácter especial</li>
                        </ul>
                    </div>

                    <!-- Botón de envío -->
                    <button type="submit" class="btn-reset" id="submitBtn">
                        <i class="bi bi-check2-circle"></i> Actualizar Contraseña
                    </button>
                </form>

                <!-- Footer de seguridad -->
                <div class="security-footer">
                    <i class="bi bi-shield-fill-check"></i> Conexión segura 
                    <i class="bi bi-dot"></i> 
                    <span class="highlight">B-Sport v0.0.2</span>
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
        function togglePassword(id) {
            const input = document.getElementById(id);
            const btn = input.parentElement.querySelector('.toggle-password i');
            
            if (input.type === 'password') {
                input.type = 'text';
                btn.classList.remove('bi-eye');
                btn.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                btn.classList.remove('bi-eye-slash');
                btn.classList.add('bi-eye');
            }
        }

        // ============================================
        // VALIDACIÓN DE FORTALEZA DE CONTRASEÑA
        // ============================================
        const passInput = document.getElementById('pass1');
        const pass2Input = document.getElementById('pass2');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const matchMessage = document.getElementById('matchMessage');
        const submitBtn = document.getElementById('submitBtn');

        // Requisitos
        const reqLength = document.getElementById('req-length');
        const reqUppercase = document.getElementById('req-uppercase');
        const reqLowercase = document.getElementById('req-lowercase');
        const reqNumber = document.getElementById('req-number');
        const reqSpecial = document.getElementById('req-special');

        function validatePassword(password) {
            const checks = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
            };

            // Actualizar UI de requisitos
            updateRequirement(reqLength, checks.length);
            updateRequirement(reqUppercase, checks.uppercase);
            updateRequirement(reqLowercase, checks.lowercase);
            updateRequirement(reqNumber, checks.number);
            updateRequirement(reqSpecial, checks.special);

            // Calcular fortaleza
            const strength = Object.values(checks).filter(v => v).length;
            const strengthPercent = (strength / 5) * 100;
            
            strengthFill.style.width = strengthPercent + '%';
            
            if (strength <= 2) {
                strengthFill.style.background = 'var(--color-red)';
                strengthText.textContent = 'Contraseña débil';
                strengthText.style.color = 'var(--color-red)';
            } else if (strength <= 4) {
                strengthFill.style.background = 'var(--color-yellow)';
                strengthText.textContent = 'Contraseña media';
                strengthText.style.color = 'var(--color-yellow)';
            } else {
                strengthFill.style.background = '#22c55e';
                strengthText.textContent = 'Contraseña fuerte';
                strengthText.style.color = '#22c55e';
            }

            return checks;
        }

        function updateRequirement(element, valid) {
            if (valid) {
                element.classList.add('valid');
                element.classList.remove('invalid');
                element.innerHTML = '<i class="bi bi-check-circle-fill"></i> ' + element.textContent.split(' ').slice(1).join(' ');
            } else {
                element.classList.add('invalid');
                element.classList.remove('valid');
                element.innerHTML = '<i class="bi bi-x-circle"></i> ' + element.textContent.split(' ').slice(1).join(' ');
            }
        }

        function checkPasswordsMatch() {
            const pass1 = passInput.value;
            const pass2 = pass2Input.value;
            
            if (pass2.length === 0) {
                matchMessage.textContent = '';
                return true;
            }
            
            if (pass1 === pass2) {
                matchMessage.textContent = '✓ Las contraseñas coinciden';
                matchMessage.style.color = '#22c55e';
                return true;
            } else {
                matchMessage.textContent = '✗ Las contraseñas no coinciden';
                matchMessage.style.color = 'var(--color-red)';
                return false;
            }
        }

        function validateForm() {
            const password = passInput.value;
            const checks = validatePassword(password);
            const allValid = Object.values(checks).every(v => v);
            const passwordsMatch = checkPasswordsMatch();
            
            submitBtn.disabled = !(allValid && passwordsMatch);
            
            return allValid && passwordsMatch;
        }

        // Event listeners
        passInput.addEventListener('input', validateForm);
        pass2Input.addEventListener('input', validateForm);

        // ============================================
        // VALIDACIÓN DEL FORMULARIO
        // ============================================
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                
                const password = passInput.value;
                const checks = validatePassword(password);
                
                if (!Object.values(checks).every(v => v)) {
                    alertify.error('<i class="bi bi-exclamation-triangle"></i> La contraseña no cumple con los requisitos de seguridad.');
                } else if (!checkPasswordsMatch()) {
                    alertify.error('<i class="bi bi-exclamation-triangle"></i> Las contraseñas no coinciden.');
                }
                
                return false;
            }
            
            // Mostrar loading
            const btnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Actualizando...';
            submitBtn.disabled = true;
            
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = btnText;
                    submitBtn.disabled = false;
                }
            }, 3000);
        });

        // ============================================
        // NOTIFICACIONES SEGÚN PARÁMETROS URL
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($error_match): ?>
            setTimeout(() => {
                alertify.error('<i class="bi bi-exclamation-triangle"></i> Las contraseñas no coinciden.');
            }, 300);
            <?php endif; ?>
            
            <?php if ($error_length): ?>
            setTimeout(() => {
                alertify.error('<i class="bi bi-exclamation-triangle"></i> La contraseña debe tener al menos 8 caracteres.');
            }, 300);
            <?php endif; ?>
            
            <?php if ($success): ?>
            setTimeout(() => {
                alertify.success('<i class="bi bi-check-circle"></i> ¡Contraseña actualizada correctamente! Redirigiendo...');
                setTimeout(() => {
                    window.location.href = 'login.php?reset_success=1';
                }, 2000);
            }, 300);
            <?php endif; ?>
        });

        // ============================================
        // TECLA ENTER PARA ENVIAR
        // ============================================
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !submitBtn.disabled) {
                const activeElement = document.activeElement;
                if (activeElement && activeElement.tagName !== 'BUTTON') {
                    document.getElementById('resetForm').dispatchEvent(new Event('submit', { cancelable: true }));
                }
            }
        });

        // ============================================
        // MENSAJE EN CONSOLA
        // ============================================
        console.log('%c B-SPORT  Restablecer Contraseña', 
                    'font-size: 16px; font-weight: bold; color: #eab308; background: #0a0a0a; padding: 6px 12px; border-left: 4px solid #dc2626; border-radius: 4px;');
        console.log('%cValidación segura • Versión 0.0.2', 
                    'font-size: 12px; color: #dc2626; font-weight: bold;');
    </script>
</body>
</html>