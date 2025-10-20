<?php
$host = "localhost";
$user = "root";
$pass = "root"; // Contraseña de MAMP
$db = "bookrush"; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>
