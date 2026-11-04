<?php
// config/conexion.php
// Conexión a MySQL con PDO. Configuración por defecto de XAMPP.

$host    = '127.0.0.1';
$db      = 'tecnostock';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$opciones = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // errores como excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,         // filas como arreglos asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,                    // consultas preparadas reales
];

try {
    $pdo = new PDO($dsn, $user, $pass, $opciones);
} catch (PDOException $e) {
    error_log('Error de conexión: ' . $e->getMessage());
    die('No se pudo conectar con la base de datos.');
}