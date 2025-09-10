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
    <img src="/proyectolect/imagenes/c4cdp.jpg" alt="Imagen capítulo 2">
    
    <div class="texto">
      <h2>Capítulo 4</h2>
      <p>
        Alberto vuelve a casa y se enfrenta a un ambiente cargado. Su madre, frágil y melancólica, le habla del pasado con nostalgia: 
        evoca su juventud brillante, los viajes, las amistades perdidas y el matrimonio que ahora la consume. Entre lágrimas, confiesa que su esposo, 
        el padre de Alberto, ha regresado tras años de ausencia, trayendo promesas de reconciliación que ella rechaza. Alberto escucha en silencio, 
        atrapado entre la compasión por su madre y el resentimiento hacia ese hombre ausente.
      </p>
      <p>
        Mientras ella se desahoga, Alberto se siente atrapado en dos mundos: el violento y jerárquico colegio militar y esta casa silenciosa, impregnada de recuerdos rotos.
         Recibe un sobre con dinero, la única muestra de afecto indirecto de su padre. Él abraza a su madre, finge optimismo, pero en su mente solo piensa en la Pies Dorados, 
         en el Esclavo, en el colegio. La calidez del hogar no logra disipar el peso del Leoncio Prado ni las culpas que arrastra. La escena mezcla ternura y desolación: 
         una madre aferrada a un hijo como único apoyo, un muchacho que ya no se siente niño, y un vínculo familiar carcomido por el abandono.
      </p>
    </div>
  </div>
<div class="acordeon">
  <h3 onclick="toggleGlosario3()">Mostrar / Ocultar Glosario</h3>
  <div class="contenido-glosario" id="glosario3">
    <ul>
      <li><strong>Cargado:</strong> Con un ambiente pesado, lleno de tensión emocional.</li>
      <li><strong>Frágil:</strong> Débil física o emocionalmente.</li>
      <li><strong>Melancólica:</strong> Tristeza suave y reflexiva por algo perdido.</li>
      <li><strong>Nostalgia:</strong> Tristeza por recordar tiempos mejores que ya no volverán.</li>
      <li><strong>Ausencia:</strong> Falta o desaparición prolongada de alguien.</li>
      <li><strong>Reconciliación:</strong> Volver a llevarse bien después de un conflicto.</li>
      <li><strong>Compasión:</strong> Sentimiento de lástima y ternura hacia el sufrimiento de otro.</li>
      <li><strong>Resentimiento:</strong> Rabia o dolor guardado por una ofensa o abandono.</li>
      <li><strong>Desahoga:</strong> Expresar libremente lo que se siente para aliviarse.</li>
      <li><strong>Disipar:</strong> Hacer desaparecer algo que pesa o preocupa.</li>
      <li><strong>Desolación:</strong> Tristeza extrema, sensación de vacío o soledad.</li>
    </ul>
  </div>
</div>

<div class="botones">
  <a href="/proyectolect/trivia/cdp/4_1.php">Comenzar con las preguntas</a>
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
      "Cargado",
      "Frágil",
      "Melancólica",
      "Nostalgia",
      "Ausencia",
      "Reconciliación",
      "Compasión",
      "Resentimiento",
      "Desahoga",
      "Disipar",
      "Desolación"
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
