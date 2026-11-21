<?php

/*
 * Minimal mysqli-style layer on top of PDO (pgsql).
 *
 * AstroMaze was written against mysqli. This file implements the handful of
 * mysqli_* functions the app actually uses, so the existing pages run on
 * Postgres (Supabase) without being rewritten. It is only loaded when
 * DATABASE_URL is set (see includes/db.php).
 */

if (function_exists('mysqli_prepare')) {
    die('pg_compat.php cannot be used while the mysqli extension is loaded.');
}

class PgConn
{
    public PDO $pdo;
    public string $error = '';
}

class PgStmt
{
    public PgConn $conn;
    public string $sql;
    public string $types = '';
    public array $params = [];
    public array $rows = [];
    public int $affected = 0;
    public int $numRows = 0;

    public function __construct(PgConn $conn, string $sql)
    {
        $this->conn = $conn;
        $this->sql = $sql;
    }
}

class PgResult
{
    public array $rows;
    public int $pos = 0;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }
}

$GLOBALS['pg_compat_last_error'] = '';

function pg_compat_connect(string $url)
{
    $p = parse_url($url);

    if (!$p || empty($p['host'])) {
        error_log('pg_compat: DATABASE_URL could not be parsed.');
        return false;
    }

    $dsn = sprintf(
        'pgsql:host=%s;port=%d;dbname=%s;sslmode=%s',
        $p['host'],
        $p['port'] ?? 5432,
        ltrim($p['path'] ?? '/postgres', '/'),
        getenv('DB_SSLMODE') ?: 'require'
    );

    try {
        $pdo = new PDO(
            $dsn,
            urldecode($p['user'] ?? ''),
            urldecode($p['pass'] ?? ''),
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Client-side binding keeps this safe with Supabase's
                // transaction pooler as well as the session pooler.
                PDO::ATTR_EMULATE_PREPARES => true,
                PDO::ATTR_TIMEOUT => 10,
            ]
        );

        $timezone = getenv('APP_TIMEZONE') ?: 'Asia/Manila';

        $pdo->exec("SET search_path TO public, extensions");
        $pdo->exec("SET TIME ZONE " . $pdo->quote($timezone));
        date_default_timezone_set($timezone);

    } catch (PDOException $e) {
        error_log('pg_compat connect failed: ' . $e->getMessage());
        $GLOBALS['pg_compat_last_error'] = $e->getMessage();
        return false;
    }

    $conn = new PgConn();
    $conn->pdo = $pdo;

    return $conn;
}

function mysqli_connect_error()
{
    return $GLOBALS['pg_compat_last_error'];
}

function mysqli_prepare($conn, $sql)
{
    return new PgStmt($conn, $sql);
}

function mysqli_stmt_bind_param($stmt, $types, ...$vars)
{
    $stmt->types = $types;
    $stmt->params = $vars;
    return true;
}

function mysqli_stmt_execute($stmt)
{
    try {
        $st = $stmt->conn->pdo->prepare($stmt->sql);

        foreach ($stmt->params as $i => $value) {

            if ($value === null) {
                $st->bindValue($i + 1, null, PDO::PARAM_NULL);
            } elseif (($stmt->types[$i] ?? 's') === 'i') {
                $st->bindValue($i + 1, (int) $value, PDO::PARAM_INT);
            } else {
                $st->bindValue($i + 1, (string) $value, PDO::PARAM_STR);
            }
        }

        $st->execute();

        $stmt->affected = $st->rowCount();
        $stmt->rows = $st->columnCount() > 0 ? $st->fetchAll(PDO::FETCH_ASSOC) : [];
        $stmt->numRows = count($stmt->rows);

        return true;

    } catch (PDOException $e) {
        error_log('pg_compat execute failed: ' . $e->getMessage());
        $stmt->conn->error = $e->getMessage();
        return false;
    }
}

function mysqli_stmt_get_result($stmt)
{
    return new PgResult($stmt->rows);
}

function mysqli_stmt_store_result($stmt)
{
    return true;
}

function mysqli_stmt_num_rows($stmt)
{
    return $stmt->numRows;
}

function mysqli_stmt_affected_rows($stmt)
{
    return $stmt->affected;
}

function mysqli_stmt_close($stmt)
{
    return true;
}

function mysqli_query($conn, $sql)
{
    try {
        $st = $conn->pdo->query($sql);
        return new PgResult($st->columnCount() > 0 ? $st->fetchAll(PDO::FETCH_ASSOC) : []);
    } catch (PDOException $e) {
        error_log('pg_compat query failed: ' . $e->getMessage());
        $conn->error = $e->getMessage();
        return false;
    }
}

function mysqli_fetch_assoc($result)
{
    if ($result->pos >= count($result->rows)) {
        return null;
    }

    return $result->rows[$result->pos++];
}

function mysqli_num_rows($result)
{
    return count($result->rows);
}

?>
