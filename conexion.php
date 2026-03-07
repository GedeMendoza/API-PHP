<?php
// =====================================================
// ARCHIVO: conexion.php
// DESCRIPCIÓN: Conexión a MariaDB en Railway
// INSTRUCCIÓN: Cambia los valores por los de tu
//              proyecto en Railway Dashboard
// =====================================================

$host     = getenv('DB_HOST')     ?: 'localhost';
$puerto   = getenv('DB_PORT')     ?: '3306';
$usuario  = getenv('DB_USER')     ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$base     = getenv('DB_NAME')     ?: 'mibd';

$conn = new mysqli($host, $usuario, $password, $base, (int)$puerto);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Fallo de conexión: " . $conn->connect_error]);
    exit();
}

$conn->set_charset("utf8mb4");
