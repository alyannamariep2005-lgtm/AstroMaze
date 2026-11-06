<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// SEARCH USERS
$search = $_GET['search'] ?? '';

$userQuery = "
    SELECT
        user_id,
        username,
        email,
        role,
        created_at
    FROM users
    WHERE username LIKE ? OR email LIKE ?
    ORDER BY created_at DESC
";

$stmt = mysqli_prepare($conn, $userQuery);

$searchTerm = "%" . $search . "%";

mysqli_stmt_bind_param($stmt, "ss", $searchTerm, $searchTerm);
mysqli_stmt_execute($stmt);

$userResult = mysqli_stmt_get_result($stmt);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AstroMaze | Users Management</title>

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
            <h1>Users Management</h1>
        </header>

        <div class="dashboard-content">

            <section class="welcome-section">
                <h2>Registered Users</h2>
                <p>Manage and view all registered AstroMaze players.</p>

                <!-- SUCCESS: UPDATED -->
                <?php if (isset($_GET['updated'])) { ?>
                    <div class="alert alert-success mt-3">
                        User information updated successfully!
                    </div>
                <?php } ?>

                <!-- SUCCESS: DELETED -->
                <?php if (isset($_GET['deleted'])) { ?>
                    <div class="alert alert-success mt-3">
                        User account deleted successfully!
                    </div>
                <?php } ?>

                <!-- ERROR: SELF DELETE -->
                <?php if (isset($_GET['error']) && $_GET['error'] === 'self_delete') { ?>
                    <div class="alert alert-danger mt-3">
                        You cannot delete your own administrator account!
                    </div>
                <?php } ?>

                <!-- ERROR: DELETE FAILED -->
                <?php if (isset($_GET['error']) && $_GET['error'] === 'delete_failed') { ?>
                    <div class="alert alert-danger mt-3">
                        Unable to delete this user account.
                    </div>
                <?php } ?>

            </section>

            <!-- USERS TABLE -->
            <section class="activity-section">

                <!-- TABLE HEADER AND SEARCH -->
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h3>All Users</h3>

                    <form method="GET" action="users.php" class="d-flex gap-2">

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search username or email..."
                            value="<?php echo htmlspecialchars($search); ?>"
                        >

                        <button type="submit" class="btn btn-dark">
                            Search
                        </button>

                        <a href="users.php" class="btn btn-outline-secondary">
                            Reset
                        </a>

                    </form>

                </div>

                <!-- USERS DATA -->
                <div class="table-responsive mt-4">

                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>USER ID</th>
                                <th>USERNAME</th>
                                <th>EMAIL</th>
                                <th>ROLE</th>
                                <th>DATE REGISTERED</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php while ($user = mysqli_fetch_assoc($userResult)) { ?>

                                <tr>
                                    <td>
                                        <?php echo $user['user_id']; ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($user['role']); ?>
                                    </td>

                                    <td>
                                        <?php echo date("M d, Y", strtotime($user['created_at'])); ?>
                                    </td>

                                    <td>
                                        <div class="d-flex gap-2">

                                            <!-- EDIT BUTTON -->
                                            <a
                                                href="edit_user.php?id=<?php echo $user['user_id']; ?>"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                Edit
                                            </a>

                                            <!-- DELETE BUTTON -->
                                            <?php if ($user['role'] === 'user') { ?>

                                                <form
                                                    method="POST"
                                                    action="delete_user.php"
                                                    onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="user_id"
                                                        value="<?php echo $user['user_id']; ?>"
                                                    >

                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        Delete
                                                    </button>

                                                </form>

                                            <?php } ?>

                                        </div>
                                    </td>
                                </tr>

                            <?php } ?>

                            <?php if (mysqli_num_rows($userResult) === 0) { ?>

                                <tr>
                                    <td colspan="6" class="text-center">
                                        No registered users found.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </div>

    </main>

</div>

</body>
</html>
