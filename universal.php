<?php
session_start();
include 'conexion.php';

$libros = [
  1 => [
    "nombre" => "Jorge o el hijo del pueblo",
    "descripcion" => "Una obra pionera que denuncia las injusticias sociales en el Perú rural.",
    "imagen" => "avesnido.png",
    "archivo" => "Aves_sin_nido.php"
  ],
  2 => [
    "nombre" => "Mitos y Leyendas de Arequipa",
    "descripcion" => "Una crítica intensa a la violencia en un colegio militar de Lima.",
    "imagen" => "perros.png",
    "archivo" => "mitos_leyendas_presentacion.php"
  ],
  3 => [
    "nombre" => "Poesias Completas",
    "descripcion" => "Un conmovedor cuento sobre la muerte y el honor de un gallo de pelea.",
    "imagen" => "zorro_condor.png",
    "archivo" => "mitos_leyendas.html"
  ],
  4 => [
    "nombre" => "Crimen y Castigo",
    "descripcion" => "Un conmovedor cuento sobre la muerte y el honor de un gallo de pelea.",
    "imagen" => "zorro_condor.png",
    "archivo" => "crimen_castigo.php"
  ],
  5 => [
    "nombre" => "Mujercitas",
    "descripcion" => "Un conmovedor cuento sobre la muerte y el honor de un gallo de pelea.",
    "imagen" => "mujerdentro.jpg",
    "archivo" => "mujercitas.php"
  ],
  6 => [
    "nombre" => "Orgullo y Prejuicio",
    "descripcion" => "Un conmovedor cuento sobre la muerte y el honor de un gallo de pelea.",
    "imagen" => "orguportada.jpg",
    "archivo" => "orgullo.php"
  ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

  <meta charset="UTF-8">
  <title>Literatura Universal</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Fredoka', sans-serif;
      font-size: 23px;
    }

    body {
      background: linear-gradient(135deg, #353346ff 0%, #2a2a3e 50%, #1e1e2e 100%);
      color: #1e334e;
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
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
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .logo {
      width: 50px;
      height: 50px;
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

    /* Estilo especial para el botón activo */
    nav a.active {
      background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%) !important;
      color: #ffffff !important;
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
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 30px;
      margin: 50px auto;
      max-width: 1400px;
      perspective: 1000px;
      z-index: 1;
      position: relative;
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
      min-height: 420px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
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
      height: 200px;
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

    .volver {
      text-align: center;
      margin-top: 50px;
      animation: bounceIn 1s ease-out;
      z-index: 1;
      position: relative;
    }

    @keyframes bounceIn {
      0% { transform: scale(0.3) translateY(50px); opacity: 0; }
      50% { transform: scale(1.05); }
      70% { transform: scale(0.9); }
      100% { transform: scale(1) translateY(0); opacity: 1; }
    }

    .volver a {
      background: linear-gradient(135deg, #3b82f6 0%, #1e40af 50%, #1e3a8a 100%);
      color: white;
      text-decoration: none;
      padding: 18px 35px;
      border-radius: 30px;
      font-weight: bold;
      font-size: 1.2em;
      transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
      box-shadow: 
        0 8px 25px rgba(59, 130, 246, 0.4),
        0 4px 10px rgba(0, 0, 0, 0.2);
      position: relative;
      overflow: hidden;
      text-transform: uppercase;
      letter-spacing: 1px;
      display: inline-block;
    }

    .volver a::before {
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

    .volver a:hover::before {
      transform: translateX(100%);
    }

    .volver a:hover {
      background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 50%, #2563eb 100%);
      transform: translateY(-5px) scale(1.05);
      box-shadow: 
        0 15px 40px rgba(59, 130, 246, 0.6),
        0 8px 20px rgba(0, 0, 0, 0.3);
    }

    /* Animación de entrada para toda la página */
    @keyframes fadeIn {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }

    .libros-grid {
      animation: fadeIn 1.2s ease-out;
    }
  </style>
</head>
<body>
  <!-- Barra superior -->
  <div class="top-bar">
    <h1>
      <img src="imagenes/LOGO_BOOK_RUSH.png" alt="BookRush Logo" class="logo">
      BookRush
    </h1>
    
    <div class="top-icons">
      <div class="icon-container">
        <img src="imagenes/usuario.png" alt="Mi Usuario" class="icon" />
        <div class="tooltip">
          <p><strong>Usuario Visitante</strong></p>
          <p>Explora nuestros libros universales</p>
        </div>
      </div>
      
      <div class="icon-container">
        <img src="imagenes/estrella.png" alt="Mis Puntos" class="icon" />
        <div class="tooltip">
          <p><strong>Literatura Universal</strong></p>
          <p>Descubre los clásicos mundiales</p>
        </div>
      </div>
      
      <div class="icon-container">
        <img src="imagenes/puerta.png" alt="Ir a Inicio" class="icon" onclick="window.location.href='index.php'" />
        <div class="tooltip">
          <p><strong>Volver al Inicio</strong></p>
          <p>Regresa a la página principal</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Menú lateral -->
  <header>
    <nav>
      <a href="nacional.php">Literatura Nacional</a>
      <a href="regional.php">Literatura Regional</a>
      <a href="universal.php" class="active">Literatura Universal</a>
    </nav>
  </header>

  <main>
    <section class="section">
      <h2>Literatura Universal</h2>
      <div class="libros-grid">
        <?php foreach ($libros as $libro): ?>
          <div class="libro">
            <img src="imagenes/<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['nombre']) ?>">
            <h3><?= htmlspecialchars($libro['nombre']) ?></h3>
            <p><?= htmlspecialchars($libro['descripcion']) ?></p>
            <a href="cuentos/<?= htmlspecialchars($libro['archivo']) ?>" target="_blank">
              <button>Leer cuento completo</button>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
      
      <div class="volver">
        <a href="index.php">← Volver a la página principal</a>
      </div>
    </section>
  </main>
</body>
</html>
