<?php
include '../conexion.php';
session_start();

// Verificar si se pasó el ID
if (!isset($_GET['id'])) {
    die("Libro no especificado");
}

$id = intval($_GET['id']);

// Buscar el libro en la base de datos
$stmt = $conn->prepare("SELECT * FROM libros WHERE id_libro = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Libro no encontrado");
}

$libro = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($libro['titulo']) ?> - Detalle</title>

  <link href="https://fonts.cdnfonts.com/css/sergio-trendy" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(to right, #5147aa, #69aae7);
      color: #1d2938;
      font-family: 'Fredoka', sans-serif;
      padding: 40px;
    }

    h1 {
      text-align: center;
      color: #000000;
      font-size: 2.5em;
      margin-bottom: 40px;
      font-family: 'Sergio Trendy', sans-serif;
    }

    .detalle-container {
      display: flex;
      align-items: flex-start;
      justify-content: center;
      flex-wrap: wrap;
      background-color: #fffef8;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(30, 51, 78, 0.1);
      padding: 30px;
      max-width: 1000px;
      margin: 0 auto;
      gap: 30px;
    }

    .detalle-container img {
      width: 320px;
      height: 420px;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(30, 51, 78, 0.1);
    }

    .detalle-texto {
      flex: 1;
      min-width: 300px;
    }

    .detalle-texto h2 {
      font-size: 1.3em;
      color: #1e334e;
      margin-bottom: 10px;
    }

    .detalle-texto p {
      font-size: 0.95em;
      color: #333;
      line-height: 1.6;
      margin-bottom: 10px;
    }

    .botones {
      margin-top: 20px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .botones a {
      background-color: #1c2853;
      color: #e2e2e2;
      border: none;
      padding: 10px 14px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      font-size: 0.95em;
      text-decoration: none;
      text-align: center;
      transition: background-color 0.3s;
    }

    .botones a:hover {
      background-color: #262d4e;
    }

    .volver {
      background-color: rgba(198, 203, 218, 1);
      color: black !important;
    }

    .volver:hover {
      background-color: #e7ecf0;
    }

    @media (max-width: 768px) {
      .detalle-container {
        flex-direction: column;
        text-align: center;
        align-items: center;
      }

      .detalle-container img {
        width: 100%;
        max-width: 400px;
        height: auto;
      }

      .detalle-texto {
        text-align: center;
      }

      .botones {
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <h1><?= htmlspecialchars($libro['titulo']) ?></h1>

  <div class="detalle-container">
    <img src="../imagenes/<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>">

    <div class="detalle-texto">
      <h2>Autor: <?= htmlspecialchars($libro['AUTOR']) ?></h2>
      <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($libro['descripcion'])) ?></p>

      <?php if (!empty($libro['resumen'])): ?>
        <p><strong>Resumen:</strong> <?= nl2br(htmlspecialchars($libro['resumen'])) ?></p>
      <?php endif; ?>

      <?php if (!empty($libro['personajes'])): ?>
        <p><strong>Personajes:</strong> <?= nl2br(htmlspecialchars($libro['personajes'])) ?></p>
      <?php endif; ?>

      <div class="botones">
        <a href="../mapa_capitulos/mapa_capitulos.php?id_libro=<?= $libro['id_libro'] ?>">🧩 Capítulos con preguntas</a>

        <?php if (!empty($libro['libro_pdf'])): ?>
          <a href="../pdf/<?= htmlspecialchars($libro['libro_pdf']) ?>" target="_blank">📘 Descargar libro completo</a>
        <?php else: ?>
          <a href="#" style="background-color: #ccc; pointer-events: none;">📘 PDF no disponible</a>
        <?php endif; ?>

        <a href="../index.php" class="volver">⬅️ Volver</a>
      </div>
    </div>
  </div>
</body>
</html>
