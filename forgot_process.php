<?php
require 'config.php';

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

        // Send reset email
        $resetLink = "http://yourdomain.com/reset_password.php?token=$token";
        mail($email, "Reset Password", "Click here to reset: $resetLink");
        echo "Reset link sent to your email.";
    } else {
        echo "Email not found.";
    }
}
?>