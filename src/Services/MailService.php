<?php
namespace App\Services; // Puedes ajustar este namespace si tu estructura es diferente

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailService {
    private $mail;
    private $config; // Añadido para almacenar la configuración

    public function __construct() {
        $this->mail = new PHPMailer(true); // Habilitar excepciones

        // Carga la configuración desde el archivo mail_config.php
        // Asegúrate de que esta ruta sea correcta para tu MailService.php
        $this->config = require __DIR__ . '/../../config/mail_config.php';

        // Configuración SMTP (ajusta estos valores a tu servidor de correo)
        $this->mail->isSMTP();
        $this->mail->Host       = $this->config['host'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $this->config['username'];
        $this->mail->Password   = $this->config['password'];
        $this->mail->SMTPSecure = $this->config['secure']; // 'ssl' o 'tls'
        $this->mail->Port       = $this->config['port'];   // 465 o 587

        // --- INICIO DE LA CORRECCIÓN PARA EL ERROR SSL/TLS ---
        // Desactivar la verificación automática de TLS si el puerto es 587 (STARTTLS)
        // Esto es a menudo necesario en entornos de desarrollo local.
        $this->mail->SMTPAutoTLS = false;

        // Opciones para deshabilitar la verificación de certificados SSL
        // ¡ADVERTENCIA! No recomendado para entornos de producción.
        // En producción, asegúrate de que tus certificados CA estén actualizados.
        $this->mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        // --- FIN DE LA CORRECCIÓN ---

        // Configuración general del remitente
        $this->mail->setFrom($this->config['from_email'], $this->config['from_name']);
        $this->mail->isHTML(true); // Formato HTML
        $this->mail->CharSet = 'UTF-8'; // Codificación de caracteres
    }

    /**
     * Envía un correo electrónico.
     * @param string $toEmail Dirección de correo del destinatario.
     * @param string $toName Nombre del destinatario.
     * @param string $subject Asunto del correo.
     * @param string $body Contenido HTML del correo.
     * @param string $altBody Contenido de texto plano del correo (opcional).
     * @throws Exception Si hay un error al enviar el correo.
     */
    public function send($toEmail, $toName, $subject, $body, $altBody = '') {
        $this->mail->clearAddresses(); // Limpiar destinatarios anteriores
        $this->mail->addAddress($toEmail, $toName);
        $this->mail->Subject = $subject;
        $this->mail->Body    = $body;
        $this->mail->AltBody = $altBody ?: strip_tags($body); // Si no se da altBody, lo genera del body HTML

        $this->mail->send();
    }
}
