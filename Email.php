<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Load composer's autoloader
require_once 'vendor/autoload.php';
require_once 'config.php';

function SendEmail($email, $subject, $body, $code)
{

    $MailBody = str_replace ( '%%code%%' , $code , $body );
    $MailSubject = str_replace ( '%%code%%' , $code , $subject );

    echo sprintf("Preparing to send an e-mail! <br /> Email: %s <br /> Subject: %s <br /> Body: %s <br /> Code: %s<br />\n", $email, $MailSubject, $MailBody, $code);

    $mail = new PHPMailer(true);   // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = 2;      // Enable verbose debug output
        $mail->isSMTP();           // Set mailer to use SMTP
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASS;
        
        if(MAIL_TLS)
            $mail->SMTPSecure = 'tls';

        $mail->Port = MAIL_PORT;
    
        //Recipients
        $mail->setFrom(MAIL_USER, MAIL_SENDER_NAME);
        $mail->addAddress($email);
    
        //Content
        $mail->isHTML(true);
        $mail->Subject = $MailSubject;
        $mail->Body    = $MailBody;
    
        $mail->send();
        echo sprintf("Success: %s - %s<br />\n", $email, $code);
    } catch (Exception $e) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
}
?>