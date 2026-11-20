
<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: users.php");
    exit;
}

// Get submitted information
$userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');

// Validate inputs
if (!$userId || $username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid user information.");
}

// Prevent editing the administrator's own account
if ($userId === (int) $_SESSION['user_id']) {
    die("You cannot edit your own account here.");
}

// Check if the user exists
$checkQuery = "SELECT user_id FROM users WHERE user_id = ?";
$checkStmt = mysqli_prepare($conn, $checkQuery);

mysqli_stmt_bind_param($checkStmt, "i", $userId);
mysqli_stmt_execute($checkStmt);

$checkResult = mysqli_stmt_get_result($checkStmt);

if (mysqli_num_rows($checkResult) === 0) {
    die("User not found.");
}

// Check if username or email is already used by another account
$duplicateQuery = "
    SELECT user_id
    FROM users
    WHERE (username = ? OR email = ?)
    AND user_id != ?
";

$duplicateStmt = mysqli_prepare($conn, $duplicateQuery);

mysqli_stmt_bind_param($duplicateStmt, "ssi", $username, $email, $userId);
mysqli_stmt_execute($duplicateStmt);

$duplicateResult = mysqli_stmt_get_result($duplicateStmt);

if (mysqli_num_rows($duplicateResult) > 0) {
    die("Username or email is already being used by another account.");
}

// Update user information
$updateQuery = "
    UPDATE users
    SET username = ?, email = ?
    WHERE user_id = ?
";

$updateStmt = mysqli_prepare($conn, $updateQuery);

mysqli_stmt_bind_param($updateStmt, "ssi", $username, $email, $userId);

if (mysqli_stmt_execute($updateStmt)) {
    header("Location: users.php?updated=1");
    exit;
} else {
    die("Failed to update user information.");
}
?>
