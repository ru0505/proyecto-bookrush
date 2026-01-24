<?php
$host = "localhost";
$user = "root";
$pass = "root";
$db = "bookrush"; // Nombre correcto de la base de datos

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
