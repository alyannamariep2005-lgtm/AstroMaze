<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// GET ALL GAME RECORDS
$query = "
    SELECT
        game_records.game_id,
        users.username,
        game_records.score,
        game_records.orbs_collected,
        game_records.fuel_remaining,
        game_records.game_status,
        game_records.played_at
    FROM game_records
    INNER JOIN users
        ON game_records.user_id = users.user_id
    ORDER BY game_records.played_at DESC
";

// SEARCH GAME RECORDS
$search = $_GET['search'] ?? '';

$query = "
    SELECT
        game_records.game_id,
        users.username,
        game_records.score,
        game_records.orbs_collected,
        game_records.fuel_remaining,
        game_records.game_status,
        game_records.played_at
    FROM game_records
    INNER JOIN users
        ON game_records.user_id = users.user_id
    WHERE users.username LIKE ?
    ORDER BY game_records.played_at DESC
";

$stmt = mysqli_prepare($conn, $query);

$searchTerm = "%" . $search . "%";

mysqli_stmt_bind_param($stmt, "s", $searchTerm);
mysqli_stmt_execute($stmt);

$gameResult = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Game Records | AstroMaze Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
            <a href="users.php">Users</a>
            <a href="game_records.php" class="active">Game Records</a>
            <a href="leaderboard.php">Leaderboard</a>
        </nav>

        <div class="sidebar-logout">
            <a href="../logout.php">Logout</a>
        </div>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="admin-main">

        <header class="admin-header">
            <h1>Game Records</h1>
        </header>

        <section class="dashboard-content">

            <div class="welcome-section">
                <h2>Game Records</h2>
                <p>View and manage all recorded AstroMaze games.</p>
            </div>

                        <!-- SUCCESS MESSAGE -->
            <?php if (isset($_GET['deleted'])) { ?>
                <div class="alert alert-success mt-3">
                    Game record deleted successfully!
                </div>
            <?php } ?>

            <!-- ERROR MESSAGE -->
            <?php if (isset($_GET['error']) && $_GET['error'] === 'delete_failed') { ?>
                <div class="alert alert-danger mt-3">
                    Unable to delete this game record.
                </div>
            <?php } ?>

                        <!-- SEARCH BAR -->
            <form method="GET" action="game_records.php" class="mb-4">

                <div class="d-flex gap-2">

                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search by player username..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >

                    <button type="submit" class="btn btn-primary">
                        Search
                    </button>

                    <a href="game_records.php" class="btn btn-outline-light">
                        Reset
                    </a>

                </div>

            </form>
            <!-- GAME RECORDS TABLE -->
            <div class="activity-section">

                <h3>All Game Records</h3>

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>GAME ID</th>
                                <th>PLAYER</th>
                                <th>SCORE</th>
                                <th>ORBS</th>
                                <th>FUEL</th>
                                <th>STATUS</th>
                                <th>DATE PLAYED</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (mysqli_num_rows($gameResult) > 0) { ?>

                                <?php while ($game = mysqli_fetch_assoc($gameResult)) { ?>

                                    <tr>
                                        <td><?php echo $game['game_id']; ?></td>

                                        <td><?php echo htmlspecialchars($game['username']); ?></td>

                                        <td><?php echo $game['score']; ?></td>

                                        <td><?php echo $game['orbs_collected']; ?></td>

                                        <td><?php echo $game['fuel_remaining']; ?>%</td>

                                        <td>
                                            <?php if ($game['game_status'] === 'won') { ?>
                                                <span class="status-badge status-won">WON</span>
                                            <?php } else { ?>
                                                <span class="status-badge status-lost">LOST</span>
                                            <?php } ?>
                                        </td>

                                        <td>
                                            <?php echo date("M d, Y h:i A", strtotime($game['played_at'])); ?>
                                        </td>

                                      <td>
                                            <form method="POST" action="delete_game_record.php"
                                                    onsubmit="return confirm('Are you sure you want to delete this game record? This action cannot be undone.');">

                                                    <input
                                                        type="hidden"
                                                        name="game_id"
                                                        value="<?php echo $game['game_id']; ?>"
                                                    >

                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        Delete
                                                    </button>

                                                </form>
                                            </td>
                                        </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="8" class="text-center">
                                        No game records found.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>