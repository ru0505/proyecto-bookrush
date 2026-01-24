<?php
session_start();
session_destroy();
session_unset();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sesión Limpiada</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
        }
        h1 { color: #667eea; margin-bottom: 20px; }
        p { color: #555; font-size: 1.1em; line-height: 1.6; }
        .success { font-size: 60px; margin-bottom: 20px; }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 15px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success">✅</div>
        <h1>Sesión Limpiada</h1>
        <p>La sesión de PHP y el caché han sido eliminados correctamente.</p>
        <p>Todas las preguntas aleatorias y datos de sesión se han reiniciado.</p>
        <a href="index.php" class="btn">Ir a Inicio</a>
    </div>
</body>
</html>
