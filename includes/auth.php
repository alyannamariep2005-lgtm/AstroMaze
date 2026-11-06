
<?php

// Start session if one hasn't started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Check if user is logged in
function require_login() {

    if (!isset($_SESSION['user_id'])) {
        header("Location: /AstroMaze/index.php");
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
