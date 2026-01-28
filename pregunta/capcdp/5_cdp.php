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
    <img src="/proyectolect/imagenes/c5cdp.jpg" alt="Imagen capítulo 2">
    
    <div class="texto">
      <h2>Capítulo 5</h2>
      <p>
        Alberto avanzaba por las avenidas de Miraflores acompañado de sus amigos de infancia, muchachos de gestos despreocupados y risas fáciles.
         Las fachadas ostentosas, los jardines geométricamente podados y el perfume salobre del mar parecían pertenecer a otra ciudad, 
         una tan distinta al Leoncio Prado que casi le resultaba ilusoria. El grupo conversaba de trivialidades, lanzaba comentarios mordaces 
         sobre las muchachas que pasaban y celebraba con carcajadas anécdotas que a Alberto le sonaban huecas.
      </p>
      <p>
        Mientras fingía atención, su mente vagaba. Seguía encadenada a los pasillos sombríos del colegio militar, al tono helado del Jaguar,
         al murmullo nervioso del Esclavo. Esa supuesta libertad que respiraba en Miraflores le resultaba un escenario artificioso, apenas un 
         velo superficial que ocultaba otras jerarquías, otras formas de sometimiento.
      </p>
      <p>
        Entonces apareció Teresa. No lucía la afectación altiva de las demás. Hablaba poco, movía las manos con una delicadeza casi involuntaria, 
        y sus ojos parecían guardar silencios más hondos que cualquier palabra. Su discreción desentonaba con la frivolidad del paseo. 
        Alberto la miró sin atreverse a romper esa distancia, sintiendo un extraño sosiego que no comprendía. Mientras el grupo seguía su ronda de bromas y chismes,
         él comprendió que en ningún espacio —ni en el colegio ni en esas calles pulcras— existía una verdadera libertad, solo cárceles invisibles.
      </p>
    </div>
  </div>
<div class="acordeon">
  <h3 onclick="toggleGlosario4()">Mostrar / Ocultar Glosario</h3>
  <div class="contenido-glosario" id="glosario4">
    <ul>
      <li><strong>Ostentosas:</strong> Llamativas y lujosas, hechas para mostrar riqueza.</li>
      <li><strong>Geométricamente podados:</strong> Arreglados de forma precisa y simétrica, como figuras.</li>
      <li><strong>Salobre:</strong> Con sabor u olor a sal, como el mar.</li>
      <li><strong>Ilusoria:</strong> Que parece real, pero es engañosa.</li>
      <li><strong>Trivialidades:</strong> Cosas superficiales o sin importancia.</li>
      <li><strong>Comentarios mordaces:</strong> Frases irónicas o crueles, con malicia.</li>
      <li><strong>Escenario artificioso:</strong> Algo falso o preparado para aparentar.</li>
      <li><strong>Afectación altiva:</strong> Actitud orgullosa y fingida para parecer superior.</li>
      <li><strong>Frivolidad:</strong> Falta de profundidad, quedarse en lo superficial.</li>
      <li><strong>Pulcras:</strong> Muy limpias, cuidadas hasta el detalle.</li>
      <li><strong>Cárceles invisibles:</strong> Metáfora para normas o presiones que limitan sin que se vean.</li>
    </ul>
  </div>
</div>
<div class="botones">
  <a href="/proyectolect/trivia/cdp/5_1.php">Comenzar con las preguntas</a>
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
      "Ostentosas",
      "Geométricamente podados",
      "Salobre",
      "Ilusoria",
      "Trivialidades",
      "Comentarios mordaces",
      "Escenario artificioso",
      "Afectación altiva",
      "Frivolidad",
      "Pulcras",
      "Cárceles invisibles"
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
