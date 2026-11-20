
<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// HANDLE DISABLE / ENABLE USER ACTIONS
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $userId = intval($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    // Only allow disable or enable actions
    if ($userId > 0 && in_array($action, ['disable', 'enable'])) {

        // Only regular user accounts can be disabled or enabled
        $updateQuery = mysqli_prepare(
            $conn,
            "UPDATE users
             SET is_active = ?
             WHERE user_id = ? AND role = 'user'"
        );

        $status = ($action === 'enable') ? 1 : 0;

        mysqli_stmt_bind_param(
            $updateQuery,
            "ii",
            $status,
            $userId
        );

        if (mysqli_stmt_execute($updateQuery)) {

            if (mysqli_stmt_affected_rows($updateQuery) > 0) {

                mysqli_stmt_close($updateQuery);

                if ($action === 'disable') {
                    header("Location: users.php?disabled=1");
                } else {
                    header("Location: users.php?enabled=1");
                }

                exit;

            }

        }

        mysqli_stmt_close($updateQuery);

        header("Location: users.php?error=action_failed");
        exit;
    }
}

// SEARCH USERS
$search = $_GET['search'] ?? '';

$userQuery = "
    SELECT
        user_id,
        username,
        email,
        role,
        created_at,
        is_active
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

                <!-- SUCCESS: DISABLED -->
                <?php if (isset($_GET['disabled'])) { ?>
                    <div class="alert alert-success mt-3">
                        User account disabled successfully!
                    </div>
                <?php } ?>

                <!-- SUCCESS: ENABLED -->
                <?php if (isset($_GET['enabled'])) { ?>
                    <div class="alert alert-success mt-3">
                        User account enabled successfully!
                    </div>
                <?php } ?>

                <!-- ERROR: ACTION FAILED -->
                <?php if (isset($_GET['error']) && $_GET['error'] === 'action_failed') { ?>
                    <div class="alert alert-danger mt-3">
                        Unable to update this user account. Please try again.
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
                                <th>STATUS</th>
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

                                    <!-- ACCOUNT STATUS -->
                                    <td>

                                        <?php if ($user['is_active'] == 1) { ?>

                                            <span class="badge bg-success">
                                                Active
                                            </span>

                                        <?php } else { ?>

                                            <span class="badge bg-secondary">
                                                Disabled
                                            </span>

                                        <?php } ?>

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

                                            <!-- DISABLE / ENABLE BUTTON -->
                                            <?php if ($user['role'] === 'user') { ?>

                                                <form
                                                    method="POST"
                                                    action="users.php"
                                                    onsubmit="return confirm('<?php echo ($user['is_active'] == 1) ? 'Are you sure you want to disable this user account?' : 'Are you sure you want to enable this user account?'; ?>');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="user_id"
                                                        value="<?php echo $user['user_id']; ?>"
                                                    >

                                                    <?php if ($user['is_active'] == 1) { ?>

                                                        <input
                                                            type="hidden"
                                                            name="action"
                                                            value="disable"
                                                        >

                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                        >
                                                            Disable
                                                        </button>

                                                    <?php } else { ?>

                                                        <input
                                                            type="hidden"
                                                            name="action"
                                                            value="enable"
                                                        >

                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-outline-success"
                                                        >
                                                            Enable
                                                        </button>

                                                    <?php } ?>

                                                </form>

                                            <?php } ?>

                                        </div>

                                    </td>

                                </tr>

                            <?php } ?>

                            <?php if (mysqli_num_rows($userResult) === 0) { ?>

                                <tr>
                                    <td colspan="7" class="text-center">
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
