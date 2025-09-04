<?php
session_start();
// estados de capítulos completados (temporal). En producción leer por usuario desde BD.
$completed = $_SESSION['completed'] ?? [1,2,3];
$startIndex = (!empty($completed) ? max($completed) : 1) - 1;
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Menú de Capítulos - Drácula</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <style>
        :root{--bg1:#1a0f2e;--bg2:#0f0816}
        body{font-family:Arial,Helvetica,sans-serif;background:linear-gradient(180deg,var(--bg1) 0%, var(--bg2) 100%);color:#fff;margin:0;padding:18px}
        .header{max-width:1100px;margin:0 auto;padding:8px 12px 18px;display:flex;align-items:center;gap:12px}
        .back-btn{display:inline-flex;align-items:center;gap:8px;background:transparent;color:#fff;padding:8px 12px;border-radius:18px;text-decoration:none;border:1px solid rgba(255,255,255,0.06)}
        .back-btn svg{height:22px;width:22px}
        .title{ flex:1;text-align:center;font-size:20px;color:#f3e6ff }

        .stage{max-width:1100px;margin:0 auto}
        .map-wrap{position:relative;height:860px;margin:8px auto 50px;max-width:520px}
        svg#route-svg{position:absolute;inset:0;width:100%;height:100%;overflow:visible}

        .coin{position:absolute;display:flex;align-items:center;justify-content:center;width:68px;height:68px;border-radius:50%;background:linear-gradient(180deg,#ffd54f,#f39c12);color:#4a2b00;font-weight:800;font-size:16px;text-decoration:none;box-shadow:0 10px 30px rgba(0,0,0,.5);transition:transform .18s ease,box-shadow .18s ease;z-index:3}
        .coin .num{color:#fff;text-shadow:0 1px 0 rgba(0,0,0,.4);font-weight:800}
        .coin:hover{box-shadow:0 18px 48px rgba(0,0,0,.65)}
        .coin.completed{background:linear-gradient(180deg,#9be15d,#4ade80);color:#083012}
        .coin .check{position:absolute;right:-6px;bottom:-6px;background:#fff;color:#2d7a2f;border-radius:50%;width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-size:14px;box-shadow:0 6px 18px rgba(0,0,0,.35)}

        .walker{position:absolute;width:72px;height:72px;transform:translate(-50%,-50%);pointer-events:none;z-index:6}
        .char-left,.char-right{position:absolute;width:72px;height:72px;z-index:4}
        .char-left{left:-88px}
        .char-right{right:-88px}
        .trophy{position:absolute;bottom:8px;left:50%;transform:translateX(-50%);width:84px;z-index:3}
        @media(max-width:700px){ .map-wrap{height:760px}.char-left{left:-60px}.char-right{right:-60px} }
    </style>
</head>
<body>
    <div class="header">
        <a class="back-btn" href="/proyecto-bookrush/cuentos/dracula.php" title="Volver">
            <svg viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </a>
        <div class="title">Capítulos de Drácula</div>
    </div>

    <div class="stage">
        <div class="map-wrap" id="map-wrap">
            <img class="char-left" src="../imagenes/magodentro.jpg" style="top:80px" alt="personaje">
            <img class="char-right" src="../imagenes/mujerdentro.jpg" style="top:380px" alt="personaje">

            <svg id="route-svg" viewBox="0 0 520 920" preserveAspectRatio="xMidYMid meet">
                <path id="route" d="M260,40 C200,120 320,180 260,260 C200,340 320,400 260,480 C200,560 320,620 260,700 C200,780 320,840 260,920" fill="none" stroke="rgba(255,200,40,0.12)" stroke-width="30" stroke-linecap="round" />
            </svg>

            <?php for($n=1;$n<=27;$n++):
                $isDone = in_array($n, $completed);
            ?>
                <a class="coin<?php echo $isDone? ' completed':''; ?>" data-index="<?php echo $n; ?>" href="capdrac/<?php echo $n; ?>.php" title="Capítulo <?php echo $n; ?>">
                    <span class="num"><?php echo $n; ?></span>
                    <?php if($isDone): ?><span class="check">✓</span><?php endif; ?>
                </a>
            <?php endfor; ?>

            <img id="walker" class="walker" src="../imagenes/usuario.png" alt="walker">
            <img class="trophy" src="../imagenes/estrella.png" alt="trophy">
        </div>
    </div>

    <script>
    (function(){
        const path = document.getElementById('route');
        const svg = document.getElementById('route-svg');
        const coins = Array.from(document.querySelectorAll('.coin'));
        const walker = document.getElementById('walker');

        function placeCoins(){
            const pathLen = path.getTotalLength();
            const wrapRect = document.getElementById('map-wrap').getBoundingClientRect();
            const svgPoint = svg.createSVGPoint();

            // distribuir usando (i+1)/(N+1) para evitar extremos
            coins.forEach((coin, idx) => {
                const t = (idx + 1) / (coins.length + 1);
                const pt = path.getPointAtLength(t * pathLen);
                svgPoint.x = pt.x; svgPoint.y = pt.y;
                const screenPt = svgPoint.matrixTransform(svg.getScreenCTM());
                const cx = screenPt.x - wrapRect.left;
                const cy = screenPt.y - wrapRect.top;
                coin.style.left = cx + 'px';
                coin.style.top = cy + 'px';
                const rot = (idx % 2 === 0) ? -10 : 10;
                coin.style.transform = `translate(-50%,-50%) rotate(${rot}deg)`;
                coin.style.zIndex = 4 + idx;
                coin.style.display = 'flex';
            });
        }

        // spacing sobre la longitud del path (margin + spacing)
        function computeLengths(){
            const L = path.getTotalLength();
            const margin = L * 0.06;
            const spacing = (L - 2 * margin) / (Math.max(coins.length - 1, 1));
            return coins.map((c,i)=> margin + i * spacing);
        }

        let lengths = computeLengths();

        // startIndex basado en último completado desde PHP
        const startIndex = <?php echo $startIndex; ?>;

        function setWalkerAtLength(len){
            const p = svg.createSVGPoint();
            const pt = path.getPointAtLength(len);
            p.x = pt.x; p.y = pt.y;
            const s = p.matrixTransform(svg.getScreenCTM());
            const wrapRect = document.getElementById('map-wrap').getBoundingClientRect();
            walker.style.left = (s.x - wrapRect.left) + 'px';
            walker.style.top = (s.y - wrapRect.top) + 'px';
        }

        function animateWalkerTo(targetLength, duration = 700){
            const start = performance.now();
            const current = lengths[startIndex] || lengths[0];
            function frame(now){
                const t = Math.min(1,(now-start)/duration);
                const eased = t < 0.5 ? 2*t*t : -1 + (4-2*t)*t;
                const v = current + (targetLength - current) * eased;
                setWalkerAtLength(v);
                if(t<1) requestAnimationFrame(frame);
            }
            requestAnimationFrame(frame);
        }

        window.addEventListener('resize', ()=>{ setTimeout(()=>{ placeCoins(); lengths = computeLengths(); setWalkerAtLength(lengths[startIndex]||lengths[0]); }, 120); });

        // inicial
        setTimeout(()=>{ placeCoins(); lengths = computeLengths(); setWalkerAtLength(lengths[startIndex]||lengths[0]); }, 120);

        // click handler: animar moneda + walker y navegar
        coins.forEach((c,i)=>{
            c.addEventListener('click', function(ev){
                ev.preventDefault();
                const target = lengths[i];
                c.style.transition = 'transform 260ms ease';
                c.style.transform = 'translate(-50%,-50%) scale(1.12)';
                c.style.zIndex = 999;
                animateWalkerTo(target, 650);
                setTimeout(()=>{ c.style.transform = `translate(-50%,-50%) rotate(${(i%2===0)?-10:10}deg)`; c.style.zIndex = 4 + i; window.location.href = c.getAttribute('href'); }, 780);
            });
        });
    })();
    </script>

</body>
</html>

