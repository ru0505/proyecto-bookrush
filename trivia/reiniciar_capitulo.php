<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['dni'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['capitulo'])) {
    header("Location: ../pregunta/preguntas_cdp.php");
    exit;
}

$dni = $_SESSION['dni'];
$capitulo = intval($_GET['capitulo']);

// Borrar todos los registros de ese usuario y capítulo
$stmt = $conn->prepare("DELETE FROM puntajes WHERE dni = ? AND capitulo = ?");
$stmt->bind_param("si", $dni, $capitulo);
$stmt->execute();

// Redirigir de nuevo al selector de capítulos
header("Location: pregunta/preguntas_cdp.php");
exit;
?>
