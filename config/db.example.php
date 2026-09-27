<?php
// ─────────────────────────────────────────────────────────────────────────────
// SafeSphere — Database Configuration
// ─────────────────────────────────────────────────────────────────────────────
// INSTRUCTIONS:
//   1. Copy this file:   cp config/db.example.php config/db.php
//   2. Edit config/db.php with your local MySQL credentials.
//   3. NEVER commit config/db.php to Git (it's in .gitignore).
// ─────────────────────────────────────────────────────────────────────────────

define('DB_HOST', 'localhost');       // Usually 'localhost' on XAMPP
define('DB_NAME', 'safesphere');      // The database name (create it first)
define('DB_USER', 'root');            // XAMPP default MySQL user
define('DB_PASS', '');                // XAMPP default has no password — change if yours does

function get_db() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            error_log('SafeSphere DB Error: ' . $e->getMessage());
            die("We're experiencing database connectivity issues. Please try again in a moment.");
        }
    }
    return $pdo;
}
