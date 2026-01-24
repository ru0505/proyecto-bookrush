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

// Obtener puntajes del usuario por capítulo para este libro
$puntajes_por_capitulo = [];
if (isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario'])) {
    $id_usuario = $_SESSION['id_usuario'];
    $stmtPuntajes = $conn->prepare("
        SELECT CAPITULO, SUM(PUNTAJE) as total 
        FROM puntajes 
        WHERE id_usuario = ? AND id_libro = ? 
        GROUP BY CAPITULO
    ");
    $stmtPuntajes->bind_param("ii", $id_usuario, $id_libro);
    $stmtPuntajes->execute();
    $resultPuntajes = $stmtPuntajes->get_result();
    
    while ($row = $resultPuntajes->fetch_assoc()) {
        $puntajes_por_capitulo[$row['CAPITULO']] = intval($row['total']);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Capítulos - <?= htmlspecialchars($nombre_libro) ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/mapa_capitulos.css">

    <style>
        .header-titulo {
            display: flex;
            align-items: center; /* Centrar verticalmente */
            justify-content: center; /* Centrar horizontalmente */
            gap: 20px; /* Espacio entre el texto y la imagen */
            margin-bottom: 30px;
            flex-wrap: wrap; /* Para que se adapte en celulares */
        }

        .page-title {
            margin: 0; 
            text-align: right; 
        }

        .img-mascota-titulo {
            width: 120px; /* Tamaño de Lecturín */
            height: auto;
            filter: drop-shadow(0 5px 5px rgba(0,0,0,0.2));
            animation: flotar 3s ease-in-out infinite;
        }

        @keyframes flotar {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        @media (max-width: 600px) {
            .header-titulo {
                flex-direction: column;
                gap: 10px;
            }
            .page-title { text-align: center; }
            .img-mascota-titulo { width: 100px; }
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <a href="../index.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
            <img src="../imagenes/lecturin//lecturin_saltando.png" alt="Logo Book Rush" style="height: 70px; margin-right: 5px;">
            <h1>Book Rush</h1>
        </a>
        
        <a href="../detalle_libros/detalle_libro.php?id=<?= $id_libro ?>" class="btn-volver-top">
            ← Volver
        </a>
    </div>

    <div class="container">
        
        <div class="header-titulo">
            <h1 class="page-title">Capítulos de <?= htmlspecialchars($nombre_libro) ?></h1>
            <img src="../imagenes/lecturin/lecturin_lado.png" alt="Lectirín" class="img-mascota-titulo">
        </div>

        <?php if (empty($capitulos)): ?>
            <div class="mensaje-vacio">
                📚 Este libro aún no tiene capítulos disponibles.
            </div>
        <?php else: ?>
            <div class="capitulos-grid">
                <?php foreach ($capitulos as $index => $cap):
                    $id_cap = $cap['id_capitulo'];
                    $puntaje_cap = $puntajes_por_capitulo[$id_cap] ?? 0;
                    
                    // Determinar estado del capítulo (Lógica de tu amigo intacta)
                    $estado = "blocked"; 
                    
                    if ($puntaje_cap >= 80) {
                        $estado = "completed";
                    } elseif ($index == 0) {
                        $estado = "playable";
                    } else {
                        $cap_anterior = $capitulos[$index - 1]['id_capitulo'];
                        $puntaje_anterior = $puntajes_por_capitulo[$cap_anterior] ?? 0;
                        
                        if ($puntaje_anterior >= 80) {
                            $estado = "playable";
                        }
                    }
                    
                    $displayTitle = preg_replace('/^\s*capitu?lo\s*\d+\s*/iu', '', $cap['titulo']);
                ?>
                    <a href="<?= $estado != 'blocked' ? '../contenido_capitulo/contenido_capitulo.php?id_capitulo='.$id_cap.'&id_libro='.$id_libro : '#' ?>" 
                       class="capitulo-card <?= $estado ?>" 
                       <?= $estado == 'blocked' ? 'onclick="return false;" style="cursor: not-allowed;"' : '' ?>>
                        <div class="capitulo-numero"><?= $id_cap ?></div>
                        <div class="capitulo-titulo"><?= htmlspecialchars($displayTitle) ?></div>
                        <?php if ($puntaje_cap > 0): ?>
                            <div class="capitulo-puntaje"><?= $puntaje_cap ?>/100 pts</div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>