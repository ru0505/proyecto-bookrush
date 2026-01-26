<?php
$host = "localhost";
$user = "root";
$pass = "root";
$db = "bookrush"; // Nombre correcto de la base de datos

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Configurar el charset a UTF-8
$conn->set_charset("utf8mb4");
?>
