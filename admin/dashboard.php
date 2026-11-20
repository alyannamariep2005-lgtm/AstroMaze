
<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_role('admin');

// TOTAL USERS
$userQuery = "
    SELECT COUNT(*) AS total_users
    FROM users
    WHERE role = 'user'
";

$userResult = mysqli_query($conn, $userQuery);
$totalUsers = mysqli_fetch_assoc($userResult)['total_users'];


// TOTAL GAMES
$gameQuery = "
    SELECT COUNT(*) AS total_games
    FROM game_records
";

$gameResult = mysqli_query($conn, $gameQuery);
$totalGames = mysqli_fetch_assoc($gameResult)['total_games'];


// COMPLETED GAMES
$completedQuery = "
    SELECT COUNT(*) AS completed_games
    FROM game_records
    WHERE game_status = 'won'
";

$completedResult = mysqli_query($conn, $completedQuery);
$completedGames = mysqli_fetch_assoc($completedResult)['completed_games'];


// AVERAGE SCORE
$averageQuery = "
    SELECT AVG(score) AS average_score
    FROM game_records
";

$averageResult = mysqli_query($conn, $averageQuery);
$averageScore = mysqli_fetch_assoc($averageResult)['average_score'];

$averageScore = round($averageScore ?? 0, 2);

// RECENT GAME ACTIVITY
$activityQuery = "
    SELECT
        users.username,
        game_records.score,
        game_records.orbs_collected,
        game_records.game_status,
        game_records.played_at
    FROM game_records
    INNER JOIN users
        ON game_records.user_id = users.user_id
    ORDER BY game_records.played_at DESC
    LIMIT 5
";

$activityResult = mysqli_query($conn, $activityQuery);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AstroMaze | Admin Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Admin CSS -->
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

            <a href="dashboard.php" class="active">Dashboard</a>

            <a href="users.php">Users</a>

            <a href="game_records.php">Game Records</a>

            <a href="leaderboard.php">Leaderboard</a>

        </nav>

        <div class="sidebar-logout">
            <a href="/AstroMaze/logout.php">Logout</a>
        </div>

    </aside>


    <!-- MAIN CONTENT -->
    <main class="admin-main">

        <!-- HEADER -->
        <header class="admin-header">
            <h1>Dashboard</h1>
        </header>


        <div class="dashboard-content">

            <!-- WELCOME SECTION -->
            <section class="welcome-section">
                <h2>Welcome back, Admin!</h2>
                <p>Here's what's going on with AstroMaze today.</p>
            </section>


            <!-- STATISTICS CARDS -->
            <section class="row g-3">

                <div class="col">
                    <div class="stat-card">
                        <h3><?php echo $totalUsers; ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>

                <div class="col">
                    <div class="stat-card">
                        <h3><?php echo $totalGames; ?></h3>
                        <p>Total Games</p>
                    </div>
                </div>

                <div class="col">
                    <div class="stat-card">
                        <h3><?php echo $completedGames; ?></h3>
                        <p>Completed Games</p>
                    </div>
                </div>

                <div class="col">
                    <div class="stat-card">
                        <h3><?php echo $averageScore; ?></h3>
                        <p>Average Score</p>
                    </div>
                </div>

            </section>


            <!-- RECENT ACTIVITY -->
            
            <section class="activity-section">

                <h3>Recent Activity</h3>

                <div class="table-responsive mt-4">

                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>PLAYER</th>
                                <th>SCORE</th>
                                <th>ORBS</th>
                                <th>STATUS</th>
                                <th>DATE PLAYED</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php while ($activity = mysqli_fetch_assoc($activityResult)) { ?>

                                <tr>

                                    <td>
                                        <?php echo htmlspecialchars($activity['username']); ?>
                                    </td>

                                    <td>
                                        <?php echo $activity['score']; ?>
                                    </td>

                                    <td>
                                        <?php echo $activity['orbs_collected']; ?>
                                    </td>

                                    <td>
                                        <?php if ($activity['game_status'] === 'won') { ?>
                                            <span class="status-badge status-won">WON</span>
                                        <?php } else { ?>
                                            <span class="status-badge status-lost">LOST</span>
                                        <?php } ?>
                                    </td>

                                    <td>
                                        <?php echo date("M d, Y h:i A", strtotime($activity['played_at'])); ?>
                                    </td>

                                </tr>

                            <?php } ?>

                            <?php if (mysqli_num_rows($activityResult) === 0) { ?>
                                <tr>
                                    <td colspan="5" class="text-center">
                                        No recent game activity yet.
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
