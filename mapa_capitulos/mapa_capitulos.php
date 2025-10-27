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
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Capítulos - <?= htmlspecialchars($nombre_libro) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Fredoka', sans-serif;
            background: linear-gradient(135deg, #353346ff 0%, #2a2a3e 50%, #1e1e2e 100%) fixed;
            background-attachment: fixed;
            min-height: 100vh;
            padding: 130px 20px 40px;
            color: #fff;
        }

        .top-bar {
            width: 100%;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
            background-size: 200% 200%;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1100;
            box-shadow: 0 8px 32px rgba(30, 58, 138, 0.3);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            animation: slideDown 0.8s ease-out, topBarMove 6s ease-in-out infinite;
            overflow: hidden;
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

        .container {
            max-width: 1100px;
            margin: 0 auto;
            position: relative;
        }

        .page-title {
            text-align: center;
            color: white;
            font-size: 2.2em;
            margin-bottom: 30px;
        }

        .back-btn {
            background: #ff8c42;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1em;
            text-decoration: none;
            font-family: 'Fredoka', sans-serif;
            transition: all 0.3s ease;
            display: inline-block;
            margin-bottom: 20px;
            /* Position fixed so it can stick to the left edge of the viewport */
            position: fixed;
            left: 12px;
            top: 120px; /* placed under the top-bar (top-bar ~100px) */
            z-index: 1110;
        }

        .back-btn:hover {
            background: #e67a35;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 140, 66, 0.4);
        }

        .capitulos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        .capitulo-card {
            background: linear-gradient(135deg, #666, #444);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            color: #aaa;
            font-weight: 700;
            font-size: 1.1em;
            transition: all 0.3s ease;
            border: 3px solid rgba(102,102,102,0.6);
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 8px;
        }

        .capitulo-card.blocked {
            background: linear-gradient(135deg, #555, #333);
            border-color: rgba(85,85,85,0.6);
            cursor: not-allowed;
            opacity: 0.6;
        }

        .capitulo-card.playable {
            background: linear-gradient(135deg, #ff8c42, #d46a2a);
            color: white;
            border-color: rgba(255,140,66,0.8);
            box-shadow: 0 8px 20px rgba(255,140,66,0.4);
        }

        .capitulo-card.completed {
            background: linear-gradient(135deg, #4a9b4e, #2d5a30);
            color: white;
            border-color: rgba(74,155,78,0.8);
            box-shadow: 0 8px 20px rgba(74,155,78,0.4);
        }

        .capitulo-card:hover:not(.blocked) {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.3);
        }

        .capitulo-numero {
            font-size: 1.8em;
            font-weight: 800;
        }

        .capitulo-titulo {
            font-size: 0.85em;
            font-weight: 400;
            opacity: 0.9;
        }

        .mensaje-vacio {
            text-align: center;
            color: white;
            font-size: 1.3em;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
        }

        @media (max-width: 768px) {
            .capitulos-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 15px;
                padding: 15px;
            }
            
            .capitulo-card {
                min-height: 100px;
                padding: 15px;
            }
            /* On small screens move the button a bit lower and closer to the edge */
            .back-btn {
                left: 8px;
                top: 90px;
                padding: 10px 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div style="display: flex; align-items: center;">
            <img src="../imagenes/LOGO_BOOK_RUSH.png" alt="Logo Book Rush" style="height: 50px; margin-right: 10px;">
            <h1>Book Rush</h1>
        </div>
    </div>

    <div class="container">
        <a href="../detalle_libros/detalle_libro.php?id=<?= $id_libro ?>" class="back-btn">← Volver al libro</a>
        
        <h1 class="page-title" style="margin-top: 60px;">Capítulos de <?= htmlspecialchars($nombre_libro) ?></h1>

        <?php if (empty($capitulos)): ?>
            <div class="mensaje-vacio">
                📚 Este libro aún no tiene capítulos disponibles.
            </div>
        <?php else: ?>
            <div class="capitulos-grid">
                <?php foreach ($capitulos as $index => $cap): 
                    $estado = "blocked";
                    if (in_array($cap['id_capitulo'], $completed)) {
                        $estado = "completed";
                    } elseif ($index == $startIndex) {
                        $estado = "playable";
                    }
                    // Mostrar solo el título limpio dentro del box: quitar prefijos como "Capítulo 1"
                    $displayTitle = preg_replace('/^\s*capitu?lo\s*\d+\s*/iu', '', $cap['titulo']);
                ?>
                    <a href="../contenido_capitulo/contenido_capitulo.php?id_capitulo=<?= $cap['id_capitulo'] ?>&id_libro=<?= $id_libro ?>" 
                       class="capitulo-card <?= $estado ?>">
                        <div class="capitulo-numero"><?= $cap['id_capitulo'] ?></div>
                        <div class="capitulo-titulo"><?= htmlspecialchars($displayTitle) ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
