<?php
session_start();
$completed = $_SESSION['completed'] ?? [1];
$startIndex = (!empty($completed) ? max($completed) : 1) - 1;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Menú de Capítulos - El Caballero Carmelo</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root{--bg1:#2e1f26;--bg2:#1e1516;--accent:#b86f2f;--gold:#f0c97a;--shadow:rgba(184,111,47,0.2)}
        body{
            font-family:Arial,Helvetica,sans-serif;
            background: linear-gradient(180deg, rgba(46,31,38,0.95) 0%, rgba(23,18,20,0.98) 100%);
            color:#fff;
            margin:0;padding:18px;position:relative;overflow-x:hidden
        }

        .header{max-width:1100px;margin:0 auto;padding:8px 12px 18px;display:flex;align-items:center;gap:12px;position:relative;z-index:10}
        .back-btn{display:inline-flex;align-items:center;gap:8px;background:rgba(184,111,47,0.2);color:#fff;padding:12px 20px;border-radius:25px;text-decoration:none;border:2px solid rgba(184,111,47,0.25);transition:all 0.3s ease;font-weight:600;font-size:14px;letter-spacing:0.5px;position:absolute;left:-120px;text-transform:uppercase}
        .back-btn:hover{background:rgba(184,111,47,0.3);border-color:rgba(184,111,47,0.4);transform:translateY(-2px)}
        .title{flex:1;text-align:center;font-size:24px;color:#fbe8cf;text-shadow:0 2px 10px rgba(184,111,47,0.15);font-weight:300}

        .stage{max-width:1200px;margin:0 auto;position:relative;z-index:5}
        .map-wrap{position:relative;height:800px;margin:8px auto 50px;max-width:900px;border-radius:20px;background:rgba(46,31,38,0.05);backdrop-filter:blur(6px);border:1px solid rgba(240,201,122,0.04);box-shadow:0 10px 30px rgba(0,0,0,0.3);padding:40px}

        .main-path{position:absolute;height:8px;background:linear-gradient(90deg, rgba(240,201,122,0.9), rgba(240,201,122,1), rgba(240,201,122,0.9));z-index:0;border-radius:4px;box-shadow:0 0 20px rgba(240,201,122,0.2)}

        .coin{position:absolute;display:flex;align-items:center;justify-content:center;width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#6b4f45,#4a382f);color:#fff;font-weight:800;font-size:15px;text-decoration:none;box-shadow:0 8px 20px rgba(0,0,0,.4), 0 0 10px rgba(75,45,30,0.15);transition:all .18s ease;z-index:3;border:3px solid rgba(80,50,35,0.5)}
        .coin .num{color:#ffdca3}

        .coin.blocked{background:linear-gradient(135deg,#5a463f,#312521);border-color:rgba(70,40,30,0.5);cursor:not-allowed}
        .coin.playable{background:linear-gradient(135deg,#ffb86a,#d48a3a);color:#fff;border-color:rgba(240,201,122,0.8);box-shadow:0 8px 20px rgba(0,0,0,.4), 0 0 15px rgba(240,201,122,0.2)}
        .coin.completed{background:linear-gradient(135deg,#6aa06b,#3e7a4f);border-color:rgba(80,150,100,0.6)}

        .final-level{background:linear-gradient(135deg,#b86f2f,#8a4a2a) !important;border-color:rgba(184,111,47,0.8) !important;box-shadow:0 15px 40px rgba(0,0,0,.6), 0 0 35px rgba(184,111,47,0.4) !important;width:85px !important;height:85px !important;font-size:20px !important;border-width:4px !important}

        @media(max-width:900px){.map-wrap{height:700px;transform:scale(0.88)} .coin{width:50px;height:50px}}
        @media(max-width:700px){.map-wrap{height:500px;transform:scale(0.7)} .coin{width:44px;height:44px}}
    </style>
</head>
<body>
    <div class="header">
        <a class="back-btn" href="/proyecto-bookrush/index.php" title="Ir al Menú Principal">Menú Principal</a>
        <div class="title">Capítulos - El Caballero Carmelo</div>
    </div>

    <div class="stage">
        <div class="map-wrap" id="map-wrap">
            <!-- Nivel final (13) -->
            <a href="capcarm/13.php" class="coin final-level blocked" style="left:620px;top:220px"><span class="num">13</span></a>

            <!-- SVG path adaptado para 13 puntos -->
            <svg style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;pointer-events:none;">
                <!-- Path updated to pass through the center of each coin (1..12 then final 13) -->
                <path d="M 600 480 L 710 400 L 820 400 L 900 500 L 800 600 L 660 650 L 520 650 L 400 570 L 300 490 L 260 370 L 360 280 L 500 230 L 662.5 262.5"
                      stroke="rgba(240,201,122,1)" stroke-width="8" fill="none" stroke-linecap="round" stroke-linejoin="round" style="filter: drop-shadow(0 0 12px rgba(240,201,122,0.4));" />
            </svg>

            <!-- Niveles 1..12 -->
            <a href="capcarm/1.php" class="coin playable" style="left:570px;top:450px"><span class="num">1</span></a>
            <a href="capcarm/2.php" class="coin blocked" style="left:680px;top:370px"><span class="num">2</span></a>
            <a href="capcarm/3.php" class="coin blocked" style="left:790px;top:370px"><span class="num">3</span></a>
            <a href="capcarm/4.php" class="coin blocked" style="left:870px;top:470px"><span class="num">4</span></a>
            <a href="capcarm/5.php" class="coin blocked" style="left:770px;top:570px"><span class="num">5</span></a>
            <a href="capcarm/6.php" class="coin blocked" style="left:630px;top:620px"><span class="num">6</span></a>
            <a href="capcarm/7.php" class="coin blocked" style="left:490px;top:620px"><span class="num">7</span></a>
            <a href="capcarm/8.php" class="coin blocked" style="left:370px;top:540px"><span class="num">8</span></a>
            <a href="capcarm/9.php" class="coin blocked" style="left:270px;top:460px"><span class="num">9</span></a>
            <a href="capcarm/10.php" class="coin blocked" style="left:230px;top:340px"><span class="num">10</span></a>
            <a href="capcarm/11.php" class="coin blocked" style="left:330px;top:250px"><span class="num">11</span></a>
            <a href="capcarm/12.php" class="coin blocked" style="left:470px;top:200px"><span class="num">12</span></a>

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
                    this.style.transition = 'all 0.2s ease';
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => { this.style.transform = 'scale(1)'; }, 200);
                    return;
                }
                this.style.transition = 'transform 0.2s ease';
                this.style.transform = 'scale(1.15)';
                setTimeout(() => { this.style.transform = 'scale(1)'; }, 200);
            });
        });

        function updateLevelStates() {
            const level1 = document.querySelector('a[href="capcarm/1.php"]');
            if (level1) { level1.classList.remove('blocked'); level1.classList.add('playable'); }
            const level13 = document.querySelector('a[href="capcarm/13.php"]');
            if (level13) { level13.classList.remove('blocked'); level13.classList.add('playable'); }
        }
    });
    </script>

</body>
</html>
