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
            $_SESSION['usuario'] = $usuario['NOMBRE']; 
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
} // <-- cierre del if POST
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <link href="https://fonts.cdnfonts.com/css/sergio-trendy" rel="stylesheet">

  <meta charset="UTF-8">
  <title>Iniciar Sesión - Book Rush</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #162a55ff;
      font-family: 'Sergio Trendy', sans-serif;
    }
    .form-container {
      max-width: 400px;
      margin: 120px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    h2 {
      color: #000000ff;
      text-align: center;
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-top: 15px;
      color: #1e334e;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      margin-top: 20px;
      width: 100%;
      background-color: #317fbeff;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 6px;
      font-size: 16px;
    }
    button:hover {
      background-color: #27424dff;
    }
    .mensaje {
      margin-top: 10px;
      color: red;
      text-align: center;
    }
    .volver {
      display: inline-block;
      background-color: #1a966dff;  /* Mismo color que Ingresar */
      color: white;
      padding: 10px;
      border-radius: 6px;
      border: none;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      text-align: center;
      text-decoration: none;  /* Quitar subrayado */
      margin-top: 20px;
    }

    .volver:hover {
      background-color: #25383bff; /* Efecto hover opcional */
    }
</style>
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
