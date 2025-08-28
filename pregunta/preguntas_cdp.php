<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['dni'])) {
    header("Location: ../login.php");
    exit;
}

$dni = $_SESSION['dni'];

// Puntaje máximo por capítulo (ajusta según tu sistema)
$maximos = [
    1 => 280,
    2 => 160,
    3 => 20,
    4 => 200,
    5 => 20
];

$puntajes = [];
for ($i = 1; $i <= 5; $i++) {
    $sql = "SELECT SUM(PUNTAJE) AS total FROM puntajes WHERE DNI=? AND CAPITULO=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $dni, $i);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $puntajes[$i] = $result['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Capítulos con Preguntas</title>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Fredoka', sans-serif;
        background: #736dc9ff;
        color: #020202;
        margin: 0;
        padding: 0;
        text-align: center;
        position: relative;
    }
    /* Botón X */
    .cerrar {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 40px;
        color: white;
        text-decoration: none;
        font-weight: bold;
        background: rgba(0,0,0,0.3);
        padding: 5px 15px;
        border-radius: 50%;
        transition: background 0.3s, transform 0.2s;
    }
    .cerrar:hover {
        background: rgba(0,0,0,0.6);
        transform: scale(1.1);
    }
    h1 {
        font-size: 40px;
        margin: 50px 0 30px;
        color: white;
        text-shadow: 2px 2px 4px #000;
    }
    .capitulo {
        display: inline-block;
        background-color: #38256b;
        color: white;
        padding: 20px;
        border-radius: 20px;
        font-size: 22px;
        font-weight: bold;
        text-decoration: none;
        margin: 15px;
        width: 200px;
        box-shadow: 2px 2px 8px rgba(0,0,0,0.3);
        transition: transform 0.2s;
        position: relative;
    }
    .capitulo:hover {
        transform: scale(1.05);
        background-color: #277abd;
    }
    .bloqueado {
        background-color: gray;
        pointer-events: none;
        opacity: 0.6;
    }
    .progreso {
        background: #fcf2c0;
        border-radius: 10px;
        height: 15px;
        margin-top: 10px;
        overflow: hidden;
    }
    .barra {
        background: #0f6cba;
        height: 100%;
        transition: width 0.3s;
    }
</style>
</head>
<body>

<!-- Botón para volver al libro -->
<a href="../cuentos/ciudad_perros.php" class="cerrar">✖</a>

<h1>📚 Capítulos con Preguntas</h1>

<?php
for ($i = 1; $i <= 5; $i++) {
    $porcentaje = 0;
    if ($maximos[$i] > 0) {
        $porcentaje = min(100, round(($puntajes[$i] / $maximos[$i]) * 100));
    }
    $bloqueado = ($i > 1 && $puntajes[$i-1] < $maximos[$i-1]) ? "bloqueado" : "";
    echo "<a href='../pregunta/capcdp/{$i}_cdp.php' class='capitulo $bloqueado'>
            Capítulo $i
            <div class='progreso'>
                <div class='barra' style='width: {$porcentaje}%;'></div>
            </div>
            <small>{$porcentaje}% completado</small>
          </a>";
}
?>

</body>
</html>
