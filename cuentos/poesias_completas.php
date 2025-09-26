<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <title>POESÍAS COMPLETAS</title>
    <style>
        /* Mantener la estructura y disposición de `dracula.php`, solo cambiar colores y textos */
        :root{--bg1:#5a1f1a;--bg2:#4a1510;--accent:#d3a15f}
        body{
            font-family: 'Segoe UI', sans-serif;
            background:
                linear-gradient(180deg, rgba(90,31,26,0.95) 0%, rgba(74,21,16,0.95) 100%),
                    url('../imagenes/melgar_portada.jpg');
            background-size: cover;
            background-position: center;
            color:#fff;
            margin:0;
            padding:18px;
            position:relative;
            overflow-x:hidden;
        }

        .header-space{height:18px}
        .volver{margin:0 0 12px}
        .flecha-grande::before{content:'←';font-size:50px;color:var(--accent);text-decoration:none}

        .contenedor{max-width:1200px;margin:auto;padding:20px;display:flex;gap:30px;flex-wrap:wrap}

        h1{font-size:70px;font-weight:bold;margin-top:30px;text-transform:uppercase;padding-bottom:10px;text-align:center;display:block;margin-left:auto;margin-right:auto;font-family: 'Fredoka', sans-serif;color:var(--accent)}

        .resumen{flex:1;min-width:300px;font-size:20px;padding-left:40px;line-height:1.6;color:#fff}
        .resumen h2{font-size:50px;margin-top:10px}

        .imagen{flex:1;text-align:center}
        .imagen img{width:100%;max-width:700px;border-radius:10px;box-shadow:0 12px 30px rgba(0,0,0,0.4)}

    .boton{display:inline-block;background-color:rgba(10,6,6,0.85);color:var(--accent);padding:10px 20px;margin:10px 10px 10px 0;text-decoration:none;border-radius:25px;font-weight:bold;border:2px solid rgba(0,0,0,0.6);box-shadow:0 10px 30px rgba(0,0,0,0.5)}
    .boton:hover{background-color:rgba(20,10,10,0.95);transform:translateY(-2px);box-shadow:0 14px 36px rgba(0,0,0,0.6)}

        .personajes,.lista{margin-top:30px;color:#fff}
        .personaje{background:rgba(0,0,0,0.25);color:#fff;padding:10px 20px;border-radius:25px;display:inline-block;font-weight:700;margin-right:8px}

        .lista ul{list-style:none;padding:0}
        .lista li{margin-bottom:10px;color:#fff}

    </style>
</head>
<body>

    <div class="volver">
    <a href="../index.php" class="flecha-grande"></a>
    </div>

    <h1>POESÍAS COMPLETAS</h1>

    <div class="contenedor">
        <div class="imagen">
                    <img src="../imagenes/melgar_portada.jpg" alt="POESÍAS COMPLETAS - Portada">
        </div>

        <div class="resumen">
            <h2>Resumen</h2>
            <p>
                Una obra que reúne la totalidad de la obra poética de Mariano Melgar. Se encuentran sus célebres yaravíes, cantos breves inspirados en la tradición quechua, donde la pasión amorosa y el dolor del desengaño adquieren una voz íntima y mestiza que lo convierten en pionero de una lírica nacional.
            </p>
            <a href="../pregunta/menupoesias.php" class="boton">Capítulos con preguntas</a>
            <?php if (isset($_SESSION['usuario'])): ?>
            <a href="../descargar.php?archivo=poesias_completas.pdf" class="boton">Descargar libro completo</a>
            <?php else: ?>
                <p><strong>Debes iniciar sesión para descargar el libro.</strong></p>
                <a href="../login.php" class="boton">Iniciar sesión</a>
            <?php endif; ?>

            <div class="personajes">
                <div class="personaje">Personajes</div>
            </div>

            <div class="lista">
                <ul>
                    <li>Silvia “Musa amada” - Amor idealizado, ternura y dolor por el desengaño amoroso.</li>
                    <li>El Ruiseñor - Encarna la voz dulce y poética, símbolo del arte y la sensibilidad.</li>
                    <li>El Calesero - Figura de tono popular, que refleja la tensión entre lo vulgar y lo sublime.</li>
                    <li>El Lobo y la Ballena - Animales alegóricos usados para transmitir enseñanzas morales y críticas sociales.</li>
                    <li>El Yo Poético - Melgar se presenta como protagonista en muchos versos, un joven sensible que canta al amor, la libertad y la patria.</li>
                </ul>
            </div>
        </div>
    </div>

</body>
</html>
