<?php
session_start();
include 'conexion.php';

// Verificación de sesión
$usuario = $_SESSION['usuario']['NOMBRE'] ?? null;
$id_usuario = $_SESSION['usuario']['ID_USUARIO'] ?? null;
$dni = $_SESSION['usuario']['DNI'] ?? null;


// Inicializar variables
$puntaje = 0;
$total_respondidas = 0;

// 1. Obtener puntaje y respuestas desde progreso_usuario
if ($id_usuario) {
    $stmt = $conn->prepare("SELECT SUM(puntaje_obtenido) AS total_puntos, COUNT(*) AS respondidas FROM progreso_usuario WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res->fetch_assoc();
    $puntaje = $data['total_puntos'] ?? 0;
    $total_respondidas = $data['respondidas'] ?? 0;
}

// 2. Puntaje por DNI (reemplaza si hay datos aquí)
if ($dni) {
    $stmt = $conn->prepare("SELECT SUM(PUNTAJE) AS total FROM puntajes WHERE DNI = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($fila = $resultado->fetch_assoc()) {
        $puntaje = $fila['total'] ?? 0;
    }

    $stmt2 = $conn->prepare("SELECT COUNT(*) AS respondidas FROM puntajes WHERE DNI = ?");
    $stmt2->bind_param("s", $dni);
    $stmt2->execute();
    $resultado2 = $stmt2->get_result();
    if ($fila2 = $resultado2->fetch_assoc()) {
        $total_respondidas = $fila2['respondidas'];
    }
}

$_SESSION['puntaje'] = $puntaje;
$_SESSION['respondidas'] = $total_respondidas;

// 3. Libros disponibles
if (isset($_GET['version']) && $_GET['version'] === 'otra') {
    $libros = [
        5 => ["nombre" => "Caperucita Roja", "descripcion" => "Una niña, un bosque y un lobo curioso.", "imagen" => "caperucita.png", "archivo" => "cuentos/caperucita.html"],
        6 => ["nombre" => "Los Tres Cerditos", "descripcion" => "Cerditos que construyen casas y enfrentan al lobo.", "imagen" => "cerditos.png", "archivo" => "cuentos/cerditos.html"],
        7 => ["nombre" => "1984", "descripcion" => "Una historia de realidad pura.", "imagen" => "caperucita.png", "archivo" => "cuentos/caperucita.html"],
        8 => ["nombre" => "Un mundo feliz", "descripcion" => "Nos muestra que nuestro mundo no es tan feliz como nosotros lo vemos.", "imagen" => "cerditos.png", "archivo" => "cuentos/cerditos.html"]
      ];
} else {
    $libros = [
        1 => ["nombre" => "Dracula", "descripcion" => "rácula es una novela de terror gótico escrita por el autor irlandés Bram Stoker.", "imagen" => "dracportada.jpg", "archivo" => "cuentos/dracula.php"],
        2 => ["nombre" => "El Mago de OZ", "descripcion" => "El maravilloso mago de Oz narra la historia de Dorothy, una niña que vive en Kansas y es arrastrada por un
tornado, junto a su perro Toto, hasta la mágica tierra de Oz.", "imagen" => "magodentro.jpg", "archivo" => "cuentos/magooz.php"],
  
        3 => ["nombre" => "Frankenstein", "descripcion" => "Frankenstein narra la historia de Victor, quien logra dar vida a un ser construido a partir de restos humanos. ", "imagen" => "frankportada.jpg", "archivo" => "cuentos/frankenstein.php"],
        4 => ["nombre" => "Un mundo feliz", "descripcion" => "Nos muestra que nuestro mundo no es tan feliz como nosotros lo vemos.", "imagen" => "cerditos.png", "archivo" => "cuentos/cerditos.html"]
      ];
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Book Rush - Lecturas Infantiles</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.cdnfonts.com/css/sergio-trendy" rel="stylesheet">

  <style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Sergio Trendy', sans-serif;
    font-size:23px;
    }

    body {
    background-color: #353346ff; 
    color: #1e334e; 
    }

  
    .top-bar {
    width: 100%;
    background-color: #5147aaff; 
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1100;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .top-bar h1 {
    font-size: 35px;
    color: #d85e39; 
    font-family: 'Sergio Trendy', sans-serif;
    }

    .top-bar .links a {
    margin-left: 20px;
    text-decoration: none;
    color: #1e334e; 
    font-weight: bold;
    }

    .top-bar .links a:hover {
    color: #0f6cba; 
    }

    header {
    background-color:rgb(24, 43, 83); 
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    position: fixed;
    top: 60px;
    left: 0;
    height: calc(100vh - 60px);
    width: 200px;
    box-shadow: 2px 0 10px rgb(22, 25, 26);
    z-index: 1000;
    }

    nav {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 20px;
    }

    nav a {
    text-decoration: none;
    color:rgb(200, 211, 224);
    font-weight: 500;
    padding: 8px 4px;
    border-left: 4px solid transparent;
    }

    nav a:hover {
    border-left: 4px solid #d85e39; 
    color: #d85e39;
    }

    main {
    margin-left: 200px;
    padding-top: 100px;
    }

    .hero {
    background: linear-gradient(to right, #5147aaff, #69aae7ff);
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fdfdfdff;
    font-size: 70px;
    font-weight: bold;
    text-align: center;
    }


    .section {
    padding: 40px 80px;
    font-size:25px;
    }

    .section h2 {
    color: #d85e39;
    margin-bottom: 20px;
    text-align: center;
    font-family: 'Sergio Trendy', sans-serif;
    }

    .libros-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .libro {
      background-color: #fcf2c0;
      padding: 15px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(30, 51, 78, 0.1);
      text-align: center;
    }

    .libro img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 8px;
    }

    .libro h3 {
      font-size: 1.2em;
      margin: 10px 0 5px;
      color: #1e334e;
    }

    .libro p {
      font-size: 0.95em;
      color: #333;
    }

    .libro button {
      margin-top: 10px;
      background-color: #f0c64b;
      color: #1e334e;
      border: none;
      padding: 8px 12px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }

    .libro button:hover {
      background-color: #ffd447;
    }
    .libro {
      transition: transform 0.3s ease;
    }

    .libro:hover {
      transform: scale(1.05);
    }

    .leer-mas {
    text-align: center;
    margin-top: 40px;
    }

    .leer-mas form button {
    padding: 20px 20px;
    background-color: #0f6cba;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 30px;
    }

    .leer-mas form button:hover {
    background-color: #d85e39; 
    }


    footer {
    background-color: rgba(41, 39, 37, 1);
    color: #fff;
    text-align: center;
    padding: 20px 20px;
    margin-top: 40px;
    }

    .usuario-logueado {
      display: flex;
      align-items: center;
      font-weight: bold;
      color: #9eb0c7ff;
      gap: 25px;
    }

    .usuario-logueado a {
      text-decoration: none;
      color: #d85e39;
      font-weight: bold;
    }

    .usuario-logueado a:hover {
      color: #ccbd7dff;;
    }
        .icon-logout {
      width: 70px;
      height: 70px;
      cursor: pointer;
    }

    .modal {
      display: none; /* Oculto por defecto */
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5); /* Fondo oscuro */
      justify-content: center;
      align-items: center;
    }

    .modal-contenido {
      background-color: #fff;
      padding: 30px;
      border-radius: 15px;
      text-align: center;
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
      width: 300px;
      
    }

    .modal-contenido button {
      margin: 10px;
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      background-color: #f0c64b;
      color: #1e334e;
      font-weight: bold;
      cursor: pointer;
    }

    .modal-contenido button:hover {
      background-color: #d8b238;
    }
        
    .top-icons {
    display: flex;
    gap: 30px;
    align-items: center;
    transform: translateX(-80px); 
    }
    .icon-container {
      position: relative;
    }

    .icon {
      width: 60px;
      height: 60px;
      cursor: pointer;
    }

    .tooltip {
      display: none;
      position: absolute;
      top: 60px;
      left: -40px;
      background-color: #ccbd7dff;
      color: #756e47ff;
      padding: 20px;
      border-radius: 10px;
      width: 180px;
      box-shadow: 0px 4px 8px rgba(0,0,0,0.3);
      z-index: 10;
      font-size: 14px;
    }

    .icon-container:hover .tooltip {
      display: block;
    }
    

    .tooltip ul {
      margin: 40px 10px 20px 15px;
      padding: 10;
    }
    .boton-top {
      background-color: #d17f3cff;
      color: #fafafaff;
      padding: 10px 15px;
      border-radius: 8px;
      font-weight: bold;
      text-decoration: none;
      transition: background-color 0.3s;
      border: none;
    }

    .boton-top:hover {
      background-color: #cac6ddff;
      color: #fff;
    }
    /* Estilo para el fondo oscuro */
    #confirmacion-modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.6);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Caja del modal centrado */
    .modal-content {
      background-color: white;
      padding: 30px;
      border-radius: 15px;
      text-align: center;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      max-width: 400px;
      width: 80%;
      font-size: 18px;
    }

    /* Botones del modal */
    .modal-content button {
      margin: 10px;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 8px;
      border: none;
      transition: background-color 0.3s ease;
    }

    .modal-content button.confirmar {
      background-color: #d85e39;
      color: white;
    }

    .modal-content button.cancelar {
      background-color: #ccc;
    }

    .modal-content button:hover {
      opacity: 0.9;
    }

  </style>
</head>

<script>
  function mostrarConfirmacion() {
    document.getElementById('confirmacion-modal').style.display = 'block';
  }

  function cerrarModal() {
    document.getElementById('confirmacion-modal').style.display = 'none';
  }

  function cerrarSesion() {
    window.location.href = 'logout.php';
  }
</script>

<body>

<div class="top-bar">
  <div style="display: flex; align-items: center;">
    <img src="imagenes/LOGO_BOOK_RUSH.png" alt="Logo Book Rush" style="height: 50px; margin-right: 10px;">
    <h1>Book Rush</h1>
  </div>

  <div class="top-icons">
    <?php if ($usuario): ?>
      <!-- Perfil del usuario -->
      <a href="perfil.php" style="text-decoration: none;">
        <div class="icon-container" style="cursor: pointer;">
          <img src="imagenes/usuario.png" alt="Usuario" class="icon">
          <div class="tooltip">
            <strong>Usuario:</strong> <?= htmlspecialchars($usuario) ?><br>
            <strong>DNI:</strong> <?= htmlspecialchars($dni) ?><br>
          </div>
        </div>
      </a>

      <!-- Puntaje -->
      <div class="icon-container">
        <img src="imagenes/estrella.png" alt="Puntaje" class="icon">
        <div class="tooltip">
          <strong>Total de puntos:</strong> <?= $puntaje ?><br>
          <strong>Preguntas respondidas:</strong> <?= $total_respondidas ?><br><br>
          <strong>Capítulos:</strong>
          <ul>
            <?php
              $stmt = $conn->prepare("SELECT CAPITULO, SUM(PUNTAJE) as total FROM puntajes WHERE DNI = ? GROUP BY CAPITULO");
              $stmt->bind_param("s", $dni);
              $stmt->execute();
              $res = $stmt->get_result();
              while ($fila = $res->fetch_assoc()) {
                echo "<li>" . htmlspecialchars($fila['CAPITULO']) . ": " . $fila['total'] . " pts</li>";
              }
            ?>
          </ul>
        </div>
      </div>

      <!-- Cerrar sesión con confirmación -->
    <div class="icon-container" style="cursor: pointer;">
      <img src="imagenes/puerta.png" alt="Cerrar sesión" class="icon" onclick="mostrarConfirmacion()">
    </div>

    <!-- Modal de confirmación -->
    <div id="confirmacion-modal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
      <div style="background: white; padding: 20px; border-radius: 10px; width: 300px; max-width: 80%; margin: 100px auto; text-align: center;">
        <p>¿Estás seguro de que deseas cerrar sesión?</p>
        <button onclick="cerrarSesion()" style="margin: 5px; padding: 8px 16px; background-color: #d85e39; color: white; border: none; border-radius: 5px;">Sí</button>
        <button onclick="cerrarModal()" style="margin: 5px; padding: 8px 16px; background-color: #1e334e; color: white; border: none; border-radius: 5px;">Cancelar</button>
      </div>
    </div>

      
      

    <?php else: ?>
      <a href="login.php" class="boton-top">Iniciar Sesión</a>
      <a href="registro.php" class="boton-top">Registrarse</a>

    <?php endif; ?>
  </div>
</div>


<header>
  <nav>
    <a href="nacional.php">Literatura Nacional</a>
    <a href="regional.php">Literatura Regional</a>
    <a href="universal.php">Literatura Universal</a>
    
  </nav>
</header>

<main>
  <section class="hero">
    ¡Descubre el mágico mundo de la lectura!
  </section>
  <!--libros-->
  <section class="section">
    <h2>Libros para ti</h2>
    <div class="libros-grid">
  <?php foreach ($libros as $id => $libro): ?>
    <div class="libro">
      <img src="imagenes/<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['nombre']) ?>">
      <h3><?= htmlspecialchars($libro['nombre']) ?></h3>
      <p><?= htmlspecialchars($libro['descripcion']) ?></p>
      <?php if (isset($libro['archivo']) && file_exists($libro['archivo'])): ?>
        <a href="<?= htmlspecialchars($libro['archivo']) ?>">
          <button>Leer cuento completo</button>
        </a>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>


    <div class="leer-mas">
      <?php if (isset($_GET['version']) && $_GET['version'] === 'otra'): ?>
        <!-- Botón para volver atrás -->
        <form method="get">
          <button type="submit">Volver atrás</button>
        </form>
      <?php else: ?>
        <!-- Botón para seguir leyendo -->
        <form method="get">
          <button type="submit" name="version" value="otra">Seguir leyendo</button>
        </form>
      <?php endif; ?>
    </div>

  </section>

  <footer>
    <p>&copy; 2025 Book Rush. Todos los derechos reservados.</p>
  </footer>
</main>

</body>
</html>