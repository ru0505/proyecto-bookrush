<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$dni = $_SESSION['dni'];
$id_libro = isset($_GET['id_libro']) ? intval($_GET['id_libro']) : 1;
$capitulo = isset($_GET['id_capitulo']) ? intval($_GET['id_capitulo']) : 1;

// Obtener puntaje total del usuario para este libro y capítulo
$stmt = $conn->prepare("
    SELECT SUM(PUNTAJE) as total 
    FROM puntajes 
    WHERE DNI = ? AND id_libro = ? AND CAPITULO = ?
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
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/total.css">
</head>
<body>
    <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="imagenes/LOGO_BOOK_RUSH.png" alt="Logo" style="height: 50px;">
            <h1>Book Rush</h1>
        </div>
        
        <!-- Botón Volver al Menú Principal en la parte superior derecha -->
        <a href="index.php" class="btn-volver-top">
          ← Menú Principal
        </a>
    </div>

    <div class="container">
        <div class="card">
            <h1 class="page-title">Capítulo <?= $capitulo ?></h1>
            <h2 class="score"><?= $puntaje_total ?>/100</h2>
            <p class="message"><?= htmlspecialchars($mensaje) ?></p>
            
            <div class="actions">
                <?php if ($puntaje_total >= 60): ?>
                    <a class="btn" href="pregunta/preguntas_frank.php?capitulo=<?= $capitulo + 1 ?>">
                      Siguiente capítulo
                    </a>
                <?php endif; ?>

                <a class="btn" href="reiniciar_capitulo.php?id_capitulo=<?= $capitulo ?>&id_libro=<?= $id_libro ?>">
                  Reintentar capítulo
                </a>
                
                <a class="btn" href="mapa_capitulos/mapa_capitulos.php?id_libro=<?= $id_libro ?>">
                  Volver a capítulos
                </a>
            </div>
        </div>
    </div>
</body>
</html>
