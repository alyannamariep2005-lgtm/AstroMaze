<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_login();

// Get the top players based on their highest scores
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/leaderboard.css">
    

    <title>AstroMaze | Leaderboard </title>
</head>
<body>

    <?php include '../includes/navbar.php'; ?>

    <main class="container py-5">

        <div class="text-center mb-5">
            <h1 class="leaderboard-title">LEADERBOARD</h1>
            <p class="leaderboard-description">
                Discover who's leading the cosmic adventure!
            </p>
        </div>

        <div class="table-responsive leaderboard-table">
            <table class="table table-dark table-hover align-middle text-center">

            <thead>
                <tr>
                    <th>RANK</th>
                    <th>PLAYER</th>
                    <th>HIGHEST SCORE</th>
                    <th>GAMES PLAYED</th>
                </tr>
            </thead>

                <tbody>
                    <?php
                    $rank = 1;

                    while ($player = mysqli_fetch_assoc($result)) {
                    ?>
                        <tr class="<?php echo ((int)$player['user_id'] === (int)$_SESSION['user_id']) ? 'current-player' : ''; ?>">
                            <td>
                                <?php if ($rank === 1) { ?>
                                    <span class="rank-badge rank-gold">1</span>
                                <?php } elseif ($rank === 2) { ?>
                                    <span class="rank-badge rank-silver">2</span>
                                <?php } elseif ($rank === 3) { ?>
                                    <span class="rank-badge rank-bronze">3</span>
                                <?php } else { ?>
                                    <?php echo $rank; ?>
                                <?php } ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($player['username']); ?>

                                <?php if ((int)$player['user_id'] === (int)$_SESSION['user_id']) { ?>
                                    <span class="you-badge">YOU</span>
                                <?php } ?>
                            </td>

                            <td>
                                <?php echo $player['highest_score']; ?>
                            </td>

                            <td>
                                <?php echo $player['games_played']; ?>
                            </td>
                        </tr>
                    <?php
                        $rank++;
                    }
                    ?>

                    <?php if (mysqli_num_rows($result) === 0) { ?>
                        <tr>
                            <td colspan="4">No players have recorded scores yet.</td>
                        </tr>
                    <?php } ?>

                </tbody>

            </table>
        </div>

    </main>
    
</body>
</html>