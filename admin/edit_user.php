<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// Get the selected user ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$userId = (int) $_GET['id'];

// Prevent administrator from editing their own account
if ($userId === (int) $_SESSION['user_id']) {
    header("Location: users.php");
    exit;
}

// Retrieve user information
$query = "
    SELECT user_id, username, email
    FROM users
    WHERE user_id = ?
";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Redirect if user does not exist
if (!$user) {
    header("Location: users.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AstroMaze | Edit User</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>

<div class="admin-layout">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar">

        <div class="admin-logo">
            <span class="logo-astro">Astro</span><span class="logo-maze">Maze</span>
        </div>

        <nav class="sidebar-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="users.php" class="active">Users</a>
            <a href="game_records.php">Game Records</a>
            <a href="leaderboard.php">Leaderboard</a>
        </nav>

        <div class="sidebar-logout">
            <a href="/AstroMaze/logout.php">Logout</a>
        </div>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="admin-main">

        <header class="admin-header">
            <h1>Edit User</h1>
        </header>

        <div class="dashboard-content">

            <section class="welcome-section">
                <h2>Update User Information</h2>
                <p>Edit the username and email address of this player.</p>
            </section>

            <!-- EDIT FORM -->
            <section class="activity-section">

                <form method="POST" action="update_user.php">

                    <input
                        type="hidden"
                        name="user_id"
                        value="<?php echo $user['user_id']; ?>"
                    >

                    <!-- USERNAME -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Username</label>

                        <input
                            type="text"
                            name="username"
                            class="form-control"
                            value="<?php echo htmlspecialchars($user['username']); ?>"
                            required
                        >
                    </div>

                    <!-- EMAIL -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Email Address</label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?php echo htmlspecialchars($user['email']); ?>"
                            required
                        >
                    </div>

                    <!-- BUTTONS -->
                    <div class="d-flex gap-2">

                        <button type="submit" class="btn btn-dark">
                            Save Changes
                        </button>

                        <a href="users.php" class="btn btn-outline-secondary">
                            Cancel
                        </a>

                    </div>

                </form>

            </section>

        </div>

    </main>

</div>

</body>
</html>
