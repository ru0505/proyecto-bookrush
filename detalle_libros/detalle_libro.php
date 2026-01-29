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
  <link rel="stylesheet" href="../css/detalle_libro.css?v=<?= time() ?>">
</head>
<body>
  <div class="top-bar">
    <a href="../index.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
      <img src="../imagenes/lecturin/lecturin_saltando.png" alt="Logo Book Rush" style="height: 70px; margin-right: 10px;">
      <h1>Book Rush</h1>
    </a>
    
    <a href="../index.php" class="btn-volver-top">
      ← Volver
    </a>
  </div>

  <div class="container">
    <h1 class="page-title"><?= htmlspecialchars($libro['titulo']) ?></h1>
    
    <div class="detalle-container">
      <img src="../<?= htmlspecialchars($libro['imagen2']) ?>" alt="<?= htmlspecialchars($libro['titulo']) ?>">
      
      <div class="detalle-texto">
        <h2><?= htmlspecialchars($libro['AUTOR']) ?></h2>
        
        <p><strong>Descripción:</strong><br>
        <?php 
            $descripcion = htmlspecialchars($libro['descripcion']);
            $descripcion_formateada = preg_replace('/\*(.*?)\*/', '<strong style="color: #d35400;">$1</strong>', $descripcion);
            echo nl2br($descripcion_formateada);
        ?>
        </p>
        
        <?php if (!empty($libro['resumen'])): ?>
          <p><strong>Resumen:</strong><br>
          <?php 
              $resumen = htmlspecialchars($libro['resumen']);
              $resumen_formateado = preg_replace('/\*(.*?)\*/', '<strong style="color: #d35400;">$1</strong>', $resumen);
              echo nl2br($resumen_formateado);
          ?>
          </p>
        <?php endif; ?>

        <?php if (!empty($libro['personajes'])): ?>
          <p><strong>Personajes:</strong></p>
          <ul style="list-style: none; padding-left: 0; margin-top: 10px;">
          <?php 
             // Limpiar y normalizar el texto de personajes
             $personajes_texto = $libro['personajes'];
             
             // Primero intentar separar por saltos de línea
             $personajes_array = preg_split('/\r\n|\r|\n/', $personajes_texto);
             
             // Si no hay saltos de línea, intentar separar por comas
             if (count($personajes_array) == 1) {
                 $personajes_array = explode(',', $personajes_texto);
             }
             
             // Si no hay comas, intentar separar por guiones al inicio de línea
             if (count($personajes_array) == 1) {
                 $personajes_array = preg_split('/\s*-\s*/', $personajes_texto);
             }
             
             foreach ($personajes_array as $personaje) {
                 $personaje = trim($personaje);
                 // Eliminar guiones o asteriscos al inicio
                 $personaje = preg_replace('/^[\-\*•]\s*/', '', $personaje);
                 $personaje = trim($personaje);
                 
                 if (!empty($personaje)) {
                     // Aplicar formato de negrita con color naranja a texto entre asteriscos
                     $personaje_escapado = htmlspecialchars($personaje);
                     $personaje_formateado = preg_replace('/\*(.*?)\*/', '<strong style="color: #d35400;">$1</strong>', $personaje_escapado);
                     
                     echo '<li style="margin-bottom: 8px; padding-left: 20px; position: relative;">';
                     echo '<span style="position: absolute; left: 0;">•</span>';
                     echo $personaje_formateado;
                     echo '</li>';
                 }
             }
          ?>
          </ul>
        <?php endif; ?>

        <div class="botones">
          <a href="../mapa_capitulos/mapa_capitulos.php?id_libro=<?= $libro['id_libro'] ?>">
            Capítulos con preguntas
          </a>

          <?php if (!empty($libro['archivo'])): ?>
            <a href="../libros/<?= htmlspecialchars($libro['archivo']) ?>" target="_blank">
              Descargar libro
            </a>
          <?php else: ?>
            <span class="btn-disabled">Descarga disponible próximamente</span>
          <?php endif; ?>
          
        </div>
      </div>
    </div>
  </div>
</body>
</html>