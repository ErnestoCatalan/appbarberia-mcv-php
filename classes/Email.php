<?php
namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token) {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['EMAIL_PORT'];
            
            // Configuración del remitente y destinatario
            $mail->setFrom($_ENV['EMAIL_FROM_EMAIL'], $_ENV['EMAIL_FROM_NAME']);
            $mail->addAddress($this->email, $this->nombre);
            
            // Contenido del email
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Confirma tu cuenta - Elite Barber';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong>, has creado tu cuenta en Elite Barber. Solo debes confirmarla presionando el siguiente enlace:</p>";
            $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['APP_URL'] . "/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a></p>";
            $contenido .= "<p>Si tú no solicitaste esta cuenta, puedes ignorar este mensaje.</p>";
            $contenido .= "</html>";

            $mail->Body = $contenido;
            $mail->AltBody = strip_tags($contenido);

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error al enviar email: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function enviarInstrucciones() {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['EMAIL_PORT'];
            
            $mail->setFrom($_ENV['EMAIL_FROM_EMAIL'], $_ENV['EMAIL_FROM_NAME']);
            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Restablece tu password - Elite Barber';

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong>, Has solicitado restablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
            $contenido .= "<p>Presiona aquí: <a href='" . $_ENV['APP_URL'] . "/recuperar?token=" . $this->token . "'>Restablecer Password</a></p>";
            $contenido .= "<p>Si tú no solicitaste esta cuenta, puedes ignorar este mensaje.</p>";
            $contenido .= "</html>";
            
            $mail->Body = $contenido;
            $mail->AltBody = strip_tags($contenido);

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error al enviar email: " . $mail->ErrorInfo);
            return false;
        }
    }

    // Enviar notificación de aprobación de barbería
    public function enviarAprobacionBarberia() {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['EMAIL_PORT'];
            
            $mail->setFrom($_ENV['EMAIL_FROM_EMAIL'], $_ENV['EMAIL_FROM_NAME']);
            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = '¡Felicidades! Tu barbería ha sido aprobada';

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong>,</p>";
            $contenido .= "<p>¡Excelentes noticias! Tu solicitud para registrar la barbería ha sido <strong>APROBADA</strong>.</p>";
            $contenido .= "<p>Ahora puedes acceder al panel de administración de tu barbería con tu cuenta actual.</p>";
            $contenido .= "<p><strong>Accede a tu panel:</strong> <a href='" . $_ENV['APP_URL'] . "/admin-barberia'>Mi Panel de Barbería</a></p>";
            $contenido .= "<p>En tu panel podrás:</p>";
            $contenido .= "<ul>";
            $contenido .= "<li>Gestionar tus citas</li>";
            $contenido .= "<li>Crear y editar servicios</li>";
            $contenido .= "<li>Ver tu historial de citas</li>";
            $contenido .= "</ul>";
            $contenido .= "<p>¡Bienvenido a nuestra comunidad de barberías!</p>";
            $contenido .= "</html>";
            
            $mail->Body = $contenido;
            $mail->AltBody = strip_tags($contenido);

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error al enviar email de aprobación: " . $mail->ErrorInfo);
            return false;
        }
    }

    // Enviar notificación de rechazo de barbería
    public function enviarRechazoBarberia($motivo = '') {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['EMAIL_PORT'];
            
            $mail->setFrom($_ENV['EMAIL_FROM_EMAIL'], $_ENV['EMAIL_FROM_NAME']);
            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Resultado de tu solicitud de barbería';

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong>,</p>";
            $contenido .= "<p>Lamentamos informarte que tu solicitud para registrar la barbería ha sido <strong>RECHAZADA</strong>.</p>";
            if($motivo) {
                $contenido .= "<p><strong>Motivo:</strong> " . $motivo . "</p>";
            }
            $contenido .= "<p>Puedes contactarnos para más información o enviar una nueva solicitud con la información corregida.</p>";
            $contenido .= "</html>";
            
            $mail->Body = $contenido;
            $mail->AltBody = strip_tags($contenido);

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error al enviar email de rechazo: " . $mail->ErrorInfo);
            return false;
        }
    }

    // Notificación al superadmin de nueva solicitud de barbería
    public function enviarNotificacionSolicitudSuperAdmin($nombreBarberia, $nombreSolicitante, $emailSolicitante, $telefono, $direccion) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $_ENV['EMAIL_PORT'];
            
            $mail->setFrom($_ENV['EMAIL_FROM_EMAIL'], $_ENV['EMAIL_FROM_NAME']);
            $mail->addAddress($this->email, 'Superadministrador');
            $mail->Subject = '⚠️ Nueva solicitud de barbería - ' . $nombreBarberia;

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola,</strong></p>";
            $contenido .= "<p>Se ha recibido una nueva solicitud de registro de barbería:</p>";
            $contenido .= "<hr>";
            $contenido .= "<p><strong>Información de la Barbería:</strong></p>";
            $contenido .= "<ul>";
            $contenido .= "<li><strong>Nombre:</strong> " . $nombreBarberia . "</li>";
            $contenido .= "<li><strong>Dirección:</strong> " . $direccion . "</li>";
            $contenido .= "<li><strong>Teléfono:</strong> " . $telefono . "</li>";
            $contenido .= "</ul>";
            $contenido .= "<p><strong>Información del Solicitante:</strong></p>";
            $contenido .= "<ul>";
            $contenido .= "<li><strong>Nombre:</strong> " . $nombreSolicitante . "</li>";
            $contenido .= "<li><strong>Email:</strong> " . $emailSolicitante . "</li>";
            $contenido .= "</ul>";
            $contenido .= "<hr>";
            $contenido .= "<p><a href='" . $_ENV['APP_URL'] . "/solicitudes/gestionar' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Revisar Solicitud</a></p>";
            $contenido .= "</html>";
            
            $mail->Body = $contenido;
            $mail->AltBody = strip_tags($contenido);

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error al enviar email al superadmin: " . $mail->ErrorInfo);
            return false;
        }
    }
}