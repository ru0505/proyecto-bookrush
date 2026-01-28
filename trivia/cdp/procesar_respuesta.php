<?php
session_start();
include '../../conexion.php';

// Verificar sesión
if (!isset($_SESSION['usuario']['DNI'])) {
    echo json_encode(['status' => 'sesion']);
    exit;
}

// Verificar datos POST
if (!isset($_POST['id_libro'], $_POST['capitulo'], $_POST['id_pregunta'], $_POST['respuesta'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Datos incompletos']);
    exit;
}

// Obtener variables
$dni = $_SESSION['usuario']['DNI'];
$id_libro = intval($_POST['id_libro']);
$capitulo = intval($_POST['capitulo']);
$id_pregunta = intval($_POST['id_pregunta']);
$respuesta_usuario = strtoupper(trim($_POST['respuesta']));

// Obtener respuesta correcta y puntaje
$stmt = $conn->prepare("
    SELECT respuesta_correcta, puntaje 
    FROM preguntas 
    WHERE id_libro = ? AND id_capitulo = ? AND numero_pregunta = ?
");
$stmt->bind_param("iii", $id_libro, $capitulo, $id_pregunta);
$stmt->execute();
$result = $stmt->get_result();
$pregunta = $result->fetch_assoc();

if (!$pregunta) {
    echo json_encode(['status' => 'error', 'msg' => 'Pregunta no encontrada']);
    exit;
}

$respuesta_correcta = strtoupper(trim($pregunta['respuesta_correcta']));
$puntaje_pregunta = intval($pregunta['puntaje']);
$es_correcta = ($respuesta_usuario === $respuesta_correcta);
$puntaje = $es_correcta ? $puntaje_pregunta : 0;

// Insertar o actualizar puntaje en tabla puntajes
$stmt = $conn->prepare("
    INSERT INTO puntajes (DNI, id_libro, CAPITULO, PUNTAJE, id_pregunta)
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE PUNTAJE = VALUES(PUNTAJE)
");
$stmt->bind_param("siiii", $dni, $id_libro, $capitulo, $puntaje, $id_pregunta);

if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'msg' => 'Error al guardar el puntaje']);
    exit;
}

// Respuesta JSON
echo json_encode([
    'status' => 'ok',
    'correcta' => $respuesta_correcta,
    'es_correcta' => $es_correcta,
    'puntaje' => $puntaje
]);
?>
