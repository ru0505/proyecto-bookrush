<?php
session_start();

if (!isset($_SESSION['dni'])) {
    $_SESSION['dni'] = '';
}
if (!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = '';
}

include 'conexion.php';

$dni = $_SESSION['dni'];
$usuario = $_SESSION['usuario'] ?? '';

// Traer libros de literatura regional desde la BD
// AJUSTA estos IDs según tu base de datos
$sql = "SELECT id_libro, titulo, AUTOR, descripcion, imagen, archivo FROM libros WHERE id_libro IN (14, 17)";
$result = $conn->query($sql);

$libros = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $libros[] = $row;
    }
}

// Traer progreso del usuario
$progreso = [];
if (!empty($dni)) {
    $sql2 = "SELECT id_libro, SUM(PUNTAJE) as total FROM puntajes WHERE DNI=? GROUP BY id_libro";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $dni);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while ($row = $res2->fetch_assoc()) {
        $progreso[$row['id_libro']] = intval($row['total']);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Literatura Regional - Book Rush</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.cdnfonts.com/css/sergio-trendy" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="top-bar">
  <div style="display: flex; align-items: center;">
    <img src="imagenes/LOGO_BOOK_RUSH.png" alt="Logo Book Rush" style="height: 50px; margin-right: 10px;">
    <h1>Book Rush</h1>
  </div>

  <div class="top-icons">
    <?php if ($usuario): ?>
      <!-- Perfil del usuario -->
      <a href="perfil.php" style="text-decoration: none;">
        <div class="icon-container" style="cursor: pointer;">
          <img src="imagenes/usuario.png" alt="Usuario" class="icon">
          <div class="tooltip">
            <strong>Usuario:</strong> <?= htmlspecialchars($usuario) ?><br>
            <strong>DNI:</strong> <?= htmlspecialchars($dni) ?><br>
          </div>
        </div>
      </a>

      <!-- Puntaje -->
      <div class="icon-container">
        <img src="imagenes/estrella.png" alt="Puntaje" class="icon">
        <div class="tooltip">
          <strong>Total de puntos:</strong><br>
          <ul>
            <?php
              $stmt = $conn->prepare("SELECT CAPITULO, SUM(PUNTAJE) as total FROM puntajes WHERE DNI = ? GROUP BY CAPITULO");
              $stmt->bind_param("s", $dni);
              $stmt->execute();
              $res = $stmt->get_result();
              while ($fila = $res->fetch_assoc()) {
                echo "<li>Capítulo " . htmlspecialchars($fila['CAPITULO']) . ": " . $fila['total'] . " pts</li>";
              }
            ?>
          </ul>
        </div>
      </div>

      <!-- Cerrar sesión -->
      <div class="icon-container" style="cursor: pointer;">
        <img src="imagenes/puerta.png" alt="Cerrar sesión" class="icon" onclick="mostrarConfirmacion()">
      </div>
    <?php else: ?>
      <a href="login.php" class="boton-top">Iniciar Sesión</a>
      <a href="registro.php" class="boton-top">Registrarse</a>
    <?php endif; ?>
  </div>
</div>

<!-- Modal de confirmación -->
<div id="confirmacion-modal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
  <div style="background: white; padding: 20px; border-radius: 10px; width: 300px; max-width: 80%; margin: 100px auto; text-align: center;">
    <p>¿Estás seguro de que deseas cerrar sesión?</p>
    <button onclick="cerrarSesion()" style="margin: 5px; padding: 8px 16px; background-color: #d85e39; color: white; border: none; border-radius: 5px;">Sí</button>
    <button onclick="cerrarModal()" style="margin: 5px; padding: 8px 16px; background-color: #1e334e; color: white; border: none; border-radius: 5px;">Cancelar</button>
  </div>
</div>

<header>
  <nav>
    <a href="nacional.php">Literatura Nacional</a>
    <a href="regional.php" class="active">Literatura Regional</a>
    <a href="universal.php">Literatura Universal</a>
  </nav>
</header>

<main>
  <h2 style="text-align: center; margin-bottom: 20px;">Literatura Regional</h2>
  <div class="libros-grid">
    <?php foreach ($libros as $libro): 
      $id_libro = $libro['id_libro'];
      $puntaje_libro = $progreso[$id_libro] ?? 0;
      
      // Calcular porcentaje
      $total_puntos_libro = 500;
      $porcentaje = min(100, ($puntaje_libro / $total_puntos_libro) * 100);
    ?>
      <div class="libro">
        <img src="<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>">
        <h3><?= htmlspecialchars($libro['titulo']) ?></h3>
        <p><?= htmlspecialchars($libro['descripcion']) ?></p>
        
        <?php if ($porcentaje > 0): ?>
          <div class="progreso-container">
            <div class="progreso-barra" style="width: <?= round($porcentaje) ?>%; background: linear-gradient(90deg, #4a90e2, #357abd);"></div>
          </div>
          <p class="progreso-texto"><?= round($porcentaje) ?>% completado</p>
        <?php endif; ?>
        
        <a href="detalle_libros/detalle_libro.php?id_libro=<?= $id_libro ?>">
          <button>📖 Leer cuento completo</button>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<footer>
  <p>&copy; 2025 Book Rush. Todos los derechos reservados.</p>
</footer>

<script>
  function mostrarConfirmacion() {
    document.getElementById('confirmacion-modal').style.display = 'block';
  }
  function cerrarModal() {
    document.getElementById('confirmacion-modal').style.display = 'none';
  }
  function cerrarSesion() {
    window.location.href = 'logout.php';
  }
</script>

</body>
</html>
