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
    $mensaje = "Necesitas repasar un poco más.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Resultado - Capítulo <?= $capitulo ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
<style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{
        font-family: 'Fredoka', sans-serif;
        background: linear-gradient(135deg, #353346ff 0%, #2a2a3e 50%, #1e1e2e 100%) fixed;
        color:#fff;
        min-height:100vh;
        padding:130px 20px 40px;
    }
    .top-bar{
        width:100%;background:linear-gradient(135deg,#1e3a8a 0%,#3b82f6 50%,#60a5fa 100%);
        padding:25px 30px;display:flex;align-items:center;justify-content:space-between;position:fixed;top:0;left:0;z-index:1100;
        box-shadow:0 8px 32px rgba(30,58,138,0.3);backdrop-filter:blur(10px);border-bottom:1px solid rgba(255,255,255,0.08);min-height:100px;
    }
    .top-bar h1{font-size:35px;color:#ff8c42;text-shadow:0 2px 10px rgba(255,140,66,0.45);margin:0}

    .container{max-width:1100px;margin:0 auto}
    .card{background:rgba(255,255,255,0.95);color:#213547;padding:36px;border-radius:15px;box-shadow:0 8px 32px rgba(0,0,0,0.3);text-align:center}
    h1.page-title{font-size:2.2em;margin-bottom:12px;color:#213547}
    h2.score{font-size:3em;margin:8px 0;color:#213547}
    p.message{font-size:1.1em;color:#213547;margin-bottom:18px}

    .actions{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:12px}
    .btn{background:#ff8c42;color:#fff;padding:12px 22px;border-radius:8px;text-decoration:none;font-weight:700;display:inline-block}
    .btn.secondary{background:#6c757d}
    .btn:hover{background:#e67a35;transform:translateY(-2px);box-shadow:0 8px 20px rgba(230,122,53,0.25)}
    .btn.secondary:hover{background:#5a6268}

    @media(max-width:768px){ .card{padding:20px} .top-bar{padding:18px} body{padding:120px 12px 30px} }
</style>
</head>
<body>
    <div class="top-bar">
        <div style="display:flex;align-items:center;gap:12px;">
            <img src="imagenes/LOGO_BOOK_RUSH.png" alt="Logo" style="height:44px">
            <h1>Book Rush</h1>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h1 class="page-title">Capítulo <?= $capitulo ?></h1>
            <h2 class="score"><?= $puntaje_total ?>/100</h2>
            <p class="message"><?= htmlspecialchars($mensaje) ?></p>

            <div class="actions">
                <?php if ($puntaje_total >= 60): ?>
                    <a class="btn" href="pregunta/preguntas_frank.php?capitulo=<?= $capitulo + 1 ?>">Siguiente capítulo →</a>
                <?php endif; ?>

                <a class="btn" href="reiniciar_capitulo.php?id_capitulo=<?= $capitulo ?>&id_libro=<?= $id_libro ?>">Reintentar capítulo</a>
                <a class="btn secondary" href="mapa_capitulos/mapa_capitulos.php?id_libro=<?= $id_libro ?>">Volver al menú de capítulos</a>
            </div>
        </div>
    </div>
</body>
</html>
