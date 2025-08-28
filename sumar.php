<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['dni'])) {
    echo json_encode(['status' => 'sesion']);
    exit;
}

$dni = $_SESSION['dni'];
$capitulo = intval($_POST['capitulo']);
$puntaje = intval($_POST['puntaje']);

// Verificar si ya existe puntaje previo
$stmt = $conn->prepare("SELECT puntaje FROM puntajes WHERE dni = ? AND capitulo = ?");
$stmt->bind_param("si", $dni, $capitulo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $puntaje_existente = intval($row['puntaje']);
    $nuevo_puntaje = $puntaje_existente + $puntaje;

    if ($nuevo_puntaje > 100) {
        $nuevo_puntaje = 100; // No pasar de 100
    }

    $stmt = $conn->prepare("UPDATE puntajes SET puntaje = ? WHERE dni = ? AND capitulo = ?");
    $stmt->bind_param("isi", $nuevo_puntaje, $dni, $capitulo);
} else {
    $nuevo_puntaje = $puntaje;
    $stmt = $conn->prepare("INSERT INTO puntajes (dni, capitulo, puntaje) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $dni, $capitulo, $nuevo_puntaje);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'msg' => '✅ Puntaje actualizado', 'puntaje_total' => $nuevo_puntaje]);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Error al guardar']);
}
