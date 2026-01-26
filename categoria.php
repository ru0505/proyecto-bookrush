<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    $_SESSION['id_usuario'] = '';
}
if (!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = '';
}

include 'conexion.php';

$id_usuario = $_SESSION['id_usuario'];
$usuario = $_SESSION['usuario'] ?? '';
$email = $_SESSION['email'] ?? '';
$racha = $_SESSION['racha'] ?? 0;

// Obtener la categoría desde la URL
$categoria = $_GET['cat'] ?? '';

if (empty($categoria)) {
    header('Location: index.php');
    exit;
}

// Traer libros de la categoría seleccionada
$stmt = $conn->prepare("SELECT id_libro, titulo, AUTOR, descripcion, imagen, archivo FROM libros WHERE categoria = ? ORDER BY id_libro");
$stmt->bind_param("s", $categoria);
$stmt->execute();
$result = $stmt->get_result();

$libros = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $libros[] = $row;
    }
}

// Traer progreso del usuario
$progreso = [];
if (!empty($id_usuario)) {
    $sql2 = "SELECT id_libro, SUM(PUNTAJE) as total FROM puntajes WHERE id_usuario=? GROUP BY id_libro";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $id_usuario);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while ($row = $res2->fetch_assoc()) {
        $progreso[$row['id_libro']] = intval($row['total']);
    }
}

// Obtener todas las categorías para el menú
$categorias_query = $conn->query("SELECT DISTINCT categoria FROM libros WHERE categoria IS NOT NULL ORDER BY categoria");
$categorias = [];
if ($categorias_query) {
    while ($cat = $categorias_query->fetch_assoc()) {
        $categorias[] = $cat['categoria'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($categoria) ?> - Book Rush</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.cdnfonts.com/css/sergio-trendy" rel="stylesheet">
  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="responsive.css">
  
  <style>
    /* CSS para hacer clickeable todo el contenedor del libro */
    .libro-link {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: transform 0.3s ease;
    }
    
    .libro-link:hover .libro {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }

    .libro {
        height: 100%;
        cursor: pointer;
    }
  </style>
</head>
<body>

<div class="top-bar">
  <button class="menu-toggle" onclick="toggleMenu()" aria-label="Menú">
    <span></span>
    <span></span>
    <span></span>
  </button>
  
  <a href="index.php" class="logo-link">
    <img src="imagenes/lecturin/lecturin_saltando.png" alt="Logo Book Rush" style="height: 70px; margin-right: 10px;">
    <h1>Book Rush</h1>
  </a>

  <div class="top-icons">
    <?php if ($usuario): ?>
      <a href="perfil.php" style="text-decoration: none;">
        <div class="icon-container" style="cursor: pointer;">
          <img src="imagenes/usuario.png" alt="Usuario" class="icon">
          
          <div class="tooltip" style="width: auto; white-space: nowrap; padding: 12px 20px; background: rgba(30, 51, 78, 0.95); font-weight: 700; font-size: 15px;">
            Mi Perfil
          </div>
        </div>
      </a>

      <div class="icon-container racha-container">
        <div class="racha-display <?= $fuego_activo ? '' : 'fuego-apagado' ?>">
          <span class="fuego-emoji">🔥</span>
          <span class="racha-numero"><?= $racha ?></span>
        </div>
 
        <div class="tooltip" style="width: auto; white-space: nowrap; padding: 12px 20px; background: rgba(30, 51, 78, 0.95); font-weight: 700; font-size: 15px;">
          Racha de días: <?= $racha ?>
        </div>
      </div>

      <div class="icon-container" style="cursor: pointer;">
        <img src="imagenes/puerta.png" alt="Cerrar sesión" class="icon" onclick="mostrarConfirmacion()">
        
        <div class="tooltip" style="width: auto; white-space: nowrap; padding: 12px 20px; background: rgba(30, 51, 78, 0.95); font-weight: 700; font-size: 15px;">
          Cerrar Sesión
        </div>
      </div>
    <?php else: ?>
      <a href="login.php" class="boton-top">Iniciar Sesión</a>
      <a href="registro.php" class="boton-top">Registrarse</a>
    <?php endif; ?>
  </div>
</div>

<div id="confirmacion-modal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center;">
  <div style="background: white; padding: 20px; border-radius: 10px; width: 300px; max-width: 80%; margin: 100px auto; text-align: center;">
    <p>¿Estás seguro de que deseas cerrar sesión?</p>
    <button onclick="cerrarSesion()" style="margin: 5px; padding: 8px 16px; background-color: #d85e39; color: white; border: none; border-radius: 5px; cursor: pointer;">Sí</button>
    <button onclick="cerrarModal()" style="margin: 5px; padding: 8px 16px; background-color: #1e334e; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancelar</button>
  </div>
</div>

<header>
  <?php if ($usuario): ?>
  <div class="mobile-user-info">
    <h3>👤 Mi Cuenta</h3>
    <div class="mobile-user-item">
      <strong>Usuario:</strong>
      <span><?= htmlspecialchars($usuario) ?></span>
    </div>
    <div class="mobile-user-item">
      <strong>Email:</strong>
      <span><?= htmlspecialchars($email) ?></span>
    </div>
    <div class="mobile-user-item">
      <strong>🔥 Racha:</strong>
      <span><?= $racha ?> días</span>
    </div>
    <button class="mobile-logout-btn" onclick="mostrarConfirmacion()">Cerrar Sesión</button>
  </div>
  <?php endif; ?>
  
  <nav>
    <?php foreach ($categorias as $cat): ?>
      <a href="categoria.php?cat=<?= urlencode($cat) ?>" class="<?= ($cat === $categoria) ? 'active' : '' ?>">
        <?= htmlspecialchars($cat) ?>
      </a>
    <?php endforeach; ?>
    <!-- ========== MEJORA: Enlace a Recompensas ========== -->
    <a href="recompensa.php" style="border-top: 2px solid rgba(255,255,255,0.2); padding-top: 15px; margin-top: 10px; background: linear-gradient(135deg, rgba(255,215,0,0.2), rgba(255,165,0,0.2));">
      🎁 Mis Recompensas
    </a>
    <!-- ========== FIN MEJORA ========== -->
  </nav>
</header>

<main>
  <h2 style="text-align: center; margin-bottom: 20px;"><?= htmlspecialchars($categoria) ?></h2>

  <div class="libros-grid">
    <?php if (!empty($libros)): ?>
      <?php foreach ($libros as $libro): 
        $id_libro = $libro['id_libro'];
        $puntaje_libro = $progreso[$id_libro] ?? 0;
        $total_puntos_libro = 500;
        $porcentaje = min(100, ($puntaje_libro / $total_puntos_libro) * 100);
      ?>
        <!-- Contenedor clickeable completo -->
        <a href="detalle_libros/detalle_libro.php?id=<?= $id_libro ?>" class="libro-link">
          <div class="libro">
            <?php if (!empty($libro['imagen'])): ?>
              <img src="<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>">
            <?php else: ?>
              <img src="imagenes/default.jpg" alt="Sin imagen">
            <?php endif; ?>

            <h3><?= htmlspecialchars($libro['titulo']) ?></h3>
            <p><strong><?= htmlspecialchars($libro['AUTOR']) ?></strong></p>
            <p><?= htmlspecialchars($libro['descripcion']) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <p style="text-align: center; color: #fff; width: 100%;">No hay libros disponibles en esta categoría.</p>
    <?php endif; ?>
  </div>
</main>

<footer>
  <p>&copy; 2025 Book Rush. Todos los derechos reservados.</p>
</footer>

<script>
  function mostrarConfirmacion() {
    document.getElementById('confirmacion-modal').style.display = 'flex';
  }
  function cerrarModal() {
    document.getElementById('confirmacion-modal').style.display = 'none';
  }
  function cerrarSesion() {
    window.location.href = 'logout.php';
  }
</script>

<script src="menu-mobile.js"></script>

</body>
</html>
