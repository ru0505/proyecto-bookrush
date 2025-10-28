<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$dni = $_SESSION['dni'];
$id_libro = isset($_GET['id_libro']) ? intval($_GET['id_libro']) : 1;
$capitulo = isset($_GET['id_capitulo']) ? intval($_GET['id_capitulo']) : 1;

// Obtener puntaje total del usuario para este libro y capítulo
$stmt = $conn->prepare("
    SELECT SUM(puntaje) as total 
    FROM puntajes 
    WHERE dni = ? AND id_libro = ? AND capitulo = ?
");
$stmt->bind_param("sii", $dni, $id_libro, $capitulo);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$puntaje_total = intval($row['total']);
if ($puntaje_total > 100) $puntaje_total = 100;

// Mensaje según rendimiento
if ($puntaje_total >= 80) {
    $mensaje = "¡Excelente trabajo! Sigue así.";
} elseif ($puntaje_total >= 60) {
    $mensaje = "Buen esfuerzo. Puedes mejorar aún más.";
} else {
    $mensaje = "Necesitas repasar un poco más.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Resultado - Capítulo <?= $capitulo ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
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
    }
    
    .card {
      background: rgba(255, 255, 255, 0.95);
      color: #213547;
      padding: 50px 40px;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
      text-align: center;
    }
    
    h1.page-title {
      font-size: 2.5em;
      margin-bottom: 20px;
      color: #1e3a8a;
    }
    
    h2.score {
      font-size: 4em;
      margin: 15px 0;
      color: #ff8c42;
      text-shadow: 0 2px 10px rgba(255, 140, 66, 0.3);
    }
    
    p.message {
      font-size: 1.4em;
      color: #213547;
      margin-bottom: 35px;
      font-weight: 600;
    }
    
    .actions {
      display: flex;
      gap: 18px;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 25px;
    }
    
    /* Botones azules más grandes */
    .btn {
      background: #3b82f6;
      color: white;
      padding: 18px 35px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: 700;
      font-size: 1.2em;
      display: inline-block;
      font-family: 'Fredoka', sans-serif;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
      border: none;
      cursor: pointer;
    }
    
    .btn:hover {
      background: #2563eb;
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
    }

    @media (max-width: 768px) {
      .card {
        padding: 35px 25px;
      }
      
      .top-bar {
        padding: 20px 20px;
      }
      
      body {
        padding: 120px 15px 30px;
      }

      h1.page-title {
        font-size: 2em;
      }

      h2.score {
        font-size: 3em;
      }

      p.message {
        font-size: 1.2em;
      }

      .btn {
        padding: 15px 25px;
        font-size: 1.1em;
        width: 100%;
      }

      .btn-volver-top {
        padding: 12px 25px;
        font-size: 1em;
      }

      .actions {
        flex-direction: column;
        gap: 12px;
      }
    }
</style>
</head>
<body>
    <div class="top-bar">
        <div style="display: flex; align-items: center; gap: 12px;">
            <img src="imagenes/LOGO_BOOK_RUSH.png" alt="Logo" style="height: 50px;">
            <h1>Book Rush</h1>
        </div>
        
        <!-- Botón Volver al Menú Principal en la parte superior derecha -->
        <a href="index.php" class="btn-volver-top">
          ← Menú Principal
        </a>
    </div>

    <div class="container">
        <div class="card">
            <h1 class="page-title">Capítulo <?= $capitulo ?></h1>
            <h2 class="score"><?= $puntaje_total ?>/100</h2>
            <p class="message"><?= htmlspecialchars($mensaje) ?></p>
            
            <div class="actions">
                <?php if ($puntaje_total >= 60): ?>
                    <a class="btn" href="pregunta/preguntas_frank.php?capitulo=<?= $capitulo + 1 ?>">
                      Siguiente capítulo
                    </a>
                <?php endif; ?>

                <a class="btn" href="reiniciar_capitulo.php?id_capitulo=<?= $capitulo ?>&id_libro=<?= $id_libro ?>">
                  Reintentar capítulo
                </a>
                
                <a class="btn" href="mapa_capitulos/mapa_capitulos.php?id_libro=<?= $id_libro ?>">
                  Volver a capítulos
                </a>
            </div>
        </div>
    </div>
</body>
</html>
