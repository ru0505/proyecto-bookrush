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
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Fredoka', sans-serif;
      background: linear-gradient(135deg, #353346ff 0%, #2a2a3e 50%, #1e1e2e 100%) fixed;
      background-attachment: fixed;
      min-height: 100vh;
      padding: 130px 20px 40px;
      color: #fff;
    }

    .top-bar {
      width: 100%;
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
      background-size: 200% 200%;
      padding: 25px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
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

    .container {
      max-width: 1100px;
      margin: 0 auto;
    }

    h1.page-title {
      text-align: center;
      color: white;
      font-size: 2.2em;
      margin-bottom: 30px;
    }

    .detalle-container {
      display: flex;
      gap: 40px;
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.3);
      align-items: flex-start;
    }

    .detalle-container img {
      width: 300px;
      height: 400px;
      object-fit: cover;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }

    .detalle-texto {
      flex: 1;
    }

    .detalle-texto h2 {
      color: #2c3e50;
      font-size: 1.5em;
      margin-bottom: 15px;
    }

    .detalle-texto p {
      color: #555;
      line-height: 1.8;
      margin-bottom: 15px;
      font-size: 1em;
    }

    .detalle-texto strong {
      color: #2c3e50;
      font-weight: 600;
    }

    .botones {
      margin-top: 25px;
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
    }

    .botones a, .botones button {
      background: #ff8c42;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      font-size: 1em;
      text-decoration: none;
      font-family: 'Fredoka', sans-serif;
      transition: all 0.3s ease;
      display: inline-block;
    }

    .botones a:hover, .botones button:hover {
      background: #e67a35;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
    }

    .btn-secondary {
      background: #6c757d !important;
    }

    .btn-secondary:hover {
      background: #5a6268 !important;
      box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
    }

    .btn-disabled {
      background: #ccc !important;
      cursor: not-allowed;
      opacity: 0.6;
    }

    .btn-disabled:hover {
      transform: none;
      box-shadow: none;
    }

    @media (max-width: 768px) {
      .detalle-container {
        flex-direction: column;
      }

      .detalle-container img {
        width: 100%;
        max-width: 350px;
      }

      .botones {
        justify-content: center;
      }
    }
  </style>
</head>
<body>
  <!-- Top Bar -->
  <div class="top-bar">
    <div style="display: flex; align-items: center;">
      <img src="../imagenes/LOGO_BOOK_RUSH.png" alt="Logo Book Rush" style="height: 50px; margin-right: 10px;">
      <h1>Book Rush</h1>
    </div>
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

          <a href="../index.php" class="btn-secondary">
            Volver
          </a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
