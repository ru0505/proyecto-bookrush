<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <title>Crimen y Castigo</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #df907dff;
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
    }

        .resumen {
            flex: 1;
            min-width: 300px;
        }

        .resumen h2 {
            font-size: 50px;
            margin-top: 0;
        }

        .imagen {
            flex: 1;
            text-align: center;
        }

        .imagen img {
            width: 100%;
            max-width: 800px;
            border-radius: 10px;
        }

        .boton {
            display: inline-block;
            background-color: #69302cff;
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
            background-color: #ce7023ff;
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
            content: "⭐";
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

    <h1>Crimen y Castigo</h1>

    <div class="contenedor">
        <div class="imagen">
            <img src="../imagenes/cdpportada.jpg" alt="Crimen y Castigo">
        </div>

        <div class="resumen">
            <h2>Resumen</h2>
            <p>
                En Crimen y Castigo, La novela sigue a Rodion Raskólnikov, un joven estudiante pobre en San Petersburgo, que asesina a una vieja usurera con la creencia de que, al eliminar a una persona "inútil", podrá hacer el bien a la humanidad y justificar su crimen.
 
            </p>

            <a href="../pregunta/preguntas_crca.php" class="boton">Capítulos con preguntas</a>
            <?php if (isset($_SESSION['usuario'])): ?>
            <a href="../descargar.php?archivo=crimen_castigp.pdf" class="boton">Descargar libro completo</a>
            <?php else: ?>
                <p><strong>Debes iniciar sesión para descargar el libro.</strong></p>
                <a href="../login.php" class="boton">Iniciar sesión</a>
            <?php endif; ?>

            <div class="personajes">
                <div class="personaje">personajes</div>
            </div>

            <div class="lista">
                <ul>
                    <li> Rodion Románovich Raskólnikov. Protagonista. - Joven estudiante pobre, inteligente pero atormentado, que comete un asesinato y se enfrenta a una intensa lucha moral y psicológica.</li>
                    <li> Dmitri Prokófich Razumikhin - tAmigo leal de Raskólnikov. Alegre, generoso y trabajador. Contrasta con el carácter oscuro del protagonista.</li>
                    <li> Porfirio Petróvich. - Juez de instrucción astuto y perspicaz. Sospecha de Raskólnikov, pero lo enfrenta con paciencia y estrategia psicológica.</li>
                    <li> Semión Zajárovich Marmeládov. - Padre alcohólico de Sonia. Figura trágica que representa la miseria y el abandono.</li>
                    <li>Arkadi Ivánovich Svidrigáilov - Hombre rico y de moral dudosa, obsesionado con Dunia. Personaje complejo, mezcla de perversión y remordimiento.</li>
                    <li> Piotr Petróvich Lujin. - Pretendiente de Dunia. Arrogante y manipulador, representa el egoísmo disfrazado de caridad.</li>
                    <li>Aliona Ivánovna.- iUsurera cruel y codiciosa. Es la víctima del asesinato de Raskólnikov.</li>
                    <li> Lizaveta Ivánovna. - sHermana sumisa y bondadosa de Aliona. Es asesinada accidentalmente durante el crimen.</li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
