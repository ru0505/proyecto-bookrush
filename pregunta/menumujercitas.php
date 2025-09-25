<?php
session_start();
$completed = $_SESSION['completed'] ?? [1];
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Menú de Capítulos - Mujercitas</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka&display=swap" rel="stylesheet">
    <style>
        :root{--paper:#fffaf6;--accent:#d85e39;--accent-2:#ff8c42;--muted:#f2e7df}
        body{
            font-family:'Fredoka', Arial, Helvetica, sans-serif;
            background: linear-gradient(180deg,var(--paper), #fff4ea);
            color:#222;
            margin:0;
            padding:24px 18px;
            -webkit-font-smoothing:antialiased;
        }

        .header{max-width:1100px;margin:0 auto;padding:8px 12px 18px;display:flex;align-items:center;gap:12px;position:relative;z-index:10}
        .back-btn{
            display:inline-flex;
            align-items:center;
            gap:8px;
            /* color naranja translúcido con blur para mantener el estilo visual */
            background:rgba(216,94,57,0.30);
            backdrop-filter:blur(10px);
            color:#fff;
            padding:12px 20px;
            border-radius:25px;
            text-decoration:none;
            border:2px solid rgba(216,94,57,0.40);
            transition:all 0.3s ease;
            font-weight:600;
            font-size:14px;
            letter-spacing:0.5px;
            position:absolute;
            left:-120px;
            text-transform:uppercase;
            z-index:12;
        }
        .back-btn:hover{background:rgba(216,94,57,0.45);border-color:rgba(216,94,57,0.7);transform:translateY(-2px);box-shadow:0 8px 20px rgba(216,94,57,0.25)}
        .title{flex:1;text-align:center;font-size:28px;color:var(--accent);font-weight:900;letter-spacing:1px}

        .stage{max-width:1000px;margin:12px auto;position:relative;z-index:5}
        .map-wrap{position:relative;height:640px;margin:8px auto 50px;max-width:880px;border-radius:18px;background:linear-gradient(180deg,rgba(255,250,246,0.95), rgba(255,245,238,0.95));border:1px solid rgba(216,94,57,0.06);box-shadow:0 12px 30px rgba(0,0,0,0.06);padding:28px}

        /* camino decorativo y suave */
    .main-path{position:absolute;height:12px;background:linear-gradient(90deg,var(--accent-2),var(--accent));z-index:1;border-radius:12px;box-shadow:0 0 18px rgba(216,94,57,0.18)}

    /* Coins más visibles: círculos, mayor contraste y sombra */
    .coin{position:absolute;display:flex;align-items:center;justify-content:center;width:76px;height:76px;border-radius:50%;background:linear-gradient(180deg,#fff,#f7efe6);color:#555;font-weight:800;font-size:18px;text-decoration:none;box-shadow:0 14px 30px rgba(216,94,57,0.08), 0 6px 16px rgba(0,0,0,0.08);transition:transform .18s ease, box-shadow .18s ease;z-index:3;border:3px solid rgba(216,94,57,0.06);transform:translate(-50%,-50%)}
    .coin .num{color:var(--accent);font-size:18px}

    .coin.blocked{background:linear-gradient(180deg,#faf7f5,#f2ebe3);color:#b2998f;border-color:rgba(200,200,200,0.5)}

    .coin.playable{background:linear-gradient(135deg,var(--accent-2),var(--accent));color:#fff;border-color:rgba(255,140,66,0.95);box-shadow:0 18px 40px rgba(216,94,57,0.18), 0 8px 20px rgba(0,0,0,0.12);cursor:pointer}
    .coin.playable .num{color:#fff}
    .coin.playable:hover{box-shadow:0 24px 54px rgba(216,94,57,0.22);transform:translate(-50%,-50%) scale(1.08)}

    .coin.completed{background:linear-gradient(135deg,#a3d29a,#6fb06a);color:#fff;border-color:rgba(95,155,99,0.9);cursor:pointer}

    .final-level{background:linear-gradient(135deg,#ff8c42,#d85e39) !important;border-color:rgba(216,94,57,0.95) !important;box-shadow:0 22px 60px rgba(216,94,57,0.20) !important;width:110px !important;height:110px !important;font-size:22px !important;border-radius:50% !important}

        @media(max-width:900px){ .map-wrap{height:560px} .coin{width:54px;height:54px;font-size:14px} .final-level{width:76px;height:76px}}

    </style>
</head>
<body>
    <div class="header">
        <a class="back-btn" href="/proyecto-bookrush/index.php" title="Ir al Menú Principal">Menú Principal</a>
        <div class="title">Capítulos de Mujercitas</div>
    </div>

    <div class="stage">
        <div class="map-wrap" id="map-wrap">
            <!-- SVG vacío: lo rellenaremos desde JS con la ruta que conecte las monedas -->
            <svg id="map-svg" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;pointer-events:none;">
                <path id="main-path" d="" stroke="rgba(216,94,57,0.95)" stroke-width="10" fill="none" stroke-linecap="round" stroke-linejoin="round" style="filter: drop-shadow(0 0 18px rgba(216,94,57,0.25));" />
            </svg>

            <!-- Capítulos 1..9: serán posicionados por JS alrededor del centro -->
            <a href="capmujercitas/1.php" class="coin playable" data-index="1"><span class="num">1</span></a>
            <a href="capmujercitas/2.php" class="coin blocked" data-index="2"><span class="num">2</span></a>
            <a href="capmujercitas/3.php" class="coin blocked" data-index="3"><span class="num">3</span></a>
            <a href="capmujercitas/4.php" class="coin blocked" data-index="4"><span class="num">4</span></a>
            <a href="capmujercitas/5.php" class="coin blocked" data-index="5"><span class="num">5</span></a>
            <a href="capmujercitas/6.php" class="coin blocked" data-index="6"><span class="num">6</span></a>
            <a href="capmujercitas/7.php" class="coin blocked" data-index="7"><span class="num">7</span></a>
            <a href="capmujercitas/8.php" class="coin blocked" data-index="8"><span class="num">8</span></a>
            <a href="capmujercitas/9.php" class="coin blocked" data-index="9"><span class="num">9</span></a>

            <!-- Nivel 10 (Final) estará en el centro -->
            <a href="capmujercitas/10.php" class="coin final-level blocked" data-index="10"><span class="num">10</span></a>

        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = document.getElementById('map-wrap');
        const svg = document.getElementById('map-svg');
        const pathEl = document.getElementById('main-path');

        // recoger coins por índice
        const coins = Array.from(document.querySelectorAll('.coin')).sort((a,b)=> parseInt(a.dataset.index)-parseInt(b.dataset.index));

        function layoutCoins() {
            const rect = map.getBoundingClientRect();
            const cx = rect.width/2;
            const cy = rect.height/2 + 20; // centro ligeramente bajo

            // radio para la circunferencia exterior
            const r = Math.min(rect.width, rect.height) * 0.28;

            // colocamos 1..9 en círculo alrededor, el 10 en el centro
            coins.forEach((coin) => {
                const idx = parseInt(coin.dataset.index,10);
                if (idx === 10) {
                    coin.style.left = cx + 'px';
                    coin.style.top = cy + 'px';
                    return;
                }

                // ángulo repartido para 9 items (de -140° a 140°) para formar un óvalo
                const start = -140 * (Math.PI/180);
                const end = 140 * (Math.PI/180);
                const t = (idx - 1) / 8; // 0..1
                const angle = start + (end - start) * t;
                // ajustar radios elípticos
                const rx = r * 1.05;
                const ry = r * 0.9;
                const x = cx + Math.cos(angle) * rx;
                const y = cy + Math.sin(angle) * ry;
                coin.style.left = x + 'px';
                coin.style.top = y + 'px';
            });

            // reconstruir path SVG conectando centros de coins en orden 1..9 y terminando en 10 (centro)
            const points = coins.map(c => {
                const r = c.getBoundingClientRect();
                const parentRect = map.getBoundingClientRect();
                // coordenadas relativas al svg (que tiene width/height 100%)
                const px = r.left - parentRect.left + r.width/2;
                const py = r.top - parentRect.top + r.height/2;
                return {x: px, y: py};
            });

            if (points.length > 0) {
                const d = points.map((p,i)=> (i===0? `M ${p.x} ${p.y}` : `L ${p.x} ${p.y}`)).join(' ');
                pathEl.setAttribute('d', d);
            }
        }

        // interacciones estéticas
        coins.forEach(c => {
            c.addEventListener('click', function(e){
                if (this.classList.contains('blocked')){
                    e.preventDefault();
                    this.style.transform = 'translate(-50%,-50%) scale(0.95)';
                    setTimeout(()=> this.style.transform = 'translate(-50%,-50%)', 180);
                    return;
                }
                // permitir navegación normal
            });
        });

        // activar primer nivel y final como jugables por defecto
        const lvl1 = document.querySelector('a[href="capmujercitas/1.php"]');
        if (lvl1) { lvl1.classList.remove('blocked'); lvl1.classList.add('playable'); }
        const lvl10 = document.querySelector('a[href="capmujercitas/10.php"]');
        if (lvl10) { lvl10.classList.remove('blocked'); lvl10.classList.add('playable'); }

        // layout inicial y en resize
        layoutCoins();
        window.addEventListener('resize', ()=> { layoutCoins(); });
    });
    </script>

</body>
</html>
