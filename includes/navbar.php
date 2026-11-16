<?php
    $currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">

    <!-- Logo -->
    <div class="navbar-logo">
        <span class="logo-astro">Astro</span>
        <span class="logo-maze">Maze</span>
    </div>

    <!-- Navbar Links -->
    <div class="navbar-links">

        <a href="home.php"
            class="nav-link <?php echo ($currentPage == 'home.php') ? 'active' : ''; ?>">
            Home
        </a>

        <a href="about.php"
            class="nav-link <?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">
            About
        </a>

        <a href="how-to-play.php"
            class="nav-link <?php echo ($currentPage == 'how-to-play.php') ? 'active' : ''; ?>">
            How To Play
        </a>

        <a href="leaderboard.php"
            class="nav-link <?php echo ($currentPage == 'leaderboard.php') ? 'active' : ''; ?>">
            Leaderboard
        </a>


    </div>

    <div class="navbar-action">

        <a href="/AstroMaze/logout.php" class="logout-link">Logout</a>

    </div>

</nav>

<a href="game.php" class="floating-play-btn">
    Play Now
</a>