<?php
// Limpiar cualquier salida accidental
ob_start();

session_start();
include("../conexion.php");

// 🔹 Parámetros dinámicos
$id_libro = isset($_GET['id_libro']) ? (int)$_GET['id_libro'] : 1;
$id_capitulo = isset($_GET['id_capitulo']) ? (int)$_GET['id_capitulo'] : 1;
$numero_pregunta = isset($_GET['pregunta']) ? (int)$_GET['pregunta'] : 1;
$id_usuario = $_SESSION['id_usuario'] ?? 0;

// Limpiar buffer y enviar header JSON para POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');
}

// 🔹 BLOQUEO TEMPORAL (Funcionalidad de tu amigo: 2 minutos)
function verificarBloqueo($conn, $id_usuario, $id_libro, $id_capitulo) {
    try {
        $stmt_limpiar = $conn->prepare("UPDATE bloqueos_temporales SET activo = 0 WHERE id_usuario = ? AND id_libro = ? AND CAPITULO = ? AND fecha_desbloqueo <= NOW() AND activo = 1");
        $stmt_limpiar->bind_param("iii", $id_usuario, $id_libro, $id_capitulo);
        $stmt_limpiar->execute();
        
        $stmt_verificar_desbloqueado = $conn->prepare("SELECT COUNT(*) as bloques_inactivos FROM bloqueos_temporales WHERE id_usuario = ? AND id_libro = ? AND CAPITULO = ? AND activo = 0");
        $stmt_verificar_desbloqueado->bind_param("iii", $id_usuario, $id_libro, $id_capitulo);
        $stmt_verificar_desbloqueado->execute();
        $res_desbloqueado = $stmt_verificar_desbloqueado->get_result()->fetch_assoc();
        
        if ($res_desbloqueado['bloques_inactivos'] > 0) {
            $stmt_limpiar_intentos = $conn->prepare("DELETE FROM intentos_fallidos WHERE id_usuario = ? AND id_libro = ? AND CAPITULO = ?");
            $stmt_limpiar_intentos->bind_param("iii", $id_usuario, $id_libro, $id_capitulo);
            $stmt_limpiar_intentos->execute();
        }
        
        $stmt = $conn->prepare("SELECT fecha_bloqueo, fecha_desbloqueo, TIMESTAMPDIFF(SECOND, NOW(), fecha_desbloqueo) AS segundos_restantes FROM bloqueos_temporales WHERE id_usuario = ? AND id_libro = ? AND CAPITULO = ? AND activo = 1 AND fecha_desbloqueo > NOW() ORDER BY fecha_desbloqueo DESC LIMIT 1");
        $stmt->bind_param("iii", $id_usuario, $id_libro, $id_capitulo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return ($resultado->num_rows > 0) ? $resultado->fetch_assoc() : null;
    } catch (Exception $e) { return null; }
}

function registrarBloqueo($conn, $id_usuario, $id_libro, $id_capitulo) {
    try {
        $stmt = $conn->prepare("INSERT INTO bloqueos_temporales (id_usuario, id_libro, CAPITULO, fecha_bloqueo, fecha_desbloqueo, activo) VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 2 MINUTE), 1) ON DUPLICATE KEY UPDATE fecha_bloqueo = NOW(), fecha_desbloqueo = DATE_ADD(NOW(), INTERVAL 2 MINUTE), activo = 1");
        $stmt->bind_param("iii", $id_usuario, $id_libro, $id_capitulo);
        return $stmt->execute();
    } catch (Exception $e) { return false; }
}

$bloqueo_info = null;
$bloqueado = false;
try {
    $bloqueo_info = verificarBloqueo($conn, $id_usuario, $id_libro, $id_capitulo);
    if ($bloqueo_info) $bloqueado = true;
} catch (Exception $e) { $bloqueo_info = null; }

// ========== PREGUNTAS ALEATORIAS ==========
if ($numero_pregunta === 1 && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt_rand = $conn->prepare("SELECT id_pregunta FROM preguntas WHERE id_libro = ? AND id_capitulo = ? ORDER BY RAND() LIMIT 5");
    $stmt_rand->bind_param("ii", $id_libro, $id_capitulo);
    $stmt_rand->execute();
    $res_rand = $stmt_rand->get_result();
    
    $lista_preguntas = [];
    while ($row = $res_rand->fetch_assoc()) {
        $lista_preguntas[] = $row['id_pregunta'];
    }
    $_SESSION['quiz_preguntas_' . $id_libro . '_' . $id_capitulo] = $lista_preguntas;
    unset($_SESSION['respuestas'][$id_capitulo]);
}

$lista_actual = $_SESSION['quiz_preguntas_' . $id_libro . '_' . $id_capitulo] ?? [];

if (empty($lista_actual)) {
    $stmt_rand = $conn->prepare("SELECT id_pregunta FROM preguntas WHERE id_libro = ? AND id_capitulo = ? ORDER BY RAND() LIMIT 5");
    $stmt_rand->bind_param("ii", $id_libro, $id_capitulo);
    $stmt_rand->execute();
    $res_rand = $stmt_rand->get_result();
    $lista_actual = [];
    while ($row = $res_rand->fetch_assoc()) {
        $lista_actual[] = $row['id_pregunta'];
    }
    $_SESSION['quiz_preguntas_' . $id_libro . '_' . $id_capitulo] = $lista_actual;
}

// 🔹 Obtener pregunta actual
if (!empty($lista_actual) && isset($lista_actual[$numero_pregunta - 1])) {
    $id_pregunta_real = $lista_actual[$numero_pregunta - 1];
    $stmt = $conn->prepare("SELECT * FROM preguntas WHERE id_pregunta = ?");
    $stmt->bind_param("i", $id_pregunta_real);
} else {
    // Fallback de seguridad
    $offset = $numero_pregunta - 1;
    $stmt = $conn->prepare("SELECT * FROM preguntas WHERE id_libro = ? AND id_capitulo = ? LIMIT 1 OFFSET ?");
    $stmt->bind_param("iii", $id_libro, $id_capitulo, $offset);
}

if (!$stmt || !$stmt->execute()) {
    echo json_encode(["error" => "Error cargando pregunta"]); exit;
}

$pregunta = $stmt->get_result()->fetch_assoc();
if (!$pregunta) die("No hay preguntas para este capítulo.");

$opciones = [
    'A' => $pregunta['opcion_a'],
    'B' => $pregunta['opcion_b'],
    'C' => $pregunta['opcion_c'],
    'D' => $pregunta['opcion_d']
];

// ✅ CORRECCIÓN CRÍTICA: La última pregunta es el tamaño de la lista (5), NO el MAX de la BD
$ultima_pregunta = count($lista_actual); 

// 🔹 Procesar respuesta
// 🔹 Procesar respuesta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $respuesta = $_POST['respuesta'] ?? "";
        $correcta = ($respuesta === $pregunta['respuesta_correcta']);
        $puntaje = $correcta ? ($pregunta['puntaje'] ?? 20) : 0;

        // =========================================================
        // 1. GUARDADO EN BASE DE DATOS (Solo si hay usuario)
        // =========================================================
        if ($id_usuario > 0) {
            // Insertar en tabla antigua
            $stmt3 = $conn->prepare("INSERT INTO puntajes (id_usuario, id_libro, CAPITULO, id_pregunta, PUNTAJE) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE PUNTAJE = VALUES(PUNTAJE)");
            $stmt3->bind_param("iiiii", $id_usuario, $id_libro, $id_capitulo, $pregunta['id_pregunta'], $puntaje);
            $stmt3->execute();

            // Insertar en tabla nueva (progreso)
            $stmt_progreso = $conn->prepare("INSERT INTO progreso_usuario (id_usuario, id_pregunta, puntaje_obtenido, fecha_respuesta) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE puntaje_obtenido = VALUES(puntaje_obtenido), fecha_respuesta = NOW()");
            if ($stmt_progreso) {
                $stmt_progreso->bind_param("iii", $id_usuario, $pregunta['id_pregunta'], $puntaje);
                $stmt_progreso->execute();
            }
        }

        // =========================================================
        // 2. LÓGICA DE JUEGO (Para TODOS, invitados y usuarios)
        // =========================================================
        $_SESSION['respuestas'][$id_capitulo][$numero_pregunta] = ['correcta' => $correcta];

        $aprobo = false;
        
        // Verificamos si es la última pregunta para calcular si aprobó
        if ($numero_pregunta == $ultima_pregunta) {
            // Contar correctas de la sesión actual
            $correctas = 0;
            if (isset($_SESSION['respuestas'][$id_capitulo])) {
                foreach ($_SESSION['respuestas'][$id_capitulo] as $r) {
                    if ($r['correcta']) $correctas++;
                }
            }
            
            // Regla: Aprobar con 4 o más
            $aprobo = ($correctas >= 4);
            
            // -----------------------------------------------------
            // A. SISTEMA DE RACHA (Solo usuarios registrados)
            // -----------------------------------------------------
            if ($id_usuario > 0 && $correctas >= 3) {
                $stmt_racha = $conn->prepare("SELECT racha, ultimo_acceso FROM usuarios WHERE ID = ?");
                $stmt_racha->bind_param("i", $id_usuario);
                $stmt_racha->execute();
                $datos_user = $stmt_racha->get_result()->fetch_assoc();

                if ($datos_user) {
                    $racha_actual = $datos_user['racha'] ?? 0;
                    $ultimo_juego = $datos_user['ultimo_acceso'] ? date('Y-m-d', strtotime($datos_user['ultimo_acceso'])) : null;
                    $hoy = date('Y-m-d');
                    $ayer = date('Y-m-d', strtotime('-1 day'));

                    if ($ultimo_juego !== $hoy) {
                        $nueva_racha = ($ultimo_juego === $ayer) ? $racha_actual + 1 : 1;
                        $stmt_upd = $conn->prepare("UPDATE usuarios SET racha = ?, ultimo_acceso = NOW() WHERE ID = ?");
                        $stmt_upd->bind_param("ii", $nueva_racha, $id_usuario);
                        $stmt_upd->execute();
                        $_SESSION['racha'] = $nueva_racha;
                    }
                }
            }
            
            // Mensajes aleatorios (Se mantiene)
            $mensajes_exito = ["Felicidades!", "Lo lograste!", "Excelente trabajo!", "Increíble!", "Fantástico!"];
            $mensaje_aleatorio = $mensajes_exito[array_rand($mensajes_exito)];
            
            if ($aprobo) {
                // -------------------------------------------------
                // B. LIMPIEZA Y RESEÑAS (Solo usuarios registrados)
                // -------------------------------------------------
                if ($id_usuario > 0) {
                    // Borrar intentos fallidos previos
                    $stmt_limpiar = $conn->prepare("DELETE FROM intentos_fallidos WHERE id_usuario = ? AND id_libro = ? AND CAPITULO = ?");
                    $stmt_limpiar->bind_param("iii", $id_usuario, $id_libro, $id_capitulo);
                    $stmt_limpiar->execute();

                    // Verificar si es el fin del libro para pedir reseña
                    $stmt_max = $conn->prepare("SELECT MAX(id_capitulo) as max_cap FROM preguntas WHERE id_libro = ?");
                    $stmt_max->bind_param("i", $id_libro);
                    $stmt_max->execute();
                    $max_cap = $stmt_max->get_result()->fetch_assoc()['max_cap'] ?? 0;

                    if ($id_capitulo == $max_cap) {
                        $stmt_rev = $conn->prepare("SELECT id_resena FROM resenas WHERE id_usuario = ? AND id_libro = ?");
                        $stmt_rev->bind_param("ii", $id_usuario, $id_libro);
                        $stmt_rev->execute();
                        if ($stmt_rev->get_result()->num_rows == 0) {
                            echo json_encode(["status" => "redirigir_resena", "mensaje" => "¡Libro completado! Déjanos tu opinión.", "id_libro" => $id_libro]);
                            exit;
                        }
                    }
                }
            } else {
                // -------------------------------------------------
                // C. REGISTRO DE FALLOS Y BLOQUEO (Solo usuarios)
                // -------------------------------------------------
                // Invitados no se bloquean porque no tenemos ID para rastrearlos en la BD
                if ($id_usuario > 0) {
                    $stmt_reg = $conn->prepare("INSERT INTO intentos_fallidos (id_usuario, id_libro, CAPITULO, fecha_bloqueo) VALUES (?, ?, ?, NOW())");
                    $stmt_reg->bind_param("iii", $id_usuario, $id_libro, $id_capitulo);
                    $stmt_reg->execute();

                    $stmt_cnt = $conn->prepare("SELECT COUNT(*) as intentos FROM intentos_fallidos WHERE id_usuario = ? AND id_libro = ? AND CAPITULO = ?");
                    $stmt_cnt->bind_param("iii", $id_usuario, $id_libro, $id_capitulo);
                    $stmt_cnt->execute();
                    $intentos = $stmt_cnt->get_result()->fetch_assoc()['intentos'] ?? 0;

                    if ($intentos >= 3) {
                        registrarBloqueo($conn, $id_usuario, $id_libro, $id_capitulo);
                        echo json_encode(["status" => "capitulo_reprobado_bloqueado", "mensaje" => "3 intentos fallidos. Bloqueo 2 min.", "segundos_restantes" => 120]);
                        exit;
                    }
                }
            }
        }

        // Respuesta final JSON (Para todos)
        echo json_encode([
            "status" => "ok",
            "es_correcta" => $correcta,
            "siguiente" => ($numero_pregunta < $ultima_pregunta) ? $numero_pregunta + 1 : 0,
            "es_ultima" => ($numero_pregunta == $ultima_pregunta),
            "aprobo" => $aprobo,
            "mensaje_exito" => $aprobo ? $mensaje_aleatorio : ""
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "error" => $e->getMessage()]);
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Pregunta <?= $numero_pregunta ?> - Capítulo <?= $id_capitulo ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/trivia.css">
</head>
<body>

<?php if ($bloqueado): ?>
  <div id="modal-bloqueo" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); display: flex; justify-content: center; align-items: center; z-index: 9999;">
    <div style="background: white; padding: 40px; border-radius: 12px; text-align: center;">
      <h2 style="color: #e74c3c;">⏸️ Bloqueado Temporalmente</h2>
      <p>Has excedido los intentos permitidos.</p>
      <div style="font-size: 48px; font-weight: bold; color: #e74c3c; margin: 20px 0;">
        <span id="minutos">00</span>:<span id="segundos">00</span>
      </div>
      <button onclick="window.location.href='../mapa_capitulos/mapa_capitulos.php?id_libro=<?= $id_libro ?>'" style="background: #4a90e2; color: white; padding: 12px 24px; border: none; border-radius: 6px; cursor: pointer;">Volver al Mapa</button>
    </div>
  </div>
  <script>
    let segundos = <?= intval($bloqueo_info['segundos_restantes'] ?? 120) ?>;
    const timer = setInterval(() => {
        const m = Math.floor(segundos / 60).toString().padStart(2,'0');
        const s = (segundos % 60).toString().padStart(2,'0');
        document.getElementById('minutos').textContent = m;
        document.getElementById('segundos').textContent = s;
        if (segundos-- <= 0) { clearInterval(timer); location.reload(); }
    }, 1000);
  </script>
<?php endif; ?>

  <!-- Modal de Felicitaciones -->
  <div id="modal-felicitaciones" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); display: none; justify-content: center; align-items: center; z-index: 9999;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 50px; border-radius: 16px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.3); max-width: 500px;">
      <div style="font-size: 80px; margin-bottom: 20px;">🎉</div>
      <h2 style="color: white; font-size: 32px; margin-bottom: 15px; font-weight: 700;">Felicitaciones</h2>
      <p id="mensaje-felicitaciones" style="color: white; font-size: 18px; line-height: 1.6; margin-bottom: 30px;"></p>
      <button onclick="cerrarModalFelicitaciones()" style="background: white; color: #667eea; padding: 15px 40px; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; transition: transform 0.2s; box-shadow: 0 4px 15px rgba(0,0,0,0.2);" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">Continuar</button>
    </div>
  </div>

  <div class="top-bar">
    <a href="../index.php" style="display: flex; align-items: center; gap: 12px; text-decoration: none; color: inherit;">
      <img src="../imagenes/lecturin/lecturin_saltando.png" alt="Logo" style="height: 48px;">
      <h1>Book Rush</h1>
    </a>
  </div>

  <div class="container">
    <div class="progreso">Pregunta <?= $numero_pregunta ?> de <?= $ultima_pregunta ?></div>
    <div class="timer" id="timer">Tiempo: 25 segundos</div>
    
    <div class="pregunta-card">
      <div class="pregunta"><?= $numero_pregunta ?>. <?= htmlspecialchars($pregunta['enunciado']) ?></div>
    </div>
    
    <div class="opciones">
    <?php foreach ($opciones as $letra => $texto): ?>
      <button type="button" class="opcion" data-respuesta="<?= $letra ?>">
        <?= $letra ?>) <?= htmlspecialchars($texto) ?>
      </button>
    <?php endforeach; ?>
    </div>

    <div id="resultado" style="display: none; margin-top: 20px; font-weight: bold; text-align: center;"></div>
  </div>

  <script>
  const botones = document.querySelectorAll(".opcion");
  const resultado = document.getElementById("resultado");
  let respondido = false;
  let tiempo = 25;
  let usuario_bloqueado = <?= $bloqueado ? 'true' : 'false' ?>;

  const countdown = setInterval(() => {
      if(usuario_bloqueado) return;
      tiempo--;
      document.getElementById("timer").textContent = "Tiempo: " + tiempo + " segundos";
      if (tiempo <= 0 && !respondido) {
          respondido = true;
          clearInterval(countdown);
          enviarRespuesta("");
      }
  }, 1000);

  botones.forEach(boton => {
      boton.addEventListener("click", () => {
          if (respondido || usuario_bloqueado) return;
          respondido = true;
          clearInterval(countdown);
          enviarRespuesta(boton.dataset.respuesta);
      });
  });

  function enviarRespuesta(respuesta) {
      botones.forEach(b => b.disabled = true);
      
      fetch("", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: "respuesta=" + encodeURIComponent(respuesta)
      })
      .then(res => res.json())
      .then(data => {
          resultado.style.display = "block";
          
          if (data.status === "error") {
             resultado.textContent = "Error: " + data.error;
             resultado.style.color = "red"; return;
          }
          if (data.status.includes("bloqueado")) {
              location.reload(); return;
          }
          // ✅ RESTAURADO: Manejo de la redirección a reseña
          if (data.status === "redirigir_resena") {
              resultado.textContent = data.mensaje;
              resultado.style.color = "#2ecc71";
              setTimeout(() => window.location.href = "../resenas/dejar_resena.php?id_libro=" + data.id_libro, 1500);
              return;
          }

          if (data.es_correcta) {
              resultado.textContent = "¡Correcto!";
              resultado.style.color = "#2ecc71";
          } else {
              resultado.textContent = respuesta === "" ? "¡Tiempo agotado!" : "Incorrecto";
              resultado.style.color = "#e74c3c";
          }

          setTimeout(() => {
              if (data.siguiente > 0) {
                  window.location.href = "?id_libro=<?= $id_libro ?>&id_capitulo=<?= $id_capitulo ?>&pregunta=" + data.siguiente;
              } else {
                  if (data.aprobo && data.mensaje_exito) {
                      mostrarModalFelicitaciones(data.mensaje_exito);
                  } else {
                      window.location.href = "../total.php?id_libro=<?= $id_libro ?>&id_capitulo=<?= $id_capitulo ?>";
                  }
              }
          }, 1500);
      })
      .catch(err => {
          console.error(err);
          resultado.textContent = "Error de conexión";
          resultado.style.display = "block";
      });
  }

  function mostrarModalFelicitaciones(mensaje) {
      document.getElementById('mensaje-felicitaciones').textContent = mensaje;
      document.getElementById('modal-felicitaciones').style.display = 'flex';
  }

  function cerrarModalFelicitaciones() {
      document.getElementById('modal-felicitaciones').style.display = 'none';
      window.location.href = "../total.php?id_libro=<?= $id_libro ?>&id_capitulo=<?= $id_capitulo ?>";
  }
  </script>
</body>
</html>