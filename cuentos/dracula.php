<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <title>DRÁCULA</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #b9b9b9ff;
            color: #000000ff;
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
            margin-top: 50;
            font-family: 'Fredoka', sans-serif;
        }

        .imagen {
            flex: 1;
            text-align: center;
        }

        .imagen img {
            width: 100%;
            max-width: 700px;
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

        .personajes, .lista {
            margin-top: 30px;
        }

        .personaje {
            background-color: #000000ff;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            font-weight: bold;
        }

        .descripcion {
            margin-top: 30px;
        }

        .lista ul {
            list-style: none;
            padding: 0;
        }

        .lista li::before {
            content: "🧛🏻‍♂️";
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

    <h1>DRÁCULA</h1>

    <div class="contenedor">
        <div class="imagen">
            <img src="../imagenes/dracdentro.jpg" alt="DRÁCULA">
        </div>

        <div class="resumen">
            <h2>Resumen</h2>
            <p>
                Drácula es una novela de terror gótico escrita por el autor irlandés Bram Stoker. La historia sigue los esfuerzos de un grupo de personas por detener al conde Drácula, un antiguo vampiro que viaja desde Transilvania a Inglaterra para expandir su reinado de oscuridad.
    </p>
            <a href="../pregunta/preguntas_drac.php" class="boton">Capítulos con preguntas</a>
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
                    <li> Conde Drácula - Un antiguo noble transilvano y vampiro inmortal. Es el antagonista principal de la novela.</li>
                    <li> Jonathan Harker - Joven abogado inglés que viaja al castillo de Drácula en Transilvania al comienzo de la novela.</li>
                    <li> Mina Murray - Prometida (y luego esposa) de Jonathan Harker. Intelectual y muy inteligente.</li>
                    <li> Lucy Westenra - Mejor amiga de Mina. Bella y dulce joven inglesa.</li>
                    <li> Profesor Abraham Van Helsing - Médico y erudito holandés, experto en enfermedades raras y lo sobrenatural.</li>
                    <li> Dr. John Seward - Director de un manicomio en Inglaterra, antiguo pretendiente de Lucy.</li>
                    <li> Arthur Holmwood (Lord Godalming)- Prometido de Lucy, luego se convierte en Lord tras la muerte de su padre.</li>
                    <li> Quincey P. Morris - Atractivo texano y otro de los pretendientes de Lucy.</li>
                    <li> Renfield - Paciente del Dr. Seward internado en el manicomio. </li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
