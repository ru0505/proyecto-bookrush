<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <title>El Caballero Carmelo</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #2e1f26; /* fondo uniforme café */
            color: #f4efe9;
            margin: 0;
            padding: 0;
            font-family: 'Fredoka', sans-serif;
            background-attachment: fixed;
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
        display: block;
        margin-left: auto;
        margin-right: auto;
        font-family: 'Fredoka', sans-serif;
        color: #f6d29a; /* dorado suave para título */
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
            color: #ffdca3;
        }

        .imagen {
            flex: 1;
            text-align: center;
        }

        .imagen img {
            width: 100%;
            max-width: 700px;
            border-radius: 10px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6);
            display: block;
            margin: 0 auto;
        }

        .boton {
            display: inline-block;
            background-color: #8b1538; /* mismo carmesí usado en drácula para consistencia */
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
            background-color: rgba(0,0,0,0.25);
            color: #ffdca3;
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
            content: "🐎";
            margin-right: 8px;
        }

        .volver {
            margin: 20px;
        }

        .flecha-grande::before {
            content: "←";
            font-size: 50px;
            color: #f6d29a;
            text-decoration: none;
        }
        .flecha-grande {
            text-decoration: none;
        }

        /* Fondo temático sutil adicional */
        /* Colapsar hero para evitar franja: fondo uniforme de página ya determina el color */
        .hero {
                background: transparent;
                padding: 0;
                margin: 0;
                height: 0;
                min-height: 0;
                border-bottom: none;
                box-shadow: none;
                display:block;
            }

    </style>
</head>
<body>

    <div class="volver">
    <a href="../index.php" class="flecha-grande" title="Volver"></a>
    </div>

    <h1>El Caballero Carmelo</h1>

    <div class="hero">
        <!-- Cabecera simple para evitar doble fondo; imagen de portada mostrada en el bloque principal -->
    </div>

    <div class="contenedor">
        <div class="imagen">
            <img src="../imagenes/caballerocarme.png" alt="El Caballero Carmelo portada">
        </div>

        <div class="resumen">
            <h2>Resumen</h2>
            <p>
                "Caballero Carmelo" sigue a un joven que se convierte en el mejor amigo de un caballo, Carmelo. Juntos, atraviesan las dificultades de un mundo rural en transformación, donde el amor, la amistad y la pérdida marcan su camino. La historia es un retrato conmovedor de la vida en el campo y la lucha por encontrar un lugar en la sociedad.
            </p>
            <a href="../pregunta/menucarmelo.php" class="boton">Capítulos con preguntas</a>
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
                    <li>Carmelo – el caballo, símbolo de libertad y amistad.</li>
                    <li>José – el joven protagonista, soñador y valiente.</li>
                    <li>Don Manuel – el sabio anciano del pueblo, guía de José.</li>
                    <li>María – interés amoroso de José, representa la esperanza.</li>
                    <li>Don Pedro – el terrateniente, antagonista del cambio.</li>
                    <li>Los amigos de José – reflejan el espíritu de camaradería y aventura.</li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
