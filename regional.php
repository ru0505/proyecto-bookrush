<?php
session_start();
include 'conexion.php';

$libros = [
  1 => [
    "nombre" => "Jorge el hijo del pueblo",
    "descripcion" => "Una obra pionera que denuncia las injusticias sociales en el Perú rural.",
    "imagen" => "avesnido.png",
    "archivo" => "Aves_sin_nido.html"
  ],
  2 => [
    "nombre" => "Mitos y Leyendas ",
    "descripcion" => "Una crítica intensa a la violencia en un colegio militar de Lima.",
    "imagen" => "perros.png",
    "archivo" => "La_ciudad_los_perros.html"
  ],
  3 => [
    "nombre" => "Poesias y Leyendas",
    "descripcion" => "Un conmovedor cuento sobre la muerte y el honor de un gallo de pelea.",
    "imagen" => "zorro_condor.png",
    "archivo" => "El_caballero_Carmelo.html"
  ]
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <link href="https://fonts.cdnfonts.com/css/sergio-trendy" rel="stylesheet">

  <meta charset="UTF-8">
  <title>Literatura Regional</title>
  <style>
    body {
      background: linear-gradient(to right, #5147aaff, #69aae7ff);
      color: #1e334e;
      font-family: 'Inter', sans-serif;
      padding: 40px;
      font-family: 'Sergio Trendy', sans-serif;
    }
    .libros-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr); /* ← Aquí está el cambio */
      gap: 20px;
      margin-top: 20px;
    }


    .libro {
      background-color: #fcfcfcff;
      padding: 15px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(30, 51, 78, 0.1);
      text-align: center;
    }

    .libro img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      border-radius: 8px;
    }

    .libro h3 {
      font-size: 1.2em;
      margin: 10px 0 5px;
      color: #1e334e;
    }

    .libro p {
      font-size: 0.95em;
      color: #333;
    }

    .libro button {
      margin-top: 10px;
      background-color: #473783ff;
      color: #ffffffff;
      border: none;
      padding: 8px 12px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }

    .libro button:hover {
      background-color: #282b3bff;
    }
    .libro {
      transition: transform 0.3s ease;
    }

    .libro:hover {
      transform: scale(1.05);
    }
    h1 {
      color: #000000ff;
      text-align: center;
      font-family: 'Fredoka', sans-serif;
    }
    .volver {
      text-align: center;
      margin-top: 40px;
    }
    .volver a {
      background-color:rgba(21, 24, 187, 1);
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
    }
    .volver a:hover {
      background-color: #252c55ff;
    }
  </style>
</head>
<body>
  <h1>Literatura Regional</h1>
  <div class="libros-grid">
    <?php foreach ($libros as $libro): ?>
      <div class="libro">
        <img src="platos/<?= htmlspecialchars($libro['imagen']) ?>" alt="<?= htmlspecialchars($libro['nombre']) ?>">
        <h3><?= htmlspecialchars($libro['nombre']) ?></h3>
        <p><?= htmlspecialchars($libro['descripcion']) ?></p>
        <a href="<?= htmlspecialchars($libro['archivo']) ?>">
          <button>Leer cuento completo</button>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="volver">
    <a href="index.php">← Volver a la pagina principal</a>
  </div>
</body>
</html>
