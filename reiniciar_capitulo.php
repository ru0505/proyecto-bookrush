<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$dni = $_SESSION['usuario'];

// Validar parámetros
if (!isset($_GET['libro']) || !isset($_GET['capitulo'])) {
    header("Location: index.php");
    exit;
}

$id_libro = intval($_GET['libro']);
$id_capitulo = intval($_GET['capitulo']);

// Borrar todos los registros de ese usuario, libro y capítulo
$stmt = $conn->prepare("DELETE FROM puntajes WHERE dni = ? AND id_libro = ? AND capitulo = ?");
$stmt->bind_param("sii", $dni, $id_libro, $id_capitulo);
$stmt->execute();

// Redirigir de nuevo al selector de capítulos del libro
header("Location: trivia/trivia.php?libro=$id_libro&capitulo=$id_capitulo");
exit;
?>
