<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$nombre = $_SESSION['usuario'] ?? "Usuario";

/* ===== PUNTOS TOTALES ===== */
$stmt = $conn->prepare("SELECT IFNULL(SUM(puntaje_obtenido),0) AS total FROM progreso_usuario WHERE id_usuario=?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$puntos = (int)$stmt->get_result()->fetch_assoc()['total'];

/* ===== MISIONES / COFRES ===== */
$misiones = [
    ["nombre"=>"Cofre de madera","meta"=>500,"imagen"=>"imagenes/estrella.png"],
    ["nombre"=>"Cofre de plata","meta"=>3000,"imagen"=>"imagenes/estrella.png"],
    ["nombre"=>"Cofre de oro","meta"=>5000,"imagen"=>"imagenes/estrella.png"]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Retos - Book Rush</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/recompensa.css">
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

<!-- ===== BARRA SUPERIOR ===== -->
<div class="top-bar">
  <div class="top-left">
    <img src="imagenes/LOGO_BOOK_RUSH.png" alt="Logo Book Rush">
    <h2>Book Rush</h2>
  </div>

  <div class="top-icons">
    <div class="icon-container">
      <img src="imagenes/usuario.png" class="icon" alt="Usuario">
      <div class="tooltip">
        <strong>Usuario:</strong> <?= htmlspecialchars($nombre) ?>
      </div>
    </div>

    <div class="icon-container">
      <a href="index.php">
        <img src="imagenes/puerta.png" class="icon" alt="Volver">
      </a>
    </div>
  </div>
</div>

<!-- ===== BOTÓN VOLVER ===== -->
<a href="index.php" class="volver-btn">⬅ Volver al Inicio</a>

<!-- ===== CONTENIDO ===== -->
<div class="recompensas-container">

<h2>🎒 Mis Retos</h2>
<div class="mis-puntos">⭐ <?= number_format($puntos) ?> puntos mágicos</div>

<div class="bloque-retos">
<h2>🗺️ Misiones de Lectura</h2>

<?php foreach($misiones as $m):
    $progreso = min(100, ($puntos / $m['meta']) * 100);
    $actual = min($puntos, $m['meta']);
    $completo = $puntos >= $m['meta'];
?>
<div class="mision <?= $completo ? 'completo' : '' ?>">

  <img src="<?= $m['imagen'] ?>" alt="<?= $m['nombre'] ?>">

  <div class="mision-info">
    <h3><?= $m['nombre'] ?></h3>

    <div class="barra">
      <div class="progreso" style="width:<?= $progreso ?>%"></div>
    </div>

    <div class="meta"><?= number_format($actual) ?> / <?= number_format($m['meta']) ?> puntos</div>
    
    <?php if ($completo): ?>
      <span class="insignia-completo">✅ ¡Desbloqueado!</span>
    <?php else: ?>
      <span class="insignia-pendiente">🔒 Faltan <?= number_format($m['meta'] - $actual) ?> puntos</span>
    <?php endif; ?>
  </div>

</div>
<?php endforeach; ?>
</div>

<div class="premios">
<h2>🎁 Premios Culturales</h2>
<p class="prox">
Próximamente: canjea tus puntos por premios culturales como entradas al cine, teatro, museos y más 🎭🎬📚
</p>
<p style="margin-top: 15px; font-size: 14px; opacity: 0.8;">
💡 <em>Sigue leyendo y acumulando puntos para desbloquear recompensas exclusivas</em>
</p>
</div>

</div>

</body>
</html>
