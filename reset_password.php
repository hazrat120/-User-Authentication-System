<?php
require 'config.php';
session_start();

// Check if token exists in URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    try {
        // Verify token validity and expiration
        $stmt = $pdo->prepare("SELECT * FROM users WHERE token = ? AND token_expiry > NOW()");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                // Validate password
                if (empty($_POST['password']) || strlen($_POST['password']) < 8) {
                    throw new Exception("Password must be at least 8 characters long");
                }

                // Verify password confirmation
                if ($_POST['password'] !== $_POST['confirm_password']) {
                    throw new Exception("Passwords do not match");
                }

                // Hash new password
                $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                // Update password and clear reset token
                $updateStmt = $pdo->prepare("UPDATE users SET password = ?, token = NULL, token_expiry = NULL WHERE id = ?");
                $updateStmt->execute([$newPassword, $user['id']]);

                // Redirect with success message
                $_SESSION['success'] = "Password reset successfully! You can now login.";
                header("Location: login.php");
                exit();
            }

            // Show reset form
            ?>
            <!DOCTYPE html>
            <html>
            <head>
                <title>Reset Password</title>
                <style>
                    .container { max-width: 400px; margin: 50px auto; padding: 20px; }
                    .error { color: red; }
                    .success { color: green; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h2>Reset Password</h2>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="error"><?= $_SESSION['error'] ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <form method="POST">
                        <div>
                            <label>New Password:</label>
                            <input type="password" name="password" required minlength="8">
                        </div>
                        <div>
                            <label>Confirm Password:</label>
                            <input type="password" name="confirm_password" required minlength="8">
                        </div>
                        <button type="submit">Reset Password</button>
                    </form>
                </div>
            </body>
            </html>
            <?php
        } else {
            $_SESSION['error'] = "Invalid or expired token.";
            header("Location: login.php");
            exit();
        }
    } catch (Exception $e) {
        // Handle errors
        $_SESSION['error'] = $e->getMessage();
        header("Location: reset_password.php?token=" . urlencode($token));
        exit();
    }
} else {
    // No token provided
    $_SESSION['error'] = "Invalid password reset request.";
    header("Location: login.php");
    exit();
}
?>