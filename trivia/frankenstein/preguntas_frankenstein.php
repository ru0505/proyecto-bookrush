<?php
session_start();
include("../../conexion.php");

// Parámetros
$id_libro = 4; 
$id_capitulo = isset($_GET['capitulo']) ? (int)$_GET['capitulo'] : 1;
$numero_pregunta = isset($_GET['pregunta']) ? (int)$_GET['pregunta'] : 1;

// Obtener la pregunta actual
$stmt = $conn->prepare("SELECT * FROM preguntas WHERE id_libro = ? AND id_capitulo = ? AND numero_pregunta = ?");
$stmt->bind_param("iii", $id_libro, $id_capitulo, $numero_pregunta);
$stmt->execute();
$result = $stmt->get_result();
$pregunta = $result->fetch_assoc();

if (!$pregunta) {
    echo "No hay preguntas en este capítulo.";
    exit;
}

// Opciones
$opciones = [
    'A' => $pregunta['opcion_a'],
    'B' => $pregunta['opcion_b'],
    'C' => $pregunta['opcion_c'],
    'D' => $pregunta['opcion_d']
];

// Obtener la última pregunta de este capítulo
$stmt2 = $conn->prepare("SELECT MAX(numero_pregunta) AS max_preg FROM preguntas WHERE id_libro = ? AND id_capitulo = ?");
$stmt2->bind_param("ii", $id_libro, $id_capitulo);
$stmt2->execute();
$result2 = $stmt2->get_result();
$row = $result2->fetch_assoc();
$ultima_pregunta = (int)$row['max_preg'];

// Si se responde algo, procesar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuesta = $_POST['respuesta'] ?? "";
    $correcta = ($respuesta === $pregunta['respuesta_correcta']);

    if ($correcta) {
        $puntaje = 20; // o el valor que quieras por respuesta correcta

        $stmtSumar = $conn->prepare("
            INSERT INTO puntajes (dni, id_libro, capitulo, id_pregunta, puntaje)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE puntaje = VALUES(puntaje)
        ");
        $stmtSumar->bind_param("siiii", $_SESSION['usuario']['DNI'], $id_libro, $id_capitulo, $numero_pregunta, $puntaje);
        $stmtSumar->execute();
    }

    // Guardar resultado en sesión
    $_SESSION['respuestas'][$id_capitulo][$numero_pregunta] = [
        'respuesta' => $respuesta,
        'correcta' => $correcta
    ];

    // Calcular siguiente pregunta
    $siguiente = ($numero_pregunta < $ultima_pregunta) ? $numero_pregunta + 1 : 0;

    echo json_encode([
        "status" => "ok",
        "es_correcta" => $correcta,
        "siguiente" => $siguiente
    ]);
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pregunta <?= $numero_pregunta ?> - Capítulo <?= $id_capitulo ?></title>
  <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<div id="timer">Tiempo: 15 segundos</div>
<div class="pregunta"><?= $numero_pregunta ?>. <?= htmlspecialchars($pregunta['enunciado']) ?></div>

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
let tiempo = 15;

// Timer
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
    .then(res => res.json())
    .then(data => {
        if (data.status === "ok") {
            if (data.es_correcta) {
                resultado.textContent = "✅ ¡Correcto!";
                resultado.style.color = "green";

                // 🔹 Mandar puntaje al sumar.php
                fetch("../../sumar.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id_libro=4&capitulo=<?= $id_capitulo ?>&puntaje=20"
                })
                .then(r => r.json())
                .then(rdata => console.log(rdata));
            } else if (respuesta === "") {
                resultado.textContent = "⏰ Tiempo agotado.";
                resultado.style.color = "gray";
            } else {
                resultado.textContent = "❌ Incorrecto.";
                resultado.style.color = "red";
            }

            setTimeout(() => {
                if (data.siguiente > 0) {
                    window.location.href = "?capitulo=<?= $id_capitulo ?>&pregunta=" + data.siguiente;
                } else {
                    window.location.href = "../../total.php?libro=<?= $id_libro ?>&capitulo=<?= $id_capitulo ?>";
                }
            }, 1500);
        }
    })
    .catch(() => alert("Error al conectar con el servidor."));
}

</script>

</body>
</html>
