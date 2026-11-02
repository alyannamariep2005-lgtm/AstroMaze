<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AstroMaze | Login</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="login-container">

        <div class="login-header">
            <h3>AstroMaze</h3>

            <select name="user_type" class="form-select">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <div class="login-card">

            <h2>Welcome, Astronaut!</h2>

            <form action="" method="POST">

                <label>Username</label>
                <input
                    type="text"
                    name="username"
                    class="form-control"
                    placeholder="Enter username"
                    required
                >

                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter password"
                    required
                >

                <div class="login-options">

                    <label>
                        <input type="checkbox" name="remember">
                        Remember Me
                    </label>

                    <a href="#">Forgot Password?</a>

                </div>

                <button type="submit" class="btn login-btn">
                    Log In
                </button>

            </form>

            <p class="signup-link">
                Don't have an account yet?
                <a href="signup.php">Sign Up.</a>
            </p>

        </div>

    </div>

    <script src="js/main.js"></script>

</body>
</html>