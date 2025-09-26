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
    <img src="/proyectolect/imagenes/ccdp.jpg" alt="Imagen capítulo 1">
    
    <div class="texto">
      <h2>Capítulo 1</h2>
      <p>
        “El círculo de muchachos rodeaba al Cava. Algunos reían en voz baja, otros lo miraban con curiosidad y otros con desprecio. El Jaguar se acercó y lo miró un momento sin hablar.
—¿Tienes miedo? —preguntó.
El Cava negó con la cabeza. Había sudor en su cara.
—Bueno —dijo el Jaguar—. Si te descubren, di que tú solo robaste las respuestas. Nadie más.
El Cava no contestó. El Jaguar volvió la cara hacia los otros.
—¿Está bien? —preguntó.
</p>
<p>
Nadie dijo nada. El Cava bajó la vista, sintiendo que la vergüenza se le subía como un calor a la cabeza.
Salieron del baño. La cuadra estaba a oscuras, pero Cava no necesitaba ver para orientarse entre las dos columnas de literas; conocía de memoria ese recinto estirado y alto. Lo colmaba ahora una serenidad silenciosa, alterada instantáneamente por ronquidos o murmullos. Llegó a su cama, la segunda de la derecha, la de abajo, a un metro de la entrada. Mientras sacaba a tientas del ropero el pantalón, la camisa caqui y los botines, sentía junto a su rostro el aliento teñido de tabaco de Vallano, que dormía en la litera superior. Distinguió en la oscuridad la doble hilera de dientes grandes y blanquísimos del negro y pensó en un roedor. Sin bulla, lentamente, se despojó del pijama de franela azul y se vistió. Echó sobre sus hombros el sacón de paño. Luego, pisando despacio porque los botines crujían, caminó hasta la litera del Jaguar, que estaba al otro extremo de la cuadra, junto al baño.
—Jaguar.
—Sí. Toma.
      </p>
      <p>
        Cava alargó la mano, tocó dos objetos fríos, uno de ellos áspero. Conservó en la mano la linterna, guardó la lima en el bolsillo del sacón.
—¿Tienes miedo? —preguntó el Jaguar.
—No —respondió Cava.
—Bueno —dijo el Jaguar—. No te demores.
Cava salió de la cuadra con pasos cautelosos. Aplastado contra la pared del patio, permaneció inmóvil unos segundos. Advirtió que el miedo lo paralizaría si no actuaba. Miró la pista de desfile iluminada y la neblina que la cubría. Luego se deslizó en dirección a las aulas.”
      </p>
    </div>
  </div>

<div class="acordeon">
    <h3 onclick="toggleGlosario()"> Mostrar / Ocultar Glosario</h3>
    <div class="contenido-glosario" id="glosario">
      <ul>
        <li><strong>Garita:</strong> Caseta pequeña donde se hace vigilancia.</li>
        <li><strong>Neblina tenue:</strong> Capa ligera de nubes bajas que nubla la vista.</li>
        <li><strong>Camaradería:</strong> Amistad y compañerismo en un grupo.</li>
        <li><strong>Humillaciones:</strong> Avergonzar o rebajar a alguien públicamente.</li>
        <li><strong>Alianzas frágiles:</strong> Relaciones temporales y poco confiables.</li>
        <li><strong>Amenazas invisibles:</strong> Peligros que no se ven, pero se sienten.</li>
        <li><strong>Jerarquía:</strong> Organización de poder en niveles superiores e inferiores.</li>
        <li><strong>Círculo:</strong> Grupo secreto de cadetes con más poder que los oficiales.</li>
        <li><strong>Moneda diaria:</strong> Metáfora para algo que ocurre constantemente.</li>
        <li><strong>Brutalidad:</strong> Violencia extrema, sin compasión.</li>
      </ul>
    </div>
  </div>

  <div class="botones">
    <a href="/PROYECTO-BOOKRUSH/trivia/cdp/1.php">Comenzar con las preguntas</a>
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
      "Garita",
      "Neblina",
      "Camaradería",
      "Humillaciones",
      "Alianzas",
      "Amenazas",
      "Jerarquía",
      "Círculo",
      "Moneda",
      "Brutalidad"
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