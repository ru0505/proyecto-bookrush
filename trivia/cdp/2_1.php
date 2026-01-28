<?php
session_start();
include '../../conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit;
}

$dni = $_SESSION['usuario']['DNI'];

// Capítulo 2 fijo
$id_libro = 1;
$id_capitulo = 2;
$numero_pregunta = isset($_GET['pregunta']) ? intval($_GET['pregunta']) : 1;

// Reiniciar puntajes si es la primera pregunta del capítulo
if ($numero_pregunta == 1) {
    $stmt = $conn->prepare("DELETE FROM puntajes WHERE dni = ? AND id_libro = ? AND capitulo = ?");
    $stmt->bind_param("iii", $dni, $id_libro, $id_capitulo);
    $stmt->execute();
}

// Obtener pregunta
$stmt = $conn->prepare("SELECT * FROM preguntas WHERE id_libro = ? AND id_capitulo = ? AND numero_pregunta = ?");
$stmt->bind_param("iii", $id_libro, $id_capitulo, $numero_pregunta);
$stmt->execute();
$result = $stmt->get_result();
$pregunta = $result->fetch_assoc();

if (!$pregunta) {
    echo "No hay pregunta.";
    exit;
}

$enunciado = $pregunta['enunciado'];
$respuesta_correcta = strtoupper(trim($pregunta['respuesta_correcta']));
$puntaje_pregunta = intval($pregunta['puntaje']);

$opciones = [
    'A' => $pregunta['opcion_a'],
    'B' => $pregunta['opcion_b'],
    'C' => $pregunta['opcion_c'],
    'D' => $pregunta['opcion_d']
];

// Contar total de preguntas del capítulo
$stmt2 = $conn->prepare("SELECT COUNT(*) as total_preguntas FROM preguntas WHERE id_libro = ? AND id_capitulo = ?");
$stmt2->bind_param("ii", $id_libro, $id_capitulo);
$stmt2->execute();
$total_preguntas = $stmt2->get_result()->fetch_assoc()['total_preguntas'];

// Procesar envío
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $respuesta_usuario = strtoupper(trim($_POST['respuesta'] ?? ''));
    $es_correcta = ($respuesta_usuario === $respuesta_correcta);

    $stmt3 = $conn->prepare("SELECT 1 FROM puntajes WHERE dni = ? AND id_libro = ? AND capitulo = ? AND id_pregunta = ?");
    $stmt3->bind_param("iiii", $dni, $id_libro, $id_capitulo, $numero_pregunta);
    $stmt3->execute();
    $ya_existe = $stmt3->get_result()->num_rows > 0;

    if (!$ya_existe) {
        $puntaje = $es_correcta ? $puntaje_pregunta : 0;
        $stmt4 = $conn->prepare("INSERT INTO puntajes (dni, id_libro, capitulo, puntaje, id_pregunta) VALUES (?, ?, ?, ?, ?)");
        $stmt4->bind_param("siiii", $dni, $id_libro, $id_capitulo, $puntaje, $numero_pregunta);
        $stmt4->execute();
    }

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'ok',
        'es_correcta' => $es_correcta,
        'siguiente' => ($numero_pregunta < $total_preguntas) ? $numero_pregunta + 1 : 0
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pregunta <?= $numero_pregunta ?> - Capítulo 2</title>
<style>
body { font-family: Arial, sans-serif; background: #fcf2c0; color: #1e334e; text-align: center; padding: 40px; }
.pregunta { font-size: 24px; margin-bottom: 30px; }
.opcion { margin: 15px auto; padding: 20px; width: 340px; background-color: #f0c64b; border: 3px solid #1e334e; border-radius: 20px; font-weight: bold; font-size: 18px; cursor: pointer; transition: transform 0.2s; }
.opcion:hover { background-color: #ffe680; transform: scale(1.03); }
#timer { font-size: 22px; color: #d85e39; margin-bottom: 30px; }
#resultado { font-size: 20px; margin-top: 20px; }
</style>
</head>
<body>

<div id="timer">Tiempo: 15 segundos</div>
<div class="pregunta"><?= $numero_pregunta ?>. <?= htmlspecialchars($enunciado) ?></div>

<form id="form-respuesta">
<?php foreach ($opciones as $letra => $texto): ?>
  <button type="button" class="opcion" data-respuesta="<?= $letra ?>"><?= $letra ?>) <?= htmlspecialchars($texto) ?></button>
<?php endforeach; ?>
</form>

<div id="resultado"></div>

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
    .then(res => res.json())
    .then(data => {
        if(data.status === "ok") {
            if(data.es_correcta) {
                resultado.textContent = "✅ ¡Correcto!";
                resultado.style.color = "green";
            } else if(respuesta === "") {
                resultado.textContent = "⏰ Tiempo agotado.";
                resultado.style.color = "gray";
            } else {
                resultado.textContent = "❌ Incorrecto.";
                resultado.style.color = "red";
            }

            setTimeout(() => {
                if(data.siguiente > 0) {
                    window.location.href = "?pregunta=" + data.siguiente;
                } else {
                    window.location.href = "../../total.php?capitulo=<?= $id_capitulo ?>";
                }
            }, 1500);
        }
    })
    .catch(() => alert("Error al conectar con el servidor."));
}
</script>

</body>
</html>
