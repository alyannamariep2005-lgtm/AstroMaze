
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>How to Play | AstroMaze</title>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/AstroMaze/css/navbar.css">
    <link rel="stylesheet" href="/AstroMaze/css/how-to-play.css">
    <link rel="stylesheet" href="/AstroMaze/css/footer.css">

</head>

<body>

    <?php include '../includes/navbar.php'; ?>

    <main class="how-to-play-page">

        <section class="htp-hero">
            <h1>HOW TO PLAY</h1>
            <p>
                Ready to explore the cosmic unknown?
                Learn the basics and find your way through the maze!
            </p>
        </section>

        <section class="htp-step">
            <div class="step-image">
                <img src="../assets/images/step1.png" alt="Step 1">
            </div>

            <div class="step-content">
                <h2>01. NAVIGATE THE MAZE</h2>
                <p>
                    Use the arrow keys or WASD to move your character
                    through the maze. Explore different paths and
                    find your way around obstacles.
                </p>
            </div>
        </section>

        <section class="htp-step reverse">
            <div class="step-image">
                <img src="../assets/images/step2.png" alt="Step 2">
            </div>

            <div class="step-content">
                <h2>02. COLLECT ENERGY ORBS</h2>
                <p>
                    Gather all the energy orbs scattered throughout
                    the maze. Each orb contributes to your score,
                    so make sure you collect them all!
                </p>
            </div>
        </section>

        <section class="htp-step">
            <div class="step-image">
                <img src="../assets/images/step3.png" alt="Step 3">
            </div>

            <div class="step-content">
                <h2>03. MANAGE YOUR FUEL</h2>
                <p>
                    Your fuel decreases as you move. Look for fuel
                    power-ups to replenish your energy and keep
                    exploring before it runs out.
                </p>
            </div>
        </section>

        <section class="htp-cta">
            <h2>READY TO EXPLORE?</h2>
            <p>
                Collect every orb, manage your fuel,
                and conquer the maze!
            </p>

            <a href="game.php" class="play-button">PLAY NOW</a>
        </section>

    </main>

    <?php include '../includes/footer.php'; ?>

</body>
</html>
