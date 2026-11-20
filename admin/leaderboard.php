<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// GET PLAYER LEADERBOARD
$query = "
    SELECT
        users.user_id,
        users.username,
        MAX(game_records.score) AS highest_score,
        COUNT(game_records.game_id) AS games_played
    FROM users
    INNER JOIN game_records
        ON users.user_id = game_records.user_id
    GROUP BY users.user_id, users.username
    ORDER BY highest_score DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Leaderboard | AstroMaze Admin</title>

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
            <a href="game_records.php">Game Records</a>
            <a href="leaderboard.php" class="active">Leaderboard</a>
        </nav>

        <div class="sidebar-logout">
            <a href="../logout.php">Logout</a>
        </div>

    </aside>

    <!-- MAIN CONTENT -->
    <main class="admin-main">

        <header class="admin-header">
            <h1>Leaderboard</h1>
        </header>

        <section class="dashboard-content">

            <div class="welcome-section">
                <h2>Player Rankings</h2>
                <p>Monitor player performance and highest game scores.</p>
            </div>

            <!-- LEADERBOARD TABLE -->
            <div class="activity-section">

                <h3>Top Players</h3>

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>RANK</th>
                                <th>PLAYER</th>
                                <th>HIGHEST SCORE</th>
                                <th>GAMES PLAYED</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (mysqli_num_rows($result) > 0) { ?>

                                <?php $rank = 1; ?>

                                <?php while ($player = mysqli_fetch_assoc($result)) { ?>

                                    <tr>
                                        <td>
                                            <?php echo $rank; ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($player['username']); ?>
                                        </td>

                                        <td>
                                            <?php echo $player['highest_score']; ?>
                                        </td>

                                        <td>
                                            <?php echo $player['games_played']; ?>
                                        </td>
                                    </tr>

                                    <?php $rank++; ?>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="4" class="text-center">
                                        No player rankings available yet.
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