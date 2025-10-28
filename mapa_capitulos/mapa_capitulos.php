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
    <link rel="stylesheet" href="../css/mapa_capitulos.css">
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
