<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Mitos y Leyendas (Arequipa)</title>
    <style>
        body {
            font-family: 'Fredoka', sans-serif;
            background: linear-gradient(180deg,rgba(11,36,54,0.85) 0%, rgba(7,32,42,0.92) 100%), url('../imagenes/puerta.png');
            background-size: cover;
            background-position: center;
            color: #111;
            margin: 0;
            padding: 0;
        }
        .contenedor {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            align-items: flex-start;
        }
        h1 {
            font-size: 56px;
            font-weight: bold;
            margin-top: 30px;
            text-transform: uppercase;
            padding-bottom: 10px;
            text-align: center;
            display: block;
            margin-left: auto;
            margin-right: auto;
            color: #cfcfcfff; /* plomo */
        }
        .resumen {
            flex: 1;
            min-width: 300px;
            font-size: 16px;
            padding-left: 20px;
            line-height: 1.6;
            color: #cfcfcfff; /* plomo */
            background: none; /* eliminar recuadro blanco */
            padding: 0; /* mantener diseño sin recuadro */
            border-radius: 0;
        }
        .imagen {
            flex: 1;
            text-align: center;
        }
        .imagen img {
            width: 100%;
            max-width: 640px;
            border-radius: 10px;
        }
        .boton {
            display: inline-block;
            background-color: #491a17ff;
            color: white;
            padding: 10px 20px;
            margin: 10px 10px 10px 0;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
        }
    .personajes, .lista { margin-top: 30px; }
    .personaje { background: transparent; color: #cfcfcfff; padding: 6px 0; display: inline-block; font-weight: 700; }
    .lista ul { list-style: none; padding: 0; }
    .lista li { margin-bottom: 8px; color: #cfcfcfff; }
        .volver { margin: 20px; }
        .flecha-grande::before { content: "←"; font-size: 40px; color: black; text-decoration: none; }
        .flecha-grande { text-decoration: none; }
    </style>
</head>
<body>

    <div class="volver">
        <a href="../index.php" class="flecha-grande"></a>
    </div>

    <h1>Mitos y Leyendas (Arequipa)</h1>

    <div class="contenedor">
        <div class="imagen">
            <img src="../imagenes/castillo.jpg" alt="Mitos y Leyendas Arequipa">
        </div>

        <div class="resumen">
            <h2>Resumen</h2>
            <p>
El libro reúne una selección de mitos y leyendas de la región de Arequipa, mostrando la riqueza cultural y la cosmovisión de sus habitantes a lo largo del tiempo. A través de relatos transmitidos oralmente, se mezclan lo real y lo fantástico, lo sagrado y lo cotidiano, para explicar el origen del mundo, la lucha entre el bien y el mal, y los misterios que habitan en volcanes, cementerios, iglesias y ríos. Historias como La Mano del Cementerio, El Diablo de la Catedral, El Chaco Pesca o El Puente del Diablo revelan creencias populares que advierten, enseñan y preservan la identidad colectiva. Más que simples cuentos, estos relatos reflejan el miedo, la fe, el humor y la imaginación de los pueblos arequipeños, convirtiéndose en parte esencial de su folclore y memoria histórica.
            </p>

            <a href="../pregunta/menumitos.php" class="boton">Capítulos con preguntas</a>

            <?php if (isset($_SESSION['usuario'])): ?>
            <a href="../descargar.php?archivo=cdp.pdf" class="boton">Descargar libro completo</a>
            <?php else: ?>
                <p><strong>Debes iniciar sesión para descargar el libro.</strong></p>
                <a href="../login.php" class="boton">Iniciar sesión</a>
            <?php endif; ?>

            <div class="personajes">
                <div class="personaje">Personajes</div>
            </div>

            <div class="lista">
                <ul>
                    <li><strong>Mónica, la condenada (La Mano del Cementerio)</strong> - Joven hermosa pero cruel con su madre. Tras ser maldecida, muere joven y su espíritu atormenta el cementerio, apareciéndose de blanco a los visitantes.</li>
                    <li><strong>El sacerdote (La Mano del Cementerio)</strong> - Hombre de fe que logra detener la maldición de la difunta bendiciendo su tumba, lo que evita que la mano siga emergiendo.</li>
                    <li><strong>El Diablo de la Catedral</strong> - Representado en una talla de madera llegada desde Francia, es una imagen única dentro de un templo católico, símbolo del poder del mal incluso en un lugar sagrado.</li>
                    <li><strong>El Inca (El Chaco Pesca)</strong> - Autoridad suprema que da inicio a la festividad de pesca, guía y premia a los competidores, simbolizando la unión entre tradición, religión y naturaleza.</li>
                    <li><strong>La joven del Puente del Diablo</strong> - Trabajadora de una picantería que sufre un aborto forzado; años después encuentra una criatura infernal que simboliza su castigo, lo que origina la leyenda del puente.</li>
                    <li><strong>Los duendes arequipeños</strong> - Pequeños seres traviesos, originados de niños no bautizados o abortados. Son invisibles a los adultos impuros pero visibles a los niños, capaces de hacer travesuras o causar terror.</li>
                    <li><strong>El Hijo del Misti (Misticito)</strong> - Pequeño volcán que amenaza con crecer y provocar catástrofes. Es encadenado por los gentiles y fuerzas sobrenaturales para evitar su erupción.</li>
                    <li><strong>Doña Mariquita la Montufar (Vieron el Diablo)</strong> - Viejecilla piadosa que soporta la burla de un joven travieso disfrazado de demonio. Su inocencia y fe la convierten en símbolo de resistencia contra el mal.</li>
                    <li><strong>La Sirena del Puente Bolognesi</strong> - Hermosa mujer con cola de pez que encantaba a los hombres desde una roca del río Chili, provocando su desaparición en las aguas.</li>
                    <li><strong>El Fraile sin Cabeza</strong> - Espectro con hábito franciscano que vaga de noche por el callejón de la Catedral buscando su cabeza perdida tras ser decapitado en una riña.</li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
