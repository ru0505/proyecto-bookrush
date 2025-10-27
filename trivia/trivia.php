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
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin:0; padding:0 }
    body {
      font-family: 'Fredoka', sans-serif;
      background: linear-gradient(135deg, #353346ff 0%, #2a2a3e 50%, #1e1e2e 100%) fixed;
      color: #fff;
      min-height: 100vh;
      padding: 130px 20px 40px;
    }

    .top-bar {
      width: 100%;
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
      padding: 25px 30px;
      display:flex; align-items:center; justify-content:space-between;
      position: fixed; top:0; left:0; z-index:1100;
      box-shadow:0 8px 32px rgba(30,58,138,0.3); backdrop-filter: blur(10px);
      border-bottom:1px solid rgba(255,255,255,0.08);
      min-height:100px;
    }
    .top-bar h1{ font-size:35px; color:#ff8c42; text-shadow:0 2px 10px rgba(255,140,66,0.45); margin:0 }

    .container { max-width:1100px; margin:0 auto }

    .timer { text-align:center; color:#ff8c42; font-weight:700; margin-bottom:14px; font-size:1.2em }
    .pregunta { text-align:center; font-size:1.3em; margin: 8px 0 22px }

    .opciones { display:flex; gap:18px; justify-content:center; flex-wrap:wrap }
    .opcion {
      background:#ffd265; color:#1e334e; border:3px solid #213547; border-radius:12px;
      padding:18px 30px; min-width: 220px; font-weight:700; cursor:pointer; font-size:1em;
      transition: transform .15s ease, box-shadow .15s ease; text-align:left;
    }
    .opcion:hover { transform: translateY(-4px); box-shadow:0 10px 20px rgba(0,0,0,0.15) }

    #resultado { text-align:center; margin-top:20px; font-weight:800 }

    @media (max-width:900px){ .opcion{ min-width: 100%; text-align:center } .container{ padding:0 12px } }
  </style>
</head>
<body>

  <div class="top-bar">
    <div style="display:flex;align-items:center;gap:12px;">
      <img src="../imagenes/LOGO_BOOK_RUSH.png" alt="Logo" style="height:44px">
      <h1>Book Rush</h1>
    </div>
  </div>

  <div class="container">
    <div class="timer" id="timer">Tiempo: 15 segundos</div>
    <div class="pregunta"><?= $numero_pregunta ?>. <?= htmlspecialchars($pregunta['enunciado']) ?></div>

    <div class="opciones">
    <?php foreach ($opciones as $letra => $texto): ?>
      <button type="button" class="opcion" data-respuesta="<?= $letra ?>">
        <?= $letra ?>) <?= htmlspecialchars($texto) ?>
      </button>
    <?php endforeach; ?>
    </div>

    <div id="resultado"></div>
  </div>

  <script>
  const botones = document.querySelectorAll(".opcion");
  const resultado = document.getElementById("resultado");
  let respondido = false;
  let tiempo = 15;

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
              if (data.es_correcta) {
                  resultado.textContent = "✅ ¡Correcto!";
                  resultado.style.color = "#2d8f4a";
              } else if (respuesta === "") {
                  resultado.textContent = "⏰ Tiempo agotado.";
                  resultado.style.color = "#6c757d";
              } else {
                  resultado.textContent = "❌ Incorrecto.";
                  resultado.style.color = "#c0392b";
              }

              setTimeout(() => {
                  if (data.siguiente > 0) {
                      window.location.href = "?id_libro=<?= $id_libro ?>&id_capitulo=<?= $id_capitulo ?>&pregunta=" + data.siguiente;
                  } else {
                      window.location.href = "../total.php?id_libro=<?= $id_libro ?>&id_capitulo=<?= $id_capitulo ?>";
                  }
              }, 1200);
          }
      })
      .catch(error => { console.error('Error:', error); alert("Error al conectar con el servidor: " + error.message); });
  }
  </script>

</body>
</html>