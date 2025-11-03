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

// Consulta a la base de datos 
$stmt = $conn->prepare("SELECT titulo, contenido, glosario, imagen FROM capitulos WHERE id_capitulo = ? AND id_libro = ?");
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
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #7ca2c2ff;
      color: #1e334e;
    }
    .volver-x {
      position: absolute;
      top: 10px;
      left: 10px;
      font-size: 24px;
      text-decoration: none;
      color: #3951d8ff;
      background: none;
      border: none;
      cursor: pointer;
    }
    .contenido {
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: flex-start;
      padding: 60px 40px 40px;
      gap: 40px;
    }
    .contenido img {
      width: 800px;
      border-radius: 20px;
      box-shadow: 10px 4px 10px rgba(0,0,0,0.2);
    }
    .texto {
      max-width: 700px;
      line-height: 1.6;
      background: #fff;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .botones {
      display: flex;
      justify-content: flex-end;
      padding: 20px 40px;
    }
    .botones a {
      background-color: #0f6cba;
      color: #fff;
      padding: 12px 20px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s;
    }
    .botones a:hover {
      background-color: #084d8a;
    }
    .acordeon {
      width: 90%;
      max-width: 800px;
      margin: 30px auto;
      background-color: #85c3ecff;
      border-radius: 10px;
      box-shadow: 2px 2px 8px rgba(0,0,0,0.2);
    }
    .acordeon h3 {
      background-color: #39d8d0ff;
      color: white;
      margin: 0;
      padding: 15px;
      cursor: pointer;
      border-radius: 10px 10px 0 0;
      font-size: 18px;
    }
    .contenido-glosario {
      display: none;
      padding: 15px;
      background-color: #fff;
      border-radius: 0 0 10px 10px;
    }
    .contenido-glosario li { margin-bottom: 10px; color: #1e334e; }
    .resaltado { background-color: #f0c64b; font-weight: bold; border-radius: 4px; padding: 0 4px; }
  </style>
</head>
<body>

  <a href="../mapa_capitulos/mapa_capitulos.php?id=<?= $id_libro ?>" class="volver-x">✖</a>

  <div class="contenido">
    <img src="../<?= htmlspecialchars($capitulo['imagen']) ?>" alt="Imagen capítulo <?= $id_capitulo ?>">
    
    <div class="texto">
      <h2><?= htmlspecialchars($capitulo['titulo']) ?></h2>
      <p><?= nl2br(htmlspecialchars($capitulo['contenido'] ?? 'Contenido pendiente...')) ?></p>
    </div>
  </div>

  <div class="acordeon">
    <h3 onclick="toggleGlosario()">Mostrar / Ocultar Glosario</h3>
    <div class="contenido-glosario" id="glosario">
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

  <div class="botones">
    <a href="../trivia/trivia.php?id_libro=<?= $id_libro ?>&id_capitulo=<?= $id_capitulo ?>&pregunta=1">
      Comenzar con las preguntas
    </a>
  </div>

  <script>
    function toggleGlosario() {
      const glosario = document.getElementById('glosario');
      glosario.style.display = (glosario.style.display === 'block') ? 'none' : 'block';
    }
  </script>

</body>
</html>