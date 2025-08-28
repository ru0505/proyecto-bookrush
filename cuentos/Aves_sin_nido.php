<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <title>La ciudad y los perros</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #373d5cff;
            color: #313c49ff;
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
        /* border-bottom: 5px solid #1e334e; */ /* Comentado o eliminado para quitar subrayado */
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
            background-color: #6c4c41;
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
            background-color: #6c4c41;
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

        .volver a {
            text-decoration: none;
            font-weight: bold;
            color: #0f6cba;
            max-width: 30px;
        }

    </style>
</head>
<body>

    <div class="volver">
        <a href="../index.php"><------</a>
    </div>

    <h1>La ciudad y los perros</h1>

    <div class="contenedor">
        <div class="imagen">
            <img src="../imagenes/cdpportada.jpg" alt="La ciudad y los perros">
        </div>

        <div class="resumen">
            <h2>Resumen</h2>
            <p>
                En *La ciudad y los perros*, un grupo de cadetes del Colegio Militar Leoncio Prado vive entre violencia, humillaciones y secretos oscuros. El “Círculo” trama venganzas, mientras el destino de todos se desmorona. Traición, poder y brutalidad revelan la corrupción de una sociedad entera. ¿Hasta dónde llega la lealtad? 
            </p>

            <a href="../pregunta/preguntas_cdp.php" class="boton">Capítulos con preguntas</a>
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
                    <li> El Jaguar - líder del “Círculo”, violento pero con un código de honor.</li>
                    <li> Ricardo Arana “El Esclavo” - tímido, víctima constante de abusos.</li>
                    <li> Alberto Fernández “El Poeta” - narrador en parte, sensible y observador.</li>
                    <li> El Teniente Gamboa - oficial que busca justicia.</li>
                    <li> El Boa - compañero brutal y temido.</li>
                    <li> El Serrano Cava - cómplice en robos de exámenes.</li>
                    <li> Teresa - interés amoroso del Poeta.</li>
                    <li> El Coronel - símbolo de la autoridad corrupta.</li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
