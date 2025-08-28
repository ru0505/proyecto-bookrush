<?php
session_start();
include '../../conexion.php'; 

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit;
}

$dni = $_SESSION['usuario']['DNI'];
$id_capitulo = '4'; // varchar en base de datos
$id_pregunta = 16;   // int

// Reiniciar puntajes de ese capítulo y usuario solo si es la primera pregunta
if ($id_pregunta == 1) {
    $stmt = $conn->prepare("DELETE FROM puntajes WHERE dni = ? AND capitulo = ?");
    $stmt->bind_param("ss", $dni, $id_capitulo);
    $stmt->execute();
}

// Obtener la pregunta
$stmt = $conn->prepare("SELECT * FROM preguntas WHERE id_capitulo = ? AND id_pregunta = ?");
$stmt->bind_param("si", $id_capitulo, $id_pregunta);
$stmt->execute();
$resultado = $stmt->get_result();
$pregunta = $resultado->fetch_assoc();

if (!$pregunta) {
    echo "No hay pregunta.";
    exit;
}

$enunciado = $pregunta['enunciado'];
$respuesta_correcta = strtoupper(trim($pregunta['respuesta_correcta']));
$puntaje_pregunta = (int)$pregunta['puntaje'];

$opciones = [
    'A' => $pregunta['opcion_a'],
    'B' => $pregunta['opcion_b'],
    'C' => $pregunta['opcion_c'],
    'D' => $pregunta['opcion_d']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuesta = strtoupper(trim($_POST['respuesta'] ?? ''));
    $correcto = $respuesta === $respuesta_correcta;

    // Verificar si ya se registró esta pregunta para este usuario
    $stmt = $conn->prepare("SELECT * FROM puntajes WHERE dni = ? AND capitulo = ? AND id_pregunta = ?");
    $stmt->bind_param("ssi", $dni, $id_capitulo, $id_pregunta);
    $stmt->execute();
    $ya_existe = $stmt->get_result()->num_rows > 0;

    if ($correcto && !$ya_existe) {
        $stmt = $conn->prepare("INSERT INTO puntajes (dni, capitulo, puntaje, id_pregunta) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssii", $dni, $id_capitulo, $puntaje_pregunta, $id_pregunta);
        $stmt->execute();
    }

    header('Content-Type: application/json');
    echo json_encode(['correcto' => $correcto]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pregunta <?= $id_pregunta ?> - Capítulo <?= $id_capitulo ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #fcf2c0;
      color: #1e334e;
      text-align: center;
      padding: 40px;
    }
    .pregunta {
      font-size: 24px;
      margin-bottom: 30px;
    }
    .opcion {
      margin: 15px auto;
      padding: 20px;
      width: 340px;
      background-color: #f0c64b;
      border: 3px solid #1e334e;
      border-radius: 20px;
      font-weight: bold;
      font-size: 18px;
      cursor: pointer;
      transition: transform 0.2s;
    }
    .opcion:hover {
      background-color: #ffe680;
      transform: scale(1.03);
    }
    #timer {
      font-size: 22px;
      color: #d85e39;
      margin-bottom: 30px;
    }
    #resultado {
      font-size: 20px;
      margin-top: 20px;
    }
  </style>
</head>
<body>

  <div id="timer">Tiempo: 15 segundos</div>
  <div class="pregunta"><?= $id_pregunta ?>. <?= htmlspecialchars($enunciado) ?></div>

  <form id="form-respuesta">
    <?php foreach ($opciones as $letra => $texto): ?>
      <button type="button" class="opcion" data-respuesta="<?= $letra ?>">
        <?= $letra ?>) <?= htmlspecialchars($texto) ?>
      </button>
    <?php endforeach; ?>
  </form>

  <div id="resultado"></div>

  <script>
    const botones = document.querySelectorAll(".opcion");
    const resultado = document.getElementById("resultado");
    let respondido = false;

    botones.forEach(boton => {
      boton.addEventListener("click", () => {
        if (respondido) return;
        respondido = true;

        const respuesta = boton.dataset.respuesta;
        fetch("", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "respuesta=" + encodeURIComponent(respuesta)
        })
        .then(res => res.json())
        .then(data => {
          if (data.correcto) {
            resultado.textContent = "✅ ¡Correcto!";
            resultado.style.color = "green";
          } else {
            resultado.textContent = "❌ Incorrecto.";
            resultado.style.color = "red";
          }
          setTimeout(() => {
            window.location.href = "2.php";
          }, 2000);
        });
      });
    });

    // Temporizador
    let tiempo = 15;
    const timer = document.getElementById("timer");
    const countdown = setInterval(() => {
      tiempo--;
      timer.textContent = "Tiempo: " + tiempo + " segundos";
      if (tiempo <= 0 && !respondido) {
        respondido = true;
        clearInterval(countdown);
        fetch("", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "respuesta="
        }).then(() => {
          resultado.textContent = "⏰ Tiempo agotado.";
          resultado.style.color = "gray";
          setTimeout(() => {
            window.location.href = "2.php";
          }, 2000);
        });
      }
    }, 1000);
  </script>
</body>
</html>
