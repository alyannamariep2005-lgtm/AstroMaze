
<?php

// Start session if one hasn't started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load database connection
require_once __DIR__ . '/db.php';

// Check if user is logged in
function require_login() {

    global $conn;

    if (!isset($_SESSION['user_id'])) {
        header("Location: /AstroMaze/index.php");
        exit;
    }

    // Check whether the account is still active
    $query = mysqli_prepare(
        $conn,
        "SELECT is_active FROM users WHERE user_id = ?"
    );

    mysqli_stmt_bind_param(
        $query,
        "i",
        $_SESSION['user_id']
    );

    mysqli_stmt_execute($query);

    $result = mysqli_stmt_get_result($query);
    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($query);

    // Log out the user if the account is disabled or no longer exists
    if (!$user || $user['is_active'] != 1) {

        // Clear session information
        $_SESSION = [];

        // Destroy the session
        session_destroy();

        // Redirect to login page
        header("Location: /AstroMaze/index.php?disabled=1");
        exit;
    }
}

// Check if user has the required role
function require_role($role) {

    require_login();

    if ($_SESSION['role'] !== $role) {

        if ($_SESSION['role'] === 'admin') {
            header("Location: /AstroMaze/admin/dashboard.php");
        } else {
            header("Location: /AstroMaze/user/home.php");
        }

        exit;
    }
}

?>
