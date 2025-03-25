<?php
require 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        $stmt = $pdo->prepare("UPDATE users SET verified = 1, token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        echo "Email verified! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Invalid token.";
    }
}
?>