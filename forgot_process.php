<?php
require 'config.php';
require 'vendor/autoload.php'; // Load Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate reset token (expires in 1 hour)
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+1 hour'));

        $stmt = $pdo->prepare("UPDATE users SET token = ?, token_expiry = ? WHERE email = ?");
        $stmt->execute([$token, $expiry, $email]);

        // Send reset email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'sandbox.smtp.mailtrap.io';
            $mail->SMTPAuth = true;
            $mail->Username = '68df252b9d3a9c'; 
            $mail->Password = 'b73e67a0d9c796';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 2525;

            // Email content
            $mail->setFrom('noreply@yourdomain.com', 'Your Site Name');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Password';
            $resetLink = "http://localhost/PHP%20Learning/User-Authentication-System/reset_password.php?token=$token";
            $mail->Body = "Click <a href='$resetLink'>here</a> to reset your password.";

            $mail->send();
            echo "Reset link sent to your email.";
        } catch (Exception $e) {
            echo "Email could not be sent. Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Email not found.";
    }
}
?>
