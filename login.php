<?php
session_start();
include 'conexion.php';

$mensaje = "";

// Verificar conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['dni']) && !empty($_POST['password'])) {
    $dni = trim($_POST['dni']);
    $password = $_POST['password'];

    // Buscar usuario por DNI
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE DNI = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verificar contraseña
        if (password_verify($password, $usuario['CONTRASENA'])) {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['dni'] = $usuario['DNI'];

            if (isset($usuario['puntaje_total'])) {
                $_SESSION['puntaje'] = $usuario['puntaje_total'];
            }

            // Redirige al index en la sección login
            header("Location: index.php#login");
            exit();
        } else {
            $mensaje = "⚠️ Contraseña incorrecta.";
        }
    } else {
        $mensaje = "⚠️ Usuario no encontrado.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">
  <meta charset="UTF-8">
  <title>Iniciar Sesión - Book Rush</title>
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
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      overflow: hidden;
    }

    /* Partículas flotantes en el fondo */
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

    /* Partículas secundarias con movimiento más lento */
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

    .form-container {
      max-width: 420px;
      width: 90%;
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
      margin-bottom: 30px;
      font-size: 32px;
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

    label {
      display: block;
      margin-top: 20px;
      color: #1e334e;
      font-weight: 600;
      font-size: 16px;
    }

    input {
      width: 100%;
      padding: 12px 15px;
      margin-top: 8px;
      border: 2px solid #3b82f6;
      border-radius: 10px;
      font-size: 16px;
      transition: all 0.3s ease;
      background: rgba(255, 255, 255, 0.9);
      font-family: 'Fredoka', sans-serif;
    }

    input:focus {
      outline: none;
      border-color: #ff8c42;
      box-shadow: 0 0 15px rgba(255, 140, 66, 0.4);
      transform: scale(1.02);
    }

    button {
      margin-top: 25px;
      width: 100%;
      background: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%);
      color: white;
      border: none;
      padding: 14px;
      border-radius: 10px;
      font-size: 18px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
      font-family: 'Fredoka', sans-serif;
    }

    button:hover {
      background: linear-gradient(135deg, #ff8c42 0%, #d85e39 100%);
      box-shadow: 0 12px 30px rgba(255, 140, 66, 0.6);
      transform: translateY(-2px);
    }

    button:active {
      transform: translateY(0);
      box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
    }

    .mensaje {
      margin-top: 15px;
      color: #d85e39;
      text-align: center;
      font-weight: 600;
      font-size: 15px;
      padding: 10px;
      background: rgba(216, 94, 57, 0.1);
      border-radius: 8px;
      border-left: 4px solid #d85e39;
    }

    .volver {
      display: inline-block;
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      color: white;
      padding: 14px;
      border-radius: 10px;
      border: none;
      cursor: pointer;
      width: 100%;
      font-size: 18px;
      font-weight: 700;
      text-align: center;
      text-decoration: none;
      margin-top: 15px;
      transition: all 0.3s ease;
      box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
      font-family: 'Fredoka', sans-serif;
    }

    .volver:hover {
      background: linear-gradient(135deg, #f0c64b 0%, #d8a93c 100%);
      box-shadow: 0 12px 30px rgba(240, 198, 75, 0.6);
      transform: translateY(-2px);
    }

    .volver:active {
      transform: translateY(0);
      box-shadow: 0 6px 15px rgba(16, 185, 129, 0.4);
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Iniciar Sesión</h2>
    <form method="POST" autocomplete="off">
      <label for="dni">DNI</label>
      <input type="text" name="dni" required>

      <label for="password">Contraseña</label>
      <input type="password" name="password" required>

      <button type="submit">Ingresar</button>
    </form>
    <a href="index.php" class="volver">← Volver al inicio</a>

    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
  </div>
</body>
</html>
