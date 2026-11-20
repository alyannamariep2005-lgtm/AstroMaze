
<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: users.php");
    exit;
}

// Get the selected user ID
$userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

if (!$userId) {
    header("Location: users.php");
    exit;
}

// Prevent administrator from deleting their own account
if ($userId === (int) $_SESSION['user_id']) {
    header("Location: users.php?error=self_delete");
    exit;
}

// Delete only regular user accounts
$deleteQuery = "
    DELETE FROM users
    WHERE user_id = ? AND role = 'user'
";

$stmt = mysqli_prepare($conn, $deleteQuery);

mysqli_stmt_bind_param($stmt, "i", $userId);

if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: users.php?deleted=1");
        exit;
    }

}

header("Location: users.php?error=delete_failed");
exit;
?>
