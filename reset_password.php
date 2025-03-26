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
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Reset Password</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <body class="d-flex justify-content-center align-items-center vh-100 bg-light">
                <div class="card p-4 shadow" style="width: 350px;">
                    <h2 class="text-center mb-3">Reset Password</h2>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= $_SESSION['error'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="8">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                    </form>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
