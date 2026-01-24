<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Corregir: $_SESSION['usuario'] es solo el nombre, no el array completo
$nombre = $_SESSION['usuario'] ?? 'Desconocido';
$id_usuario = $_SESSION['id_usuario'] ?? 0;

// Obtener datos completos del usuario desde la BD
if ($id_usuario > 0) {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE ID = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($usuario_db = $result->fetch_assoc()) {
        $nombre = $usuario_db['NOMBRE'] ?? $nombre;
        $rol = $usuario_db['ROL'] ?? 'Invitado';
        $email = $usuario_db['email'] ?? 'N/A';
    } else {
        $rol = 'Invitado';
        $email = 'N/A';
    }
} else {
    $rol = 'Invitado';
    $email = 'N/A';
}

// ========== MEJORA: Usar progreso_usuario para puntos ==========
$puntaje_total = 0;
if ($id_usuario > 0) {
    $stmt = $conn->prepare("SELECT IFNULL(SUM(puntaje_obtenido), 0) AS total FROM progreso_usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($fila = $res->fetch_assoc()) {
        $puntaje_total = $fila['total'] ?? 0;
    }
}

// Libros leídos con detalles
$libros_leidos = 0;
$libros_con_progreso = [];
if ($id_usuario > 0) {
    // Obtener libros con puntaje
    $stmt = $conn->prepare("
        SELECT 
            l.id_libro,
            l.titulo,
            l.AUTOR,
            l.categoria,
            l.imagen,
            SUM(p.PUNTAJE) as puntaje_total
        FROM puntajes p
        JOIN libros l ON p.id_libro = l.id_libro
        WHERE p.id_usuario = ?
        GROUP BY l.id_libro, l.titulo, l.AUTOR, l.categoria, l.imagen
        HAVING puntaje_total > 0
        ORDER BY puntaje_total DESC
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($fila = $res->fetch_assoc()) {
        $libros_con_progreso[] = $fila;
        $libros_leidos++;
    }
}



// Estadísticas por categoría (dinámicas)
$stats_categoria = [];
if ($id_usuario > 0) {
    $stmt = $conn->prepare("
        SELECT 
            l.categoria,
            COUNT(c.id_capitulo) AS total_capitulos,
            COUNT(DISTINCT CASE 
                WHEN p.id_usuario IS NOT NULL AND p.PUNTAJE > 0 
                THEN CONCAT(c.id_libro, '-', c.id_capitulo)
            END) AS capitulos_completados,
            SUM(CASE WHEN p.id_usuario IS NOT NULL THEN p.PUNTAJE ELSE 0 END) AS puntaje_categoria
        FROM libros l
        JOIN capitulos c ON l.id_libro = c.id_libro
        LEFT JOIN puntajes p ON c.id_libro = p.id_libro 
                            AND c.id_capitulo = p.CAPITULO 
                            AND p.id_usuario = ?
        WHERE l.categoria IS NOT NULL
        GROUP BY l.categoria
        ORDER BY l.categoria
    ");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($fila = $res->fetch_assoc()) {
        $total = intval($fila['total_capitulos']);
        $completados = intval($fila['capitulos_completados']);
        $porcentaje = $total > 0 ? round(($completados / $total) * 100, 1) : 0;
        
        $stats_categoria[$fila['categoria']] = [
            'total' => $total,
            'completados' => $completados,
            'porcentaje' => $porcentaje,
            'puntaje' => intval($fila['puntaje_categoria'])
        ];
    }
}

// Determinar categoría favorita
$categoria_favorita = 'Ninguna';
$max_porcentaje = 0;
foreach ($stats_categoria as $cat => $data) {
    if ($data['porcentaje'] > $max_porcentaje) {
        $max_porcentaje = $data['porcentaje'];
        $categoria_favorita = $cat;
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
  <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="responsive.css">
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
    
    /* Estilos para Ruta de Aprendizaje */
    .ruta-aprendizaje {
      background: linear-gradient(135deg, rgba(26, 35, 126, 0.95), rgba(13, 71, 161, 0.95));
      background-image: url('source/entendiendo-las-redes-neuronales-artificiales-una-guia-completa.jpg');
      background-size: cover;
      background-position: center;
      background-blend-mode: overlay;
      padding: 35px;
      border-radius: 15px;
      margin: 35px 0;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
      position: relative;
      overflow: hidden;
    }
    
    .ruta-aprendizaje::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(26, 35, 126, 0.85), rgba(13, 71, 161, 0.85));
      z-index: 0;
    }
    
    .ruta-aprendizaje > * {
      position: relative;
      z-index: 1;
    }
    
    .ruta-aprendizaje h3 {
      color: #ffffff;
      font-size: 28px;
      margin-bottom: 10px;
      text-align: center;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .ruta-aprendizaje .subtitulo {
      color: #b3e5fc;
      text-align: center;
      margin-bottom: 30px;
      font-size: 14px;
    }
    
    .categoria-item {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      border: 1px solid rgba(255, 255, 255, 0.2);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .categoria-item:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }
    
    .categoria-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .categoria-titulo {
      display: flex;
      align-items: center;
      gap: 12px;
      font-size: 20px;
      font-weight: 700;
      color: #ffffff;
    }
    
    .categoria-icono {
      font-size: 28px;
    }
    
    .categoria-porcentaje {
      font-size: 24px;
      font-weight: 700;
      color: #4fc3f7;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .progress-container {
      background: rgba(0, 0, 0, 0.3);
      height: 28px;
      border-radius: 14px;
      overflow: hidden;
      margin-bottom: 10px;
      position: relative;
      box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .progress-bar {
      height: 100%;
      border-radius: 14px;
      transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      padding-right: 10px;
    }
    
    .progress-bar::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(90deg, 
        rgba(255, 255, 255, 0) 0%, 
        rgba(255, 255, 255, 0.2) 50%, 
        rgba(255, 255, 255, 0) 100%);
      animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
      0% { transform: translateX(-100%); }
      100% { transform: translateX(100%); }
    }
    
    .progress-bar.nacional {
      background: linear-gradient(90deg, #ff6b6b, #ff8787);
      box-shadow: 0 2px 8px rgba(255, 107, 107, 0.5);
    }
    
    .progress-bar.regional {
      background: linear-gradient(90deg, #ffa726, #ffb74d);
      box-shadow: 0 2px 8px rgba(255, 167, 38, 0.5);
    }
    
    .progress-bar.universal {
      background: linear-gradient(90deg, #66bb6a, #81c784);
      box-shadow: 0 2px 8px rgba(102, 187, 106, 0.5);
    }
    
    .progress-bar span {
      font-size: 12px;
      font-weight: 700;
      color: white;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
      z-index: 2;
    }
    
    .categoria-stats {
      display: flex;
      justify-content: space-between;
      color: #e1f5fe;
      font-size: 13px;
    }
    
    .categoria-stats span {
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .favorita-badge {
      background: linear-gradient(135deg, #ffd700, #ffed4e);
      color: #1e334e;
      padding: 12px 25px;
      border-radius: 25px;
      text-align: center;
      margin-top: 20px;
      font-weight: 700;
      font-size: 16px;
      box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.02); }
    }
    
    .mensaje-inicio {
      text-align: center;
      color: #b3e5fc;
      font-size: 18px;
      padding: 40px 20px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      border: 2px dashed rgba(255, 255, 255, 0.3);
    }
    
    .mensaje-inicio span {
      font-size: 48px;
      display: block;
      margin-bottom: 15px;
    }
    
    /* ========== MEJORA: Estilos para puntos por libro ========== */
    .libros-resumen-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }
    
    .libro-resumen-card {
      background: linear-gradient(135deg, rgba(255,255,255,0.95), rgba(240,242,245,0.95));
      border-radius: 12px;
      padding: 20px;
      display: flex;
      gap: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
      border: 2px solid rgba(74,144,226,0.2);
    }
    
    .libro-resumen-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(74,144,226,0.3);
      border-color: #4a90e2;
    }
    
    .libro-portada-mini {
      width: 80px;
      height: 110px;
      object-fit: cover;
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }
    
    .libro-portada-placeholder {
      width: 80px;
      height: 110px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 40px;
      background: linear-gradient(135deg, #e3f2fd, #bbdefb);
      border-radius: 8px;
      box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .libro-info-mini {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }
    
    .libro-info-mini h4 {
      font-size: 16px;
      color: #1e334e;
      margin: 0;
      line-height: 1.3;
    }
    
    .autor-mini {
      font-size: 13px;
      color: #6c757d;
      margin: 0;
    }
    
    .categoria-mini {
      font-size: 12px;
      color: #4a90e2;
      font-weight: 600;
      margin: 0;
    }
    
    .puntos-libro-badge {
      background: linear-gradient(135deg, #ffd700, #ffed4e);
      color: #1e334e;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 14px;
      font-weight: 700;
      text-align: center;
      box-shadow: 0 2px 6px rgba(255,215,0,0.4);
      margin-top: auto;
    }
    
    .mensaje-vacio {
      text-align: center;
      color: #6c757d;
      padding: 30px;
      font-size: 15px;
    }
    /* ========== FIN MEJORA ========== */
    
    /* Responsive para Perfil */
    @media (max-width: 768px) {
      .perfil-container {
        padding: 20px 15px;
        margin: 20px 10px;
      }
      
      .info-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .ruta-aprendizaje {
        padding: 20px 15px;
        margin: 25px 0;
      }
      
      .ruta-aprendizaje h3 {
        font-size: 24px;
      }
      
      .categoria-item {
        padding: 15px;
      }
      
      .categoria-titulo {
        font-size: 16px;
      }
      
      .categoria-porcentaje {
        font-size: 20px;
      }
      
      .progress-container {
        height: 24px;
      }
      
      .detalle-section {
        margin-top: 25px;
      }
      
      .libro-grupo {
        padding: 15px;
      }
      
      /* Grid de libros en tablet */
      .libros-resumen-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 15px;
      }
      
      .libro-resumen-card {
        padding: 15px;
      }
    }
    
    @media (max-width: 480px) {
      .perfil-container {
        padding: 15px 10px;
        margin: 15px 5px;
      }
      
      .perfil-header h2 {
        font-size: 24px;
      }
      
      .user-icon {
        width: 80px;
        height: 80px;
        font-size: 38px;
      }
      
      /* Responsive para grid de libros en móviles */
      .libros-resumen-grid {
        grid-template-columns: 1fr;
        gap: 15px;
      }
      
      .libro-resumen-card {
        padding: 15px;
        flex-direction: column;
        align-items: center;
        text-align: center;
      }
      
      .libro-portada-mini,
      .libro-portada-placeholder {
        width: 100px;
        height: 140px;
        margin-bottom: 10px;
      }
      
      .libro-info-mini h4 {
        font-size: 15px;
      }
      
      .detalle-section h3 {
        font-size: 18px;
        padding-right: 25px;
      }
      
      #toggle-icon {
        font-size: 16px !important;
      }
      
      .puntaje-destacado {
        padding: 20px;
      }
      
      .puntaje-valor {
        font-size: 32px;
      }
      
      .ruta-aprendizaje h3 {
        font-size: 20px;
      }
      
      .categoria-icono {
        font-size: 22px;
      }
      
      .categoria-titulo {
        font-size: 14px;
      }
      
      .categoria-porcentaje {
        font-size: 18px;
      }
      
      .categoria-stats {
        font-size: 11px;
      }
      
      .favorita-badge {
        padding: 10px 20px;
        font-size: 14px;
      }
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
    <a href="perfil.php" style="text-decoration: none;">
      <div class="icon-container" style="cursor: pointer;">
        <img src="imagenes/usuario.png" alt="Usuario" class="icon">

        <div class="tooltip" style="width: auto; white-space: nowrap; padding: 12px 20px; background: rgba(30, 51, 78, 0.95); font-weight: 700; font-size: 20px;">
          Mi Perfil
        </div>
      </div>
    </a>
    
    <div class="icon-container" style="cursor: pointer;">
      <img src="imagenes/puerta.png" alt="Cerrar sesión" class="icon" onclick="mostrarConfirmacion()">
      
      <div class="tooltip" style="width: auto; white-space: nowrap; padding: 12px 20px; background: rgba(30, 51, 78, 0.95); font-weight: 700; font-size: 20px;">
        Cerrar Sesión
      </div>
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
  <div class="mobile-user-info">
    <h3>👤 Mi Perfil</h3>
    <div class="mobile-user-item">
      <strong>Nombre:</strong>
      <span><?= htmlspecialchars($nombre) ?></span>
    </div>
    <div class="mobile-user-item">
      <strong>Email:</strong>
      <span><?= htmlspecialchars($email) ?></span>
    </div>
    <div class="mobile-user-item">
      <strong>Rol:</strong>
      <span><?= htmlspecialchars($rol) ?></span>
    </div>
    <div class="mobile-user-item">
      <strong>Libros:</strong>
      <span><?= htmlspecialchars($libros_leidos) ?></span>
    </div>
    <button class="mobile-logout-btn" onclick="mostrarConfirmacion()">Cerrar Sesión</button>
  </div>
  
  <nav>
    <a href="index.php">Inicio</a>
    <?php 
    $categorias_menu = $conn->query("SELECT DISTINCT categoria FROM libros WHERE categoria IS NOT NULL ORDER BY categoria");
    if ($categorias_menu) {
      while ($cat = $categorias_menu->fetch_assoc()) {
        echo '<a href="categoria.php?cat=' . urlencode($cat['categoria']) . '">' . htmlspecialchars($cat['categoria']) . '</a>';
      }
    }
    ?>    <!-- ========== MEJORA: Enlace a Recompensas ========== -->
    <a href="recompensa.php" style="border-top: 2px solid rgba(255,255,255,0.2); padding-top: 15px; margin-top: 10px; background: linear-gradient(135deg, rgba(255,215,0,0.2), rgba(255,165,0,0.2));">
      🎁 Mis Recompensas
    </a>
    <!-- ========== FIN MEJORA ========== -->  </nav>
</header>

<main>
  <div class="perfil-container">
    <div class="perfil-header">
      <div class="user-icon"><?= strtoupper(substr($nombre, 0, 1)) ?></div>
      <h2>Mi Perfil</h2>
    </div>
    
    <div class="info-grid">
      <div class="info-card">
        <strong>Nombre</strong>
        <span><?= htmlspecialchars($nombre) ?></span>
      </div>
      <div class="info-card">
        <strong>Email</strong>
        <span><?= htmlspecialchars($email) ?></span>
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

    <!-- Ruta de Aprendizaje -->
    <div class="ruta-aprendizaje">
      <h3> Tu Ruta de Aprendizaje</h3>
      <p class="subtitulo">Progreso por tipo de literatura</p>
      
      <?php if (!empty($stats_categoria)): ?>
        
        <?php 
        // Iconos por categoría
        $iconos = [
          'Aventura y fantasía' => '🗺️',
          'Drama' => '🎭',
          'Novela romántica' => '💕',
          'Policial y de misterio' => '🔍',
          'Terror gótico' => '🦇',
          'Tradiciones y cuentos' => '📚'
        ];
        
        foreach ($stats_categoria as $nombre_cat => $datos): 
          $icono = $iconos[$nombre_cat] ?? '📖';
          $clase_css = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó'], ['_', 'a', 'e', 'i', 'o'], $nombre_cat));
        ?>
        
        <div class="categoria-item">
          <div class="categoria-header">
            <div class="categoria-titulo">
              <span class="categoria-icono"><?= $icono ?></span>
              <span><?= htmlspecialchars($nombre_cat) ?></span>
            </div>
            <div class="categoria-porcentaje"><?= $datos['porcentaje'] ?>%</div>
          </div>
          <div class="progress-container">
            <div class="progress-bar <?= $clase_css ?>" style="width: <?= $datos['porcentaje'] ?>%;">
              <?php if ($datos['porcentaje'] > 15): ?>
                <span><?= $datos['completados'] ?>/<?= $datos['total'] ?></span>
              <?php endif; ?>
            </div>
          </div>
          <div class="categoria-stats">
            <span>📖 <?= $datos['completados'] ?>/<?= $datos['total'] ?> capítulos</span>
            <span> <?= $datos['puntaje'] ?> pts</span>
          </div>
        </div>
        
        <?php endforeach; ?>
        
        <?php if ($max_porcentaje > 0): ?>
          <div class="favorita-badge">
             Tu categoría dominante: Literatura <?= htmlspecialchars($categoria_favorita) ?>
          </div>
        <?php endif; ?>
        
      <?php else: ?>
        <div class="mensaje-inicio">
          <span></span>
          <strong>¡Comienza tu aventura literaria!</strong><br>
          Completa capítulos para ver tu progreso aquí.
        </div>
      <?php endif; ?>
    </div>

    <!-- Sección de Libros Leídos -->
    <div class="detalle-section" style="margin-top: 40px;">
      <h3>Mis Libros Leídos</h3>
      
      <?php if (!empty($libros_con_progreso)): ?>
        <div class="libros-resumen-grid">
          <?php foreach ($libros_con_progreso as $libro): ?>
            <div class="libro-resumen-card">
              <?php if (!empty($libro['imagen'])): ?>
                <img src="<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>" class="libro-portada-mini">
              <?php else: ?>
                <div class="libro-portada-placeholder">📖</div>
              <?php endif; ?>
              
              <div class="libro-info-mini">
                <h4><?= htmlspecialchars($libro['titulo']) ?></h4>
                <p class="autor-mini">Por: <?= htmlspecialchars($libro['AUTOR']) ?></p>
                <p class="categoria-mini"><?= htmlspecialchars($libro['categoria']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="mensaje-vacio">
          <p>📖 Aún no has empezado a leer ningún libro.</p>
          <p style="font-size: 14px; margin-top: 10px; opacity: 0.8;">¡Explora nuestra biblioteca y comienza tu aventura!</p>
        </div>
      <?php endif; ?>
    </div>

    </div>
  </div>
</main>

<footer>
  <p>&copy; 2026 Book Rush. Todos los derechos reservados.</p>
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

<script src="menu-mobile.js"></script>

</body>
</html>
