<?php
require 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE token = ? AND token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE users SET password = ?, token = NULL, token_expiry = NULL WHERE id = ?");
            $stmt->execute([$newPassword, $user['id']]);
            echo "Password reset successful! <a href='login.php'>Login</a>";
        } else {
            // Show reset form
            echo '
            <form method="POST">
                <input type="password" name="password" placeholder="New Password" required>
                <button type="submit">Reset Password</button>
            </form>
            ';
        }
    } else {
        echo "Invalid or expired token.";
    }
}
?>