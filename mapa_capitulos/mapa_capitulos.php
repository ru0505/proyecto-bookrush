<?php
session_start();
include '../conexion.php';

// Obtenemos el id del libro desde la URL
$id_libro = isset($_GET['id_libro']) ? intval($_GET['id_libro']) : 0;

if ($id_libro <= 0) {
    header("Location: ../index.php");
    exit;
}

// Consulta para obtener el nombre del libro (solo para el título)
$stmtLibro = $conn->prepare("SELECT titulo FROM libros WHERE id_libro = ?");
$stmtLibro->bind_param("i", $id_libro);
$stmtLibro->execute();
$nombre_libro = $stmtLibro->get_result()->fetch_assoc()['titulo'] ?? 'Libro desconocido';

// Consulta para obtener los capítulos del libro
$stmtCap = $conn->prepare("SELECT id_capitulo, titulo FROM capitulos WHERE id_libro = ? ORDER BY id_capitulo ASC");
$stmtCap->bind_param("i", $id_libro);
$stmtCap->execute();
$capitulos = $stmtCap->get_result()->fetch_all(MYSQLI_ASSOC);

// Simulación de progreso (por ahora)
$completed = $_SESSION['completed'] ?? [];
$startIndex = (!empty($completed) ? max($completed) : 1) - 1;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Capítulos - <?= htmlspecialchars($nombre_libro) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        /* === Mantiene todo tu CSS original sin cambios === */
        :root{--bg1:#1a0f2e;--bg2:#0f0816;--accent:#8b1538;--gold:#d4af37;--shadow:rgba(139,21,56,0.3)}
        body{
            font-family:Arial,Helvetica,sans-serif;
            background:
                linear-gradient(180deg,rgba(26,15,46,0.85) 0%, rgba(15,8,22,0.9) 100%),
                url('../imagenes/castillo.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color:#fff;
            margin:0;
            padding:18px;
            position:relative;
            overflow-x:hidden
        }
        body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:radial-gradient(circle at 20% 80%, rgba(139,21,56,0.1) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(139,21,56,0.08) 0%, transparent 50%);pointer-events:none;z-index:1}
        .header{max-width:1100px;margin:0 auto;padding:8px 12px 18px;display:flex;align-items:center;gap:12px;position:relative;z-index:10}
        .back-btn{
            display:inline-flex;align-items:center;gap:8px;
            background:rgba(139,21,56,0.3);
            backdrop-filter:blur(10px);
            color:#fff;
            padding:12px 20px;
            border-radius:25px;
            text-decoration:none;
            border:2px solid rgba(139,21,56,0.4);
            transition:all 0.3s ease;
            font-weight:600;
            font-size:14px;
            letter-spacing:0.5px;
            position:absolute;
            left:-120px;
            text-transform:uppercase
        }
        .back-btn:hover{background:rgba(139,21,56,0.5);border-color:rgba(139,21,56,0.7);transform:translateY(-2px);box-shadow:0 8px 20px rgba(139,21,56,0.3)}
        .title{flex:1;text-align:center;font-size:24px;color:#f3e6ff;text-shadow:0 2px 10px rgba(139,21,56,0.5);font-weight:300;letter-spacing:1px}

        .stage{max-width:1200px;margin:0 auto;position:relative;z-index:5}
        .map-wrap{position:relative;height:950px;margin:8px auto 50px;max-width:950px;border-radius:20px;background:rgba(26,15,46,0.2);backdrop-filter:blur(20px);border:1px solid rgba(139,21,56,0.2);box-shadow:0 20px 40px rgba(0,0,0,0.3);padding:50px}
        .coin{position:absolute;display:flex;align-items:center;justify-content:center;width:65px;height:65px;border-radius:50%;background:linear-gradient(135deg,#666,#444);color:#aaa;font-weight:800;font-size:15px;text-decoration:none;box-shadow:0 8px 20px rgba(0,0,0,.4), 0 0 15px rgba(102,102,102,0.2);transition:all .18s ease;z-index:3;border:3px solid rgba(102,102,102,0.6)}
        .coin.blocked{background:linear-gradient(135deg,#555,#333);border-color:rgba(85,85,85,0.6);cursor:not-allowed}
        .coin.playable{background:linear-gradient(135deg,#ff8c42,#d46a2a);color:#fff;border-color:rgba(255,140,66,0.8);box-shadow:0 8px 20px rgba(0,0,0,.4), 0 0 15px rgba(255,140,66,0.4)}
        .coin.completed{background:linear-gradient(135deg,#4a9b4e,#2d5a30);color:#fff;border-color:rgba(74,155,78,0.8);box-shadow:0 8px 20px rgba(0,0,0,.4), 0 0 15px rgba(74,155,78,0.4)}
        .final-level{background:linear-gradient(135deg,#8b1538,#5d0e24) !important;border-color:rgba(139,21,56,0.8) !important;box-shadow:0 15px 40px rgba(0,0,0,.7), 0 0 35px rgba(139,21,56,0.7) !important;width:85px !important;height:85px !important;font-size:20px !important;border-width:4px !important}
    </style>
</head>
<body>
    <div class="header">
        <a class="back-btn" href="../detalle_libros/detalle_libro.php?id=<?= $id_libro ?>" title="Volver al libro">Atrás</a>
        <div class="title">Capítulos de <?= htmlspecialchars($nombre_libro) ?></div>
    </div>

    <div class="stage">
        <div class="map-wrap">
            <?php
            if (empty($capitulos)) {
                echo "<p style='text-align:center;color:#fff;font-size:18px;margin-top:200px;'>📚 Este libro aún no tiene capítulos disponibles.</p>";
            } else {
                // Posiciones aproximadas en espiral o zig-zag (simplificadas)
                $x = 500; $y = 450;
                $step = 70;
                $dir = 1;

                foreach ($capitulos as $index => $cap) {
                    $estado = "blocked";
                    if (in_array($cap['id_capitulo'], $completed)) $estado = "completed";
                    elseif ($index == $startIndex) $estado = "playable";

                    echo "<a href='capitulos/{$id_libro}/{$cap['id_capitulo']}.php' class='coin {$estado}' style='left:{$x}px;top:{$y}px'>
                            <span class='num'>{$cap['id_capitulo']}</span>
                          </a>";

                    // mover coordenadas
                    $x += $step * $dir;
                    if ($x > 850 || $x < 200) {
                        $dir *= -1;
                        $y += 90;
                    }
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
