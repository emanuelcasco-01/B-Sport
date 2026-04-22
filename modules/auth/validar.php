<?php
session_start();
require_once "../../config/conexion.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    try {
        // Buscamos al usuario sin filtrar por estado todavía
        $sql = "SELECT id_usuario, id_rol, nombre, password_hash, estado FROM usuario 
                WHERE (username = ? OR email = ?) LIMIT 1";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            
            // VERIFICACIÓN DE ESTADO INACTIVO
            if ($user['estado'] != 1) {
                echo json_encode(['status' => 'error', 'msg' => 'Tu cuenta está inactiva. Contacta al administrador.']);
                exit;
            }

            // LOGIN EXITOSO
            $token = bin2hex(random_bytes(32));
            $sqlSesion = "INSERT INTO sesion (id_usuario, token, fecha_inicio, fecha_expiracion, activa) 
                        VALUES (?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 30 MINUTE), 1)";
            $stmtSesion = $conexion->prepare($sqlSesion);
            $stmtSesion->execute([$user['id_usuario'], $token]);

            $_SESSION['id_usuario'] = $user['id_usuario'];
            $_SESSION['id_rol']     = $user['id_rol'];
            $_SESSION['nombre']     = $user['nombre'];
            $_SESSION['token']      = $token;
            
            echo json_encode(['status' => 'success', 'redirect' => '../../views/dashboard.php']);
            exit;

        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Usuario o contraseña incorrectos.']);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'msg' => 'Error de conexión con la base de datos.']);
        exit;
    }
}