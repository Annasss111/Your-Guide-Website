<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function send_email_enhanced($to, $subject, $message, $options = []) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'medanasdaoud519@gmail.com';
        $mail->Password = 'cjrqwoiwjloefotq'; // No spaces
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('medanasdaoud519@gmail.com', 'Your Guide Tunisia');
        $mail->addAddress($to);
        if (isset($options['reply_to'])) {
            $mail->addReplyTo($options['reply_to']);
        }

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Message could not be sent. PHPMailer Error: {$mail->ErrorInfo}");
        return $mail->ErrorInfo;
    }
}
?>