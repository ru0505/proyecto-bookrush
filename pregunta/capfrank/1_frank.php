<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
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
        El primer capítulo de Frankenstein inicia cuando Victor Frankenstein, el narrador principal de la historia, comienza a relatar su vida desde sus orígenes. Este capítulo forma parte del relato que Victor le cuenta a Robert Walton, el explorador que lo encuentra moribundo en el Ártico y que transmite la historia al lector a través de cartas dirigidas a su hermana, Margaret Saville.
</p>
<p>
Victor inicia su narración explicando la historia de su familia. Es hijo de Alphonse Frankenstein y Caroline Beaufort, quienes son presentados como personas nobles y bondadosas. Alphonse, un hombre rico y respetado en Ginebra, se hace cargo del padre de Caroline, su antiguo amigo Beaufort, cuando este cae en la pobreza y muere poco tiempo después. Alphonse cuida de Caroline y, finalmente, se casa con ella, pese a la diferencia de edad. Este acto de compasión y responsabilidad sienta las bases del entorno moral en el que Victor crece.
      </p>
      <p>
        Victor enfatiza que fue criado en un hogar lleno de afecto, con padres que se esforzaron por guiarlo con ternura y principios. Describe su infancia como feliz y privilegiada, llena de amor y sin carencias materiales. Sus padres, al querer hacer el bien, adoptan a una niña huérfana llamada Elizabeth Lavenza, a quien encuentran en una familia campesina italiana durante un viaje. Caroline la considera un “regalo” para Victor, como si estuviera destinada a ser su compañera desde la infancia.
      </p>
      <p>
        Victor describe a Elizabeth como su "más preciosa posesión". Ella es bella, dulce y de naturaleza angelical, lo opuesto a la energía más impetuosa y científica de Victor. Desde pequeños, crecen como hermanos y desarrollan un vínculo muy fuerte, aunque también desde temprana edad se insinúa que su relación será más que fraternal.
  </p>
  <p>
    En este capítulo, Victor también expresa su primer interés por el conocimiento. Aunque aún es un niño, siente una fuerte atracción por los misterios de la naturaleza. Se muestra fascinado por los libros de alquimia y los antiguos pensadores como Cornelio Agrippa, Paracelso y Alberto Magno, cuyas ideas sobre la transmutación de los elementos y el poder oculto de la naturaleza encienden su imaginación. Sus padres desaprueban estos estudios, pero no lo guían con firmeza hacia una ciencia más racional, lo que permite que Victor cultive una visión mística y romántica del conocimiento.
  </p>
  <p>
    El capítulo termina presentando el escenario para el desarrollo del carácter de Victor Frankenstein: un joven brillante y apasionado, criado en el seno de una familia amorosa, con un fuerte sentido del destino, y con una temprana obsesión por descubrir los secretos más profundos del universo. Esta combinación de privilegio, amor y ambición científica sentará las bases de la tragedia que vendrá.
  </p>
    </div>
  </div>

<div class="acordeon">
    <h3 onclick="toggleGlosario()"> Mostrar / Ocultar Glosario</h3>
    <div class="contenido-glosario" id="glosario">
      <ul>
        <li><strong>Narrador:</strong> Persona que cuenta la historia en una obra literaria.</li>
        <li><strong>Relatar:</strong> Contar o narrar hechos o sucesos.</li>
        <li><strong>Explorador:</strong> Persona que viaja a lugares desconocidos para descubrirlos o estudiarlos.</li>
        <li><strong>Ártico:</strong> Región polar del hemisferio norte, extremadamente fría.</li>
        <li><strong>Cartas:</strong> Mensajes escritos que se envían entre personas, forma de comunicación epistolar.</li>
        <li><strong>Privilegiado:</strong> Que disfruta de ventajas o beneficios que otros no tienen.</li>
        <li><strong>Huérfana:</strong> Niña que ha perdido a sus padres.</li>
        <li><strong>Campesina:</strong> Persona que vive en el campo y trabaja en tareas agrícolas.</li>
        <li><strong>Posesión:</strong> Algo que se tiene o se considera propio.</li>
        <li><strong>Impulsivo:</strong> Que actúa sin reflexionar, de manera espontánea.</li>
        <li><strong>Alquimia:</strong> Antigua práctica precientífica que buscaba convertir metales en oro y hallar el elixir de la vida.</li>
        <li><strong>Transmutación:</strong> Cambio de una cosa en otra, especialmente en contextos alquímicos.</li>
        <li><strong>Tragedia:</strong> Suceso triste o dramático con consecuencias dolorosas.</li>
        <li><strong>Moral:</strong> Conjunto de valores que guían el comportamiento humano.</li>
        <li><strong>Racional:</strong> Que se basa en la razón y el pensamiento lógico.</li>
      </ul>
    </div>
  </div>

  <div class="botones">
    <a href="../../trivia/frankenstein/preguntas_frankenstein.php?capitulo=1&pregunta=1" class="boton">Comenzar con las preguntas</a>
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