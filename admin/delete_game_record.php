<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// ONLY ALLOW POST REQUESTS
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: game_records.php");
    exit;
}

// GET THE GAME ID
$gameId = filter_input(INPUT_POST, 'game_id', FILTER_VALIDATE_INT);

if (!$gameId) {
    header("Location: game_records.php");
    exit;
}

// DELETE THE SELECTED GAME RECORD
$deleteQuery = "
    DELETE FROM game_records
    WHERE game_id = ?
";

$stmt = mysqli_prepare($conn, $deleteQuery);

mysqli_stmt_bind_param($stmt, "i", $gameId);

if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        header("Location: game_records.php?deleted=1");
        exit;
    }

}

header("Location: game_records.php?error=delete_failed");
exit;
?>