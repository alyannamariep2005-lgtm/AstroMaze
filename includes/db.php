<?php

// On Railway, DATABASE_URL points at Supabase (Postgres).
// Locally (XAMPP) it is not set, so the original MySQL connection is used.
$databaseUrl = getenv('DATABASE_URL');

if ($databaseUrl) {

    require_once __DIR__ . '/pg_compat.php';

    $conn = pg_compat_connect($databaseUrl);

    if (!$conn) {
        die("Database connection failed.");
    }

} else {

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
}

?>
