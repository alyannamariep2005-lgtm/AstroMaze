
<?php

$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "astromaze_db";

// Create database connection
$conn = mysqli_connect(
    $host,
    $username,
    $password,
    $database
);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

?>
