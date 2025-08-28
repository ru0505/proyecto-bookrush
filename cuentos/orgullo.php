<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <title>ORGULLO Y PREJUICIO</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #9e8b62ff;
            color: #ffffffff;
            margin: 0;
            padding: 0;
            font-family: 'Fredoka', sans-serif;
            
        }

        .contenedor {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        h1 {
        font-size: 70px;
        font-weight: bold;
        margin-top: 30px;
        text-transform: uppercase;
        padding-bottom: 10px;
        text-align: center;
        display: block; /* inline-block no es necesario aquí */
        margin-left: auto;
        margin-right: auto;
        font-family: 'Fredoka', sans-serif;
    }

        .resumen {
            flex: 1;
            min-width: 300px;
            font-size: 20px;
            padding-left: 40px;
            line-height: 1.6;
        }

        .resumen h2 {
            font-size: 50px;
            margin-top: 0;
            font-family: 'Fredoka', sans-serif;
        }

        .imagen {
            flex: 1;
            text-align: center;
        }

        .imagen img {
            width: 100%;
            max-width: 900px;
            border-radius: 10px;
        }

        .boton {
            display: inline-block;
            background-color: #2e2a12ff;
            color: white;
            padding: 10px 20px;
            margin: 10px 10px 10px 0;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
        }

        .personajes, .lista {
            margin-top: 30px;
        }

        .personaje {
            background-color: #4a5726ff;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            font-weight: bold;
        }

        .descripcion {
            margin-top: 10px;
        }

        .lista ul {
            list-style: none;
            padding: 0;
        }

        .lista li::before {
            content: "⚔";
        }

        .volver {
            margin: 20px;
        }

        .flecha-grande::before {
            content: "←";
            font-size: 50px;
            color: black;
            text-decoration: none;
        }
        .flecha-grande {
            text-decoration: none;
        }

    </style>
</head>
<body>

    <div class="volver">
    <a href="../index.php" class="flecha-grande"></a>
    </div>

    <h1>ORGULLO Y PREJUICIO</h1>

    <div class="contenedor">
        <div class="imagen">
            <img src="../imagenes/orgudentro.jpg" alt="ORGULLO">
        </div>

        <div class="resumen">
            <h2>Resumen</h2>
            <p>
                Frankenstein narra la historia de Victor, quien logra dar vida a un ser construido a partir de restos humanos. Sin embargo, al contemplar el resultado, se siente horrorizado por su obra y la abandona. La criatura, rechazada por su creador y por la sociedad debido a su apariencia, sufre una existencia solitaria, por ello se llena de dolor y resentimiento, y decide vengarse de su creador.
            </p>

            <a href="../pregunta/preguntas_frank.php" class="boton">Capítulos con preguntas</a>
            <?php if (isset($_SESSION['usuario'])): ?>
            <a href="../descargar.php?archivo=cdp.pdf" class="boton">Descargar libro completo</a>
            <?php else: ?>
                <p><strong>Debes iniciar sesión para descargar el libro.</strong></p>
                <a href="../login.php" class="boton">Iniciar sesión</a>
            <?php endif; ?>

            <div class="personajes">
                <div class="personaje">personajes</div>
            </div>

            <div class="lista">
                <ul>
                    <li> Victor Frankenstein - Es el protagonista de la novela. Un joven científico suizo obsesionado con descubrir los secretos de la vida.</li>
                    <li> La criatura (el monstruo de Frankenstein) - Ser artificial creado por Victor. Es físicamente imponente y de apariencia grotesca, pero inicialmente es sensible, inteligente y busca afecto.</li>
                    <li> Robert Walton. - Capitán de un barco que explora el Ártico. Es quien narra la historia a través de cartas dirigidas a su hermana.</li>
                    <li> Elizabeth Lavenza - Prima adoptiva y prometida de Victor.</li>
                    <li> Henry Clerval - Mejor amigo de Victor desde la infancia.</li>
                    <li> Alphonse Frankenstein - Padre de Victor.</li>
                    <li> William Frankenstein - Hermano menor de Victor.</li>
                    <li> Justine Moritz - Criada de la familia Frankenstein, acusada injustamente del asesinato de William.</li>
                    <li> De Lacey - Anciano ciego que vive en una cabaña en el bosque. </li>
                    <li> Felix, Agatha y Safie - Miembros de la familia De Lacey. Sin saberlo, educan a la criatura a través de sus conversaciones y enseñanzas.</li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
