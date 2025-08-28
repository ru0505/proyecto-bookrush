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

// Verificamos que el ID sea válido
$puntaje_total = 0;
if ($id_usuario) {
    $stmt = $conn->prepare("SELECT puntaje_total FROM usuarios WHERE ID_USUARIO = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($row = $resultado->fetch_assoc()) {
        $puntaje_total = $row['puntaje_total'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

  <meta charset="UTF-8">
  <title>Perfil - Book Rush</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      background-color: #fcf2c0;
      font-family: 'Inter', sans-serif;
    }
    .navbar {
      background-color: #0f6cba;
      padding: 10px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .navbar a {
      color: white;
      text-decoration: none;
      margin-right: 20px;
      font-weight: bold;
    }
    .navbar .nav-left a {
      margin-right: 15px;
    }
    .perfil-container {
      max-width: 500px;
      margin: 100px auto;
      background: #fff;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #0f6cba;
      text-align: center;
    }
    .info {
      margin-top: 20px;
      line-height: 1.8;
    }
    .info strong {
      color: #1e334e;
    }
    .puntaje {
      margin-top: 20px;
      padding: 10px;
      background: #f0c64b;
      color: #1e334e;
      font-size: 18px;
      text-align: center;
      border-radius: 8px;
    }
  </style>
</head>
<body>

  <!-- Navegación -->
  <div class="navbar">
    <div class="nav-left">
      <a href="index.php">🏠 Inicio</a>
    </div>
    <div class="nav-right">
      <a href="logout.php">🚪Cerrar Sesión</a>
    </div>
  </div>

  <div class="perfil-container">
    <h2>Perfil de Usuario</h2>
    <div class="info">
      <p><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
      <p><strong>DNI:</strong> <?= htmlspecialchars($dni) ?></p>
      <p><strong>Rol:</strong> <?= htmlspecialchars($rol) ?></p>
    </div>
    <div class="puntaje">
      🎯 Puntaje Total: <strong><?= htmlspecialchars($puntaje_total) ?></strong>
    </div>
  </div>
</body>
</html>
