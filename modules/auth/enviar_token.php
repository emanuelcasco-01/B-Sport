<?php
require_once "../../config/conexion.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/SMTP.php';
require '../../PHPMailer/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $sql = "SELECT id_usuario, nombre FROM usuario WHERE email = ? AND estado = 1 LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $ip = $_SERVER['REMOTE_ADDR']; 

        $sqlInsert = "INSERT INTO recuperacion_password (id_usuario, token, fecha_expiracion, usado, ip_solicitud) 
                    VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 15 MINUTE), 0, ?)";
        
        $stmtInsert = $conexion->prepare($sqlInsert);
        $stmtInsert->execute([$user['id_usuario'], $token, $ip]);

        // 4. Configurar PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor (Ajusta con tu Contraseña de Aplicación de Google)
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'emanuelcasco17@gmail.com'; 
            $mail->Password   = 'xgry ybpx egas wkbd'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Destinatarios
            $mail->setFrom('emanuelcasco17@gmail.com', 'Sistema B-Sport');
            $mail->addAddress($email, $user['nombre']);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Recuperar Contraseña - B-Sport';
            
            // Ajusta esta URL a la de tu proyecto
            $link = "http://localhost/sistema_B_Sport/views/auth/reset.php?token=$token";
            
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd;'>
                    <h2 style='color: #007bff;'>Hola {$user['nombre']},</h2>
                    <p>Has solicitado restablecer tu contraseña en el sistema <strong>B-Sport</strong>.</p>
                    <p>Haz clic en el botón de abajo para continuar. Este enlace expira en 15 minutos.</p>
                    <a href='$link' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Restablecer Contraseña</a>
                    <br><br>
                    <p style='color: #777; font-size: 12px;'>Si no solicitaste este cambio, puedes ignorar este correo.</p>
                </div>
            ";

            $mail->send();
            header("Location: ../../views/auth/recuperar.php?ok=1");
            exit();

        } catch (Exception $e) {
            // Para depuración en desarrollo
            die("Error al enviar el correo: {$mail->ErrorInfo}");
        }
    } else {
        // Correo no encontrado
        header("Location: ../../views/auth/recuperar.php?error=1");
        exit();
    }
}