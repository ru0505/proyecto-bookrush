<?php
include 'conexion.php';
$mensaje = "";

// 1. Validamos que llegue 'email', 'dni', 'nombre' y 'password'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['dni'], $_POST['nombre'], $_POST['password'])) {
    
    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']); // Guardamos el email
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = 'lector';

    // 2. Verificamos duplicados por Email
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $mensaje = "El correo electrónico ya está registrado.";
    } else {
        // Verificamos duplicados por DNI también
        $stmt2 = $conn->prepare("SELECT * FROM usuarios WHERE DNI = ?");
        $stmt2->bind_param("s", $dni);
        $stmt2->execute();
        $resultado2 = $stmt2->get_result();
        
        if ($resultado2->num_rows > 0) {
            $mensaje = "El DNI ya está registrado.";
        } else {
            // 3. Insertamos el usuario con DNI y email, ponemos 'NOW()' en ultimo_acceso.
            // Así la cuenta nace "activa" para el sistema de recordatorios.
            $sql = "INSERT INTO usuarios (DNI, NOMBRE, email, CONTRASENA, ROL, ultimo_acceso) VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt3 = $conn->prepare($sql);
            // "sssss" -> DNI, Nombre, Email, Pass, Rol (5 strings)
            $stmt3->bind_param("sssss", $dni, $nombre, $email, $password, $rol);
            
            if ($stmt3->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $mensaje = "Error al registrar usuario: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro - Book Rush</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://fonts.cdnfonts.com/css/sergio-trendy" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    .registro-container {
      max-width: 450px;
      margin: 80px auto;
      background: rgba(255, 255, 255, 0.95);
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      backdrop-filter: blur(10px);
    }
    
    .registro-container h2 {
      color: #1e334e;
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
      font-weight: 700;
    }
    
    .registro-container label {
      display: block;
      margin-top: 20px;
      color: #1e334e;
      font-weight: 600;
      margin-bottom: 8px;
    }
    
    .registro-container input {
      width: 100%;
      padding: 12px 15px;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
      box-sizing: border-box;
    }
    
    .registro-container input:focus {
      outline: none;
      border-color: #4a90e2;
      box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }
    
    .registro-container button {
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
    
    .registro-container button:hover {
      background: linear-gradient(135deg, #357abd, #2868a8);
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);
    }
    
    .registro-container .volver {
      display: block;
      text-align: center;
      margin-top: 20px;
      color: #4a90e2;
      text-decoration: none;
      font-weight: 600;
      padding: 12px;
      border: 2px solid #4a90e2;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .registro-container .volver:hover {
      background-color: #4a90e2;
      color: white;
      transform: translateY(-2px);
    }
    
    .mensaje {
      margin-top: 20px;
      padding: 12px;
      border-radius: 8px;
      text-align: center;
      font-weight: 600;
      background-color: #fff3cd;
      color: #856404;
      border: 1px solid #ffeaa7;
    }
  </style>
</head>
<body>
  <div class="registro-container">
    <h2>Crear Cuenta</h2>
    <form method="POST" autocomplete="off">
      <label for="dni">DNI</label>
      <input type="text" name="dni" placeholder="Ingrese su DNI" required maxlength="20">

      <label for="nombre">Nombre</label>
      <input type="text" name="nombre" placeholder="Ingrese su nombre completo" required>

      <label for="email">Correo Electrónico</label>
      <input type="email" name="email" placeholder="ejemplo@correo.com" required>

      <label for="password">Contraseña</label>
      <input type="password" name="password" placeholder="Crea una contraseña segura" required minlength="6">

      <button type="submit">Registrarse</button>
    </form>
    
    <a href="index.php" class="volver">← Volver al inicio</a>
    
    <?php if ($mensaje): ?>
      <div class="mensaje <?= strpos($mensaje, '❌') !== false || strpos($mensaje, '⚠️') !== false ? 'error' : '' ?>">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
