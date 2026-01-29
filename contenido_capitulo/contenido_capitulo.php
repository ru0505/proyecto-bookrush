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

// Procesar el glosario
$glosario_items = [];
if (!empty($capitulo['glosario'])) {
    $glosario_decode = json_decode($capitulo['glosario'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($glosario_decode)) {
        $glosario_items = $glosario_decode;
    } else {
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
  
  <style>
  /* Estilos del Modal */
  .modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.6);
    z-index: 1200;
    justify-content: center;
    align-items: center;
    padding: 20px;
    padding-top: 140px;
    overflow-y: auto;
  }

  .modal-content {
    background-color: white;
    border-radius: 20px;
    padding: 45px 40px;
    max-width: 550px;
    width: 100%;
    box-shadow: 0 20px 60px rgba(0,0,0,0.4);
    text-align: center;
    max-height: calc(100vh - 160px);
    overflow-y: auto;
    margin: auto;
    position: relative; /* Necesario para posicionar la X */
    animation: modalSlideIn 0.4s ease-out;
  }

  /* Estilo para la X de cerrar */
  .close-modal {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 28px;
    font-weight: bold;
    color: #aaa;
    cursor: pointer;
    transition: color 0.3s;
    line-height: 1;
  }

  .close-modal:hover {
    color: #e74c3c;
  }

  @keyframes modalSlideIn {
    0% { opacity: 0; transform: translateY(-30px) scale(0.95); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
  }

  .modal-emoji { font-size: 50px; margin-bottom: 20px; }
  .modal-title { color: #4a5568; margin-bottom: 15px; font-size: 1.8em; }
  .modal-subtitle { color: #718096; margin-bottom: 25px; line-height: 1.6; }
  
  .modal-box { padding: 20px; margin-bottom: 25px; text-align: left; border-radius: 8px; }
  .modal-requisitos { background-color: #fff5f0; border-left: 4px solid #ff8c42; }
  .modal-advertencia { background-color: #fff0f0; border-left: 4px solid #e74c3c; padding: 15px; }
  
  .modal-box-title { font-weight: 600; margin-bottom: 12px; }
  .modal-requisitos .modal-box-title { color: #ff8c42; }
  .modal-advertencia .modal-box-title { color: #e74c3c; margin-bottom: 8px; }
  
  .modal-list { color: #4a5568; margin: 0; padding-left: 20px; line-height: 1.8; }
  .modal-list strong { color: #ff8c42; }
  
  .modal-advertencia-text { color: #4a5568; font-size: 0.95em; margin: 0; line-height: 1.6; }
  .modal-note { color: #a0aec0; font-size: 0.9em; margin-bottom: 25px; }
  
  .modal-buttons { display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
  
  .modal-btn {
    border: none; padding: 12px 30px; border-radius: 8px; font-size: 1em;
    cursor: pointer; font-weight: 600; transition: all 0.3s ease;
  }
  /* .modal-btn-secondary ya no se usa, pero se puede dejar por si acaso */
  .modal-btn-secondary { background-color: #e2e8f0; color: #4a5568; }
  .modal-btn-secondary:hover { background-color: #cbd5e0; }
  .modal-btn-primary { background-color: #ff8c42; color: white; }
  .modal-btn-primary:hover { background-color: #e67a35; }

  @media (max-width: 768px) {
    .modal-overlay { padding-top: 120px; }
    .modal-content { padding: 30px 20px; max-width: 95%; max-height: calc(100vh - 140px); }
    .modal-btn { width: 100%; }
  }
  </style>
</head>
<body>

  <div class="top-bar">
    <a href="../index.php" style="display: flex; align-items: center; gap: 12px; text-decoration: none; color: inherit;">
      <img src="../imagenes/lecturin/lecturin_saltando.png" alt="Logo Book Rush" style="height: 70px;">
      <h1>Book Rush</h1>
    </a>
    
    <a href="../mapa_capitulos/mapa_capitulos.php?id_libro=<?= $id_libro ?>" class="btn-volver-top">
      ← Volver
    </a>
  </div>

  <div class="container">
    <h2 class="page-title"><?= htmlspecialchars($capitulo['titulo']) ?></h2>
    
    <div class="layout-principal">
        
        <div class="content-card">
          <img class="cap-img" src="../<?= htmlspecialchars($capitulo['imagen'] ?? "imagenes/capitulo{$id_capitulo}.jpg") ?>" alt="Imagen capítulo <?= $id_capitulo ?>">
          
          <div class="texto">
            <div class="texto-contenido">
                <?php 
                    // 1. Obtener texto seguro
                    $texto = htmlspecialchars($capitulo['contenido'] ?? 'Contenido pendiente...');

                    // 2. Reemplazar *palabra* por <strong>palabra</strong>
                    // El color #d35400 es un naranja oscuro, puedes cambiarlo si gustas.
                    $texto_formateado = preg_replace('/\*(.*?)\*/', '<strong style="color: #d35400;">$1</strong>', $texto);
                    
                    // 3. Imprimir con saltos de línea
                    echo nl2br($texto_formateado);
                ?>
            </div>
            
            <div class="botones">
              <a class="btn" href="#" onclick="mostrarModal(); return false;">
                Comenzar con las preguntas
              </a>
              <a class="btn" href="../detalle_libros/detalle_libro.php?id=<?= $id_libro ?>">
                Volver al libro
              </a>
            </div>
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

    function mostrarModal() {
      document.getElementById('modalInstrucciones').style.display = 'flex';
    }

    function cerrarModal() {
      document.getElementById('modalInstrucciones').style.display = 'none';
    }

    function iniciarTrivia() {
      window.location.href = '../trivia/trivia.php?id_libro=<?= $id_libro ?>&id_capitulo=<?= $id_capitulo ?>&pregunta=1';
    }
  </script>

  <div id="modalInstrucciones" class="modal-overlay">
    <div class="modal-content">
      <span class="close-modal" onclick="cerrarModal()">&times;</span>

      <div class="modal-emoji">🎯</div>
      <h2 class="modal-title">Antes de comenzar</h2>
      <p class="modal-subtitle">
        Lee atentamente el contenido del capítulo y<br>prepárate para la trivia
      </p>
      
      <div class="modal-box modal-requisitos">
        <p class="modal-box-title">Requisitos para pasar:</p>
        <ul class="modal-list">
          <li>Necesitas al menos 4/5 aciertos correctos para pasar al siguiente capítulo.</li>
        </ul>
      </div>

      <div class="modal-box modal-advertencia">
        <p class="modal-box-title">⚠️ Advertencia:</p>
        <p class="modal-advertencia-text">
          Si fallas <strong>3 veces todo el quiz</strong>, serás bloqueado por <strong>2 minutos</strong>.
        </p>
      </div>

      <p class="modal-note">
        Si no logras el puntaje, podrás reintentar el capítulo.
      </p>

      <div class="modal-buttons">
        <button onclick="iniciarTrivia()" class="modal-btn modal-btn-primary">
          Comenzar trivia →
        </button>
      </div>
    </div>
  </div>

</body>
</html>