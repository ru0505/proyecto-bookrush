<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['dni'])) {
    echo json_encode(['status' => 'sesion']);
    exit;
}

$dni = $_SESSION['dni'];
$id_libro = intval($_POST['id_libro']);   // 🔹 Ahora también recibimos el libro
$capitulo = intval($_POST['capitulo']);
$puntaje = intval($_POST['puntaje']);

// Verificar si ya existe puntaje previo en este capítulo y libro
$stmt = $conn->prepare("SELECT puntaje FROM puntajes WHERE dni = ? AND id_libro = ? AND capitulo = ?");
$stmt->bind_param("sii", $dni, $id_libro, $capitulo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $puntaje_existente = intval($row['puntaje']);
    $nuevo_puntaje = $puntaje_existente + $puntaje;

    if ($nuevo_puntaje > 100) {
        $nuevo_puntaje = 100; // No pasar de 100
    }

    $stmt = $conn->prepare("UPDATE puntajes SET puntaje = ? WHERE dni = ? AND id_libro = ? AND capitulo = ?");
    $stmt->bind_param("isii", $nuevo_puntaje, $dni, $id_libro, $capitulo);
} else {
    $nuevo_puntaje = $puntaje;
    $stmt = $conn->prepare("INSERT INTO puntajes (dni, id_libro, capitulo, puntaje) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siii", $dni, $id_libro, $capitulo, $nuevo_puntaje);
}

if ($stmt->execute()) {
    echo json_encode(['status' => 'ok', 'msg' => '✅ Puntaje actualizado', 'puntaje_total' => $nuevo_puntaje]);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Error al guardar']);
}
