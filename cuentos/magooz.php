<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <title>El mago y el Oz</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #49829cff;
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
            max-width: 800px;
            border-radius: 10px;
        }

        .boton {
            display: inline-block;
            background-color: #171849ff;
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
            background-color: #312657ff;
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
            content: "🦁";
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

    <h1>El mago de Oz</h1>

    <div class="contenedor">
        <div class="imagen">
            <img src="../imagenes/magoportada.jpg" alt="El mago de Oz">
        </div>

        <div class="resumen">
            <h2>Resumen</h2>
            <p>
                El maravilloso mago de Oz narra la historia de Dorothy, una niña que vive en Kansas y es arrastrada por un tornado, junto a su perro Toto, hasta la mágica tierra de Oz.
            </p>

            <a href="../pregunta/preguntas_mago.php" class="boton">Capítulos con preguntas</a>
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
