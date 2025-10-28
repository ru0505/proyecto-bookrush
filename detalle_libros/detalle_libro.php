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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($libro['titulo']) ?> - Book Rush</title>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/detalle_libro.css">
</head>
<body>
  <!-- Top Bar -->
  <div class="top-bar">
    <div style="display: flex; align-items: center;">
      <img src="../imagenes/LOGO_BOOK_RUSH.png" alt="Logo Book Rush" style="height: 50px; margin-right: 10px;">
      <h1>Book Rush</h1>
    </div>
    
    <!-- Botón Volver en la parte superior derecha -->
    <a href="../index.php" class="btn-volver-top">
      ← Volver
    </a>
  </div>

  <div class="container">
    <h1 class="page-title"><?= htmlspecialchars($libro['titulo']) ?></h1>
    
    <div class="detalle-container">
      <img src="../imagenes/<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>">
      
      <div class="detalle-texto">
        <h2><?= htmlspecialchars($libro['AUTOR']) ?></h2>
        <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($libro['descripcion'])) ?></p>
        
        <?php if (!empty($libro['resumen'])): ?>
          <p><strong>Resumen:</strong><br><?= nl2br(htmlspecialchars($libro['resumen'])) ?></p>
        <?php endif; ?>

        <?php if (!empty($libro['personajes'])): ?>
          <p><strong>Personajes:</strong><br><?= nl2br(htmlspecialchars($libro['personajes'])) ?></p>
        <?php endif; ?>

        <div class="botones">
          <a href="../mapa_capitulos/mapa_capitulos.php?id_libro=<?= $libro['id_libro'] ?>">
            Capítulos con preguntas
          </a>

          <?php if (!empty($libro['archivo'])): ?>
            <a href="../libros/<?= htmlspecialchars($libro['archivo']) ?>" target="_blank">
              Descargar PDF
            </a>
          <?php else: ?>
            <span class="btn-disabled">PDF no disponible</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
