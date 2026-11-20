<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$error = '';
$success = '';
$accountEmail = '';
$showResetForm = false;

// Step 1: Check whether the submitted email belongs to an active account.
// No email or reset link is sent; a matching account can continue directly.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_email'])) {
    $accountEmail = trim($_POST['email'] ?? '');

    if (!filter_var($accountEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $lookup = mysqli_prepare($conn, 'SELECT user_id FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
        mysqli_stmt_bind_param($lookup, 's', $accountEmail);
        mysqli_stmt_execute($lookup);
        mysqli_stmt_store_result($lookup);
        $accountExists = mysqli_stmt_num_rows($lookup) === 1;
        mysqli_stmt_close($lookup);

        if ($accountExists) {
            $showResetForm = true;
        } else {
            $error = 'No active account was found with that email address.';
        }
    }
}

// Step 2: Re-check the account and update its password.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $accountEmail = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!filter_var($accountEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($newPassword) < 8) {
        $error = 'Your password must be at least 8 characters long.';
        $showResetForm = true;
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'The passwords do not match.';
        $showResetForm = true;
    } else {
        $lookup = mysqli_prepare($conn, 'SELECT user_id FROM users WHERE email = ? AND is_active = 1 LIMIT 1');
        mysqli_stmt_bind_param($lookup, 's', $accountEmail);
        mysqli_stmt_execute($lookup);
        $result = mysqli_stmt_get_result($lookup);
        $account = mysqli_fetch_assoc($result);
        mysqli_stmt_close($lookup);

        if (!$account) {
            $error = 'No active account was found with that email address.';
        } else {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $update = mysqli_prepare($conn, 'UPDATE users SET password = ? WHERE user_id = ?');
            mysqli_stmt_bind_param($update, 'si', $passwordHash, $account['user_id']);

            if (mysqli_stmt_execute($update)) {
                $success = 'Your password has been changed successfully. You can now log in.';
                $accountEmail = '';
            } else {
                $error = 'We could not update your password. Please try again.';
                $showResetForm = true;
            }
            mysqli_stmt_close($update);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | AstroMaze</title>
    <link rel="stylesheet" href="../css/login.css">
    <style>
        .login-card h1 { margin-bottom: 15px; }
        .forgot-description { color: #d0c5dc; font-size: 14px; line-height: 1.6; text-align: center; margin-bottom: 25px; }
        .form-success { color: #9ee6b0; font-size: 13px; line-height: 1.5; text-align: center; margin-bottom: 18px; }
        .forgot-back { display: block; margin-top: 22px; color: #9c86b9; text-align: center; font-size: 13px; text-decoration: none; }
        .forgot-back:hover { color: #fff; text-decoration: underline; }
    </style>
</head>
<body>
    <header class="login-header">
        <div class="logo"><span>Astro</span><span class="logo-accent">Maze</span></div>
    </header>

    <main class="login-container">
        <section class="login-card">
            <?php if ($showResetForm): ?>
                <h1>Reset Your Password</h1>
                <p class="forgot-description">Choose a new password for your AstroMaze account.</p>
            <?php else: ?>
                <h1>Forgot Password?</h1>
                <p class="forgot-description">Enter the email address linked to your AstroMaze account. If the account exists, you can choose a new password.</p>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <p class="form-error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>
            <?php if ($success !== ''): ?>
                <p class="form-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endif; ?>

            <?php if ($showResetForm): ?>
                <form method="POST" action="forgotpass.php">
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($accountEmail, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" placeholder="At least 8 characters" minlength="8" required autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your new password" minlength="8" required autocomplete="new-password">
                    </div>
                    <button type="submit" name="update_password" value="1" class="login-button">Update Password</button>
                </form>
            <?php elseif ($success === ''): ?>
                <form method="POST" action="forgotpass.php">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($accountEmail, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Enter your registered email" required autocomplete="email">
                    </div>
                    <button type="submit" name="check_email" value="1" class="login-button">Continue</button>
                </form>
            <?php endif; ?>

            <a class="forgot-back" href="../index.php">Back to Log In</a>
        </section>
    </main>
</body>
</html>
