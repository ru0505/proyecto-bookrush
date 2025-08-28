<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['dni'])) {
    echo "Debes iniciar sesión.";
    exit;
}

$dni = $_SESSION['dni'];
$capitulo = isset($_GET['cap']) ? intval($_GET['cap']) : 1;

// Obtener puntajes del usuario por capítulo
$stmt = $conn->prepare("SELECT SUM(puntaje) as total FROM puntajes WHERE dni = ? AND capitulo = ?");
$stmt->bind_param("si", $dni, $capitulo);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$puntaje_total = intval($row['total']);
if ($puntaje_total > 100) {
    $puntaje_total = 100; // Limitar a 100 puntos como máximo
}

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
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <title>Resultado - Capítulo <?= $capitulo ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background: #fcf2c0;
            color: #1e334e;
            padding: 40px;
        }
        .boton {
            margin: 10px;
            padding: 12px 24px;
            background-color: #f0c64b;
            border: none;
            color: #1e334e;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
        }
        .boton:hover {
            background-color: #d85e39;
            color: white;
        }
        .cerrar {
          position: absolute;
          top: 10px;
          right: 15px;
          text-decoration: none;
          color: #1e334e;
          font-size: 22px;
          opacity: 0.4;
          transition: opacity 0.3s;
      }

      .cerrar:hover {
          opacity: 1;
      }

    </style>
</head>
<body>
    <a href="pregunta/preguntas_cdp.php" class="cerrar">❌</a>

    <h1>Capítulo <?= $capitulo ?></h1>
    <h2><?= $puntaje_total ?>/100</h2>
    <p><?= $mensaje ?></p>

    <?php if ($puntaje_total >= 60): ?>
        <a class="boton" href="pregunta/preguntas_cdp.php?cap=<?= $capitulo + 1 ?>">➡️ Siguiente capítulo</a>
    <?php endif; ?>

    <a class="boton" href="trivia/reiniciar_capitulo.php?cap=<?= $capitulo ?>">🔄 Reintentar capítulo</a>
</body>
</html>
