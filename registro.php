<?php
include 'conexion.php';
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dni'], $_POST['nombre'], $_POST['password'])) {
    $dni = trim($_POST['dni']);
    $nombre = trim($_POST['nombre']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $rol = 'lector';

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE DNI = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $mensaje = "⚠️ El DNI ya está registrado.";
    } else {
        $stmt = $conn->prepare("INSERT INTO usuarios (DNI, NOMBRE, CONTRASENA, ROL) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $dni, $nombre, $password, $rol);
        if ($stmt->execute()) {
            header("Location: login.php");
           exit;
        } else {
            $mensaje = "❌ Error al registrar usuario.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>


  <meta charset="UTF-8">
  <title>Registro - Book Rush</title>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #fcf2c0;
      font-family: 'Fredoka', sans-serif;
    }
    .form-container {
      max-width: 480px;
      margin: 100px auto;
      background: #fff;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    h2 {
      color: #d85e39;
      text-align: center;
      margin-bottom: 25px;
    }
    label {
      display: block;
      margin-top: 18px;
      color: #1e334e;
      font-weight: bold;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 6px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    button {
      margin-top: 25px;
      width: 100%;
      background-color: #0f6cba;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 8px;
      font-size: 17px;
      cursor: pointer;
    }
    button:hover {
      background-color: #d85e39;
    }
    .mensaje {
      margin-top: 15px;
      font-weight: bold;
      text-align: center;
      color: #1e334e;
    }
    .volver {
      display: inline-block;
      background-color: #5aa58cff;  /* Mismo color que Ingresar */
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
      background-color: #b1c4c7ff; /* Efecto hover opcional */
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Registro de Usuario</h2>
    <form method="POST" autocomplete="off">
      <label for="dni">DNI</label>
      <input type="text" name="dni" required maxlength="15">

      <label for="nombre">Nombre</label>
      <input type="text" name="nombre" required>

      <label for="password">Contraseña</label>
      <input type="password" name="password" required>

      <button type="submit">Registrarse</button>
    </form>
    <a href="index.php" class="volver">← Volver al inicio</a>
    <div class="mensaje"><?= $mensaje ?></div>
  </div>
</body>
</html>
