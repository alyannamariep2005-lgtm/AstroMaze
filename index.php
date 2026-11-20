
<?php

session_start();

require_once 'includes/db.php';

// If already logged in, redirect to the correct dashboard
if (isset($_SESSION['user_id'])) {

    if (($_SESSION['role'] ?? '') === 'admin') {

        header("Location: admin/dashboard.php");
        exit;

    } elseif (($_SESSION['role'] ?? '') === 'user') {

        header("Location: user/home.php");
        exit;

    }
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get submitted login details
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {

        $error = "Please enter your username and password.";

    } else {

        // Find the account in the database
        $query = mysqli_prepare(
            $conn,
            "SELECT user_id, username, password, role, is_active
             FROM users
             WHERE username = ?"
        );

        mysqli_stmt_bind_param($query, "s", $username);
        mysqli_stmt_execute($query);

        $result = mysqli_stmt_get_result($query);
        $user = mysqli_fetch_assoc($result);

        // Verify password and check if account is active
        if (
            $user &&
            password_verify($password, $user['password']) &&
            $user['is_active'] == 1
        ) {

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Store account information in session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on account role
            if ($user['role'] === 'admin') {

                header("Location: admin/dashboard.php");
                exit;

            } else {

                header("Location: user/home.php");
                exit;

            }

        } else {

            $error = "Invalid username or password.";

        }

        mysqli_stmt_close($query);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Log In | AstroMaze</title>

    <link rel="stylesheet" href="css/login.css">
</head>

<body>

    <!-- Header -->
    <header class="login-header">
        <div class="logo">
            <span>Astro</span><span class="logo-accent">Maze</span>
        </div>
    </header>

    <!-- Login Main Section -->
    <main class="login-container">

        <!-- Rocket Illustration -->
        <img
            src="assets/images/login-rocket.png"
            alt="AstroMaze Rocket"
            class="login-rocket"
        >

        <!-- Login Card -->
        <section class="login-card">

            <h1>Welcome, Astronaut!</h1>

            <?php if (!empty($error)): ?>
                <p class="form-error">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php endif; ?>

            <form action="" method="POST">

                <!-- Username -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter your username"
                        required
                    >
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>

                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >

                        <button type="button" id="togglePassword">
                            <i>Show</i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me and Forgot Password -->
                <div class="form-options">
                    <label>
                        <input type="checkbox" name="remember">
                        Remember Me
                    </label>

                    <a href="#">Forgot Password?</a>
                </div>

                <!-- Login Button -->
                <button type="submit" class="login-button">
                    Log In
                </button>

            </form>

            <!-- Sign Up Link -->
            <p class="signup-text">
                Don't have an account yet?
                <a href="register.php">Sign Up.</a>
            </p>

        </section>

    </main>

    <script src="js/main.js"></script>

</body>
</html>
