<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['dni'])) {
    header("Location: ../login.php");
    exit;
}

$dni = $_SESSION['dni'];

// Puntaje máximo por capítulo - La Ciudad y los Perros (8 niveles urbanos)
$maximos = [
    1 => 280,
    2 => 160,
    3 => 20,
    4 => 200,
    5 => 20,
    6 => 180,
    7 => 150,
    8 => 240
];

$puntajes = [];
for ($i = 1; $i <= 8; $i++) {
    $sql = "SELECT SUM(PUNTAJE) AS total FROM puntajes WHERE DNI=? AND CAPITULO=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $dni, $i);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $puntajes[$i] = $result['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>La Ciudad y los Perros - Capítulos</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: 
                linear-gradient(135deg, rgba(30,60,114,0.85) 0%, rgba(42,82,152,0.9) 25%, rgba(52,73,94,0.9) 50%, rgba(42,82,152,0.9) 75%, rgba(30,60,114,0.85) 100%),
                url('../imagenes/cdpportada.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            animation: urbanGradient 20s ease infinite;
            color: #ecf0f1;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        @keyframes urbanGradient {
            0%, 100% { background-position: 0% 50%; }
            25% { background-position: 100% 50%; }
            50% { background-position: 0% 100%; }
            75% { background-position: 100% 0%; }
        }
        /* Elementos atmosféricos urbanos sutiles */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 80%, rgba(42,82,152,0.1) 0%, transparent 50%), 
                        radial-gradient(circle at 80% 20%, rgba(42,82,152,0.08) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }

        .header {
            background: rgba(30,60,114,0.15);
            backdrop-filter: blur(15px);
            border-bottom: 1px solid rgba(42,82,152,0.3);
            padding: 20px 40px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: rgba(42,82,152,0.2);
            border: 2px solid rgba(42,82,152,0.4);
            border-radius: 12px;
            color: #e8f0ff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            position: absolute;
            left: 20px;
            text-transform: uppercase;
        }

        .back-btn:hover {
            background: rgba(42,82,152,0.4);
            border-color: rgba(42,82,152,0.6);
            transform: translateX(-5px);
            color: #ffffff;
            text-shadow: 0 0 10px rgba(42,82,152,0.5);
        }
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #e8f0ff, #cbd5e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin: 0;
            text-shadow: 0 4px 8px rgba(0,0,0,0.3);
        }

        .main-container {
            padding: 60px 20px;
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .levels-container {
            position: relative;
            margin: 80px auto;
            width: 100%;
            max-width: 800px;
            height: 450px;
            border-radius: 20px;
            background: rgba(30,60,114,0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(42,82,152,0.25);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            padding: 50px;
        }

        /* Decoración de borde como menudrac pero con temática urbana */
        .levels-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, transparent 30%, rgba(42,82,152,0.3) 50%, transparent 70%);
            border-radius: 22px;
            z-index: -1;
        }
        /* Camino urbano conectando los niveles */
        .urban-path {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .urban-path svg {
            width: 100%;
            height: 100%;
        }

        .path-line {
            stroke: #ffd700;
            stroke-width: 6;
            fill: none;
            /* Línea continua sólida como en menudrac */
        }

        @keyframes pathFlow {
            0% { stroke-dashoffset: 0; }
            100% { stroke-dashoffset: 30; }
        }

        /* Niveles estilo monedas como menudrac */
        .level {
            position: absolute;
            width: 65px;
            height: 65px;
            background: linear-gradient(135deg, #42a5f5, #1976d2);
            border: 3px solid rgba(66,165,245,0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.18s ease;
            text-decoration: none;
            color: #ffffff;
            font-weight: 800;
            font-size: 16px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.4);
            z-index: 10;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4), 0 0 15px rgba(66,165,245,0.4);
        }

        .level:hover {
            transform: scale(1.08);
            box-shadow: 0 12px 30px rgba(0,0,0,0.6), 0 0 25px rgba(66,165,245,0.6);
        }
        /* Estados de los niveles siguiendo el patrón de menudrac */
        .level.locked {
            background: linear-gradient(135deg, #666, #444);
            border-color: rgba(102,102,102,0.6);
            color: #aaa;
            pointer-events: none;
            cursor: not-allowed;
        }

        .level.completed {
            background: linear-gradient(135deg, #4a9b4e, #2d5a30);
            border-color: rgba(74,155,78,0.8);
            color: #fff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.4), 0 0 15px rgba(74,155,78,0.4);
        }

        .level.completed:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(0,0,0,0.6), 0 0 25px rgba(74,155,78,0.6);
        }

        /* ELIMINANDO nth-child - usaremos posiciones inline como menudrac */

        /* Responsive design */
        @media (max-width: 768px) {
            .levels-container {
                height: 600px;
                margin: 40px auto;
            }

            .level {
                width: 80px;
                height: 80px;
            }

            .level-number {
                font-size: 1.2rem;
            }

            .level-title {
                font-size: 0.6rem;
            }

            .page-title {
                font-size: 1.8rem;
            }
        }

        /* Barra de progreso moderna */
        .progress-container {
            margin-top: 60px;
            padding: 30px;
            background: rgba(30,60,114,0.1);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            border: 1px solid rgba(42,82,152,0.3);
        }

        .progress-title {
            text-align: center;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #e8f0ff;
        }

        .progress-bar {
            height: 12px;
            background: rgba(42,82,152,0.2);
            border-radius: 6px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, rgba(42,82,152,0.8), rgba(30,60,114,1));
            border-radius: 6px;
            transition: width 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.2) 50%, transparent 100%);
            animation: shine 2s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .progress-text {
            text-align: center;
            margin-top: 10px;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
        }
</style>
</head>
<body>

    <div class="header">
        <a href="/proyecto-bookrush/index.php" class="back-btn">
            ← Menu Principal
        </a>
        <h1 class="page-title">La Ciudad y los Perros</h1>
    </div>

    <div class="main-container">
        <div class="levels-container">
            
            <!-- UNA SOLA LÍNEA SVG QUE CONECTA LOS 8 NIVELES COMO MENUDRAC -->
            <svg style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:1;pointer-events:none;">
                <path d="M 132 212 L 332 132 L 512 132 L 632 212 L 512 312 L 332 312 L 432 392 L 232 392" 
                      stroke="#ffd700" 
                      stroke-width="6" 
                      fill="none" 
                      stroke-linecap="round" 
                      stroke-linejoin="round"
                      style="filter: drop-shadow(0 0 12px rgba(255,215,0,0.6));" />
            </svg>

            <?php
            $levelTitles = [
                1 => "El Colegio",
                2 => "Los Cadetes", 
                3 => "La Disciplina",
                4 => "El Jaguar",
                5 => "Los Secretos",
                6 => "La Verdad",
                7 => "El Círculo",
                8 => "La Ciudad"
            ];

            // POSICIONES INLINE COMO MENUDRAC - patrón de 8 niveles
            $positions = [
                1 => "left:100px;top:180px",   // Nivel 1 - Izquierda
                2 => "left:300px;top:100px",   // Nivel 2 - Arriba izq
                3 => "left:480px;top:100px",   // Nivel 3 - Arriba der  
                4 => "left:600px;top:180px",   // Nivel 4 - Derecha
                5 => "left:480px;top:280px",   // Nivel 5 - Abajo der
                6 => "left:300px;top:280px",   // Nivel 6 - Abajo izq
                7 => "left:400px;top:360px",   // Nivel 7 - Abajo der final
                8 => "left:200px;top:360px"    // Nivel 8 - Abajo izq final
            ];

            for ($i = 1; $i <= 8; $i++) {
                $porcentaje = 0;
                if ($maximos[$i] > 0) {
                    $porcentaje = min(100, round(($puntajes[$i] / $maximos[$i]) * 100));
                }
                
                $bloqueado = ($i > 1 && $puntajes[$i-1] < $maximos[$i-1]);
                $completado = ($porcentaje >= 100);
                
                $clases = "level";
                if ($bloqueado) $clases .= " locked";
                if ($completado) $clases .= " completed";
                
                $href = $bloqueado ? "#" : "../pregunta/capcdp/{$i}_cdp.php";
                
                echo "<a href='{$href}' class='{$clases}' style='{$positions[$i]}'>
                        <span class='num'>{$i}</span>
                      </a>";
            }
            ?>
        </div>

        <!-- Barra de progreso general -->
        <div class="progress-container">
            <h3 class="progress-title">Progreso General</h3>
            <?php
            $totalObtenido = array_sum($puntajes);
            $totalMaximo = array_sum($maximos);
            $progresoTotal = $totalMaximo > 0 ? min(100, round(($totalObtenido / $totalMaximo) * 100)) : 0;
            ?>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $progresoTotal ?>%;"></div>
            </div>
            <div class="progress-text">
                <?= $progresoTotal ?>% completado (<?= $totalObtenido ?>/<?= $totalMaximo ?> puntos)
            </div>
        </div>
    </div>

</body>
</html>
