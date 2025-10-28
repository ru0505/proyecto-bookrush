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
  <link rel="stylesheet" href="../css/contenido_capitulo.css">
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
