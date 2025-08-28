<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['dni'])) {
    echo json_encode(['status' => 'sesion']);
    exit;
}

if (!isset($_POST['id_pregunta'], $_POST['respuesta'], $_POST['capitulo'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos']);
    exit;
}

$dni = $_SESSION['dni'];
$id_pregunta = intval($_POST['id_pregunta']);
$respuesta_usuario = strtoupper(trim($_POST['respuesta']));
$capitulo = intval($_POST['capitulo']);

// Obtener respuesta correcta
$stmt = $conn->prepare("SELECT respuesta_correcta FROM preguntas WHERE id_pregunta = ?");
$stmt->bind_param("i", $id_pregunta);
$stmt->execute();
$result = $stmt->get_result();
$correcta = $result->fetch_assoc();

if (!$correcta) {
    echo json_encode(['status' => 'error', 'msg' => 'Pregunta no encontrada']);
    exit;
}

$respuesta_correcta = strtoupper(trim($correcta['respuesta_correcta']));
$es_correcta = ($respuesta_usuario === $respuesta_correcta);
$puntaje = $es_correcta ? 20 : 0;

// Guardar o actualizar en la tabla puntajes
$stmt = $conn->prepare("INSERT INTO puntajes (dni, capitulo, puntaje, id_pregunta)
                        VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE puntaje = VALUES(puntaje)");
$stmt->bind_param("siii", $dni, $capitulo, $puntaje, $id_pregunta);
$stmt->execute();

echo json_encode([
    'status' => 'ok',
    'correcta' => $respuesta_correcta,
    'es_correcta' => $es_correcta,
    'puntaje' => $puntaje
]);
?>
