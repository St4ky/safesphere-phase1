<?php
// Returns ['attempted' => n, 'correct' => n, 'total_points' => n] for a module for a given user.
function get_module_stats($user_id, $module_key) {
    $stmt = get_db()->prepare(
        "SELECT COUNT(*) as attempted,
                SUM(is_correct) as correct,
                SUM(points_awarded) as total_points
         FROM attempts WHERE user_id = ? AND module_key = ?"
    );
    $stmt->execute([$user_id, $module_key]);
    $row = $stmt->fetch();
    return [
        'attempted' => (int)($row['attempted'] ?? 0),
        'correct'   => (int)($row['correct'] ?? 0),
        'total_points' => (int)($row['total_points'] ?? 0),
    ];
}

// Has the user already answered this specific scenario? Returns the attempt row or null.
function get_attempt($user_id, $module_key, $scenario_id) {
    $stmt = get_db()->prepare(
        "SELECT * FROM attempts WHERE user_id = ? AND module_key = ? AND scenario_id = ? LIMIT 1"
    );
    $stmt->execute([$user_id, $module_key, $scenario_id]);
    return $stmt->fetch() ?: null;
}

// Records an attempt (only if not already answered) and awards points.
function record_attempt($user_id, $module_key, $scenario_id, $user_choice, $is_correct, $points) {
    if (get_attempt($user_id, $module_key, $scenario_id)) {
        return false; // already answered, don't double count
    }
    $stmt = get_db()->prepare(
        "INSERT INTO attempts (user_id, module_key, scenario_id, user_choice, is_correct, points_awarded)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([$user_id, $module_key, $scenario_id, $user_choice, $is_correct ? 1 : 0, $points]);
    if ($points != 0) {
        award_points($user_id, $points);
    }
    check_and_award_badges($user_id);
    return true;
}

function time_ago($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return $diff . 's ago';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    return floor($diff / 86400) . 'd ago';
}

// ── Badges & Achievements System ──────────────────────────────────────────────
function get_user_badges($user_id) {
    $pdo = get_db();
    $stmt = $pdo->prepare("SELECT badge_key, earned_at FROM user_badges WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
}

function check_and_award_badges($user_id) {
    $pdo = get_db();
    $awarded = [];
    $existing = get_user_badges($user_id);

    $badge_award = function($key) use ($pdo, $user_id, &$awarded, $existing) {
        if (!isset($existing[$key])) {
            $ins = $pdo->prepare("INSERT IGNORE INTO user_badges (user_id, badge_key) VALUES (?, ?)");
            $ins->execute([$user_id, $key]);
            $awarded[] = $key;
        }
    };

    // Rule 1: First Step (Completed at least 1 attempt)
    $attCount = (int)$pdo->query("SELECT COUNT(*) FROM attempts WHERE user_id = " . (int)$user_id)->fetchColumn();
    if ($attCount >= 1) $badge_award('first_step');

    // Rule 2: Sharpshooter (At least 10 correct attempts)
    $corrCount = (int)$pdo->query("SELECT COUNT(*) FROM attempts WHERE user_id = " . (int)$user_id . " AND is_correct = 1")->fetchColumn();
    if ($corrCount >= 10) $badge_award('sharpshooter');

    // Rule 3: High Sentinel (Cyber Score >= 80)
    $score = (int)$pdo->query("SELECT cyber_score FROM users WHERE id = " . (int)$user_id)->fetchColumn();
    if ($score >= 80) $badge_award('sentinel');

    // Rule 4: Streak Master (Streak >= 3)
    $streak = (int)$pdo->query("SELECT streak_count FROM users WHERE id = " . (int)$user_id)->fetchColumn();
    if ($streak >= 3) $badge_award('streak_master');

    // Rule 5: Forensic Specialist (At least 3 forensic analyses run)
    $forCount = (int)$pdo->query("SELECT COUNT(*) FROM forensic_logs WHERE user_id = " . (int)$user_id)->fetchColumn();
    if ($forCount >= 3) $badge_award('forensic_scout');

    return $awarded;
}
