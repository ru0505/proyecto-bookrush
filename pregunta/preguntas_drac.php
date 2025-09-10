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
        :root{--bg1:#1a0f2e;--bg2:#0f0816;--accent:#8b1538;--gold:#d4af37;--shadow:rgba(139,21,56,0.3)}
        body{
            font-family:Arial,Helvetica,sans-serif;
            background:
                linear-gradient(180deg,rgba(26,15,46,0.85) 0%, rgba(15,8,22,0.9) 100%),
                url('../imagenes/castillo.jpg');
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
        
        /* Elementos atmosféricos sutiles */
        body::before{content:'';position:fixed;top:0;left:0;width:100%;height:100%;background:radial-gradient(circle at 20% 80%, rgba(139,21,56,0.1) 0%, transparent 50%), radial-gradient(circle at 80% 20%, rgba(139,21,56,0.08) 0%, transparent 50%);pointer-events:none;z-index:1}
        
        .header{max-width:1100px;margin:0 auto;padding:8px 12px 18px;display:flex;align-items:center;gap:12px;position:relative;z-index:10}
        .back-btn{
            display:inline-flex;
            align-items:center;
            gap:8px;
            background:rgba(139,21,56,0.3);
            backdrop-filter:blur(10px);
            color:#fff;
            padding:12px 20px;
            border-radius:25px;
            text-decoration:none;
            border:2px solid rgba(139,21,56,0.4);
            transition:all 0.3s ease;
            font-weight:600;
            font-size:14px;
            letter-spacing:0.5px;
            position:absolute;
            left:-120px;
            text-transform:uppercase
        }
        .back-btn:hover{background:rgba(139,21,56,0.5);border-color:rgba(139,21,56,0.7);transform:translateY(-2px);box-shadow:0 8px 20px rgba(139,21,56,0.3)}
        .back-btn svg{height:22px;width:22px}
        .title{flex:1;text-align:center;font-size:24px;color:#f3e6ff;text-shadow:0 2px 10px rgba(139,21,56,0.5);font-weight:300;letter-spacing:1px}

        .stage{max-width:1200px;margin:0 auto;position:relative;z-index:5}
        .map-wrap{position:relative;height:950px;margin:8px auto 50px;max-width:950px;border-radius:20px;background:rgba(26,15,46,0.2);backdrop-filter:blur(20px);border:1px solid rgba(139,21,56,0.2);box-shadow:0 20px 40px rgba(0,0,0,0.3);padding:50px}
        
        /* Decoración sutil de bordes */
        .map-wrap::before{content:'';position:absolute;top:-2px;left:-2px;right:-2px;bottom:-2px;background:linear-gradient(45deg, transparent 30%, rgba(139,21,56,0.3) 50%, transparent 70%);border-radius:22px;z-index:-1}
        
        /* Líneas conectoras entre niveles */
        .path-line{position:absolute;height:6px;background:linear-gradient(90deg, rgba(212,175,55,0.8), rgba(212,175,55,1), rgba(212,175,55,0.8));z-index:1;border-radius:3px;box-shadow:0 0 12px rgba(212,175,55,0.4)}
        .path-line.vertical{width:6px;height:auto;background:linear-gradient(180deg, rgba(212,175,55,0.8), rgba(212,175,55,1), rgba(212,175,55,0.8))}
        .path-line.diagonal{transform-origin:left center;background:linear-gradient(45deg, rgba(212,175,55,0.8), rgba(212,175,55,1), rgba(212,175,55,0.8))}
        
        /* LÍNEA PRINCIPAL ÚNICA - conecta todos los niveles en secuencia */
        .main-path{position:absolute;height:8px;background:linear-gradient(90deg, rgba(212,175,55,0.9), rgba(212,175,55,1), rgba(212,175,55,0.9));z-index:0;border-radius:4px;box-shadow:0 0 20px rgba(212,175,55,0.6)}
        .main-path.vertical{width:8px;height:auto;background:linear-gradient(180deg, rgba(212,175,55,0.9), rgba(212,175,55,1), rgba(212,175,55,0.9))}
        .main-path.diagonal{transform-origin:left center;background:linear-gradient(45deg, rgba(212,175,55,0.9), rgba(212,175,55,1), rgba(212,175,55,0.9))}
        
        /* Estados de los niveles */
        .coin{position:absolute;display:flex;align-items:center;justify-content:center;width:65px;height:65px;border-radius:50%;background:linear-gradient(135deg,#666,#444);color:#aaa;font-weight:800;font-size:15px;text-decoration:none;box-shadow:0 8px 20px rgba(0,0,0,.4), 0 0 15px rgba(102,102,102,0.2);transition:all .18s ease;z-index:3;border:3px solid rgba(102,102,102,0.6)}
        .coin .num{color:#aaa;text-shadow:0 1px 2px rgba(0,0,0,0.5);font-weight:800}
        
        /* Nivel bloqueado (gris) */
        .coin.blocked{background:linear-gradient(135deg,#555,#333);border-color:rgba(85,85,85,0.6);cursor:not-allowed}
        .coin.blocked .num{color:#777}
        
        /* Nivel jugable (naranja) */
        .coin.playable{background:linear-gradient(135deg,#ff8c42,#d46a2a);color:#fff;border-color:rgba(255,140,66,0.8);box-shadow:0 8px 20px rgba(0,0,0,.4), 0 0 15px rgba(255,140,66,0.4)}
        .coin.playable .num{color:#fff;text-shadow:0 1px 2px rgba(0,0,0,0.4)}
        .coin.playable:hover{box-shadow:0 12px 30px rgba(0,0,0,.6), 0 0 25px rgba(255,140,66,0.6);transform:scale(1.08)}
        
        /* Nivel completado (verde) */
        .coin.completed{background:linear-gradient(135deg,#4a9b4e,#2d5a30);color:#fff;border-color:rgba(74,155,78,0.8);box-shadow:0 8px 20px rgba(0,0,0,.4), 0 0 15px rgba(74,155,78,0.4)}
        .coin.completed .num{color:#fff;text-shadow:0 1px 2px rgba(0,0,0,0.4)}
        .coin.completed:hover{box-shadow:0 12px 30px rgba(0,0,0,.6), 0 0 25px rgba(74,155,78,0.6);transform:scale(1.05)}
        
        /* Nivel final central (rojo carmesí especial) */
        .final-level{background:linear-gradient(135deg,#8b1538,#5d0e24) !important;border-color:rgba(139,21,56,0.8) !important;box-shadow:0 15px 40px rgba(0,0,0,.7), 0 0 35px rgba(139,21,56,0.7) !important;width:85px !important;height:85px !important;font-size:20px !important;border-width:4px !important}
        .final-level .num{color:#fff !important;text-shadow:0 2px 4px rgba(0,0,0,0.7) !important}
        .final-level:hover{transform:scale(1.1) !important;box-shadow:0 20px 50px rgba(0,0,0,.8), 0 0 45px rgba(139,21,56,0.8) !important}

        .walker{position:absolute;width:72px;height:72px;transform:translate(-50%,-50%);pointer-events:none;z-index:6}
        .char-left,.char-right{position:absolute;width:80px;height:80px;z-index:4;border-radius:50%;background:rgba(26,15,46,0.6);backdrop-filter:blur(10px);border:2px solid rgba(139,21,56,0.3);box-shadow:0 10px 25px rgba(0,0,0,0.4)}
        .char-left{left:-100px;top:200px}
        .char-right{right:-100px;top:700px}
        
        /* Elementos decorativos minimalistas */
        .stage::before{content:'';position:absolute;top:50px;left:20%;width:3px;height:150px;background:linear-gradient(to bottom, transparent, rgba(139,21,56,0.3), transparent);border-radius:2px}
        .stage::after{content:'';position:absolute;bottom:50px;right:20%;width:3px;height:150px;background:linear-gradient(to bottom, transparent, rgba(139,21,56,0.3), transparent);border-radius:2px}
        
        @media(max-width:1100px){ 
            .map-wrap{max-width:900px;padding:40px 30px;height:900px}
            .coin{width:55px;height:55px;font-size:14px}
            .final-level{width:75px !important;height:75px !important;font-size:18px !important}
            .branch-line{width:150px}
        }
        
        @media(max-width:900px){ 
            .map-wrap{max-width:700px;padding:30px 20px;height:700px;transform:scale(0.8)}
            .coin{width:50px;height:50px;font-size:13px}
            .final-level{width:65px !important;height:65px !important;font-size:16px !important}
            .branch-line{width:120px}
        }
        
        @media(max-width:700px){ 
            .map-wrap{max-width:500px;padding:20px 15px;height:500px;transform:scale(0.6)}
            .coin{width:45px;height:45px;font-size:12px}
            .final-level{width:60px !important;height:60px !important;font-size:15px !important}
            .branch-line{width:100px;height:2px}
        }
    </style>
</head>
<body>
    <div class="header">
        <a class="back-btn" href="/proyectolect/cuentos/dracula.php" title="Ir al Menú Principal">
            Atras
        </a>
        <div class="title">Capítulos de Drácula</div>
    </div>

    <div class="stage">
        <div class="map-wrap" id="map-wrap">
            <!-- Nivel 27 Central (Final) -->
            <a href="capdrac/27.php" class="coin final-level playable" style="left:500px;top:530px"><span class="num">27</span></a>
            
            <!-- UNA SOLA LÍNEA SVG QUE CONECTA TODOS LOS NIVELES EN ORDEN -->
            <svg style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;pointer-events:none;">
                <path d="M 602 482 L 712 402 L 822 402 L 922 502 L 922 612 L 822 612 L 712 712 L 612 712 L 452 622 L 352 622 L 252 552 L 252 452 L 152 382 L 152 282 L 262 282 L 362 212 L 472 212 L 582 212 L 682 142 L 792 142 L 792 212 L 682 312 L 582 352 L 482 352 L 382 452 L 452 512 L 532 562" 
                      stroke="rgba(212,175,55,1)" 
                      stroke-width="8" 
                      fill="none" 
                      stroke-linecap="round" 
                      stroke-linejoin="round"
                      style="filter: drop-shadow(0 0 12px rgba(212,175,55,0.6));" />
            </svg>
            
            <!-- ZIGZAG PRINCIPAL: Solo niveles, sin líneas por ahora -->
            <a href="..capdrac/1.php" class="coin playable" style="left:570px;top:450px"><span class="num">1</span></a>
            
            <a href="capdrac/2.php" class="coin blocked" style="left:680px;top:370px"><span class="num">2</span></a>
            
            <a href="capdrac/3.php" class="coin blocked" style="left:790px;top:370px"><span class="num">3</span></a>
            
            <a href="capdrac/4.php" class="coin blocked" style="left:890px;top:470px"><span class="num">4</span></a>
            
            <a href="capdrac/5.php" class="coin blocked" style="left:890px;top:580px"><span class="num">5</span></a>
            
            <a href="capdrac/6.php" class="coin blocked" style="left:790px;top:580px"><span class="num">6</span></a>
            
            <a href="capdrac/7.php" class="coin blocked" style="left:680px;top:680px"><span class="num">7</span></a>
            
            <a href="capdrac/8.php" class="coin blocked" style="left:580px;top:680px"><span class="num">8</span></a>
            
            <a href="capdrac/9.php" class="coin blocked" style="left:420px;top:590px"><span class="num">9</span></a>
            
            <a href="capdrac/10.php" class="coin blocked" style="left:320px;top:590px"><span class="num">10</span></a>
            
            <a href="capdrac/11.php" class="coin blocked" style="left:220px;top:520px"><span class="num">11</span></a>
            
            <a href="capdrac/12.php" class="coin blocked" style="left:220px;top:420px"><span class="num">12</span></a>
            
            <a href="capdrac/13.php" class="coin blocked" style="left:120px;top:350px"><span class="num">13</span></a>
            
            <a href="capdrac/14.php" class="coin blocked" style="left:120px;top:250px"><span class="num">14</span></a>
            
            <a href="capdrac/15.php" class="coin blocked" style="left:230px;top:250px"><span class="num">15</span></a>
            
            <a href="capdrac/16.php" class="coin blocked" style="left:330px;top:180px"><span class="num">16</span></a>
            
            <a href="capdrac/17.php" class="coin blocked" style="left:440px;top:180px"><span class="num">17</span></a>
            
            <a href="capdrac/18.php" class="coin blocked" style="left:550px;top:180px"><span class="num">18</span></a>
            
            <a href="capdrac/19.php" class="coin blocked" style="left:650px;top:110px"><span class="num">19</span></a>
            
            <a href="capdrac/20.php" class="coin blocked" style="left:760px;top:110px"><span class="num">20</span></a>
            
            <a href="capdrac/21.php" class="coin blocked" style="left:760px;top:180px"><span class="num">21</span></a>
            
            <a href="capdrac/22.php" class="coin blocked" style="left:650px;top:280px"><span class="num">22</span></a>
            
            <a href="capdrac/23.php" class="coin blocked" style="left:550px;top:320px"><span class="num">23</span></a>
            
            <a href="capdrac/24.php" class="coin blocked" style="left:450px;top:320px"><span class="num">24</span></a>
            
            <a href="capdrac/25.php" class="coin blocked" style="left:350px;top:420px"><span class="num">25</span></a>
            
            <a href="capdrac/26.php" class="coin blocked" style="left:420px;top:480px"><span class="num">26</span></a>
        </div>
    </div>

    <script>
    // Sistema de progresión de niveles estilo árbol
    document.addEventListener('DOMContentLoaded', function() {
        const coins = document.querySelectorAll('.coin');
        
        // Simular progreso del usuario (primer nivel disponible)
        updateLevelStates();
        
        // Animación al hacer clic en un nivel
        coins.forEach(coin => {
            coin.addEventListener('click', function(e) {
                if (this.classList.contains('blocked')) {
                    e.preventDefault();
                    // Efecto de "bloqueado"
                    this.style.transition = 'all 0.2s ease';
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 200);
                    return;
                }
                
                // Animación de éxito para niveles jugables/completados
                this.style.transition = 'transform 0.2s ease';
                this.style.transform = 'scale(1.15)';
                
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            });
            
            // Efectos hover mejorados
            coin.addEventListener('mouseenter', function() {
                if (!this.classList.contains('blocked')) {
                    this.style.transition = 'all 0.18s ease';
                }
            });
        });
        
        function updateLevelStates() {
            // Por ahora solo el primer nivel está disponible
            // Esta lógica se conectará con el progreso real del usuario
            const level1 = document.querySelector('a[href="capdrac/1.php"]');
            if (level1) {
                level1.classList.remove('blocked');
                level1.classList.add('playable');
            }
            
            // El nivel 27 siempre visible pero podría estar bloqueado
            const level27 = document.querySelector('a[href="capdrac/27.php"]');
            if (level27) {
                level27.classList.remove('blocked');
                level27.classList.add('playable');
            }
        }
    });
    </script>

</body>
</html>