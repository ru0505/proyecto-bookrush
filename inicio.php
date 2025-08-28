<?php
session_start();
$usuario = $_SESSION['usuario'] ?? null;
$dni = $_SESSION['dni'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Book Rush</title>
  <link rel="stylesheet" href="estilo.css">
</head>
<body>
  <header>
    <div class="logo">Book Rush</div>
    <nav>
      <ul>
        <?php if ($usuario): ?>
          <li><a href="cliente.php">Explorar</a></li>
          <li><a href="logout.php">Cerrar sesión</a></li>
        <?php else: ?>
          <li><a href="login.php">Iniciar sesión</a></li>
          <li><a href="registro.php">Registrarse</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <main>
    <section class="bienvenida">
      <h1>Bienvenido a Book Rush</h1>
      <p>¡Una aventura de lectura para niños y niñas!</p>
    </section>

    <?php if ($usuario): ?>
      <section class="contenido">
        <p>Hola, <strong><?= htmlspecialchars($usuario) ?></strong> (DNI: <?= htmlspecialchars($dni) ?>)</p>
        <a href="cliente.php" class="boton">Ir a mis libros</a>
      </section>
    <?php else: ?>
      <section class="contenido">
        <p>Inicia sesión o regístrate para comenzar a leer cuentos y ganar puntos.</p>
        <a href="login.php" class="boton">Iniciar sesión</a>
        <a href="registro.php" class="boton">Registrarse</a>
      </section>
    <?php endif; ?>
  </main>

  <footer>
    <p>&copy; 2025 Book Rush - Literatura infantil para todos</p>
  </footer>
</body>
</html>
