<?php
require 'config.php';

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

    // Send verification email (use PHPMailer in production)
    $verifyLink = "http://yourdomain.com/verify.php?token=$token";
    mail($email, "Verify Your Email", "Click here to verify: $verifyLink");

    echo "Registration successful! Check your email to verify.";
}
?>