<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AstroMaze | Home</title>

    <link rel="stylesheet" href="/AstroMaze/css/navbar.css">
    <link rel="stylesheet" href="/AstroMaze/css/home.css">
    <link rel="stylesheet" href="/AstroMaze/css/footer.css">

</head>

<body>
    <?php include '../includes/navbar.php'; ?>

    <section class="hero-section">
        
    <img src="../assets/images/rocket-left.png"
        alt="Left Rocket"
        class="hero-rocket rocket-left">

    <img src="../assets/images/rocket-right.png"
        alt="Right Rocket"
        class="hero-rocket rocket-right">

    <div class="hero-content">

    <div class="hero-intro">

        <h1>EXPLORE THE COSMIC UNKNOWN</h1>

            <p>
                Set off on a cosmic adventure, navigate mysterious mazes, 
                collect glowing energy, 
                and find your way through the wonders of the unknown.
            </p>

    </div>

        
        <div class="hero-buttons">
            
            <a href="game.php" class="hero-btn primary-btn">Start Mission</a>
            <a href="about.php" class="hero-btn secondary-btn">Explore Galaxy</a>

        </div>
    </div>


    </section>

    <section class="marquee-section">

    <div class="marquee-track">

        <div class="marquee-content">
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
        </div>

        <div class="marquee-content" aria-hidden="true">
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
        </div>

    </div>

    </section>

    <section class="backstory-section">

    <div class="backstory-content">

        <h1> A JOURNEY BEYOND STARS </h1>

        <p>
            A little spaceship drifts into a mysterious galaxy filled with glowing stars and distant planets.
            Lost among winding cosmic paths, its journey becomes an unexpected adventure.

            <br><br>

            With fuel running low, explore the maze, collect energy orbs, 
            and overcome every twist and turn. Your mission is to find your way home among the stars.
        </p>

    </div>

    </section>

    <section class="marquee-section">

    <div class="marquee-track">

        <div class="marquee-content">
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
        </div>

        <div class="marquee-content" aria-hidden="true">
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
            <span>EXPLORE THE COSMIC UNKNOWN</span>
        </div>

    </div>

    </section>

    <section class="features-section">

    <div class="features-content">

    <div class="features-intro">

        <h1>A COSMIC ADVENTURE AWAITS</h1>

        <p>
            Embark on an exciting journey through mysterious mazes,
            collect glowing energy orbs, manage your fuel, and overcome
            unexpected challenges.
        </p>    

    </div>


        <div class="features-cards">

            <div class="feature-card">
                <h2>Maze Exploreation </h2>
                <p>
                    Navigate through mysterious maze pathways, discover hidden routes, 
                    and find your way through each challenging level.
                </p>
            </div>

            <div class="feature-card">
                <h2>Energy Collecton </h2>
                <p>
                    Collect glowing energy orbs to earn points and 
                    gather fuel supplies to keep your spaceship running throughout the adventure.
                </p>
            </div>

            <div class="feature-card">
                <h2>Fuel Management</h2>
                <p>
                    Keep an eye on your remaining fuel as it gradually decreases. 
                    Manage your resources wisely to survive and complete your mission before running out of energy.
                </p>
            </div>

        </div>

    </div>

    </section>

    </section>

    <?php include '../includes/footer.php'; ?>

</body>
</html>