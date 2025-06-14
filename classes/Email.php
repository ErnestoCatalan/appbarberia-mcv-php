<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token)
    {
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;
    }

    public function enviarConfirmacion() {
            // Crear el objeto de email
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $_ENV['EMAIL_HOST'];
            $mail->SMTPAuth = true;
            $mail->Port = $_ENV['EMAIL_PORT'];
            $mail->Username = $_ENV['EMAIL_USER'];
            $mail->Password = $_ENV['EMAIL_PASS'];

            $mail->setFrom($_ENV['EMAIL_USER'], 'Barbería Trece Romano');
            $mail->addAddress($this->email, $this->nombre);
            $mail->Subject = 'Confirma tu cuenta';

            // Configurar HTML
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            $contenido = "<html>";
            $contenido .= "<p><strong>Hola " . $this->nombre . "</strong>, has creado tu cuenta en Barbería Trece Romano. Solo debes confirmarla presionando el siguiente enlace:</p>";
            $contenido .= "<p>Presiona aquí: <a href='" .  $_ENV['APP_URL']  ."/confirmar-cuenta?token=" . $this->token . "'>Confirmar Cuenta</a></p>";
            $contenido .= "<p>Si tú no solicitaste esta cuenta, puedes ignorar este mensaje.</p>";
            $contenido .= "</html>";

            $mail->Body = $contenido;

            // Enviar el email
            $mail->send();

    }

    public function enviarInstrucciones() {
         // Crear el objeto de email
         $mail = new PHPMailer(true);
         $mail->isSMTP();
         $mail->Host = $_ENV['EMAIL_HOST'];
         $mail->SMTPAuth = true;
         $mail->Port = $_ENV['EMAIL_PORT'];
         $mail->Username = $_ENV['EMAIL_USER'];
         $mail->Password = $_ENV['EMAIL_PASS'];

         $mail->setFrom($_ENV['EMAIL_USER'], 'Barbería Trece Romano');
         $mail->addAddress($this->email, $this->nombre);
         $mail->Subject = 'Restablece tu password';

         // Configurar HTML
         $mail->isHTML(true);
         $mail->CharSet = 'UTF-8';

         $contenido = "<html>";
         $contenido .= "<p><strong>Hola " . $this->nombre . "</strong>, Has solicitado restablecer tu password, sigue el siguiente enlace para hacerlo.</p>";
         $contenido .= "<p>Presiona aquí: <a href='" .  $_ENV['APP_URL']  ."/recuperar?token=" . $this->token . "'>Restablecer Password</a></p>";
         $contenido .= "<p>Si tú no solicitaste esta cuenta, puedes ignorar este mensaje.</p>";
         $contenido .= "</html>";
         $mail->Body = $contenido;

         // Enviar el email
         $mail->send();
    }
}
