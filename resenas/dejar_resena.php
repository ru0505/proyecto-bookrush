<?php
session_start();
include("../conexion.php"); // Ajusta la ruta si es necesario

// 1. Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    die("Error: Debes iniciar sesión.");
}

$id_usuario = $_SESSION['id_usuario'];
$id_libro = isset($_GET['id_libro']) ? (int)$_GET['id_libro'] : 0;

if ($id_libro === 0) {
    die("Error: Libro no especificado.");
}

// 2. Procesar el formulario (POST vía AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    $calificacion = isset($input['rating']) ? (int)$input['rating'] : 0;
    $comentario = isset($input['comment']) ? trim($input['comment']) : '';

    if ($calificacion < 1 || $calificacion > 5) {
        echo json_encode(['status' => 'error', 'message' => 'La calificación es obligatoria.']);
        exit;
    }

    // Insertar reseña
    // Asumo que tu tabla se llama 'resenas' y las columnas son las que mencionaste
    $stmt = $conn->prepare("
        INSERT INTO resenas (id_usuario, id_libro, calificacion, comentario, fecha) 
        VALUES (?, ?, ?, ?, NOW())
    ");
    
    if ($stmt) {
        $stmt->bind_param("iiis", $id_usuario, $id_libro, $calificacion, $comentario);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al guardar en BD: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error en consulta: ' . $conn->error]);
    }
    exit;
}

// 3. Verificar si YA existe reseña (Evitar duplicados)
$stmt_check = $conn->prepare("SELECT id_resena FROM resenas WHERE id_usuario = ? AND id_libro = ?");
$stmt_check->bind_param("ii", $id_usuario, $id_libro);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    // Si ya existe reseña, redirigir al mapa de capítulos o donde prefieras
    header("Location: ../mapa_capitulos/mapa_capitulos.php?id_libro=" . $id_libro);
    exit;
}

// 4. Obtener nombre del libro (Opcional, para mejor UX)
$titulo_libro = "este libro";
$stmt_libro = $conn->prepare("SELECT titulo FROM libros WHERE id_libro = ?"); // Asumiendo tabla 'libros' y columna 'titulo'
if ($stmt_libro) {
    $stmt_libro->bind_param("i", $id_libro);
    $stmt_libro->execute();
    $res_libro = $stmt_libro->get_result()->fetch_assoc();
    if ($res_libro) {
        $titulo_libro = $res_libro['titulo'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dejar Reseña</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .wrapper-principal {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px; /* Espacio entre imagen y caja blanca */
            max-width: 1000px;
            width: 100%;
            padding: 20px;
            
        }

        .image-side {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .lectirin-img {
            width: 800px; /* Tamaño de Lectirín */
            max-width: 100%;
            /* Animación de salto suave */
            animation: salto 3s infinite ease-in-out;
            filter: drop-shadow(0 15px 15px rgba(0,0,0,0.2));
        }

        @keyframes salto {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* Ajuste para móviles */
        @media (max-width: 850px) {
            .wrapper-principal {
                flex-direction: column; /* Uno abajo del otro */
            }
            .lectirin-img {
                width: 150px;
                margin-bottom: 20px;
            }
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }
        h2 { color: #333; font-size: 2em; margin-bottom: 10px; text-align: center; }
        .subtitle { color: #666; text-align: center; margin-bottom: 30px; }
        .rating-section { text-align: center; margin-bottom: 30px; }
        .rating-section label { display: block; color: #333; font-weight: 600; margin-bottom: 15px; font-size: 1.1em; }
        .required { color: #e74c3c; }
        .stars { display: flex; justify-content: center; gap: 10px; }
        .star { font-size: 45px; color: #ddd; cursor: pointer; transition: all 0.2s ease; }
        .star:hover, .star.active { color: #ffd700; transform: scale(1.2); }
        .rating-text { margin-top: 10px; color: #666; font-size: 0.9em; }
        .comment-section { margin-bottom: 25px; }
        .comment-section label { display: block; color: #333; font-weight: 600; margin-bottom: 10px; }
        .optional { color: #999; font-weight: normal; font-size: 0.9em; }
        textarea { width: 100%; padding: 15px; border: 2px solid #e0e0e0; border-radius: 10px; font-family: inherit; font-size: 1em; resize: vertical; transition: border-color 0.3s; }
        textarea:focus { outline: none; border-color: #667eea; }
        .char-count { text-align: right; color: #999; font-size: 0.85em; margin-top: 5px; }
        .submit-btn { width: 100%; padding: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; font-size: 1.1em; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4); }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6); }
        .submit-btn:active { transform: translateY(0); }
        .success-message { background: #d4edda; border: 2px solid #28a745; color: #155724; padding: 20px; border-radius: 10px; text-align: center; display: none; }
        .success-message.show { display: block; }
        .success-icon { font-size: 3em; margin-bottom: 10px; }
        .error-message { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px; display: none; }
        .error-message.show { display: block; }
        /* Botón extra para cancelar */
        .cancel-btn { display: block; margin-top: 15px; text-align: center; color: #999; text-decoration: none; font-size: 0.9em; cursor: pointer;}
        .cancel-btn:hover { color: #666; }
    </style>
</head>
<body>

    <div class="wrapper-principal">

        <div class="image-side">
            <img src="../imagenes/lecturin/lecturin_saltando.png" alt="Lectirín" class="lectirin-img">
        </div>

        <div class="container">
            <h2>Deja tu reseña</h2>
            
            <p class="subtitle">¿Qué te pareció <strong><?= htmlspecialchars($titulo_libro) ?></strong>?</p>

            <div id="successMessage" class="success-message">
                <div class="success-icon">✓</div>
                <p><strong>¡Reseña publicada con éxito!</strong></p>
                <p style="font-size: 0.9em; margin-top: 10px;">Redirigiendo al mapa...</p>
            </div>

            <div id="errorMessage" class="error-message"></div>

            <div id="reviewForm">
                <div class="rating-section">
                    <label>Calificación <span class="required">*</span></label>
                    <div class="stars" id="stars">
                        <span class="star" data-rating="1">★</span>
                        <span class="star" data-rating="2">★</span>
                        <span class="star" data-rating="3">★</span>
                        <span class="star" data-rating="4">★</span>
                        <span class="star" data-rating="5">★</span>
                    </div>
                    <div class="rating-text" id="ratingText"></div>
                </div>

                <div class="comment-section">
                    <label>Comentario <span class="optional">(opcional)</span></label>
                    <textarea 
                        id="comment" 
                        placeholder="Escribe tu opinión sobre el libro..."
                        rows="6"
                        maxlength="500"
                    ></textarea>
                    <div class="char-count">
                        <span id="charCount">0</span>/500 caracteres
                    </div>
                </div>

                <button class="submit-btn" onclick="submitReview()">Publicar Reseña</button>
                <a href="../mapa_capitulos/mapa_capitulos.php?id_libro=<?= $id_libro ?>" class="cancel-btn">Saltar por ahora</a>
            </div>
        </div>

    <script>
        let selectedRating = 0;
        const stars = document.querySelectorAll('.star');
        const ratingText = document.getElementById('ratingText');
        const comment = document.getElementById('comment');
        const charCount = document.getElementById('charCount');
        const idLibro = <?= $id_libro ?>; // Pasamos el ID de PHP a JS

        // Manejo de estrellas (TU CÓDIGO)
        stars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = parseInt(this.dataset.rating);
                updateStars(selectedRating);
                updateRatingText(selectedRating);
            });
            star.addEventListener('mouseenter', function() {
                updateStars(parseInt(this.dataset.rating));
            });
            star.addEventListener('mouseleave', function() {
                updateStars(selectedRating);
            });
        });

        function updateStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }

        function updateRatingText(rating) {
            if (rating > 0) {
                ratingText.textContent = `${rating} ${rating === 1 ? 'estrella' : 'estrellas'}`;
            } else {
                ratingText.textContent = '';
            }
        }

        // Contador de caracteres (TU CÓDIGO)
        comment.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Enviar reseña (MODIFICADO PARA BACKEND)
        function submitReview() {
            const errorMessage = document.getElementById('errorMessage');
            
            if (selectedRating === 0) {
                errorMessage.textContent = 'Por favor selecciona una calificación en estrellas';
                errorMessage.classList.add('show');
                return;
            }

            errorMessage.classList.remove('show');
            const btn = document.querySelector('.submit-btn');
            btn.disabled = true;
            btn.textContent = "Enviando...";

            // Enviar datos usando FETCH API
            fetch('', { // POST al mismo archivo
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    rating: selectedRating,
                    comment: comment.value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Mostrar éxito
                    document.getElementById('reviewForm').style.display = 'none';
                    document.getElementById('successMessage').classList.add('show');

                    // Redirigir después de 2 segundos
                    setTimeout(() => {
                        window.location.href = "../mapa_capitulos/mapa_capitulos.php?id_libro=" + idLibro;
                    }, 2000);
                } else {
                    errorMessage.textContent = data.message || "Error desconocido";
                    errorMessage.classList.add('show');
                    btn.disabled = false;
                    btn.textContent = "Publicar Reseña";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorMessage.textContent = 'Error de conexión. Inténtalo de nuevo.';
                errorMessage.classList.add('show');
                btn.disabled = false;
                btn.textContent = "Publicar Reseña";
            });
        }
    </script>
</body>
</html>