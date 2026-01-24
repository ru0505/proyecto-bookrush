<?php
session_start();
include 'conexion.php';

// ========== MEJORA: Configuración de zona horaria para racha ==========
date_default_timezone_set('America/Lima');

// Inicialización segura de variables
$id_usuario = $_SESSION['id_usuario'] ?? null;
$usuario = $_SESSION['usuario'] ?? '';
$email = $_SESSION['email'] ?? '';
$racha = $_SESSION['racha'] ?? 0;

// Variables de racha mejoradas
$racha = 0;
$fuego_activo = false;

// ========== MEJORA: Sistema de racha mejorado con validación de fechas ==========
if ($id_usuario) {
    // Consultar estado de la racha en BD
    $stmt_racha = $conn->prepare("SELECT racha, ultimo_acceso FROM usuarios WHERE ID = ?");
    $stmt_racha->bind_param("i", $id_usuario);
    $stmt_racha->execute();
    $res_racha = $stmt_racha->get_result()->fetch_assoc();
    $stmt_racha->close();

    if ($res_racha) {
        $racha = $res_racha['racha'];
        $fecha_completa_bd = $res_racha['ultimo_acceso'];

        if ($fecha_completa_bd) {
            $fecha_bd = new DateTime($fecha_completa_bd);
            $hoy = new DateTime();      
            $ayer = new DateTime('-1 day'); 

            $str_bd = $fecha_bd->format('Y-m-d');
            $str_hoy = $hoy->format('Y-m-d');
            $str_ayer = $ayer->format('Y-m-d');

            // Activar fuego si el último acceso fue hoy o ayer
            if ($str_bd === $str_hoy || $str_bd === $str_ayer) {
                $fuego_activo = true;
            } else {
                $fuego_activo = false;
            }
        }
        $_SESSION['racha'] = $racha;
    }
}
// ========== FIN MEJORA ==========

// 1. Traer libros desde la BD (Igual para todos)
$sql = "SELECT id_libro, titulo, AUTOR, descripcion, imagen, archivo FROM libros";
$result = $conn->query($sql);

$libros = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $libros[] = $row;
    }
}

// 2. Traer progreso del usuario (Solo si hay ID)
$progreso = [];

if ($id_usuario) {
    // CORRECCIÓN AQUÍ: Usamos 'id_usuario' en el WHERE, no 'ID'
    $sql2 = "SELECT id_libro, SUM(PUNTAJE) as total FROM puntajes WHERE id_usuario=? GROUP BY id_libro";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $id_usuario); // 'i' porque es entero
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    while ($row = $res2->fetch_assoc()) {
        $progreso[$row['id_libro']] = intval($row['total']);
    }
    $stmt2->close();
}

// 3. Obtener categorías dinámicamente para el menú
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
  <title>Book Rush - Biblioteca Interactiva</title>
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
    <?php if ($id_usuario): ?>
      <a href="perfil.php" style="text-decoration: none;">
        <div class="icon-container" style="cursor: pointer;">
          <img src="imagenes/usuario.png" alt="Usuario" class="icon">

          <div class="tooltip">
            <strong>Usuario:</strong> <?= htmlspecialchars($usuario) ?><br>
            <strong>Email:</strong> <?= htmlspecialchars($email) ?><br>
          </div>
        </div>
      </a>

    <div class="icon-container racha-container">
      <div class="racha-display <?= $fuego_activo ? '' : 'fuego-apagado' ?>">
        <span class="fuego-emoji">🔥</span>
        <span class="racha-numero"><?= $racha ?></span>
      </div>
 
      <div class="tooltip tooltip-racha">
        <strong>🔥 Racha de Días</strong><br>
        <p style="margin: 10px 0; font-size: 16px;">
          <?php if ($fuego_activo): ?>
            <?php if ($racha >= 7): ?>
              ¡Increíble! Llevas <strong><?= $racha ?> días</strong> consecutivos leyendo 🎉
            <?php elseif ($racha >= 3): ?>
              ¡Muy bien! Llevas <strong><?= $racha ?> días</strong> seguidos 💪
            <?php elseif ($racha >= 1): ?>
              Llevas <strong><?= $racha ?> día(s)</strong>. ¡Sigue así! 🌟
            <?php endif; ?>
          <?php else: ?>
            <?php if ($racha > 0): ?>
              Tu última racha fue de <strong><?= $racha ?> día(s)</strong>.<br>
              ¡Completa una trivia hoy para reactivarla! 🚀
            <?php else: ?>
              ¡Empieza tu racha hoy! 🚀
            <?php endif; ?>
          <?php endif; ?>
        </p>
        <small style="color: rgba(255,255,255,0.8);">Ingresa cada día para mantener tu racha activa</small>
      </div>
    </div>

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
  <?php if ($id_usuario): ?>
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
      <a href="categoria.php?cat=<?= urlencode($cat) ?>">
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
  <section class="hero">
    ¡Descubre el mágico mundo de la lectura!
  </section>

  <div class="libros-grid">
  <?php if (!empty($libros)): ?>
    <?php foreach ($libros as $libro): 
      $id = $libro['id_libro'];
      $nombre = htmlspecialchars($libro['titulo']);
      $autor = htmlspecialchars($libro['AUTOR']);
      $descripcion = htmlspecialchars($libro['descripcion']);
      $imagen = htmlspecialchars($libro['imagen']);
      $archivo = htmlspecialchars($libro['archivo']); 

      $puntaje = $progreso[$id] ?? 0;
      $porcentaje = min(100, round($puntaje)); 
    ?>
      <!-- Contenedor clickeable completo -->
      <a href="detalle_libros/detalle_libro.php?id=<?= $id ?>" class="libro-link">
        <div class="libro">
          <?php if (!empty($imagen)): ?>
            <img src="<?= $imagen ?>" alt="<?= $nombre ?>">
          <?php else: ?>
            <img src="imagenes/default.jpg" alt="Sin imagen">
          <?php endif; ?>

          <h3><?= $nombre ?></h3>
          <p><strong><?= $autor ?></strong></p>
          <p><?= $descripcion ?></p>
        </div>
      </a>
    <?php endforeach; ?>
  <?php else: ?>
    <p>No hay libros disponibles.</p>
  <?php endif; ?>
</div>
</main>

<footer>
  <p>&copy; 2025 Book Rush. Todos los derechos reservados.</p>
</footer>

<script>
  function mostrarConfirmacion() {
    // Usamos 'flex' para que funcione el centrado de tu CSS nuevo
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