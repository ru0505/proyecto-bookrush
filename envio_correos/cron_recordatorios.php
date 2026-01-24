<?php
/**
 * Script de recordatorios automáticos por correo
 * Ejecutar vía CRON cada día a las 9 AM: 0 9 * * * /usr/bin/php /ruta/cron_recordatorios.php
 * 
 * INSTRUCCIONES:
 * 1. Configura tu email y contraseña de Gmail en las variables abajo
 * 2. Para Gmail, usa una contraseña de aplicación: https://myaccount.google.com/apppasswords
 * 3. Programa el CRON job en tu servidor
 */

require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ============ CONFIGURACIÓN ============
require_once __DIR__ . '/../conexion.php';

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'diego123ali@gmail.com'); // Reemplaza con tu email
define('SMTP_PASS', 'qamjofzhmcmbkhxo');      // Reemplaza con tu contraseña de app
define('SMTP_FROM', 'diego123ali@gmail.com');
define('SMTP_FROM_NAME', 'Book Rush');

// ============ LOG DE EJECUCIÓN ============
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/recordatorios_' . date('Y-m-d') . '.log';

function escribirLog($mensaje) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $entrada = "[$timestamp] $mensaje\n";
    file_put_contents($logFile, $entrada, FILE_APPEND);
    echo $entrada; // También mostrar en consola
}

// ============ INICIO DEL PROCESO ============
try {
    escribirLog("========================================");
    escribirLog("Iniciando proceso de recordatorios...");
    escribirLog("========================================");

    // Buscar usuarios inactivos por más de 3 días
    $stmt = $conn->prepare("
        SELECT 
            ID,
            NOMBRE, 
            email, 
            ultimo_acceso,
            DATEDIFF(NOW(), ultimo_acceso) AS dias_inactivo
        FROM usuarios 
        WHERE ultimo_acceso IS NOT NULL 
        AND ultimo_acceso < DATE_SUB(NOW(), INTERVAL 3 DAY)
        AND email IS NOT NULL 
        AND email != ''
        ORDER BY ultimo_acceso ASC
    ");
    
    $stmt->execute();
    $result = $stmt->get_result();
    $usuariosInactivos = [];
    
    while ($row = $result->fetch_assoc()) {
        $usuariosInactivos[] = $row;
    }

    $totalUsuarios = count($usuariosInactivos);
    escribirLog("Usuarios inactivos encontrados: $totalUsuarios");

    if ($totalUsuarios === 0) {
        escribirLog("No hay usuarios que requieran recordatorio.");
        escribirLog("Proceso finalizado correctamente.");
        exit(0);
    }

    // Contador de envíos
    $enviados = 0;
    $errores = 0;

    // ============ ENVIAR CORREOS ============
    foreach ($usuariosInactivos as $usuario) {
        try {
            $mail = new PHPMailer(true);
            
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            // Remitente y destinatario
            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($usuario['email'], $usuario['NOMBRE']);

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Te extrañamos en Book Rush - Vuelve a la aventura literaria';
            
            // Generar cuerpo HTML motivacional
            $diasInactivo = intval($usuario['dias_inactivo']);
            $nombreUsuario = htmlspecialchars($usuario['NOMBRE']);
            $mail->Body = generarHTMLMotivacional($nombreUsuario, $diasInactivo);
            
            // Versión de texto plano
            $mail->AltBody = "¡Hola {$nombreUsuario}! Te extrañamos en Book Rush. "
                           . "Han pasado {$diasInactivo} días desde tu última visita. "
                           . "Vuelve y continúa tu aventura literaria. ¡Te esperamos!";

            // Enviar correo
            $mail->send();
            
            escribirLog("OK Correo enviado a {$usuario['email']} ({$nombreUsuario})");
            $enviados++;

            // Pausa para evitar sobrecarga del servidor SMTP (0.5 segundos)
            usleep(500000);

        } catch (Exception $e) {
            escribirLog("ERROR Fallo al enviar a {$usuario['email']}: {$mail->ErrorInfo}");
            $errores++;
        }
    }

    // ============ RESUMEN FINAL ============
    escribirLog("========================================");
    escribirLog("Proceso completado:");
    escribirLog("- Total usuarios inactivos: $totalUsuarios");
    escribirLog("- Enviados exitosamente: $enviados");
    escribirLog("- Errores: $errores");
    escribirLog("========================================");
    
    $conn->close();
    exit(0);

} catch (Exception $e) {
    escribirLog("ERROR GENERAL: " . $e->getMessage());
    exit(1);
}

/**
 * Genera el HTML motivacional del correo
 */
function generarHTMLMotivacional($nombre, $diasInactivo) {
    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Te extrañamos en Book Rush</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', Arial, sans-serif; background-color: #f4f7fa;">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f7fa; padding: 40px 0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #4a90e2, #357abd); padding: 40px 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 32px; font-weight: 700;">
                                📚 Book Rush
                            </h1>
                            <p style="color: #e8f4ff; margin: 10px 0 0 0; font-size: 16px;">
                                Tu aventura literaria te espera
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Contenido principal -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <h2 style="color: #1e334e; font-size: 24px; margin: 0 0 20px 0;">
                                ¡Hola, {$nombre}! 👋
                            </h2>
                            
                            <p style="color: #555; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Han pasado <strong style="color: #4a90e2;">{$diasInactivo} días</strong> desde tu última visita y realmente te extrañamos en nuestra comunidad literaria.
                            </p>
                            
                            <p style="color: #555; font-size: 16px; line-height: 1.6; margin: 0 0 20px 0;">
                                Mientras no estabas, han sucedido muchas cosas emocionantes:
                            </p>
                            
                            <ul style="color: #555; font-size: 16px; line-height: 1.8; margin: 0 0 25px 20px; padding: 0;">
                                <li>✨ Nuevos desafíos literarios disponibles</li>
                                <li>📖 Contenido exclusivo sobre literatura nacional y regional</li>
                                <li>🏆 Tu puntaje te está esperando para seguir creciendo</li>
                                <li>🎯 ¡Otros lectores están avanzando en el ranking!</li>
                            </ul>
                            
                            <div style="text-align: center; margin: 35px 0;">
                                <a href="http://localhost:8888/proyecto-bookrush/index.php" 
                                   style="display: inline-block; background: linear-gradient(135deg, #4a90e2, #357abd); color: #ffffff; text-decoration: none; padding: 15px 40px; border-radius: 8px; font-size: 18px; font-weight: 600; box-shadow: 0 5px 15px rgba(74, 144, 226, 0.3);">
                                    🚀 Volver a Book Rush
                                </a>
                            </div>
                            
                            <p style="color: #666; font-size: 14px; line-height: 1.6; margin: 25px 0 0 0; font-style: italic; border-left: 4px solid #4a90e2; padding-left: 15px;">
                                "La lectura es un viaje que nunca termina, y tu próxima aventura está a solo un clic de distancia."
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e0e0e0;">
                            <p style="color: #888; font-size: 14px; margin: 0 0 10px 0;">
                                Book Rush - Tu plataforma de literatura peruana
                            </p>
                            <p style="color: #aaa; font-size: 12px; margin: 0;">
                                © 2025 Book Rush. Todos los derechos reservados.
                            </p>
                            <p style="color: #aaa; font-size: 11px; margin: 10px 0 0 0;">
                                Si no deseas recibir estos recordatorios, contáctanos en soporte@bookrush.com
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
}
?>
