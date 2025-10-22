<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['status' => 'sesion']);
    exit;
}

$dni = $_SESSION['dni'];
$id_libro = intval($_POST['id_libro']);
$capitulo = intval($_POST['CAPITULO']);
$puntaje = intval($_POST['PUNTAJE']);

// 🔹 Obtener el total actual (sumando todo lo anterior del mismo capítulo)
$stmt = $conn->prepare("SELECT SUM(PUNTAJE) AS total FROM puntajes WHERE DNI = ? AND id_libro = ? AND CAPITULO = ?");
$stmt->bind_param("sii", $dni, $id_libro, $capitulo);
$stmt->execute();
$resultado = $stmt->get_result();
$fila = $resultado->fetch_assoc();
$puntaje_existente = intval($fila['total'] ?? 0);

// 🔹 Calcular nuevo puntaje
$nuevo_puntaje = $puntaje_existente + $puntaje;
if ($nuevo_puntaje > 100) {
    $nuevo_puntaje = 100;
}

// 🔹 Insertar registro por pregunta (mantienes el historial)
$stmt = $conn->prepare("INSERT INTO puntajes (DNI, id_libro, CAPITULO, PUNTAJE) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siii", $dni, $id_libro, $capitulo, $puntaje);
$stmt->execute();

echo json_encode([
    'status' => 'ok',
    'msg' => '✅ Puntaje actualizado',
    'puntaje_total' => $nuevo_puntaje
]);
?>
