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
  <link rel="stylesheet" href="../css/trivia.css">
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
