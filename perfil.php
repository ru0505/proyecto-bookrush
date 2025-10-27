<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$nombre = $usuario['NOMBRE'] ?? 'Desconocido';
$dni = $usuario['DNI'] ?? 'No disponible';
$rol = $usuario['ROL'] ?? 'Invitado';
$id_usuario = $usuario['ID_USUARIO'] ?? 0;

// Puntaje total
$puntaje_total = 0;
if ($dni) {
    $stmt = $conn->prepare("SELECT SUM(PUNTAJE) AS total FROM puntajes WHERE DNI = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($fila = $res->fetch_assoc()) {
        $puntaje_total = $fila['total'] ?? 0;
    }
}

// Libros leídos
$libros_leidos = 0;
if ($dni) {
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT l.ID_LIBRO) AS total
        FROM puntajes p
        JOIN capitulos c ON p.CAPITULO = c.TITULO
        JOIN libros l ON c.ID_LIBRO = l.ID_LIBRO
        WHERE p.DNI = ?
    ");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($fila = $res->fetch_assoc()) {
        $libros_leidos = $fila['total'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil - Book Rush</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.cdnfonts.com/css/sergio-trendy" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .perfil-container {
      max-width: 900px;
      margin: 40px auto;
      background: rgba(255, 255, 255, 0.98);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .perfil-header {
      text-align: center;
      margin-bottom: 35px;
      padding-bottom: 25px;
      border-bottom: 3px solid #4a90e2;
    }
    
    .perfil-header h2 {
      color: #1e334e;
      font-size: 32px;
      margin-bottom: 10px;
    }
    
    .perfil-header .user-icon {
      width: 100px;
      height: 100px;
      margin: 0 auto 20px;
      background: linear-gradient(135deg, #4a90e2, #357abd);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 48px;
      color: white;
    }
    
    .info-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .info-card {
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
      padding: 20px;
      border-radius: 12px;
      border-left: 4px solid #4a90e2;
    }
    
    .info-card strong {
      color: #1e334e;
      display: block;
      margin-bottom: 8px;
      font-size: 14px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .info-card span {
      color: #495057;
      font-size: 18px;
      font-weight: 600;
    }
    
    .puntaje-destacado {
      background: linear-gradient(135deg, #ffd700, #ffed4e);
      padding: 25px;
      border-radius: 12px;
      text-align: center;
      margin: 30px 0;
      box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
    }
    
    .puntaje-destacado h3 {
      color: #1e334e;
      margin-bottom: 10px;
      font-size: 20px;
    }
    
    .puntaje-destacado .puntaje-valor {
      font-size: 42px;
      font-weight: 700;
      color: #d85e39;
    }
    
    .detalle-section {
      margin-top: 35px;
    }
    
    .detalle-section h3 {
      color: #1e334e;
      font-size: 24px;
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 2px solid #e9ecef;
    }
    
    .libro-grupo {
      background: #f8f9fa;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    
    .libro-grupo h4 {
      color: #4a90e2;
      font-size: 20px;
      margin-bottom: 15px;
    }
    
    .capitulo-item {
      background: white;
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 10px;
      border-left: 3px solid #4a90e2;
    }
    
    .capitulo-item strong {
      color: #1e334e;
      display: block;
      margin-bottom: 8px;
    }
    
    .pregunta-item {
      padding: 8px 15px;
      margin: 5px 0;
      background: #f8f9fa;
      border-radius: 6px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .pregunta-item .puntaje-badge {
      background: #4a90e2;
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-weight: 600;
      font-size: 14px;
    }
  </style>
</head>
<body>

<div class="top-bar">
  <div style="display: flex; align-items: center;">
    <img src="imagenes/LOGO_BOOK_RUSH.png" alt="Logo Book Rush" style="height: 50px; margin-right: 10px;">
    <h1>Book Rush</h1>
  </div>
  
  <div class="top-icons">
    <div class="icon-container" style="cursor: pointer;">
      <img src="imagenes/usuario.png" alt="Usuario" class="icon">
      <div class="tooltip">
        <strong>Usuario:</strong> <?= htmlspecialchars($nombre) ?><br>
        <strong>DNI:</strong> <?= htmlspecialchars($dni) ?><br>
      </div>
    </div>
    
    <div class="icon-container" style="cursor: pointer;">
      <img src="imagenes/puerta.png" alt="Cerrar sesión" class="icon" onclick="mostrarConfirmacion()">
    </div>
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
    <a href="index.php">Inicio</a>
    <a href="nacional.php">Literatura Nacional</a>
    <a href="regional.php">Literatura Regional</a>
    <a href="universal.php">Literatura Universal</a>
  </nav>
</header>

<main>
  <div class="perfil-container">
    <div class="perfil-header">
      <div class="user-icon">U</div>
      <h2>Mi Perfil</h2>
    </div>
    
    <div class="info-grid">
      <div class="info-card">
        <strong>Nombre</strong>
        <span><?= htmlspecialchars($nombre) ?></span>
      </div>
      <div class="info-card">
        <strong>DNI</strong>
        <span><?= htmlspecialchars($dni) ?></span>
      </div>
      <div class="info-card">
        <strong>Rol</strong>
        <span><?= htmlspecialchars($rol) ?></span>
      </div>
      <div class="info-card">
        <strong>Libros Leídos</strong>
        <span><?= htmlspecialchars($libros_leidos) ?></span>
      </div>
    </div>
    
    <div class="puntaje-destacado">
      <h3>Puntaje Total Acumulado</h3>
      <div class="puntaje-valor"><?= htmlspecialchars($puntaje_total) ?> pts</div>
    </div>

    
    <div class="detalle-section">
      <h3>Detalle de Puntajes por Libro</h3>
      <?php
      $stmt = $conn->prepare("
        SELECT l.TITULO AS libro,
               c.TITULO AS capitulo,
               q.ENUNCIADO AS pregunta,
               IFNULL(p.PUNTAJE, 0) AS puntaje
        FROM preguntas q
        JOIN capitulos c ON q.ID_CAPITULO = c.ID_CAPITULO
        JOIN libros l ON c.ID_LIBRO = l.ID_LIBRO
        LEFT JOIN puntajes p
               ON q.ID_PREGUNTA = p.ID_PREGUNTA
              AND p.DNI = ?
        ORDER BY l.TITULO, c.TITULO, q.ID_PREGUNTA
      ");
      $stmt->bind_param("s", $dni);
      $stmt->execute();
      $res = $stmt->get_result();

      $libro_actual = "";
      $capitulo_actual = "";
      $primera_vez = true;

      while ($fila = $res->fetch_assoc()) {
          if ($libro_actual !== $fila['libro']) {
              if (!$primera_vez) {
                  echo "</div></div>";
              }
              $primera_vez = false;
              $libro_actual = $fila['libro'];
              echo "<div class='libro-grupo'>";
              echo "<h4>" . htmlspecialchars($libro_actual) . "</h4>";
              $capitulo_actual = "";
          }

          if ($capitulo_actual !== $fila['capitulo']) {
              if ($capitulo_actual !== "") {
                  echo "</div>";
              }
              $capitulo_actual = $fila['capitulo'];
              echo "<div class='capitulo-item'>";
              echo "<strong>Capítulo: " . htmlspecialchars($capitulo_actual) . "</strong>";
          }

          echo "<div class='pregunta-item'>";
          echo "<span>" . htmlspecialchars($fila['pregunta']) . "</span>";
          echo "<span class='puntaje-badge'>" . htmlspecialchars($fila['puntaje']) . " pts</span>";
          echo "</div>";
      }

      if (!$primera_vez) {
          echo "</div></div>";
      }
      ?>
    </div>
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
