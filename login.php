<?php
session_start();
include 'conexion.php'; 

$mensaje = "";

// Verificar conexión
if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}

// Validamos el login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email']) && !empty($_POST['password'])) {
    
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Buscamos por email
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verificar contraseña 
        if (password_verify($password, $usuario['CONTRASENA'])) {
            
            // GUARDAR SESIÓN
            // Nota: La racha se actualiza en trivia.php, aquí solo la leemos.
            $_SESSION['id_usuario'] = $usuario['ID']; 
            $_SESSION['usuario'] = $usuario['NOMBRE']; 
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['racha'] = $usuario['racha'] ?? 0;

            if (isset($usuario['puntaje_total'])) {
                $_SESSION['puntaje'] = $usuario['puntaje_total'];
            }

            // Redirigir al inicio
            header("Location: index.php");
            exit();
        } else {
            $mensaje = "⚠️ Contraseña incorrecta.";
        }
    } else {
        $mensaje = "⚠️ No existe cuenta con este correo.";
    }
    $stmt->close();
} 
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Iniciar Sesión - Book Rush</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.cdnfonts.com/css/sergio-trendy" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .login-container {
      max-width: 450px;
      margin: 80px auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      backdrop-filter: blur(10px);
    }
    
    .login-container h2 {
      color: #1e334e;
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
      font-weight: 700;
    }
    
    .login-container label {
      display: block;
      margin-top: 20px;
      color: #1e334e;
      font-weight: 600;
      margin-bottom: 8px;
    }
    
    .login-container input {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
      box-sizing: border-box;
    }
    
    .login-container input:focus {
      outline: none;
      border-color: #4a90e2;
      box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }
    
    .login-container button {
      margin-top: 25px;
      width: 100%;
      background: linear-gradient(135deg, #4a90e2, #357abd);
      color: white;
      border: none;
      padding: 14px;
      border-radius: 8px;
      font-size: 18px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .login-container button:hover {
      background: linear-gradient(135deg, #357abd, #2868a8);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
    }
    
    .mensaje {
      margin-top: 15px;
      padding: 12px;
      background: #fee;
      color: #c33;
      text-align: center;
      border-radius: 8px;
      border-left: 4px solid #c33;
      font-weight: 600;
    }
    
    .registro-link {
      text-align: center;
      margin-top: 20px;
      color: #666;
    }
    
    .registro-link a {
      color: #4a90e2;
      text-decoration: none;
      font-weight: 600;
    }
    
    .registro-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="top-bar">
  <div style="display: flex; align-items: center;">
    <img src="imagenes/LOGO_BOOK_RUSH.png" alt="Logo Book Rush" style="height: 50px; margin-right: 10px;">
    <h1>Book Rush</h1>
  </div>
  
  <div class="top-icons">
    <a href="index.php" class="boton-top">← Volver al Inicio</a>
    <a href="registro.php" class="boton-top">Registrarse</a>
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
  <div class="login-container">
    <h2>Iniciar Sesión</h2>
    <form method="POST" autocomplete="off">
      <label for="email">Correo electrónico</label>
      <input type="email" name="email" id="email" placeholder="Ingrese su correo" required>

      <label for="password">Contraseña</label>
      <input type="password" name="password" id="password" placeholder="Ingrese su contraseña" required>

      <button type="submit">Ingresar</button>
    </form>
    
    <?php if ($mensaje): ?>
      <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>
    
    <div class="registro-link">
      ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
    </div>
  </div>
</main>

<footer>
  <p>&copy; 2025 Book Rush. Todos los derechos reservados.</p>
</footer>

</body>
</html>