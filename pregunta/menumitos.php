<?php
session_start();
// progreso temporal
$completed = $_SESSION['completed'] ?? [1];
$startIndex = (!empty($completed) ? max($completed) : 1) - 1;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Menú de Capítulos - Mitos y Leyendas (Arequipa)</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root{--bg1:#0b2436;--bg2:#07202a;--accent:#d85e39;--gold:#f0c64b;--shadow:rgba(216,94,57,0.28)}
        body{
            font-family:Arial,Helvetica,sans-serif;
            background:
                linear-gradient(180deg,rgba(11,36,54,0.85) 0%, rgba(7,32,42,0.92) 100%),
                url('../imagenes/puerta.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            color:#fff;
            margin:0;
            padding:18px;
            position:relative;
            overflow-x:hidden
        }

        .header{max-width:1100px;margin:0 auto;padding:8px 12px 18px;display:flex;align-items:center;gap:12px;position:relative;z-index:10}
        .back-btn{display:inline-flex;align-items:center;gap:8px;background:rgba(216,94,57,0.25);backdrop-filter:blur(6px);color:#fff;padding:12px 20px;border-radius:25px;text-decoration:none;border:2px solid rgba(216,94,57,0.3);transition:all 0.3s ease;font-weight:600;font-size:14px;letter-spacing:0.5px;position:absolute;left:-120px;text-transform:uppercase}
        .title{flex:1;text-align:center;font-size:24px;color:#fff;text-shadow:0 2px 10px rgba(216,94,57,0.25);font-weight:400;letter-spacing:1px}

        .stage{max-width:1200px;margin:0 auto;position:relative;z-index:5}
        .map-wrap{position:relative;height:820px;margin:8px auto 50px;max-width:950px;border-radius:20px;background:linear-gradient(180deg, rgba(7,32,42,0.35), rgba(11,36,54,0.25));backdrop-filter:blur(10px);border:1px solid rgba(216,94,57,0.08);box-shadow:0 20px 40px rgba(0,0,0,0.35);padding:40px}

    /* Línea principal SVG */
    /* Monedas base ahora gris claro para contrastar con fondo oscuro */
    .coin{position:absolute;display:flex;align-items:center;justify-content:center;width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#e6e6e6,#cfcfcf);color:#222;font-weight:800;font-size:15px;text-decoration:none;box-shadow:0 8px 20px rgba(0,0,0,.45), 0 0 12px rgba(0,0,0,0.06);transition:all .18s ease;z-index:3;border:3px solid rgba(0,0,0,0.12);transform:translate(-50%,-50%)}
        .coin .num{color:#222;text-shadow:0 1px 1px rgba(255,255,255,0.2);font-weight:800}

        .coin.blocked{background:linear-gradient(135deg,#dcdcdc,#c8c8c8);border-color:rgba(0,0,0,0.06);cursor:not-allowed;color:#444}
        .coin.playable{background:linear-gradient(135deg,#f39c12,#d46a2a);color:#111;border-color:rgba(240,198,75,0.95);box-shadow:0 8px 20px rgba(0,0,0,.45), 0 0 18px rgba(240,198,75,0.35)}
        .coin.completed{background:linear-gradient(135deg,#4a9b4e,#2d5a30);color:#fff;border-color:rgba(74,155,78,0.6);box-shadow:0 8px 20px rgba(0,0,0,.45), 0 0 15px rgba(74,155,78,0.3)}

        .final-level{background:linear-gradient(135deg,#d85e39,#a43f2a) !important;border-color:rgba(216,94,57,0.8) !important;box-shadow:0 15px 40px rgba(0,0,0,.7), 0 0 35px rgba(216,94,57,0.5) !important;width:88px !important;height:88px !important;font-size:20px !important;border-width:4px !important}

        svg{position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;pointer-events:none}

        @media(max-width:900px){ .map-wrap{height:700px;transform:scale(0.85)} .coin{width:52px;height:52px} .final-level{width:72px;height:72px} }
        @media(max-width:700px){ .map-wrap{height:520px;transform:scale(0.7)} .coin{width:44px;height:44px} .final-level{width:60px;height:60px} }
    </style>
</head>
<body>
    <div class="header">
        <a class="back-btn" href="/proyecto-bookrush/index.php" title="Ir al Menú Principal">Menú Principal</a>
        <div class="title">Mitos y Leyendas (Arequipa) - Capítulos</div>
    </div>

    <div class="stage">
        <div class="map-wrap" id="map-wrap">
            <!-- Nivel final central -->
            <a href="capmitos/16.php" class="coin final-level playable" style="left:54.74%;top:63.41%"><span class="num">16</span></a>

            <!-- Línea SVG que conecta 16 niveles en secuencia (pasa por el centro exacto de cada coin) -->
            <svg viewBox="0 0 950 820" preserveAspectRatio="xMidYMid meet">
                <!-- puntos = centros de cada coin (left + 32, top + 32) -->
                <path d="M 572 452 L 652 392 L 732 352 L 812 312 L 872 252 L 832 192 L 752 172 L 672 152 L 572 132 L 492 172 L 412 212 L 352 272 L 292 332 L 332 392 L 392 442 L 520 520"
                      stroke="rgba(240,198,75,0.95)"
                      stroke-width="10"
                      fill="none"
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      style="filter: drop-shadow(0 0 14px rgba(240,198,75,0.25));" />
            </svg>

            <!-- Niveles individuales -->
            <a href="capmitos/1.php" class="coin playable" style="left:60.21%;top:55.12%"><span class="num">1</span></a>
            <a href="capmitos/2.php" class="coin blocked" style="left:68.63%;top:47.80%"><span class="num">2</span></a>
            <a href="capmitos/3.php" class="coin blocked" style="left:77.05%;top:42.93%"><span class="num">3</span></a>
            <a href="capmitos/4.php" class="coin blocked" style="left:85.47%;top:38.05%"><span class="num">4</span></a>
            <a href="capmitos/5.php" class="coin blocked" style="left:91.79%;top:30.73%"><span class="num">5</span></a>
            <a href="capmitos/6.php" class="coin blocked" style="left:87.58%;top:23.41%"><span class="num">6</span></a>
            <a href="capmitos/7.php" class="coin blocked" style="left:79.16%;top:20.98%"><span class="num">7</span></a>
            <a href="capmitos/8.php" class="coin blocked" style="left:70.74%;top:18.54%"><span class="num">8</span></a>
            <a href="capmitos/9.php" class="coin blocked" style="left:60.21%;top:16.10%"><span class="num">9</span></a>
            <a href="capmitos/10.php" class="coin blocked" style="left:51.79%;top:20.98%"><span class="num">10</span></a>
            <a href="capmitos/11.php" class="coin blocked" style="left:43.37%;top:25.85%"><span class="num">11</span></a>
            <a href="capmitos/12.php" class="coin blocked" style="left:37.05%;top:33.17%"><span class="num">12</span></a>
            <a href="capmitos/13.php" class="coin blocked" style="left:30.74%;top:40.49%"><span class="num">13</span></a>
            <a href="capmitos/14.php" class="coin blocked" style="left:34.95%;top:47.80%"><span class="num">14</span></a>
            <a href="capmitos/15.php" class="coin blocked" style="left:41.26%;top:53.90%"><span class="num">15</span></a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const coins = document.querySelectorAll('.coin');
        updateLevelStates();

        coins.forEach(coin => {
            coin.addEventListener('click', function(e) {
                if (this.classList.contains('blocked')) {
                    e.preventDefault();
                    this.style.transition = 'transform 0.18s ease';
                    this.style.transform = 'translate(-50%,-50%) scale(0.95)';
                    setTimeout(() => { this.style.transform = 'translate(-50%,-50%) scale(1)'; }, 180);
                    return;
                }
                this.style.transition = 'transform 0.18s ease';
                this.style.transform = 'translate(-50%,-50%) scale(1.12)';
                setTimeout(() => { this.style.transform = 'translate(-50%,-50%) scale(1)'; }, 180);
            });
        });

        function updateLevelStates(){
            const level1 = document.querySelector('a[href="capmitos/1.php"]');
            if(level1){ level1.classList.remove('blocked'); level1.classList.add('playable'); }
            const final = document.querySelector('a[href="capmitos/16.php"]');
            if(final){ final.classList.remove('blocked'); final.classList.add('playable'); }
        }
    });
    </script>
</body>
</html>
