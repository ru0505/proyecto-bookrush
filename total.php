<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}


$id_usuario = $_SESSION['id_usuario'];
$id_libro = isset($_GET['id_libro']) ? intval($_GET['id_libro']) : 1;
$capitulo = isset($_GET['id_capitulo']) ? intval($_GET['id_capitulo']) : 1;

// Consultar intentos fallidos actuales
$stmt_intentos = $conn->prepare("SELECT COUNT(*) as intentos FROM intentos_fallidos WHERE id_usuario = ? AND id_libro = ? AND CAPITULO = ?");
$stmt_intentos->bind_param("iii", $id_usuario, $id_libro, $capitulo);
$stmt_intentos->execute();
$res_intentos = $stmt_intentos->get_result()->fetch_assoc();
$intentos_fallidos = $res_intentos['intentos'] ?? 0;

// Consultar si hay bloqueo activo
$stmt_bloqueo = $conn->prepare("
    SELECT fecha_desbloqueo, TIMESTAMPDIFF(SECOND, NOW(), fecha_desbloqueo) as segundos_restantes
    FROM bloqueos_temporales
    WHERE id_usuario = ? AND id_libro = ? AND CAPITULO = ? AND activo = 1 AND fecha_desbloqueo > NOW()
    LIMIT 1
");
$stmt_bloqueo->bind_param("iii", $id_usuario, $id_libro, $capitulo);
$stmt_bloqueo->execute();
$res_bloqueo = $stmt_bloqueo->get_result()->fetch_assoc();
$bloqueado = $res_bloqueo ? true : false;
$segundos_restantes = $res_bloqueo['segundos_restantes'] ?? 0;

// Obtener puntaje total del usuario para este libro y capítulo
$stmt = $conn->prepare("
    SELECT SUM(PUNTAJE) as total 
    FROM puntajes 
    WHERE id_usuario = ? AND id_libro = ? AND CAPITULO = ?
");
$stmt->bind_param("iii", $id_usuario, $id_libro, $capitulo);
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
                                <?php if ($puntaje_total >= 80): ?>
                                        <a class="btn" href="contenido_capitulo/contenido_capitulo.php?id_capitulo=<?= $capitulo + 1 ?>&id_libro=<?= $id_libro ?>">
                                            Siguiente capítulo
                                        </a>
                                <?php endif; ?>

                                <?php if ($bloqueado): ?>
                                    <div style="color: #e74c3c; font-weight: bold; margin-bottom: 10px;">
                                        Has agotado tus <b>3 intentos</b>. Estás bloqueado por 2 minutos.<br>
                                        Te quedan <b>0</b> intentos.<br>
                                        Tiempo restante: <span id="minutos">00</span>:<span id="segundos">00</span>
                                    </div>
                                    <script>
                                        let segundos = <?= $segundos_restantes ?>;
                                        function actualizarContador() {
                                            const min = Math.floor(segundos / 60);
                                            const seg = segundos % 60;
                                            document.getElementById('minutos').textContent = String(min).padStart(2, '0');
                                            document.getElementById('segundos').textContent = String(seg).padStart(2, '0');
                                            if (segundos > 0) {
                                                segundos--;
                                                setTimeout(actualizarContador, 1000);
                                            } else {
                                                location.reload();
                                            }
                                        }
                                        actualizarContador();
                                    </script>
                                    <a class="btn disabled" style="pointer-events:none;opacity:0.5;">Reintentar capítulo</a>
                                <?php else: ?>
                                    <div style="color: #555; margin-bottom: 10px;">
                                        <?php $intentos_restantes = max(0, 3 - $intentos_fallidos); ?>
                                        Te quedan <b><?= $intentos_restantes ?></b> intento(s) antes de ser bloqueado por 2 minutos.
                                    </div>
                                    <a class="btn" href="reiniciar_capitulo.php?id_capitulo=<?= $capitulo ?>&id_libro=<?= $id_libro ?>">
                                        Reintentar capítulo
                                    </a>
                                <?php endif; ?>
                                <a class="btn" href="mapa_capitulos/mapa_capitulos.php?id_libro=<?= $id_libro ?>">
                                    Volver a capítulos
                                </a>
                        </div>
        </div>
    </div>
</body>
</html>
