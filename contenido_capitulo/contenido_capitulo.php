<?php
session_start();
include '../conexion.php';

// OBTENER LOS PARÁMETROS DE LA URL
$id_capitulo = isset($_GET['id_capitulo']) ? intval($_GET['id_capitulo']) : 0;
$id_libro = isset($_GET['id_libro']) ? intval($_GET['id_libro']) : 0;

// Validar que existan los parámetros
if ($id_capitulo <= 0 || $id_libro <= 0) {
    die("Error: Parámetros inválidos.");
}

// Consulta a la base de datos (UNA SOLA VEZ)
$stmt = $conn->prepare("SELECT titulo, contenido, glosario FROM capitulos WHERE id_capitulo = ? AND id_libro = ?");
$stmt->bind_param("ii", $id_capitulo, $id_libro);
$stmt->execute();
$result = $stmt->get_result();
$capitulo = $result->fetch_assoc();

if (!$capitulo) {
    die("Error: Capítulo no encontrado.");
}

// Procesar el glosario (suponiendo que viene en formato JSON o separado por comas)
$glosario_items = [];
if (!empty($capitulo['glosario'])) {
    // Si está en JSON
    $glosario_decode = json_decode($capitulo['glosario'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($glosario_decode)) {
        $glosario_items = $glosario_decode;
    } else {
        // Si está separado por saltos de línea o comas
        $glosario_items = array_filter(explode("\n", $capitulo['glosario']));
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($capitulo['titulo']) ?> - Libro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { 
      box-sizing: border-box; 
      margin: 0; 
      padding: 0; 
    }
    
    body {
      font-family: 'Fredoka', sans-serif;
      background: linear-gradient(135deg, #353346ff 0%, #2a2a3e 50%, #1e1e2e 100%) fixed;
      background-attachment: fixed;
      color: #fff;
      min-height: 100vh;
      padding: 130px 20px 40px;
    }

    .top-bar {
      width: 100%;
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
      background-size: 200% 200%;
      padding: 25px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: fixed;
      top: 0;
      left: 0;
      z-index: 1100;
      box-shadow: 0 8px 32px rgba(30, 58, 138, 0.3);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      animation: slideDown 0.8s ease-out, topBarMove 6s ease-in-out infinite;
      overflow: hidden;
      min-height: 100px;
    }

    @keyframes slideDown {
      0% { transform: translateY(-100%); opacity: 0; }
      100% { transform: translateY(0); opacity: 1; }
    }

    @keyframes topBarMove {
      0%, 100% { 
        background-position: 0% 50%;
        transform: translateY(0px);
      }
      25% { 
        background-position: 100% 50%;
        transform: translateY(-1px);
      }
      50% { 
        background-position: 0% 100%;
        transform: translateY(0px);
      }
      75% { 
        background-position: 100% 0%;
        transform: translateY(1px);
      }
    }

    .top-bar h1 {
      font-size: 35px;
      color: #ff8c42;
      font-family: 'Fredoka', sans-serif;
      text-shadow: 0 2px 10px rgba(255, 140, 66, 0.5);
      animation: glowTitle 3s ease-in-out infinite alternate;
      margin: 0;
    }

    @keyframes glowTitle {
      0% { 
        color: #ff8c42;
        text-shadow: 0 2px 10px rgba(255, 140, 66, 0.5), 0 0 20px rgba(255, 140, 66, 0.3);
      }
      100% { 
        color: #ffab70;
        text-shadow: 0 2px 15px rgba(255, 171, 112, 0.8), 0 0 30px rgba(255, 171, 112, 0.5);
      }
    }

    /* Botón Volver en la barra superior derecha */
    .btn-volver-top {
      background: #ff8c42;
      color: white;
      border: none;
      padding: 15px 35px;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      font-size: 1.2em;
      text-decoration: none;
      font-family: 'Fredoka', sans-serif;
      transition: all 0.3s ease;
      display: inline-block;
      box-shadow: 0 4px 15px rgba(255, 140, 66, 0.4);
    }

    .btn-volver-top:hover {
      background: #e67a35;
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(255, 140, 66, 0.6);
    }

    .container { 
      max-width: 1100px; 
      margin: 0 auto; 
      position: relative;
    }

    .page-title { 
      text-align: center; 
      font-size: 2.2em; 
      margin: 10px 0 30px; 
      color: #fff;
    }

    .content-card {
      background: rgba(255, 255, 255, 0.95);
      color: #2c3e50;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      display: flex;
      gap: 40px;
      align-items: flex-start;
    }

    .cap-img { 
      width: 360px; 
      border-radius: 10px; 
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2); 
      object-fit: cover;
    }

    .texto { 
      flex: 1; 
      line-height: 1.7;
    }

    .texto h2 { 
      margin-bottom: 10px; 
      color: #213547;
    }

    /* Botones del box blanco - AZULES Y MÁS GRANDES */
    .botones { 
      display: flex; 
      gap: 15px; 
      margin-top: 25px;
      flex-wrap: wrap;
    }

    .btn { 
      background: #3b82f6;
      color: white;
      border: none;
      padding: 18px 35px;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 600;
      font-size: 1.2em;
      text-decoration: none;
      font-family: 'Fredoka', sans-serif;
      transition: all 0.3s ease;
      display: inline-block;
      box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
    }

    .btn:hover { 
      background: #2563eb;
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
    }

    .acordeon { 
      margin-top: 30px;
    }

    .acordeon-toggle { 
      background: #3b82f6;
      color: white;
      padding: 15px 30px;
      border-radius: 10px;
      cursor: pointer;
      display: inline-block;
      font-weight: 600;
      font-size: 1.1em;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
    }

    .acordeon-toggle:hover {
      background: #2563eb;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
    }

    .contenido-glosario { 
      background: rgba(255, 255, 255, 0.95);
      padding: 20px;
      border-radius: 10px;
      margin-top: 15px;
      color: #2c3e50;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .contenido-glosario ul {
      list-style-position: inside;
      line-height: 1.8;
    }

    .contenido-glosario li {
      margin: 8px 0;
    }

    @media (max-width: 900px) {
      .content-card { 
        flex-direction: column;
        padding: 30px;
      }
      
      .cap-img { 
        width: 100%; 
        max-height: 420px;
      }

      .btn-volver-top {
        padding: 12px 25px;
        font-size: 1em;
      }

      .btn {
        padding: 15px 25px;
        font-size: 1.1em;
      }

      .page-title {
        font-size: 1.8em;
      }
    }

    @media (max-width: 600px) {
      .botones {
        flex-direction: column;
      }

      .btn {
        width: 100%;
        text-align: center;
      }
    }
  </style>
</head>
<body>

  <div class="top-bar">
    <div style="display: flex; align-items: center; gap: 12px;">
      <img src="../imagenes/LOGO_BOOK_RUSH.png" alt="Logo Book Rush" style="height: 50px;">
      <h1>Book Rush</h1>
    </div>
    
    <!-- Botón Volver en la parte superior derecha -->
    <a href="../mapa_capitulos/mapa_capitulos.php?id_libro=<?= $id_libro ?>" class="btn-volver-top">
      ← Volver
    </a>
  </div>

  <div class="container">
    <h2 class="page-title"><?= htmlspecialchars($capitulo['titulo']) ?></h2>
    
    <div class="content-card">
      <img class="cap-img" src="../imagenes/capitulo<?= $id_capitulo ?>.jpg" alt="Imagen capítulo <?= $id_capitulo ?>">
      
      <div class="texto">
        <div><?= nl2br(htmlspecialchars($capitulo['contenido'] ?? 'Contenido pendiente...')) ?></div>
        
        <div class="botones">
          <a class="btn" href="../trivia/trivia.php?id_libro=<?= $id_libro ?>&id_capitulo=<?= $id_capitulo ?>&pregunta=1">
            Comenzar con las preguntas
          </a>
          <a class="btn" href="../detalle_libros/detalle_libro.php?id=<?= $id_libro ?>">
            Volver al libro
          </a>
        </div>
      </div>
    </div>

    <div class="acordeon">
      <div class="acordeon-toggle" onclick="toggleGlosario()">
        📖 Mostrar / Ocultar Glosario
      </div>
      <div class="contenido-glosario" id="glosario" style="display: none;">
        <?php if (!empty($glosario_items)): ?>
          <ul>
            <?php foreach ($glosario_items as $item): ?>
              <li><?= htmlspecialchars(trim($item)) ?></li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>No hay términos en el glosario.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script>
    function toggleGlosario() {
      const g = document.getElementById('glosario');
      g.style.display = (g.style.display === 'block') ? 'none' : 'block';
    }
  </script>

</body>
</html>
