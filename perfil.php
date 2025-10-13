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
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #353346ff 0%, #2a2a3e 50%, #1e1e2e 100%);
      font-family: 'Fredoka', sans-serif;
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
    }

    /* Partículas flotantes principales */
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: 
        radial-gradient(3px 3px at 20px 30px, rgba(255, 140, 66, 0.7), transparent),
        radial-gradient(2px 2px at 40px 70px, rgba(59, 130, 246, 0.7), transparent),
        radial-gradient(2px 2px at 90px 40px, rgba(216, 94, 57, 0.7), transparent),
        radial-gradient(2px 2px at 130px 80px, rgba(240, 198, 75, 0.7), transparent),
        radial-gradient(3px 3px at 160px 30px, rgba(255, 140, 66, 0.6), transparent),
        radial-gradient(2px 2px at 200px 60px, rgba(59, 130, 246, 0.6), transparent);
      background-repeat: repeat;
      background-size: 300px 200px;
      animation: floatParticles 20s linear infinite;
      pointer-events: none;
      z-index: 0;
    }

    @keyframes floatParticles {
      0% { transform: translate(0, 0); }
      100% { transform: translate(-300px, -200px); }
    }

    /* Partículas secundarias */
    body::after {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: 
        radial-gradient(1px 1px at 50px 50px, rgba(255, 255, 255, 0.2), transparent),
        radial-gradient(2px 2px at 100px 100px, rgba(255, 140, 66, 0.15), transparent),
        radial-gradient(1px 1px at 150px 150px, rgba(59, 130, 246, 0.15), transparent);
      background-repeat: repeat;
      background-size: 400px 200px;
      animation: floatParticlesSlow 25s linear infinite;
      pointer-events: none;
      z-index: 0;
    }

    @keyframes floatParticlesSlow {
      0% { transform: translate(0, 0); }
      100% { transform: translate(400px, 200px); }
    }

    .navbar {
      width: 100%;
      background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
      background-size: 200% 200%;
      padding: 15px 30px;
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
    }

    @keyframes slideDown {
      0% { transform: translateY(-100%); opacity: 0; }
      100% { transform: translateY(0); opacity: 1; }
    }

    @keyframes topBarMove {
      0%, 100% { background-position: 0% 50%; }
      25% { background-position: 100% 50%; }
      50% { background-position: 0% 100%; }
      75% { background-position: 100% 0%; }
    }

    .navbar a {
      color: white;
      text-decoration: none;
      font-weight: 700;
      font-size: 18px;
      padding: 8px 16px;
      border-radius: 8px;
      transition: all 0.3s ease;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .navbar a:hover {
      background: rgba(255, 140, 66, 0.3);
      box-shadow: 0 4px 15px rgba(255, 140, 66, 0.4);
      transform: translateY(-2px);
    }

    .navbar .nav-left a {
      margin-right: 15px;
    }

    .perfil-container {
      max-width: 550px;
      margin: 120px auto 50px;
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5), 0 0 40px rgba(59, 130, 246, 0.3);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      z-index: 10;
      animation: slideIn 0.8s ease-out;
    }

    @keyframes slideIn {
      0% { 
        opacity: 0;
        transform: translateY(-30px) scale(0.95);
      }
      100% { 
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    h2 {
      color: #ff8c42;
      text-align: center;
      font-size: 36px;
      margin-bottom: 30px;
      text-shadow: 0 2px 10px rgba(255, 140, 66, 0.4);
      animation: glowTitle 3s ease-in-out infinite alternate;
    }

    @keyframes glowTitle {
      0% { 
        color: #ff8c42;
        text-shadow: 0 2px 10px rgba(255, 140, 66, 0.5);
      }
      100% { 
        color: #ffab70;
        text-shadow: 0 2px 15px rgba(255, 171, 112, 0.8);
      }
    }

    .info {
      margin-top: 25px;
      line-height: 2;
      font-size: 18px;
    }

    .info p {
      margin-bottom: 15px;
      padding: 12px;
      background: rgba(59, 130, 246, 0.08);
      border-radius: 10px;
      border-left: 4px solid #3b82f6;
      transition: all 0.3s ease;
    }

    .info p:hover {
      background: rgba(59, 130, 246, 0.15);
      transform: translateX(5px);
      box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
    }

    .info strong {
      color: #1e334e;
      font-weight: 700;
    }

    .puntaje {
      margin-top: 30px;
      padding: 20px;
      background: linear-gradient(135deg, #f0c64b 0%, #ffab70 100%);
      color: #1e334e;
      font-size: 22px;
      font-weight: 700;
      text-align: center;
      border-radius: 12px;
      box-shadow: 0 8px 25px rgba(240, 198, 75, 0.5);
      animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { 
        transform: scale(1);
        box-shadow: 0 8px 25px rgba(240, 198, 75, 0.5);
      }
      50% { 
        transform: scale(1.02);
        box-shadow: 0 12px 35px rgba(240, 198, 75, 0.7);
      }
    }

    .puntaje strong {
      color: #d85e39;
      font-size: 28px;
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
