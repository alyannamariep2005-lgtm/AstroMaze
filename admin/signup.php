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

        <input type="text" name="fullname"
            placeholder="Full Name" class="form-control" required>

        <input type="text" name="username"
            placeholder="Username" class="form-control" required>

        <input type="email" name="email"
            placeholder="Email Address" class="form-control" required>

        <input type="password" name="password"
            placeholder="Password" class="form-control" required>

        <input type="password" name="confirm_password"
            placeholder="Confirm Password" class="form-control" required>

        <button type="submit" class="btn login-btn">
            Create Account
        </button>

</form>
            </form>

            <p class="signup-link">
                Already have an account?
                <a href="index.php">Log In.</a>
            </p>

        </div>

    </div>

    <script src="js/main.js"></script>

</body>
</html>