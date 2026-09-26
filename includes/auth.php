<?php
require_once __DIR__ . '/../config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function current_user($force_refresh = false) {
    if (!is_logged_in()) return null;
    static $user = null;
    if ($user === null || $force_refresh) {
        $stmt = get_db()->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if ($user) {
            // streak update: if last_active_date wasn't today, bump/reset streak
            $today = date('Y-m-d');
            if ($user['last_active_date'] !== $today) {
                $yesterday = date('Y-m-d', strtotime('-1 day'));
                $new_streak = ($user['last_active_date'] === $yesterday) ? $user['streak_count'] + 1 : 1;
                $upd = get_db()->prepare("UPDATE users SET streak_count = ?, last_active_date = ? WHERE id = ?");
                $upd->execute([$new_streak, $today, $user['id']]);
                $user['streak_count'] = $new_streak;
                $user['last_active_date'] = $today;
            }
        }
    }
    return $user;
}

// Adds points to the logged-in user's cyber_score (capped 0-100) and returns new score.
function award_points($user_id, $points) {
    $pdo = get_db();
    $stmt = $pdo->prepare("SELECT cyber_score FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $current = (int)$stmt->fetchColumn();
    $new_score = max(0, min(100, $current + $points));
    $upd = $pdo->prepare("UPDATE users SET cyber_score = ? WHERE id = ?");
    $upd->execute([$new_score, $user_id]);
    return $new_score;
}

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}
