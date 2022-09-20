<?php
namespace App\Helpers;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer {
    private $host = 'smtp.mailtrap.io';
    private $port = 2525;
    private $username = '3f450c3ce1f549';
    private $password = 'ee74e330f06a15';
    private $senderMail = 'sender@gmail.com';
    private $senderName = 'sender name';

    function sendMail(string $email)
    {
        // Create the SMTP Transport
        // $transport = (new Swift_SmtpTransport($this->host, $this->port))
        //     ->setUsername($this->username)
        //     ->setPassword($this->password);


        // $mailer = new Swift_Mailer($transport);
        // $message = new Swift_Message();
        // $message->setSubject($subject);

        // // Set the "From address"
        // $message->setFrom([$this->senderMail => $this->senderName]);

        // // Set the "To address"
        // $message->addTo($email);

        // // Set the plain-text "Body"
        // $message->setBody($content, $contentType);

        // // Send the message
        // $mailer->send($message);
        $phpmailer = new PHPMailer();
        $phpmailer->isSMTP();
        $phpmailer->Host = 'smtp.mailtrap.io';
        $phpmailer->SMTPAuth = true;
        $phpmailer->Port = 2525;
        $phpmailer->Username = '3f450c3ce1f549';
        $phpmailer->Password = 'ee74e330f06a15';
        $phpmailer->setFrom('admin@badger-dating.com', 'BadgerDating.com');
        $phpmailer->addAddress($email);
        $phpmailer->addReplyTo('no-reply@badger-dating.com', 'BadgerDating.com');
        $phpmailer->isHTML(true);
        $phpmailer->Subject = 'Test';
        $phpmailer->Body    = "
        <p>Hi,</p>
        <p>            
        Thanks for choosing BadgerDating.com!  We have received a request for a password reset on the account associated with this email address.
        </p>
        <p>
        To confirm and reset your password, please click <a href=\"http://badger-dating.com/resetpassword/$id/$param\">here</a>.  If you did not initiate this request,
        please disregard this message.
        </p>
        <p>
        If you have any questions about this email, you may contact us at support@badger-dating.com.
        </p>
        <p>
        With regards,
        <br>
        The BadgerDating.com Team
        </p>";
        if($phpmailer->send()) {
           return 'send';
        }   
    }
}
