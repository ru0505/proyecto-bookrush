<?php
session_start();
include 'conexion.php';

$libros = [
  1 => [
    "nombre" => "Jorge o el hijo del pueblo",
    "descripcion" => "Una obra pionera que denuncia las injusticias sociales en el Perú rural.",
    "imagen" => "avesnido.png",
    "archivo" => "Aves_sin_nido.html"
  ],
  2 => [
    "nombre" => "Mitos y Leyendas de Arequipa",
    "descripcion" => "Una crítica intensa a la violencia en un colegio militar de Lima.",
    "imagen" => "perros.png",
    "archivo" => "mitos_leyendas.html"
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
    body {
      background-color: #1e3a8a;
      color: #b8a2a2ff;
      font-family: 'Inter', sans-serif;
      padding: 40px;
      font-family: 'Fredoka', sans-serif;
    }
    .libros-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr); /* ← Aquí está el cambio */
      gap: 20px;
      margin-top: 20px;
    }


    .libro {
      background-color: #fffefbff;
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
      background-color: #2f2a77ff;
      color: #ffffffff;
      border: none;
      padding: 8px 12px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }

    .libro button:hover {
      background-color: #2a214bff;
    }
    .libro {
      transition: transform 0.3s ease;
    }

    .libro:hover {
      transform: scale(1.05);
    }
    h1 {
      color: #ffffffff;
      text-align: center;
      font-family: 'Fredoka', sans-serif;
    }
    .volver {
      text-align: center;
      margin-top: 40px;
    }
    .volver a {
      background-color:rgba(71, 21, 187, 1);
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
    }
    .volver a:hover {
      background-color: #2d2785ff;
    }
  </style>
</head>
<body>
  <h1>Literatura Universal</h1>
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
    <a href="index.php">← Volver a la pagina principal</a>
  </div>
</body>
</html>
