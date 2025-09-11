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
  <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

  <style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Fredoka', sans-serif;
    font-size:23px;
    }

    body {
    background: linear-gradient(135deg, #353346ff 0%, #2a2a3e 50%, #1e1e2e 100%);
    color: #1e334e;
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
    }

    /* Partículas adicionales con diferente velocidad */
    body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(1px 1px at 50px 50px, rgba(255, 255, 255, 0.2), transparent),
        radial-gradient(2px 2px at 100px 100px, rgba(255, 140, 66, 0.15), transparent),
        radial-gradient(1px 1px at 150px 150px, rgba(59, 130, 246, 0.15), transparent),
        radial-gradient(2px 2px at 200px 200px, rgba(216, 94, 57, 0.15), transparent);
    background-repeat: repeat;
    background-size: 400px 200px;
    animation: floatParticlesSlow 25s linear infinite;
    pointer-events: none;
    z-index: -10;
    }
  
    .top-bar {
    width: 100%;
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #60a5fa 100%);
    background-size: 200% 200%;
    padding: 15px 20px;
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
    overflow: hidden;
    min-height: 70px;
    }

    @keyframes slideDown {
    0% { transform: translateY(-100%); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
    }

    /* Animación de movimiento para la barra superior */
    @keyframes topBarMove {
    0%, 100% { 
      background-position: 0% 50%;
      transform: translateY(0px);
    }
    25% { 
      background-position: 100% 50%;
      transform: translateY(-1px);
    }
    50% { 
      background-position: 0% 100%;
      transform: translateY(0px);
    }
    75% { 
      background-position: 100% 0%;
      transform: translateY(1px);
    }
    }

    .top-bar h1 {
    font-size: 35px;
    color: #ff8c42;
    font-family: 'Fredoka', sans-serif;
    text-shadow: 0 2px 10px rgba(255, 140, 66, 0.5);
    animation: glowTitle 3s ease-in-out infinite alternate;
    z-index: 1200;
    position: relative;
    }

    @keyframes glowTitle {
    0% { 
      color: #ff8c42;
      text-shadow: 0 2px 10px rgba(255, 140, 66, 0.5), 0 0 20px rgba(255, 140, 66, 0.3);
    }
    100% { 
      color: #ffab70;
      text-shadow: 0 2px 15px rgba(255, 171, 112, 0.8), 0 0 30px rgba(255, 171, 112, 0.5);
    }
    }

    /* Partículas flotantes en el fondo - mejoradas y aumentadas */
    body::after {
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
        radial-gradient(2px 2px at 200px 60px, rgba(59, 130, 246, 0.6), transparent),
        radial-gradient(2px 2px at 250px 90px, rgba(216, 94, 57, 0.6), transparent),
        radial-gradient(3px 3px at 300px 20px, rgba(240, 198, 75, 0.6), transparent),
        radial-gradient(4px 4px at 350px 50px, rgba(255, 140, 66, 0.5), transparent),
        radial-gradient(3px 3px at 400px 80px, rgba(59, 130, 246, 0.5), transparent),
        radial-gradient(2px 2px at 450px 40px, rgba(216, 94, 57, 0.5), transparent),
        radial-gradient(3px 3px at 500px 70px, rgba(240, 198, 75, 0.5), transparent);
    background-repeat: repeat;
    background-size: 250px 120px;
    animation: floatParticles 18s linear infinite;
    pointer-events: none;
    z-index: -5;
    }

    /* Capa de partículas más pequeñas */
    body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(2px 2px at 30px 40px, rgba(255, 255, 255, 0.6), transparent),
        radial-gradient(2px 2px at 80px 90px, rgba(255, 140, 66, 0.5), transparent),
        radial-gradient(2px 2px at 120px 140px, rgba(59, 130, 246, 0.5), transparent),
        radial-gradient(3px 3px at 180px 180px, rgba(216, 94, 57, 0.5), transparent),
        radial-gradient(2px 2px at 220px 220px, rgba(240, 198, 75, 0.5), transparent),
        radial-gradient(2px 2px at 280px 50px, rgba(255, 255, 255, 0.4), transparent),
        radial-gradient(3px 3px at 320px 120px, rgba(255, 140, 66, 0.4), transparent),
        radial-gradient(2px 2px at 380px 160px, rgba(59, 130, 246, 0.4), transparent),
        radial-gradient(2px 2px at 50px 200px, rgba(255, 255, 255, 0.4), transparent),
        radial-gradient(3px 3px at 150px 250px, rgba(216, 94, 57, 0.4), transparent);
    background-repeat: repeat;
    background-size: 350px 180px;
    animation: floatParticlesSlow 22s linear infinite;
    pointer-events: none;
    z-index: -10;
    }

    @keyframes floatParticles {
    0% { 
      transform: translateY(100vh) translateX(0px) rotate(0deg);
      opacity: 0;
    }
    10% {
      opacity: 1;
    }
    90% {
      opacity: 1;
    }
    100% { 
      transform: translateY(-100px) translateX(100px) rotate(360deg);
      opacity: 0;
    }
    }

    @keyframes floatParticlesSlow {
    0% { 
      transform: translateY(100vh) translateX(-50px) rotate(0deg);
      opacity: 0;
    }
    15% {
      opacity: 1;
    }
    85% {
      opacity: 1;
    }
    100% { 
      transform: translateY(-100px) translateX(75px) rotate(-360deg);
      opacity: 0;
    }
    }

    .top-bar .links a {
    margin-left: 20px;
    text-decoration: none;
    color: #ffffff;
    font-weight: bold;
    transition: all 0.3s ease;
    position: relative;
    }

    .top-bar .links a::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #d85e39, #f0c64b);
    transition: width 0.3s ease;
    }

    .top-bar .links a:hover {
    color: #f0c64b;
    transform: translateY(-2px);
    }

    .top-bar .links a:hover::after {
    width: 100%;
    }

    header {
    background: linear-gradient(180deg, rgba(24, 43, 83, 0.98) 0%, rgba(30, 51, 78, 0.95) 100%);
    padding: 20px 15px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    position: fixed;
    top: 70px;
    left: 0;
    height: calc(100vh - 70px);
    width: 220px;
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.4);
    z-index: 1000;
    backdrop-filter: blur(15px);
    border-right: 2px solid rgba(255, 140, 66, 0.3);
    animation: slideInLeft 0.8s ease-out;
    overflow-y: auto;
    }

    /* Animación ondulante en el menú */
    header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, 
        rgba(59, 130, 246, 0.1) 0%, 
        rgba(216, 94, 57, 0.1) 25%,
        rgba(240, 198, 75, 0.1) 50%,
        rgba(59, 130, 246, 0.1) 75%,
        rgba(216, 94, 57, 0.1) 100%
    );
    background-size: 400% 400%;
    animation: waveMenu 8s ease-in-out infinite;
    z-index: -1;
    }

    @keyframes waveMenu {
    0%, 100% { 
        background-position: 0% 50%; 
        transform: translateY(0px);
    }
    25% { 
        background-position: 100% 50%; 
        transform: translateY(-5px);
    }
    50% { 
        background-position: 0% 100%; 
        transform: translateY(0px);
    }
    75% { 
        background-position: 100% 0%; 
        transform: translateY(5px);
    }
    }

    @keyframes slideInLeft {
    0% { transform: translateX(-100%); opacity: 0; }
    100% { transform: translateX(0); opacity: 1; }
    }

    nav {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 30px;
    width: 100%;
    z-index: 1001;
    position: relative;
    }

    nav a {
    text-decoration: none;
    color: #ffffff;
    background: linear-gradient(135deg, #ff8c42 0%, #e67347 100%);
    font-weight: 600;
    padding: 15px 12px;
    border: none;
    border-radius: 8px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    margin-bottom: 8px;
    box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    text-align: center;
    display: block;
    }

    nav a::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.6s ease;
    }

    nav a:hover::before {
    left: 100%;
    }

    nav a:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    color: #ffffff;
    transform: translateX(5px) scale(1.02);
    box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
    border-left: 4px solid #60a5fa;
    }

    main {
    margin-left: 220px;
    padding-top: 90px;
    animation: fadeIn 1.2s ease-out;
    position: relative;
    z-index: 1;
    }

    @keyframes fadeIn {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
    }

    .section {
    padding: 60px 80px;
    font-size: 25px;
    }

    .section h2 {
    color: #ff8c42;
    margin-bottom: 40px;
    text-align: center;
    font-family: 'Fredoka', sans-serif;
    font-size: 2.8em;
    text-shadow: 0 3px 15px rgba(255, 140, 66, 0.4);
    animation: titleBounce 4s ease-in-out infinite;
    }

    @keyframes titleBounce {
    0%, 100% { transform: translateY(0px) scale(1); }
    25% { transform: translateY(-3px) scale(1.02); }
    50% { transform: translateY(0px) scale(1); }
    75% { transform: translateY(-1px) scale(1.01); }
    }

    .libros-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 30px;
      margin: 50px auto;
      max-width: 1000px;
      perspective: 1000px;
    }

    .libro {
      background: linear-gradient(145deg, #f5f0e8 0%, #ede4d3 50%, #e6d7c3 100%);
      padding: 30px;
      border-radius: 25px;
      box-shadow: 
        0 15px 40px rgba(30, 51, 78, 0.25),
        0 8px 16px rgba(0, 0, 0, 0.15),
        inset 0 2px 4px rgba(255, 255, 255, 0.8);
      text-align: center;
      transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      position: relative;
      overflow: hidden;
      transform-style: preserve-3d;
      min-height: 450px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      width: 100%;
    }

    .libro::before {
      content: '';
      position: absolute;
      top: -2px;
      left: -2px;
      right: -2px;
      bottom: -2px;
      background: linear-gradient(45deg, #ff8c42, #3b82f6, #f0c64b, #d85e39);
      background-size: 400% 400%;
      border-radius: 25px;
      z-index: -1;
      opacity: 0;
      transition: opacity 0.3s ease;
      animation: gradientBorder 4s ease-in-out infinite;
    }

    @keyframes gradientBorder {
      0%, 100% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
    }

    .libro:hover::before {
      opacity: 1;
    }

    .libro:hover {
      transform: translateY(-20px) rotateX(8deg) rotateY(-8deg) scale(1.08);
      box-shadow: 
        0 30px 60px rgba(30, 51, 78, 0.4),
        0 20px 40px rgba(255, 140, 66, 0.3),
        inset 0 2px 4px rgba(255, 255, 255, 0.9);
    }

    .libro img {
      width: 100%;
      height: 240px;
      object-fit: cover;
      border-radius: 15px;
      transition: all 0.5s ease;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
      margin-bottom: 20px;
    }

    .libro:hover img {
      transform: scale(1.15) rotateZ(3deg);
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
    }

    .libro h3 {
      font-size: 1.4em;
      margin: 15px 0 10px;
      color: #1e334e;
      font-weight: bold;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
      animation: textFloat 3s ease-in-out infinite;
    }

    @keyframes textFloat {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-2px); }
    }

    .libro p {
      font-size: 1em;
      color: #444;
      line-height: 1.6;
      margin-bottom: 20px;
      flex-grow: 1;
    }

    .libro button {
      margin-top: 20px;
      background: linear-gradient(135deg, #f0c64b 0%, #f39c12 50%, #e67e22 100%);
      color: #1e334e;
      border: none;
      padding: 18px 30px;
      border-radius: 30px;
      cursor: pointer;
      font-weight: bold;
      font-size: 18px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 6px 20px rgba(240, 198, 75, 0.4);
      position: relative;
      overflow: hidden;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      min-width: 200px;
    }

    .libro button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
      transition: left 0.5s ease;
    }

    .libro button:hover::before {
      left: 100%;
    }

    .libro button:hover {
      background: linear-gradient(135deg, #ffd447 0%, #f1c40f 50%, #f39c12 100%);
      transform: translateY(-4px) scale(1.05);
      box-shadow: 0 12px 35px rgba(240, 198, 75, 0.6);
    }

    .libro button:active {
      transform: translateY(-2px) scale(1.02);
      box-shadow: 0 8px 25px rgba(240, 198, 75, 0.5);
    }

    .leer-mas {
    text-align: center;
    margin-top: 50px;
    animation: bounceIn 1s ease-out;
    }

    @keyframes bounceIn {
    0% { transform: scale(0.3) translateY(50px); opacity: 0; }
    50% { transform: scale(1.05); }
    70% { transform: scale(0.9); }
    100% { transform: scale(1) translateY(0); opacity: 1; }
    }

    .leer-mas form button {
    padding: 18px 35px;
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 50%, #1e3a8a 100%);
    color: white;
    border: none;
    border-radius: 30px;
    cursor: pointer;
    font-size: 28px;
    font-weight: bold;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 
      0 8px 25px rgba(59, 130, 246, 0.4),
      0 4px 10px rgba(0, 0, 0, 0.2);
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 1px;
    }

    .leer-mas form button::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, rgba(255,255,255,0.1), transparent, rgba(255,255,255,0.1));
    transform: translateX(-100%);
    transition: transform 0.6s ease;
    }

    .leer-mas form button:hover::before {
    transform: translateX(100%);
    }

    .leer-mas form button:hover {
    background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 50%, #2563eb 100%);
    transform: translateY(-5px) scale(1.05);
    box-shadow: 
      0 15px 40px rgba(59, 130, 246, 0.6),
      0 8px 20px rgba(0, 0, 0, 0.3);
    }


    footer {
    background: linear-gradient(135deg, rgba(41, 39, 37, 0.95) 0%, rgba(30, 30, 30, 0.9) 100%);
    color: #fff;
    text-align: center;
    padding: 30px 20px;
    margin-top: 60px;
    backdrop-filter: blur(10px);
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    animation: slideUp 0.8s ease-out;
    }

    @keyframes slideUp {
    0% { transform: translateY(100%); opacity: 0; }
    100% { transform: translateY(0); opacity: 1; }
    }

    .usuario-logueado {
      display: flex;
      align-items: center;
      font-weight: bold;
      color: #ffffff;
      gap: 25px;
    }

    .usuario-logueado a {
      text-decoration: none;
      color: #60a5fa;
      font-weight: bold;
      transition: all 0.3s ease;
      position: relative;
    }

    .usuario-logueado a::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #60a5fa, #3b82f6);
      transition: width 0.3s ease;
    }

    .usuario-logueado a:hover {
      color: #93c5fd;
      transform: translateY(-1px);
    }

    .usuario-logueado a:hover::after {
      width: 100%;
    }

    .icon-logout {
      width: 70px;
      height: 70px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .icon-logout:hover {
      transform: rotate(10deg) scale(1.1);
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      justify-content: center;
      align-items: center;
      backdrop-filter: blur(5px);
      animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
      0% { opacity: 0; }
      100% { opacity: 1; }
    }

    .modal-contenido {
      background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
      padding: 40px;
      border-radius: 20px;
      text-align: center;
      box-shadow: 
        0 20px 50px rgba(0, 0, 0, 0.4),
        0 10px 20px rgba(0, 0, 0, 0.2);
      width: 350px;
      transform: scale(0.7);
      animation: modalSlideIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
    }

    @keyframes modalSlideIn {
      0% { transform: scale(0.7) translateY(-50px); opacity: 0; }
      100% { transform: scale(1) translateY(0); opacity: 1; }
    }

    .modal-contenido button {
      margin: 15px 10px;
      padding: 12px 25px;
      border: none;
      border-radius: 25px;
      background: linear-gradient(135deg, #f0c64b 0%, #f39c12 100%);
      color: #1e334e;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 15px rgba(240, 198, 75, 0.3);
    }

    .modal-contenido button:hover {
      background: linear-gradient(135deg, #ffd447 0%, #f1c40f 100%);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(240, 198, 75, 0.5);
    }
        
    .top-icons {
    display: flex;
    gap: 30px;
    align-items: center;
    transform: translateX(-80px); 
    z-index: 1200;
    position: relative;
    }

    .icon-container {
      position: relative;
      transition: all 0.3s ease;
    }

    .icon-container:hover {
      transform: translateY(-3px);
    }

    .icon {
      width: 60px;
      height: 60px;
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }

    .icon:hover {
      transform: scale(1.15) rotate(5deg);
      filter: drop-shadow(0 8px 15px rgba(0, 0, 0, 0.3));
    }

    .tooltip {
      display: none;
      position: absolute;
      top: 70px;
      left: -40px;
      background: linear-gradient(145deg, rgba(204, 189, 125, 0.95) 0%, rgba(117, 110, 71, 0.9) 100%);
      color: #ffffff;
      padding: 25px;
      border-radius: 15px;
      width: 200px;
      box-shadow: 
        0 10px 30px rgba(0, 0, 0, 0.4),
        0 5px 15px rgba(0, 0, 0, 0.2);
      z-index: 10;
      font-size: 14px;
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      animation: tooltipSlideIn 0.3s ease-out;
    }

    @keyframes tooltipSlideIn {
      0% { opacity: 0; transform: translateY(-10px) scale(0.9); }
      100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    .icon-container:hover .tooltip {
      display: block;
    }
      content: '';
      position: absolute;
      top: 67px;
      left: 50%;
      transform: translateX(-50%);
      width: 0;
      height: 0;
      border-left: 6px solid transparent;
      border-right: 6px solid transparent;
      border-bottom: 8px solid rgba(30, 51, 78, 0.95);
      z-index: 1501;
    }

    @keyframes tooltipPop {
      0% { 
        opacity: 0; 
        transform: translateX(-50%) scale(0.8) translateY(10px); 
      }
      100% { 
        opacity: 1; 
        transform: translateX(-50%) scale(1) translateY(0); 
      }
    }

    .tooltip ul {
      margin: 40px 10px 20px 15px;
      padding: 10px;
    }

    .tooltip li {
      transition: all 0.2s ease;
      padding: 2px 0;
    }

    .tooltip li:hover {
      color: #f0c64b;
      transform: translateX(5px);
    }

    .boton-top {
      background: linear-gradient(135deg, #d17f3c 0%, #b8661f 100%);
      color: #ffffff;
      padding: 12px 20px;
      border-radius: 25px;
      font-weight: bold;
      text-decoration: none;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      border: none;
      box-shadow: 0 4px 15px rgba(209, 127, 60, 0.3);
      position: relative;
      overflow: hidden;
    }

    .boton-top::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: left 0.5s ease;
    }

    .boton-top:hover::before {
      left: 100%;
    }

    .boton-top:hover {
      background: linear-gradient(135deg, #e8904a 0%, #d17f3c 100%);
      color: #fff;
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(209, 127, 60, 0.5);
    }
    /* Estilo para el fondo oscuro del modal de confirmación */
    #confirmacion-modal {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 0;
      top: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.8);
      display: flex;
      align-items: center;
      justify-content: center;
      backdrop-filter: blur(8px);
      animation: modalFadeIn 0.3s ease-out;
    }

    /* Caja del modal centrado con diseño moderno */
    .modal-content {
      background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
      padding: 40px;
      border-radius: 20px;
      text-align: center;
      box-shadow: 
        0 25px 60px rgba(0, 0, 0, 0.4),
        0 15px 30px rgba(0, 0, 0, 0.2),
        inset 0 1px 0 rgba(255, 255, 255, 0.6);
      max-width: 450px;
      width: 85%;
      font-size: 18px;
      transform: scale(0.7);
      animation: modalSlideIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Botones del modal con efectos modernos */
    .modal-content button {
      margin: 15px 10px;
      padding: 12px 25px;
      font-size: 16px;
      cursor: pointer;
      border-radius: 25px;
      border: none;
      font-weight: bold;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .modal-content button.confirmar {
      background: linear-gradient(135deg, #d85e39 0%, #c44522 100%);
      color: white;
      box-shadow: 0 4px 15px rgba(216, 94, 57, 0.4);
    }

    .modal-content button.confirmar:hover {
      background: linear-gradient(135deg, #e67347 0%, #d85e39 100%);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(216, 94, 57, 0.6);
    }

    .modal-content button.cancelar {
      background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
      color: white;
      box-shadow: 0 4px 15px rgba(107, 114, 128, 0.4);
    }

    .modal-content button.cancelar:hover {
      background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(107, 114, 128, 0.6);
    }

    .modal-content button::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: left 0.5s ease;
    }

    .modal-content button:hover::before {
      left: 100%;
    }

    /* Efectos adicionales para mejorar la experiencia */
    @keyframes float {
      0%, 100% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
    }

    @keyframes glow {
      0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
      50% { box-shadow: 0 0 30px rgba(59, 130, 246, 0.6); }
    }

    /* Responsive design mejorado */
    @media (max-width: 768px) {
      .libros-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin: 30px 15px;
        max-width: 100%;
      }
      
      .libro {
        min-height: 380px;
        padding: 20px;
      }
      
      .libro img {
        height: 180px;
      }
      
      .section {
        padding: 40px 15px;
      }
      
      .section h2 {
        font-size: 2.2em;
      }
      
      main {
        margin-left: 0;
        padding-top: 90px;
      }
      
      header {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        width: 200px;
      }
      
      header.show {
        transform: translateX(0);
      }

      nav a {
        padding: 12px 8px;
        font-size: 18px;
      }
    }

    @media (max-width: 480px) {
      .libros-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin: 20px 10px;
      }
      
      .libro {
        min-height: 350px;
        padding: 15px;
      }
      
      .libro img {
        height: 160px;
      }
      
      .section h2 {
        font-size: 1.8em;
      }
      
      .section {
        padding: 30px 10px;
      }
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
        <a href="<?= htmlspecialchars($libro['archivo']) ?>" target="_blank" rel="noopener noreferrer">
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