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
    * { box-sizing: border-box; margin:0; padding:0; }
    body {
      font-family: 'Fredoka', sans-serif;
      background: linear-gradient(135deg, #353346ff 0%, #2a2a3e 50%, #1e1e2e 100%) fixed;
      background-attachment: fixed;
      color: #fff;
      min-height: 100vh;
      padding: 130px 20px 40px; /* espacio mayor por top-bar más gruesa */
    }

    .top-bar {
      width: 100%;
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
      padding: 25px 30px; /* más gruesa */
      display:flex; align-items:center; justify-content:space-between;
      position: fixed; top:0; left:0; z-index:1100;
      box-shadow:0 8px 32px rgba(30,58,138,0.3); backdrop-filter: blur(10px);
      border-bottom:1px solid rgba(255,255,255,0.08);
      min-height:100px;
    }
    .top-bar h1{ font-size:35px; color:#ff8c42; text-shadow:0 2px 10px rgba(255,140,66,0.45); margin:0 }

    .container { max-width:1100px; margin:0 auto; position:relative }

    /* Back button fixed at left edge */
    .volver-x {
      position: fixed; left: 8px; top: 120px; /* colocado debajo de la top-bar más alta */
      background:#ff8c42; color:#fff; border-radius:8px; padding:10px 14px;
      text-decoration:none; font-weight:700; z-index:1110; box-shadow:0 6px 16px rgba(0,0,0,0.25);
    }

    .page-title { text-align:center; font-size:2.2em; margin: 10px 0 24px; color:#fff }

    .content-card {
      background: rgba(255,255,255,0.95);
      color:#2c3e50;
      padding: 36px;
      border-radius: 15px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
      display:flex; gap:40px; align-items:flex-start;
    }

    .cap-img { width:360px; border-radius:10px; box-shadow:0 6px 18px rgba(0,0,0,0.2); object-fit:cover }

    .texto { flex:1; line-height:1.7 }
    .texto h2{ margin-bottom:10px; color:#213547 }

    .botones { display:flex; gap:12px; margin-top:18px }
    .btn { background:#ff8c42; color:#fff; padding:12px 20px; border-radius:8px; text-decoration:none; font-weight:700 }
    .btn:hover{ background:#e67a35; transform:translateY(-2px); box-shadow:0 8px 20px rgba(230,122,53,0.25) }

    .acordeon { margin-top:26px }
    .acordeon-toggle { background:#f1f5f9; color:#213547; padding:10px 14px; border-radius:8px; cursor:pointer; display:inline-block }
    .contenido-glosario { background:#fff; padding:14px; border-radius:8px; margin-top:10px; color:#213547 }

    @media (max-width:900px){
      .content-card{ flex-direction:column; }
      .cap-img{ width:100%; max-height:420px }
      .volver-x{ left:6px; top:74px }
    }
  </style>
</head>
<body>

  <div class="top-bar">
    <div style="display:flex;align-items:center;gap:12px;">
      <img src="../imagenes/LOGO_BOOK_RUSH.png" alt="Logo" style="height:44px">
      <h1>Book Rush</h1>
    </div>
  </div>

  <a href="../mapa_capitulos/mapa_capitulos.php?id=<?= $id_libro ?>" class="volver-x">← Volver</a>

  <div class="container">
    <h2 class="page-title"><?= htmlspecialchars($capitulo['titulo']) ?></h2>

      <div class="content-card">
      <img class="cap-img" src="../imagenes/capitulo<?= $id_capitulo ?>.jpg" alt="Imagen capítulo <?= $id_capitulo ?>">

      <div class="texto">
        <div><?= nl2br(htmlspecialchars($capitulo['contenido'] ?? 'Contenido pendiente...')) ?></div>

        <div class="botones">
          <a class="btn" href="../trivia/trivia.php?id_libro=<?= $id_libro ?>&id_capitulo=<?= $id_capitulo ?>&pregunta=1">Comenzar con las preguntas</a>
          <a class="btn" href="../detalle_libros/detalle_libro.php?id=<?= $id_libro ?>">Volver al libro</a>
        </div>
      </div>
    </div>

    <div class="acordeon">
      <div class="acordeon-toggle" onclick="toggleGlosario()">Mostrar / Ocultar Glosario</div>
      <div class="contenido-glosario" id="glosario" style="display:none;">
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
    function toggleGlosario(){
      const g = document.getElementById('glosario');
      g.style.display = (g.style.display === 'block') ? 'none' : 'block';
    }
  </script>

</body>
</html>