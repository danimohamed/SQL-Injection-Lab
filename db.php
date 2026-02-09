<?php
// ============================================================
// db.php - Database Connection (PDO)
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================

// Database credentials - change these to match your local MySQL setup
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'sql_injection_lab');
define('DB_USER', 'root');
define('DB_PASS', 'danidani2002@');              // Change this to your MySQL root password

/**
 * Returns a raw PDO connection used by VULNERABLE pages.
 *
 * EMULATE_PREPARES = true  so that query() sends the SQL string
 * exactly as-is to MySQL, without any driver-level escaping.
 * This is what makes the injections possible.
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => true,
        ]);
    }

    return $pdo;
}

/**
 * Returns a secure PDO connection used by the SECURE login page.
 *
 * EMULATE_PREPARES = false  so that PDO uses real MySQL server-side
 * prepared statements. Combined with bound parameters, this makes
 * SQL injection impossible - the query structure and the data are
 * sent to MySQL in two separate steps.
 */
function getSecureDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }

    return $pdo;
}

/**
 * Helper: Reset the users table to its original state.
 * Called from pages that modify/delete data so the lab stays usable.
 */
function resetUsersTable(): void
{
    $pdo = getDB();
    $pdo->exec("DELETE FROM users");
    $pdo->exec("ALTER TABLE users AUTO_INCREMENT = 1");
    $pdo->exec("INSERT INTO users (username, password, role) VALUES
        ('admin',  'admin123',  'admin'),
        ('user1',  'password1', 'user'),
        ('user2',  'letmein',   'user')
    ");
}
