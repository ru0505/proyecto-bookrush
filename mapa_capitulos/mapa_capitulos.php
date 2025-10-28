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

        /* Botón Volver en la barra superior derecha */
        .btn-volver-top {
            background: #ff8c42;
            color: white;
            border: none;
            padding: 15px 35px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1.2em;
            text-decoration: none;
            font-family: 'Fredoka', sans-serif;
            transition: all 0.3s ease;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(255, 140, 66, 0.4);
        }

        .btn-volver-top:hover {
            background: #e67a35;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(255, 140, 66, 0.6);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
        }

        .page-title {
            text-align: center;
            color: white;
            font-size: 2.2em;
            margin-bottom: 30px;
        }

        /* Grid responsive que se adapta a cualquier cantidad de capítulos */
        .capitulos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 25px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        .capitulo-card {
            background: linear-gradient(135deg, #ff8c42, #e67a35);
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            text-decoration: none;
            color: white;
            font-weight: 700;
            font-size: 1.1em;
            transition: all 0.3s ease;
            border: 3px solid rgba(255, 140, 66, 0.6);
            min-height: 150px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: hidden;
        }

        /* Efecto hover azul como las tarjetas del index */
        .capitulo-card:hover:not(.blocked) {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-color: rgba(59, 130, 246, 0.8);
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.5);
        }

        .capitulo-card.blocked {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
            border-color: rgba(156, 163, 175, 0.6);
            cursor: not-allowed;
            opacity: 0.6;
            color: #e5e7eb;
        }

        .capitulo-card.blocked:hover {
            transform: none;
            box-shadow: none;
        }

        .capitulo-card.playable {
            background: linear-gradient(135deg, #ff8c42, #e67a35);
            color: white;
            border-color: rgba(255, 140, 66, 0.8);
            box-shadow: 0 8px 20px rgba(255, 140, 66, 0.4);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { 
                box-shadow: 0 8px 20px rgba(255, 140, 66, 0.4);
            }
            50% { 
                box-shadow: 0 12px 30px rgba(255, 140, 66, 0.6);
            }
        }

        .capitulo-card.completed {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-color: rgba(16, 185, 129, 0.8);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
        }

        .capitulo-card.completed:hover {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border-color: rgba(59, 130, 246, 0.8);
            box-shadow: 0 15px 35px rgba(59, 130, 246, 0.5);
        }

        .capitulo-numero {
            font-size: 2.5em;
            font-weight: 800;
            line-height: 1;
        }

        .capitulo-titulo {
            font-size: 0.9em;
            font-weight: 500;
            opacity: 0.95;
            line-height: 1.3;
        }

        .mensaje-vacio {
            text-align: center;
            color: white;
            font-size: 1.3em;
            padding: 60px 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
        }

        /* Responsive design mejorado */
        @media (max-width: 1024px) {
            .capitulos-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .capitulos-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 15px;
                padding: 20px;
            }
            
            .capitulo-card {
                min-height: 130px;
                padding: 20px 15px;
            }

            .capitulo-numero {
                font-size: 2em;
            }

            .capitulo-titulo {
                font-size: 0.85em;
            }

            .btn-volver-top {
                padding: 12px 25px;
                font-size: 1em;
            }

            .page-title {
                font-size: 1.8em;
            }
        }

        @media (max-width: 480px) {
            .capitulos-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 12px;
                padding: 15px;
            }

            .capitulo-card {
                min-height: 110px;
                padding: 15px 10px;
            }

            .capitulo-numero {
                font-size: 1.8em;
            }

            .capitulo-titulo {
                font-size: 0.75em;
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
        
        <!-- Botón Volver en la parte superior derecha -->
        <a href="../detalle_libros/detalle_libro.php?id=<?= $id_libro ?>" class="btn-volver-top">
            ← Volver
        </a>
    </div>

    <div class="container">
        <h1 class="page-title">Capítulos de <?= htmlspecialchars($nombre_libro) ?></h1>
        
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
