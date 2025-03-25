<?php
require 'config.php';
require 'vendor/autoload.php'; // Load Composer autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        die("Email already exists.");
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification token
    $token = bin2hex(random_bytes(50));

    // Insert user into database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, token) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hashedPassword, $token]);

    // Send verification email using PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP Configuration (Example for Gmail)
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Username = '68df252b9d3a9c'; // Your Gmail
        $mail->Password = 'b73e67a0d9c796'; // Use App Password (2FA)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 2525;

        // Email content
        $mail->setFrom('noreply@yourdomain.com', 'Your Site Name');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email';
        $verifyLink = "http://localhost/PHP%20Learning/User-Authentication-System/verify.php?token=$token";
        $mail->Body = "Click <a href='$verifyLink'>here</a> to verify your email.";

        $mail->send();
        echo "Registration successful! Check your email to verify.";
    } catch (Exception $e) {
        echo "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}
?>