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
    <img src="/proyectolect/imagenes/c2cdp.jpg" alt="Imagen capítulo 2">
    
    <div class="texto">
      <h2>Capítulo 2</h2>
      <p>
        Cava avanzaba con cautela, pegado a las paredes rugosas y húmedas del colegio militar. 
        La neblina espesa cubría los corredores como un manto opaco, distorsionando las sombras 
        y haciendo que cada rincón pareciera acecharlo. Sus pasos eran sigilosos, medidos con precisión, 
        temiendo que el más leve sonido lo delatara. Sabía que un error significaba no solo la expulsión,
         sino también el desprecio implacable del Jaguar. Su corazón golpeaba con un ritmo irregular, como si quisiera escapar de su pecho.
      </p>
      <p>
        Al llegar a las aulas, extrajo la lima con manos tensas y la introdujo en la rendija de la ventana. 
        El chirrido del metal cortó el silencio como un reproche. Contuvo la respiración. Empujó la ventana 
        y un aire viciado y pesado lo envolvió al entrar. Encendió la linterna y la luz temblorosa iluminó el examen de Química. 
        Copió las preguntas con prisa febril, sin detenerse a comprenderlas.
      </p>
      <p>
        Al saltar para huir, un vidrio estalló con un sonido seco. Se paralizó, esperando pasos, voces, algo… pero no hubo nada. 
        Guardó los fragmentos en el bolsillo y escapó. En el baño lo esperaba el Jaguar, con mirada gélida. Lo tomó por las solapas,
         lo insultó y sentenció: “Si nos descubren, te vas solo”. Mientras tanto, Alberto cumplía guardia, miró al Esclavo con desprecio y 
         juntos robaron un sacón. La noche siguió densa, vigilante.
      </p>
    </div>
  </div>

 <div class="acordeon">
  <h3 onclick="toggleGlosario()">Mostrar / Ocultar Glosario</h3>
  <div class="contenido-glosario" id="glosario">
    <ul>
      <li><strong>Cautela:</strong> Precaución extrema para evitar un peligro.</li>
      <li><strong>Rugosas:</strong> Superficies ásperas, no lisas.</li>
      <li><strong>Manto opaco:</strong> Capa que cubre y no deja ver con claridad.</li>
      <li><strong>Distorsionando:</strong> Deformando o alterando la forma real de algo.</li>
      <li><strong>Acecharlo:</strong> Vigilarlo en secreto, esperando atraparlo.</li>
      <li><strong>Delatara:</strong> Revelara su presencia o lo expusiera.</li>
      <li><strong>Desprecio implacable:</strong> Rechazo absoluto, sin compasión.</li>
      <li><strong>Rendija:</strong> Abertura pequeña por donde apenas pasa algo.</li>
      <li><strong>Chirrido:</strong> Sonido agudo y desagradable del metal.</li>
      <li><strong>Viciado:</strong> Aire cargado, impuro, difícil de respirar.</li>
    </ul>
  </div>
</div>

<div class="botones">
  <a href="/proyectolect/trivia/cdp/2_1.php">Comenzar con las preguntas</a>
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
      "Cautela",
      "Rugosas",
      "Manto opaco",
      "Distorsionando",
      "Acecharlo",
      "Amenazas",
      "Delatara",
      "Desprecio implacable",
      "Rendija",
      "Viciado",
      "Chirrido"
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
