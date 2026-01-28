<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

  <meta charset="UTF-8">
  <title>Capítulo 1 - La ciudad y los perros</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #7ca2c2ff;
      color: #1e334e;
    }

    .volver-x {
      position: absolute;
      top: 10px;
      left: 10px;
      font-size: 24px;
      text-decoration: none;
      color: #3951d8ff;
      background: none;
      border: none;
      cursor: pointer;
    }

    .contenido {
      display: flex;
      flex-direction: row;
      justify-content: center;
      align-items: flex-start;
      padding: 60px 40px 40px;
      gap: 40px;
    }

    .contenido img {
      width: 800px;
      border-radius: 20px;
      box-shadow: 10px 4px 10px rgba(0,0,0,0.2);
    }

    .texto {
      max-width: 700px;
      line-height: 1.6;
      background: #fff;
      padding: 20px;
      border-radius: 15px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .botones {
      display: flex;
      justify-content: flex-end;
      padding: 20px 40px;
    }

    .botones a {
      background-color: #0f6cba;
      color: #fff;
      padding: 12px 20px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .botones a:hover {
      background-color: #084d8a;
    }

    .acordeon {
      width: 90%;
      max-width: 800px;
      margin: 30px auto;
      background-color: #85c3ecff;
      border-radius: 10px;
      box-shadow: 2px 2px 8px rgba(0,0,0,0.2);
      font-family: Arial, sans-serif;
    }

    .acordeon h3 {
      background-color: #39d8d0ff;
      color: white;
      margin: 0;
      padding: 15px;
      cursor: pointer;
      border-radius: 10px 10px 0 0;
      font-size: 18px;
    }

    .contenido-glosario {
      display: none;
      padding: 15px;
      background-color: #fff;
      border-radius: 0 0 10px 10px;
    }

    .contenido-glosario ul {
      padding-left: 20px;
    }

    .contenido-glosario li {
      margin-bottom: 10px;
      color: #1e334e;
    }
    .resaltado {
      background-color: #f0c64b;
      font-weight: bold;
      border-radius: 4px;
      padding: 0 4px;
    }

  </style>
</head>
<body>

  <a href="/proyectolect/pregunta/preguntas_cdp.php" class="volver-x">✖</a>

  <div class="contenido">
    <img src="/proyectolect/imagenes/c3cdp.jpg" alt="Imagen capítulo 2">
    
    <div class="texto">
      <h2>Capítulo 3</h2>
      <p>
        Cava avanzaba con pasos contenidos, pegado a las paredes húmedas de la cuadra. El aire nocturno era cortante y 
        la neblina envolvía los corredores como un velo opaco, deformando las sombras. No temblaba por el frío sino por 
        la expectativa opresiva que siempre precedía a las órdenes del Jaguar. En el Leoncio Prado nada era voluntario:
         el azar de los dados había decidido su destino y, si fallaba, no habría clemencia, solo represalias silenciosas pero implacables.
      </p>
      <p>
        La cuadra dormía en un murmullo monótono, roto apenas por ronquidos aislados y un olor rancio que emanaba de los baños cercanos. 
        Cada paso resonaba en su cabeza como un recordatorio de que, en ese colegio, más que disciplina, reinaba una jerarquía salvaje. 
        Las rendijas del techo dejaban pasar un viento gélido que le erizaba la piel, como si el edificio mismo respirara. Allí, 
        la humillación era una moneda diaria. Los fuertes imponían su ley con una mezcla de violencia y astucia; los débiles sobrevivían en silencio. 
        Cava, que conocía el frío de la sierra, nunca había sentido uno tan profundo, un frío que no nacía del clima sino del miedo que lo paralizaba desde dentro.
      </p>
    </div>
  </div>
<div class="acordeon">
  <h3 onclick="toggleGlosario()">Mostrar / Ocultar Glosario</h3>
  <div class="contenido-glosario" id="glosario">
    <ul>
      <li><strong>Neblina:</strong> Capa de nubes bajas que reduce la visibilidad y crea un ambiente confuso o misterioso.</li>
      <li><strong>Velo opaco:</strong> Metáfora para describir algo que cubre y oscurece, impidiendo ver con claridad.</li>
      <li><strong>Expectativa opresiva:</strong> Sensación de tensión y ansiedad que pesa como una carga.</li>
      <li><strong>Dados:</strong> Objetos usados en juegos de azar; aquí simbolizan el destino impredecible.</li>
      <li><strong>Represalias:</strong> Castigos o venganzas que se toman como respuesta a una falta.</li>
      <li><strong>Rancio:</strong> Olor desagradable, como de algo viejo, húmedo o en descomposición.</li>
      <li><strong>Rendijas:</strong> Aberturas pequeñas por donde se cuela el aire o la luz.</li>
      <li><strong>Erizar:</strong> Poner la piel de gallina por frío, miedo o impresión.</li>
      <li><strong>Humillación:</strong> Acción de avergonzar profundamente a alguien, rebajando su dignidad.</li>
    </ul>
  </div>
</div>
<div class="botones">
  <a href="/proyectolect/trivia/cdp/3_1.php">Comenzar con las preguntas</a>
</div>
 <script>
    function toggleGlosario() {
      const glosario = document.getElementById('glosario');
      glosario.style.display = (glosario.style.display === 'block') ? 'none' : 'block';
    }
  </script>
<script>
  function resaltarGlosario() {
    const palabras = [
      "Neblina",
      "Velo opaco",
      "Expectativa opresiva",
      "Dados",
      "Represalias",
      "Rancio",
      "Rendijas",
      "Desprecio implacable",
      "Rendija",
      "Erizar",
      "Humillación"
    ];

    const textoContainer = document.querySelector('.texto');

    if (!textoContainer) return;

    let html = textoContainer.innerHTML;

    palabras.forEach(palabra => {
      // Crear expresión regular que ignore mayúsculas/minúsculas y acentos
      const palabraRegex = new RegExp(`\\b(${palabra})\\b`, 'gi');

      html = html.replace(palabraRegex, '<span class="resaltado">$1</span>');
    });

    textoContainer.innerHTML = html;
  }

  // Ejecutar al cargar la página
  window.onload = resaltarGlosario;
</script>
</body>
</html>
