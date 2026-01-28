?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <title>Capítulos con Preguntas</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #6cacd1ff;
            color: #020202ff;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            font-size: 40px;
            margin: 30px 0;
            color: #ffffffff;
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


        .linea-tiempo {
            max-width: 1000px;
            margin: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 50px;
        }

        .fila {
            display: flex;
            justify-content: space-around;
            width: 100%;
            position: relative;
        }

        .boton-capitulo {
            background-color: #38256bff;
            color: white;
            padding: 40px 100px;
            border: none;
            border-radius: 20px;
            font-size: 25px;
            text-decoration: none;
            font-weight: bold;
            text-align: center;
            box-shadow: 2px 2px 2px rgba(0,0,0,0.3);
            transition: transform 0.2s;
        }

        .boton-capitulo:hover {
            transform: scale(1.05);
            background-color: #277abdff;
        }

        .flecha {
            font-size: 60px;
            color: #0f6cba;
            margin: 0 10px;
        }

        .flechas-arriba, .flechas-abajo {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
        }

        @media (max-width: 700px) {
            .fila {
                flex-direction: column;
                align-items: center;
            }

            .flechas-arriba, .flechas-abajo {
                flex-direction: column;
                gap: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="volver">
    <a href="../index.php" class="flecha-grande"></a>
    </div>

<h1>Capítulos con Preguntas</h1>

<div class="linea-tiempo">

    <div class="flechas-arriba">
        <a href="../pregunta/capcdp/1_frank.php" class="boton-capitulo">Capítulo 1</a>
        <span class="flecha">➡️</span>
        <a href="../pregunta/capcdp/2_frank.php" class="boton-capitulo">Capítulo 2</a>
        <span class="flecha">➡️</span>
        <a href="../pregunta/capcdp/3_frank.php" class="boton-capitulo">Capítulo 3</a>
    </div>

    <div class="flechas-abajo">
        <a href="../pregunta/capcdp/4_frank.php" class="boton-capitulo">Capítulo 4</a>
        <span class="flecha">➡️</span>
        <a href="../pregunta/capcdp/5_frank.php" class="boton-capitulo">Capítulo 5</a>
        
    </div>

</div>

</body>
</html>
