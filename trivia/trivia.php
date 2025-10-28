<?php
session_start();
include("../conexion.php");

// 🔹 Parámetros dinámicos
$id_libro = isset($_GET['id_libro']) ? (int)$_GET['id_libro'] : 1;
$id_capitulo = isset($_GET['id_capitulo']) ? (int)$_GET['id_capitulo'] : 1;
$numero_pregunta = isset($_GET['pregunta']) ? (int)$_GET['pregunta'] : 1;

// 🔹 Obtener la pregunta actual
$stmt = $conn->prepare("SELECT * FROM preguntas WHERE id_libro = ? AND id_capitulo = ? AND numero_pregunta = ?");
$stmt->bind_param("iii", $id_libro, $id_capitulo, $numero_pregunta);
$stmt->execute();
$pregunta = $stmt->get_result()->fetch_assoc();

if (!$pregunta) {
    die("No hay preguntas para este capítulo.");
}

// 🔹 Opciones
$opciones = [
    'A' => $pregunta['opcion_a'],
    'B' => $pregunta['opcion_b'],
    'C' => $pregunta['opcion_c'],
    'D' => $pregunta['opcion_d']
];

// 🔹 Última pregunta
$stmt2 = $conn->prepare("SELECT MAX(numero_pregunta) AS max_preg FROM preguntas WHERE id_libro = ? AND id_capitulo = ?");
$stmt2->bind_param("ii", $id_libro, $id_capitulo);
$stmt2->execute();
$ultima_pregunta = (int)$stmt2->get_result()->fetch_assoc()['max_preg'];

// 🔹 Procesar respuesta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuesta = $_POST['respuesta'] ?? "";
    $correcta = ($respuesta === $pregunta['respuesta_correcta']);
    
    if ($correcta) {
        $puntaje = $pregunta['puntaje'] ?? 20;

        $stmt3 = $conn->prepare("
            INSERT INTO puntajes (dni, id_libro, capitulo, id_pregunta, puntaje)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE puntaje = VALUES(puntaje)
        ");
        $stmt3->bind_param("siiii", $_SESSION['dni'], $id_libro, $id_capitulo, $pregunta['id_pregunta'], $puntaje);
        $stmt3->execute();
    }

    $_SESSION['respuestas'][$id_capitulo][$numero_pregunta] = [
        'respuesta' => $respuesta,
        'correcta' => $correcta
    ];

    echo json_encode([
        "status" => "ok",
        "es_correcta" => $correcta,
        "siguiente" => $numero_pregunta < $ultima_pregunta ? $numero_pregunta + 1 : 0
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pregunta <?= $numero_pregunta ?> - Capítulo <?= $id_capitulo ?></title>
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
      height: 100vh;
      overflow: hidden;
      display: flex;
      flex-direction: column;
    }

    .top-bar {
      width: 100%;
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
      background-size: 200% 200%;
      padding: 22px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 1100;
      box-shadow: 0 8px 32px rgba(30, 58, 138, 0.3);
      backdrop-filter: blur(10px);
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      animation: slideDown 0.8s ease-out, topBarMove 6s ease-in-out infinite;
      flex-shrink: 0;
    }

    @keyframes slideDown {
      0% { transform: translateY(-100%); opacity: 0; }
      100% { transform: translateY(0); opacity: 1; }
    }

    @keyframes topBarMove {
      0%, 100% { 
        background-position: 0% 50%;
      }
      50% { 
        background-position: 100% 50%;
      }
    }

    .top-bar h1 {
      font-size: 30px;
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
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 25px;
      max-width: 1150px;
      margin: 0 auto;
      width: 100%;
      overflow: hidden;
    }

    /* Timer */
    .timer {
      text-align: center;
      color: #ff8c42;
      font-weight: 700;
      margin-bottom: 22px;
      font-size: 2em;
      text-shadow: 0 4px 15px rgba(255, 140, 66, 0.6);
      animation: pulse 1s ease-in-out infinite;
      background: rgba(255, 140, 66, 0.1);
      padding: 15px 35px;
      border-radius: 12px;
      border: 2px solid rgba(255, 140, 66, 0.3);
    }

    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }

    /* Pregunta */
    .pregunta-card {
      background: rgba(255, 255, 255, 0.95);
      color: #2c3e50;
      padding: 30px 40px;
      border-radius: 15px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
      margin-bottom: 28px;
      text-align: center;
      width: 100%;
    }

    .pregunta {
      font-size: 1.5em;
      font-weight: 600;
      line-height: 1.4;
      color: #1e3a8a;
    }

    /* Opciones */
    .opciones {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      width: 100%;
      max-width: 950px;
    }

    .opcion {
      background: linear-gradient(135deg, #ff8c42 0%, #ff7425 100%);
      color: white;
      border: none;
      border-radius: 12px;
      padding: 24px 30px;
      font-weight: 700;
      cursor: pointer;
      font-size: 1.2em;
      transition: all 0.3s ease;
      text-align: left;
      box-shadow: 0 6px 20px rgba(255, 140, 66, 0.4);
      font-family: 'Fredoka', sans-serif;
      line-height: 1.4;
      min-height: 95px;
      display: flex;
      align-items: center;
    }

    .opcion:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 12px 30px rgba(255, 140, 66, 0.6);
      background: linear-gradient(135deg, #ff9d5c 0%, #ff8535 100%);
    }

    .opcion:active {
      transform: translateY(-2px) scale(0.98);
    }

    #resultado {
      text-align: center;
      margin-top: 20px;
      font-weight: 800;
      font-size: 2.2em;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
      padding: 20px;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 2000;
      min-width: 320px;
    }

    /* Indicador de progreso */
    .progreso {
      text-align: center;
      color: #60a5fa;
      font-size: 1.3em;
      font-weight: 600;
      margin-bottom: 18px;
      text-shadow: 0 2px 8px rgba(96, 165, 250, 0.4);
    }

    @media (max-width: 900px) {
      .opciones {
        grid-template-columns: 1fr;
        gap: 16px;
      }

      .opcion {
        text-align: center;
        padding: 20px 28px;
        font-size: 1.05em;
        min-height: 75px;
      }

      .pregunta-card {
        padding: 25px 30px;
      }

      .pregunta {
        font-size: 1.3em;
      }

      .timer {
        font-size: 1.6em;
        padding: 12px 25px;
      }

      #resultado {
        font-size: 1.8em;
        padding: 15px;
        min-width: 280px;
      }
    }

    @media (max-width: 600px) {
      .timer {
        font-size: 1.4em;
        padding: 10px 20px;
      }

      .pregunta {
        font-size: 1.15em;
      }

      .opcion {
        font-size: 1em;
        padding: 18px 22px;
        min-height: 70px;
      }

      .progreso {
        font-size: 1.1em;
      }

      .top-bar h1 {
        font-size: 26px;
      }
    }
  </style>
</head>
<body>

  <div class="top-bar">
    <div style="display: flex; align-items: center; gap: 12px;">
      <img src="../imagenes/LOGO_BOOK_RUSH.png" alt="Logo" style="height: 48px;">
      <h1>Book Rush</h1>
    </div>
  </div>

  <div class="container">
    <div class="progreso">Pregunta <?= $numero_pregunta ?> de <?= $ultima_pregunta ?></div>
    
    <div class="timer" id="timer">Tiempo: 12 segundos</div>
    
    <div class="pregunta-card">
      <div class="pregunta"><?= $numero_pregunta ?>. <?= htmlspecialchars($pregunta['enunciado']) ?></div>
    </div>
    
    <div class="opciones">
    <?php foreach ($opciones as $letra => $texto): ?>
      <button type="button" class="opcion" data-respuesta="<?= $letra ?>">
        <?= $letra ?>) <?= htmlspecialchars($texto) ?>
      </button>
    <?php endforeach; ?>
    </div>

    <div id="resultado" style="display: none;"></div>
  </div>

  <script>
  const botones = document.querySelectorAll(".opcion");
  const resultado = document.getElementById("resultado");
  let respondido = false;
  let tiempo = 12;

  const countdown = setInterval(() => {
      tiempo--;
      document.getElementById("timer").textContent = "Tiempo: " + tiempo + " segundos";
      if (tiempo <= 0 && !respondido) {
          respondido = true;
          clearInterval(countdown);
          enviarRespuesta("");
      }
  }, 1000);

  botones.forEach(boton => {
      boton.addEventListener("click", () => {
          if (respondido) return;
          respondido = true;
          clearInterval(countdown);
          enviarRespuesta(boton.dataset.respuesta);
      });
  });

  function enviarRespuesta(respuesta) {
      fetch("", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "respuesta=" + encodeURIComponent(respuesta)
      })
      .then(res => {
          if (!res.ok) throw new Error('Error en la respuesta del servidor');
          return res.json();
      })
      .then(data => {
          if (data.status === "ok") {
              resultado.style.display = "block";
              if (data.es_correcta) {
                  resultado.textContent = "✅ ¡Correcto!";
                  resultado.style.color = "#2ecc71";
              } else if (respuesta === "") {
                  resultado.textContent = "⏰ Tiempo agotado";
                  resultado.style.color = "#95a5a6";
              } else {
                  resultado.textContent = "❌ Incorrecto";
                  resultado.style.color = "#e74c3c";
              }

              setTimeout(() => {
                  if (data.siguiente > 0) {
                      window.location.href = "?id_libro=<?= $id_libro ?>&id_capitulo=<?= $id_capitulo ?>&pregunta=" + data.siguiente;
                  } else {
                      window.location.href = "../total.php?id_libro=<?= $id_libro ?>&id_capitulo=<?= $id_capitulo ?>";
                  }
              }, 1500);
          }
      })
      .catch(error => { 
          console.error('Error:', error); 
          alert("Error al conectar con el servidor: " + error.message); 
      });
  }
  </script>

</body>
</html>
