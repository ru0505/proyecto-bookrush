<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capítulo 1 - Aves sin Nido</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            background: linear-gradient(135deg, #2d4a3e 0%, #1a3429 100%);
            color: #f3e6d7;
            margin: 0;
            padding: 40px;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(45,74,62,0.3);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .chapter-title {
            font-size: 2.5em;
            color: #d4af37;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(212,175,55,0.5);
        }
        
        .chapter-subtitle {
            font-size: 1.2em;
            color: #8b941f;
            font-style: italic;
        }
        
        .content {
            font-size: 1.1em;
            text-align: justify;
            margin-bottom: 40px;
        }
        
        .content p {
            margin-bottom: 20px;
        }
        
        .navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-btn {
            background: linear-gradient(135deg, #d4af37, #b8941f);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212,175,55,0.3);
        }
        
        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212,175,55,0.5);
        }
        
        .nav-btn.disabled {
            background: #666;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="chapter-title">Capítulo I</h1>
            <p class="chapter-subtitle">El Pueblo de Killac</p>
        </div>
        
        <div class="content">
            <p>
                Hermosos los lugares donde se levanta la pequeña población de Kíllac, 
                capital de la provincia. Fértiles campiñas la rodean en toda la extensión 
                que alcanza la vista, y el pintoresco conjunto que ofrecen sus contornos 
                parece confirmarnos que la naturaleza tiene lugares privilegiados donde 
                concentra la belleza como los artistas el color en el lienzo o las 
                estrofas en el libro.
            </p>
            
            <p>
                La plaza principal es un cuadrado de regulares dimensiones, con 
                pileta de piedra en el centro. Los edificios que la circundan no 
                tienen más de dos pisos, y sus techos, cubiertos de tejas rojas, 
                ofrecen cierta particularidad que los distingue de los de otros 
                lugares, porque las últimas hileras vuelan sobre las paredes como 
                aleros, dando sombra a los balcones volados también, en cuyas barandillas 
                de madera se apoyan, en las horas de calor, las señoras del pueblo.
            </p>
            
            <p>
                En los días de feria, los domingos y las fiestas religiosas, la 
                plaza se cubre de una multitud abigarrada donde se confunden el 
                poncho colorado del indio con el rebozo azul de la chola, el 
                sombrero de paja de los chosos y el fieltro de los caballeros.
            </p>
            
            <p>
                <em>Continúa leyendo para descubrir más sobre la vida en este 
                pintoresco pueblo andino...</em>
            </p>
        </div>
        
        <div class="navigation">
            <a href="../menuaves.php" class="nav-btn">← Volver al Menú</a>
            <a href="2.php" class="nav-btn">Siguiente Capítulo →</a>
        </div>
    </div>
</body>
</html>