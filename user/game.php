<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AstroMaze - Play</title>

    <link rel="stylesheet" href="../css/game.css">
    <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>

<a href="home.php" class="back-btn">
    < Back to Home
</a>
    
<main class="game-container">

    <div class="game-layout">

        <!-- GAME MAZE -->
        <div class="maze-container">

            <div id="maze"></div>

            <!-- GAME OVER SCREEN -->
            <div id="game-over-screen" class="game-over-screen">
                <div class="game-over-content">
                    <h2>GAME OVER</h2>
                    <p>Your rocket ran out of fuel!</p>
                    <button id="restart-button">TRY AGAIN</button>
                </div>
            </div>

            <!-- VICTORY SCREEN -->
            <div id="victory-screen" class="game-over-screen">
                <div class="game-over-content">
                    <h2>MISSION COMPLETE!</h2>
                    <p>You collected all the energy orbs!</p>
                    <p>Earth's energy has been restored!</p>
                    <button id="victory-restart-button">PLAY AGAIN</button>
                </div>
            </div>

        </div>

        <!-- GAME HUD -->
        <div class="game-hud">

            <div class="hud-item">
                <span class="hud-label">SCORE</span>
                <span class="hud-value" id="score">0</span>
            </div>

            <div class="hud-item">
                <span class="hud-label">ORBS</span>
                <span class="hud-value" id="orbs">0</span>
            </div>

            <div class="hud-item">
                <span class="hud-label">FUEL</span>
                <span class="hud-value" id="fuel">40%</span>
            </div>

        </div>

    </div>

</main>
    <script src="../js/game.js"></script>
</body>
</html>
