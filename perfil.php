<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$nombre = $usuario['NOMBRE'] ?? 'Desconocido';
$dni = $usuario['DNI'] ?? 'No disponible';
$rol = $usuario['ROL'] ?? 'Invitado';
$id_usuario = $usuario['ID_USUARIO'] ?? 0;

// Puntaje total
$puntaje_total = 0;
if ($dni) {
    $stmt = $conn->prepare("SELECT SUM(PUNTAJE) AS total FROM puntajes WHERE DNI = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($fila = $res->fetch_assoc()) {
        $puntaje_total = $fila['total'] ?? 0;
    }
}

// Libros leídos
$libros_leidos = 0;
if ($dni) {
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT l.ID_LIBRO) AS total
        FROM puntajes p
        JOIN capitulos c ON p.CAPITULO = c.TITULO
        JOIN libros l ON c.ID_LIBRO = l.ID_LIBRO
        WHERE p.DNI = ?
    ");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($fila = $res->fetch_assoc()) {
        $libros_leidos = $fila['total'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Perfil - Book Rush</title>
  <link href="https://fonts.cdnfonts.com/css/sergio-trendy" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Sergio Trendy', sans-serif;
      background: url('imagenes/fondo_perfil.png');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
    }

    .overlay {
      background: rgba(252, 242, 192, 0.8);
      min-height: 100vh;
      padding-bottom: 50px;
    }

    .navbar {
      background-color: #0f6cba;
      padding: 12px 25px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 5px solid #f0c64b;
    }
    .navbar a {
      color: white;
      text-decoration: none;
      margin-right: 20px;
      font-weight: bold;
      font-size: 18px;
      transition: transform 0.3s;
    }
    .navbar a:hover {
      transform: scale(1.1);
    }

    .perfil-container {
      max-width: 700px;
      margin: 40px auto;
      background: #fff;
      padding: 35px;
      border-radius: 20px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.2);
      text-align: center;
      animation: bounceIn 1s ease;
    }

    h2 {
      color: #d85e39;
      font-size: 28px;
      margin-bottom: 20px;
    }

    .info {
      line-height: 2;
      font-size: 18px;
      text-align: left;
    }
    .info strong {
      color: #0f6cba;
    }

    .puntaje {
      margin-top: 25px;
      padding: 15px;
      background: linear-gradient(45deg, #f0c64b, #ffd966);
      color: #1e334e;
      font-size: 22px;
      font-weight: bold;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.15);
      animation: pulse 2s infinite;
      text-align: center;
    }

    .detalle {
      margin-top: 30px;
      text-align: left;
      font-size: 16px;
    }

    .detalle h3 {
      color: #d85e39;
      margin-bottom: 10px;
    }

    .detalle ul {
      margin-left: 20px;
      list-style: none;
    }

    .detalle li {
      margin-bottom: 5px;
    }

    /* Animaciones */
    @keyframes bounceIn {
      from { opacity: 0; transform: scale(0.8); }
      to { opacity: 1; transform: scale(1); }
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
  </style>
</head>
<body>
  <div class="overlay">
    <!-- Navegación -->
    <div class="navbar">
      <div class="nav-left">
        <a href="index.php">🏠 Inicio</a>
      </div>
      <div class="nav-right">
        <a href="logout.php">🚪 Cerrar Sesión</a>
      </div>
    </div>

    <!-- Perfil -->
    <div class="perfil-container">
      <h2> Perfil de Usuario </h2>
      <div class="info">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
        <p><strong>DNI:</strong> <?= htmlspecialchars($dni) ?></p>
        <p><strong>Rol:</strong> <?= htmlspecialchars($rol) ?></p>
      </div>
      <div class="puntaje">
         Puntaje Total: <strong><?= htmlspecialchars($puntaje_total) ?></strong><br>
      </div>

      <!-- Detalle por libro/capítulo -->
      <div class="detalle">
        <h3> Detalle de puntajes por libro</h3>
        <?php
        $stmt = $conn->prepare("
          SELECT l.TITULO AS libro,
                 c.TITULO AS capitulo,
                 q.ENUNCIADO AS pregunta,
                 IFNULL(p.PUNTAJE, 0) AS puntaje
          FROM preguntas q
          JOIN capitulos c ON q.ID_CAPITULO = c.ID_CAPITULO
          JOIN libros l ON c.ID_LIBRO = l.ID_LIBRO
          LEFT JOIN puntajes p
                 ON q.ID_PREGUNTA = p.ID_PREGUNTA
                AND p.DNI = ?
          ORDER BY l.TITULO, c.TITULO, q.ID_PREGUNTA
        ");
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $res = $stmt->get_result();

        $libro_actual = "";
        $capitulo_actual = "";

        while ($fila = $res->fetch_assoc()) {
            if ($libro_actual !== $fila['libro']) {
                if ($libro_actual !== "") {
                    echo "</ul></ul>";
                }
                $libro_actual = $fila['libro'];
                echo "<h4> " . htmlspecialchars($libro_actual) . "</h4><ul>";
                $capitulo_actual = "";
            }

            if ($capitulo_actual !== $fila['capitulo']) {
                if ($capitulo_actual !== "") {
                    echo "</ul>";
                }
                $capitulo_actual = $fila['capitulo'];
                echo "<li><strong>Capítulo: " . htmlspecialchars($capitulo_actual) . "</strong><ul>";
            }

            echo "<li> → " . htmlspecialchars($fila['puntaje']) . " pts</li>";
        }

        if ($libro_actual !== "") {
            echo "</ul></ul>";
        }
        ?>
      </div>
    </div>
  </div>
</body>
</html>
