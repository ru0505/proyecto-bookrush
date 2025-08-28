<?php
session_start();
if (!isset($_GET['archivo'])) {
    die("⚠️ Archivo no especificado.");
}

$archivo = basename($_GET['archivo']);
$ruta = __DIR__ . "/libros/" . $archivo;

if (!file_exists($ruta)) {
    die("❌ El archivo no existe: $ruta");
}

// Si el usuario inició sesión, registra la descarga
if (isset($_SESSION['dni'])) {
    include 'conexion.php';
    $dni = $_SESSION['dni'];
    $stmt = $conn->prepare("INSERT INTO descargas (dni, archivo, fecha_descarga) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $dni, $archivo);
    $stmt->execute();
    $stmt->close();
}

// Forzar descarga
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $archivo . '"');
header('Content-Length: ' . filesize($ruta));
readfile($ruta);
exit;
?>
