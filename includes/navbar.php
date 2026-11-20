
<?php

$currentPage = basename($_SERVER['PHP_SELF']);

// Get the logged-in user's profile picture
require_once __DIR__ . '/db.php';

$userId = $_SESSION['user_id'] ?? null;
$profilePicture = null;

if ($userId) {

    $pictureQuery = mysqli_prepare(
        $conn,
        "SELECT profile_picture FROM users WHERE user_id = ?"
    );

    mysqli_stmt_bind_param($pictureQuery, "i", $userId);
    mysqli_stmt_execute($pictureQuery);

    $pictureResult = mysqli_stmt_get_result($pictureQuery);
    $pictureData = mysqli_fetch_assoc($pictureResult);

    if ($pictureData) {
        $profilePicture = $pictureData['profile_picture'];
    }

    mysqli_stmt_close($pictureQuery);
}

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

        <details class="profile-dropdown">

            <summary class="profile-trigger">

                <?php if (!empty($profilePicture)): ?>

                    <img
                        src="/AstroMaze/<?php echo htmlspecialchars($profilePicture); ?>"
                        alt="Profile Picture"
                        class="navbar-profile-picture"
                    >

                <?php else: ?>

                    <span class="navbar-profile-placeholder">
                        <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)); ?>
                    </span>

                <?php endif; ?>

                Profile
                <span class="dropdown-arrow">▼</span>

            </summary>

            <div class="profile-menu">

                <a href="edit-profile.php">Edit Profile</a>

                <a href="/AstroMaze/logout.php" class="logout-link">
                    Logout
                </a>

            </div>

        </details>

    </div>

</nav>

<a href="game.php" class="floating-play-btn">
    Play Now
</a>
