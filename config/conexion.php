<?php
define('URL_BASE', 'http://localhost/sistema_B_Sport/');
$host = "localhost";
$db   = "b_sport";
$user = "root";
$pass = "";
$port = "3306";
$charset = "utf8mb4";
// Definimos el DSN 
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
// Opciones de configuración de PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Reporta errores como excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve los datos como arreglos asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Desactiva la emulación para mayor seguridad real
];

try {
    $conexion = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Error crítico en la base de datos. Intente más tarde.");
}
?>