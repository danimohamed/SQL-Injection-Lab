<?php
// ============================================================
// db.php - Database Connection (PDO)
// FOR EDUCATIONAL PURPOSES ONLY - localhost only
// ============================================================

// Database credentials - change these to match your local MySQL setup
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'sql_injection_lab');
define('DB_USER', 'root');
define('DB_PASS', '');              // Change this to your MySQL root password

/**
 * Returns a PDO connection to the sql_injection_lab database.
 *
 * IMPORTANT SECURITY NOTE:
 * - ATTR_EMULATE_PREPARES is set to false so that PDO uses real
 *   MySQL prepared statements rather than emulating them in PHP.
 *   This is critical for the secure login demo.
 * - ATTR_ERRMODE is set to EXCEPTION so errors surface clearly.
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Use REAL prepared statements, not emulated ones
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
