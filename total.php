<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$dni = $_SESSION['usuario']['DNI'];
$id_libro = isset($_GET['libro']) ? intval($_GET['libro']) : 1;
$capitulo = isset($_GET['capitulo']) ? intval($_GET['capitulo']) : 1;

// Obtener puntaje total del usuario para este libro y capítulo
$stmt = $conn->prepare("
    SELECT SUM(puntaje) as total 
    FROM puntajes 
    WHERE dni = ? AND id_libro = ? AND capitulo = ?
");
$stmt->bind_param("sii", $dni, $id_libro, $capitulo);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$puntaje_total = intval($row['total']);
if ($puntaje_total > 100) $puntaje_total = 100;

// Mensaje según rendimiento
if ($puntaje_total >= 80) {
    $mensaje = "¡Excelente trabajo! Sigue así.";
} elseif ($puntaje_total >= 60) {
    $mensaje = "Buen esfuerzo. Puedes mejorar aún más.";
} else {
    $mensaje = "Necesitas repasar un poco más. 🌱";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Resultado - Capítulo <?= $capitulo ?></title>
<style>
body { font-family: Arial, sans-serif; text-align: center; background: #fcf2c0; color: #1e334e; padding: 40px; }
.boton { margin: 10px; padding: 12px 24px; background-color: #f0c64b; border: none; color: #1e334e; font-weight: bold; border-radius: 10px; cursor: pointer; text-decoration: none; }
.boton:hover { background-color: #d85e39; color: white; }
</style>
</head>
<body>
<h1>Capítulo <?= $capitulo ?></h1>
<h2><?= $puntaje_total ?>/100</h2>
<p><?= $mensaje ?></p>

<?php if ($puntaje_total >= 60): ?>
    <a class="boton" href="pregunta/preguntas_frank.php?capitulo=<?= $capitulo + 1 ?>">➡️ Siguiente capítulo</a>
<?php endif; ?>

<a class="boton" href="trivia/reiniciar_capitulo.php?capitulo=<?= $capitulo ?>&libro=<?= $id_libro ?>">🔄 Reintentar capítulo</a>
<a class="boton" href="pregunta/preguntas_frank.php">🏠 Volver al menú de capítulos</a>
</body>
</html>
