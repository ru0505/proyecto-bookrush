<?php
/**
 * Archivo para probar el sistema de envío de emails
 * IMPORTANTE: Configura tus credenciales de Gmail en las variables a continuación
 * Para usar Gmail, necesitas generar una contraseña de aplicación:
 * https://myaccount.google.com/apppasswords
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Asegúrate de que estamos en la raíz del proyecto
require __DIR__ . '/../vendor/autoload.php';

// CONFIGURAR ESTAS VARIABLES CON TUS CREDENCIALES
$GMAIL_USER = 'diego123ali@gmail.com'; // Tu email de Gmail
$GMAIL_PASS = 'qamjofzhmcmbkhxo'; // Tu contraseña de aplicación de Gmail

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP de Gmail
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $GMAIL_USER;
    $mail->Password   = $GMAIL_PASS;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    // Remitente y destinatario
    $mail->setFrom($GMAIL_USER, 'Book Rush');
    $mail->addAddress($GMAIL_USER); // Envía a tu propio email para probar

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = 'Prueba de Sistema de Correos - Book Rush';
    $mail->Body    = '<h2>¡Sistema de correos funcionando!</h2>
                      <p>Si recibes este mensaje, PHPMailer está correctamente instalado y configurado.</p>
                      <p><strong>Próximos pasos:</strong></p>
                      <ul>
                        <li>Configurar cron_recordatorios.php</li>
                        <li>Ajustar credenciales de Gmail</li>
                        <li>Programar ejecución automática</li>
                      </ul>';

    // Versión de texto plano
    $mail->AltBody = 'Sistema de correos funcionando. PHPMailer está correctamente instalado.';

    // Enviar
    $mail->send();
    echo '<p style="color: green; font-weight: bold;">¡Éxito! El correo se envió correctamente.</p>';
    echo '<p>Revisa tu bandeja de entrada en Gmail.</p>';

} catch (Exception $e) {
    echo '<p style="color: red; font-weight: bold;">Error al enviar el correo:</p>';
    echo '<p>' . htmlspecialchars($mail->ErrorInfo) . '</p>';
    echo '<p>Verifica que:</p>
          <ul>
            <li>Las credenciales sean correctas</li>
            <li>Hayas generado una contraseña de aplicación en Gmail</li>
            <li>Tu conexión a internet esté activa</li>
          </ul>';
}
?>
