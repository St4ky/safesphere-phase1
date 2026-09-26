<?php
// Database connection settings — edit these for your environment.
define('DB_HOST', 'localhost');
define('DB_NAME', 'safesphere');
define('DB_USER', 'root');
define('DB_PASS', '');

function get_db() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
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
