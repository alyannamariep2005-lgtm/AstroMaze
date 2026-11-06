
<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    exit;
}

header("Content-Type: application/json");

// Get the submitted game results
$data = json_decode(file_get_contents("php://input"), true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid data."]);
    exit;
}

// Get the logged-in player's ID from the session
$user_id = (int) $_SESSION['user_id'];

// Get game information
$score = filter_var($data['score'] ?? null, FILTER_VALIDATE_INT);
$orbs_collected = filter_var($data['orbs_collected'] ?? null, FILTER_VALIDATE_INT);
$fuel_remaining = filter_var($data['fuel_remaining'] ?? null, FILTER_VALIDATE_INT);
$game_status = $data['game_status'] ?? '';

// Validate the submitted results
if (
    $score === false || $score < 0 ||
    $orbs_collected === false || $orbs_collected < 0 || $orbs_collected > 10 ||
    $fuel_remaining === false || $fuel_remaining < 0 || $fuel_remaining > 100 ||
    !in_array($game_status, ['won', 'lost'], true)
) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid game results."]);
    exit;
}

// Insert the game record
$query = mysqli_prepare(
    $conn,
    "INSERT INTO game_records
    (user_id, score, orbs_collected, fuel_remaining, game_status)
    VALUES (?, ?, ?, ?, ?)"
);

mysqli_stmt_bind_param(
    $query,
    "iiiis",
    $user_id,
    $score,
    $orbs_collected,
    $fuel_remaining,
    $game_status
);

if (mysqli_stmt_execute($query)) {
    echo json_encode([
        "success" => true,
        "message" => "Game record saved!"
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Unable to save game record."
    ]);
}

mysqli_stmt_close($query);

?>
