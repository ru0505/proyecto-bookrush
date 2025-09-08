<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Capítulo 1 - El Ciclón</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #5ba3c7, #2c7aa0);
            margin: 0;
            padding: 20px;
            color: #ffffff;
            line-height: 1.6;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(244, 162, 97, 0.3);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid rgba(244, 162, 97, 0.4);
        }

        .back-btn:hover {
            background: rgba(244, 162, 97, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(244, 162, 97, 0.3);
        }

        h1 {
            color: #e9c46a;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .chapter-content {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            border-left: 5px solid #e9c46a;
        }

        .chapter-text {
            font-size: 1.1rem;
            margin-bottom: 20px;
            text-align: justify;
        }

        .question-section {
            background: rgba(244, 162, 97, 0.1);
            padding: 25px;
            border-radius: 15px;
            margin-top: 30px;
            border: 2px solid rgba(244, 162, 97, 0.3);
        }

        .question {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: #f3f8ff;
            font-weight: 600;
        }

        .options {
            display: grid;
            gap: 15px;
            margin-bottom: 25px;
        }

        .option {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px 20px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .option:hover {
            background: rgba(233, 196, 106, 0.2);
            border-color: rgba(233, 196, 106, 0.5);
            transform: translateX(5px);
        }

        .option.selected {
            background: rgba(233, 196, 106, 0.3);
            border-color: #e9c46a;
        }

        .option.correct {
            background: rgba(74, 155, 78, 0.3);
            border-color: #4a9b4e;
        }

        .option.incorrect {
            background: rgba(231, 111, 81, 0.3);
            border-color: #e76f51;
        }

        .submit-btn {
            background: linear-gradient(135deg, #e9c46a, #f4a261);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-right: 15px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(233, 196, 106, 0.4);
        }

        .next-btn {
            background: linear-gradient(135deg, #4a9b4e, #2d5a30);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            opacity: 0.5;
            pointer-events: none;
        }

        .next-btn.enabled {
            opacity: 1;
            pointer-events: auto;
        }

        .next-btn.enabled:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 155, 78, 0.4);
        }

        .feedback {
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            display: none;
        }

        .feedback.correct {
            background: rgba(74, 155, 78, 0.2);
            border: 2px solid #4a9b4e;
            color: #ffffff;
        }

        .feedback.incorrect {
            background: rgba(231, 111, 81, 0.2);
            border: 2px solid #e76f51;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <a href="../preguntas_mago.php" class="back-btn">← Regresar</a>
    
    <div class="container">
        <div class="header">
            <h1>Capítulo 1: El Ciclón</h1>
        </div>

        <div class="chapter-content">
            <div class="chapter-text">
                Dorothy vivía en las grandes llanuras de Kansas con el tío Henry, que era granjero, y la tía Em, que era la esposa del granjero. Su casa era pequeña, porque la madera para construirla había tenido que ser transportada en carro desde muchas millas de distancia. Había cuatro paredes, un piso y un techo, lo que constituía una habitación; y esta habitación contenía una cocina oxidada, un armario para los platos, una mesa, tres o cuatro sillas y las camas.
            </div>
            
            <div class="chapter-text">
                El tío Henry y la tía Em tenían un gran lecho en una esquina, y Dorothy una pequeña cama en otra esquina. No había ático ni sótano -excepto un pequeño agujero cavado en el suelo, llamado ciclón, donde la familia podía ir en caso de que surgiera uno de esos grandes torbellinos, tan poderosos que aplastan cualquier construcción en su camino.
            </div>
        </div>

        <div class="question-section">
            <div class="question">
                ¿Dónde vivía Dorothy con sus tíos?
            </div>
            
            <div class="options">
                <div class="option" data-answer="a">
                    A) En las montañas de Colorado
                </div>
                <div class="option" data-answer="b">
                    B) En las grandes llanuras de Kansas
                </div>
                <div class="option" data-answer="c">
                    C) En la ciudad de Nueva York
                </div>
                <div class="option" data-answer="d">
                    D) En los bosques de Oregón
                </div>
            </div>

            <button class="submit-btn" onclick="checkAnswer()">Verificar Respuesta</button>
            <a href="2.php" class="next-btn" id="nextBtn">Siguiente Capítulo →</a>
            
            <div class="feedback" id="feedback"></div>
        </div>
    </div>

    <script>
        let answered = false;
        const correctAnswer = 'b';

        function checkAnswer() {
            if (answered) return;
            
            const selectedOption = document.querySelector('.option.selected');
            if (!selectedOption) {
                alert('Por favor selecciona una respuesta');
                return;
            }

            answered = true;
            const userAnswer = selectedOption.dataset.answer;
            const feedbackDiv = document.getElementById('feedback');
            const nextBtn = document.getElementById('nextBtn');

            // Mostrar todas las respuestas
            document.querySelectorAll('.option').forEach(option => {
                if (option.dataset.answer === correctAnswer) {
                    option.classList.add('correct');
                } else if (option.classList.contains('selected')) {
                    option.classList.add('incorrect');
                }
                option.style.pointerEvents = 'none';
            });

            // Mostrar feedback
            if (userAnswer === correctAnswer) {
                feedbackDiv.className = 'feedback correct';
                feedbackDiv.innerHTML = '¡Correcto! Dorothy vivía con sus tíos en las grandes llanuras de Kansas.';
            } else {
                feedbackDiv.className = 'feedback incorrect';
                feedbackDiv.innerHTML = 'Incorrecto. La respuesta correcta es: En las grandes llanuras de Kansas.';
            }
            
            feedbackDiv.style.display = 'block';
            nextBtn.classList.add('enabled');
        }

        // Seleccionar opciones
        document.querySelectorAll('.option').forEach(option => {
            option.addEventListener('click', function() {
                if (answered) return;
                
                document.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    </script>
</body>
</html>
