<?php
session_start();
include '../conexion.php';

// Asegúrate de que el usuario esté logueado (ajusta si usas otro key)
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit;
}

$dni = $_SESSION['usuario']['DNI'] ?? null;
$id_libro = 4; 

// Puntaje máximo por capítulo (ajusta si quieres)
$maximos = [1 => 100, 2 => 100, 3 => 100, 4 => 100, 5 => 100];

$puntajes = [];
for ($i = 1; $i <= 5; $i++) {
    $sql = "SELECT SUM(PUNTAJE) AS total FROM puntajes WHERE DNI=? AND id_libro=? AND CAPITULO=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $dni, $id_libro, $i);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $puntajes[$i] = $res['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Capítulos - Frankenstein</title>
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Fredoka', sans-serif; background: #736dc9ff; color: #020202; margin: 0; padding: 0; text-align: center; }
    .cerrar { position: absolute; top: 15px; right: 20px; font-size: 40px; color: white; text-decoration: none; font-weight: bold; background: rgba(0,0,0,0.3); padding: 5px 15px; border-radius: 50%; }
    h1 { font-size: 40px; margin: 50px 0 30px; color: white; text-shadow: 2px 2px 4px #000; }
    .capitulo { display: inline-block; background-color: #38256b; color: white; padding: 20px; border-radius: 20px; font-size: 22px; font-weight: bold; text-decoration: none; margin: 15px; width: 200px; box-shadow: 2px 2px 8px rgba(0,0,0,0.3); }
    .capitulo:hover { transform: scale(1.03); background-color: #277abd; }
    .bloqueado { background-color: gray; pointer-events: none; opacity: 0.6; }
    .progreso { background: #fcf2c0; border-radius: 10px; height: 15px; margin-top: 10px; overflow: hidden; }
    .barra { background: #0f6cba; height: 100%; transition: width 0.3s; }
</style>
</head>
<body>
<a href="../cuentos/frankenstein.php" class="cerrar">✖</a>
<h1>📚 Capítulos - Frankenstein</h1>

<?php
for ($i = 1; $i <= 5; $i++) {
    $porcentaje = 0;
    if ($maximos[$i] > 0) {
        $porcentaje = min(100, round(($puntajes[$i] / $maximos[$i]) * 100));
    }
    // desbloqueo: si i>1 requiere completar i-1 (ajusta regla si quieres otra)
    $bloqueado = ($i > 1 && $puntajes[$i-1] < $maximos[$i-1]) ? "bloqueado" : "";
    // enlace al contenido del capítulo (archivo que ya tienes en capfrank/)
    echo "<a href='capfrank/{$i}_frank.php' class='capitulo $bloqueado'>
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
