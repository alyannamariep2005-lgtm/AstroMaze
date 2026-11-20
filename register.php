
<?php

require_once 'includes/db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form values
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check required fields
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {

        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } elseif (strlen($password) < 8) {

        $error = "Password must be at least 8 characters long.";

    } else {

        // Check if username or email already exists
        $check = mysqli_prepare(
            $conn,
            "SELECT user_id FROM users WHERE username = ? OR email = ?"
        );

        mysqli_stmt_bind_param($check, "ss", $username, $email);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {

            $error = "Username or email is already registered.";

        } else {

            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $insert = mysqli_prepare(
                $conn,
                "INSERT INTO users (username, email, password)
                 VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $insert,
                "sss",
                $username,
                $email,
                $hashed_password
            );

            if (mysqli_stmt_execute($insert)) {

                $success = "Account created successfully! You can now log in.";

            } else {

                $error = "Something went wrong. Please try again.";

            }

            mysqli_stmt_close($insert);
        }

        mysqli_stmt_close($check);
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign Up | AstroMaze</title>

    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="css/register.css">
</head>

<body>

    <!-- Header -->
    <header class="login-header">
        <div class="logo">
            <span>Astro</span><span class="logo-accent">Maze</span>
        </div>
    </header>

    <!-- Registration Section -->
    <main class="login-container">

        <!-- Rocket Illustration -->
        <img
            src="assets/images/login-rocket.png"
            alt="AstroMaze Rocket"
            class="login-rocket"
        >

        <!-- Registration Card -->
        <section class="login-card register-card">

            <h1>Create Your Account</h1>

            <?php if (!empty($error)): ?>
                <p class="form-error">
                    <?php echo htmlspecialchars($error); ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <p class="form-success">
                    <?php echo htmlspecialchars($success); ?>
                </p>
            <?php endif; ?>

            <form action="" method="POST" id="registerForm">

                <!-- Username -->
                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Choose a username"
                        required
                    >
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
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
                            placeholder="Create a password"
                            required
                        >

                        <button type="button" class="toggle-password" data-target="password">
                            Show
                        </button>
                    </div>
                </div>

                
                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="confirm-password">Confirm Password</label>

                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="confirm-password"
                            name="confirm_password"
                            placeholder="Confirm your password"
                            required
                        >

                        <button type="button" class="toggle-password" data-target="confirm-password">
                            Show
                        </button>
                    </div>
                </div>

                <p id="password-error" class="form-error"></p>

                <!-- Sign Up Button -->
                <button type="submit" class="login-button">
                    Sign Up
                </button>

            </form>

            <!-- Login Link -->
            <p class="signup-text">
                Already have an account?
                <a href="index.php">Log In.</a>
            </p>

        </section>

    </main>

    <script src="js/main.js"></script>

</body>
</html>
