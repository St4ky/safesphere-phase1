<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
$user = current_user();

$scenarios = require __DIR__ . '/../includes/data/upi_data.php';
$by_id = [];
foreach ($scenarios as $s) { $by_id[$s['id']] = $s; }

$flash = '';

// Handle verdict submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $flash = 'Session expired, please try again.';
    } else {
        $sid    = $_POST['scenario_id'] ?? '';
        $action = $_POST['action'] ?? ''; // 'accept' or 'reject'
        if (isset($by_id[$sid]) && in_array($action, ['accept', 'reject'], true)) {
            $s = $by_id[$sid];
            // Correct = fraud→reject, legit→accept
            $correct = ($s['is_fraud'] && $action === 'reject') || (!$s['is_fraud'] && $action === 'accept');
            $points  = $correct ? 10 : -5;
            record_attempt($user['id'], 'upi', $sid, $action, $correct, $points);
            $user = current_user(true);
        }
    }
}

// Which scenario to show in detail pane
$selected_id = $_GET['id'] ?? null;
if (!$selected_id || !isset($by_id[$selected_id])) {
    $selected_id = $scenarios[0]['id'];
    foreach ($scenarios as $s) {
        if (!get_attempt($user['id'], 'upi', $s['id'])) { $selected_id = $s['id']; break; }
    }
}
$selected         = $by_id[$selected_id];
$selected_attempt = get_attempt($user['id'], 'upi', $selected_id);
$stats            = get_module_stats($user['id'], 'upi');

$page_title    = 'Module 2: UPI Fraud Simulator';
$page_subtitle = 'Spot the scam before you tap — accept or reject UPI collect requests';
$active        = 'upi';
$base          = '../';
include __DIR__ . '/../includes/header.php';
?>

<div class="flex-between mb-16">
    <div class="text-sm text-muted"><?= $stats['attempted'] ?> of <?= count($scenarios) ?> scenarios reviewed &middot; <?= $stats['correct'] ?> correct</div>
    <div class="progress-track" style="width:200px;">
        <div class="progress-fill" style="width:<?= count($scenarios) ? round($stats['attempted']/count($scenarios)*100) : 0 ?>%"></div>
    </div>
</div>

<?php if ($flash): ?><div class="alert alert-error"><?= e($flash) ?></div><?php endif; ?>

<div class="upi-grid">

    <!-- LEFT: Notification List -->
    <div class="upi-list">
        <?php foreach ($scenarios as $s):
            $attempt = get_attempt($user['id'], 'upi', $s['id']);
            $isActive = $s['id'] === $selected_id;
            $classes  = 'upi-notification' . ($isActive ? ' active' : '') . ($attempt ? ' answered' : '');
            if ($attempt) $classes .= $attempt['is_correct'] ? ' legit-answered' : ' fraud-answered';
        ?>
        <a href="?id=<?= e($s['id']) ?>" class="<?= $classes ?>" data-id="<?= e($s['id']) ?>" style="text-decoration:none;display:block;">
            <div class="upi-notif-app">📱 <?= e($s['app']) ?></div>
            <div class="upi-notif-header">
                <div>
                    <div class="upi-notif-sender"><?= e($s['sender_name']) ?></div>
                    <div class="upi-notif-id"><?= e($s['sender_upi']) ?></div>
                </div>
                <div class="upi-notif-amount"><?= e($s['amount']) ?></div>
            </div>
            <div class="upi-notif-note"><?= e(substr($s['note'], 0, 80)) ?>…</div>
            <div class="flex gap-8 mt-8" style="align-items:center;justify-content:space-between;">
                <span class="text-xs text-muted"><?= e($s['time']) ?></span>
                <?php if ($attempt): ?>
                    <span class="badge <?= $attempt['is_correct'] ? 'badge-green' : 'badge-red' ?>">
                        <?= $attempt['is_correct'] ? '✓ Correct' : '✕ Wrong' ?>
                    </span>
                <?php elseif ($s['urgency'] === 'high'): ?>
                    <span class="badge badge-red" style="font-size:9px;">URGENT</span>
                <?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- RIGHT: Phone Detail -->
    <div>
        <div class="phone-frame">
            <div class="phone-screen">
                <div class="phone-header">
                    <div class="phone-header-app">📱 <?= e($selected['app']) ?></div>
                    <div class="phone-header-icons">
                        <span>📶</span><span>🔋</span>
                        <span><?= date('H:i') ?></span>
                    </div>
                </div>
                <div class="phone-body">
                    <div class="phone-collect-label">💸 Collect Request</div>
                    <div class="phone-amount-display <?= !$selected['is_fraud'] ? 'legit' : '' ?>"><?= e($selected['amount']) ?></div>
                    <div class="phone-from">from <strong><?= e($selected['sender_name']) ?></strong></div>
                    <?php if ($selected['urgency'] === 'high'): ?>
                    <div class="phone-urgency">⚠️ Action required — time sensitive</div>
                    <?php endif; ?>
                    <div class="phone-note"><?= e($selected['note']) ?></div>

                    <?php if (!$selected_attempt): ?>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="scenario_id" value="<?= e($selected['id']) ?>">
                        <div class="phone-actions">
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">✕ Reject</button>
                            <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">✓ Accept</button>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="phone-actions">
                        <span class="btn btn-outline btn-sm" style="flex:1;opacity:.5;cursor:default;">✕ Reject</span>
                        <span class="btn btn-outline btn-sm" style="flex:1;opacity:.5;cursor:default;">✓ Accept</span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Verdict / Explanation Panel -->
        <div class="upi-verdict-panel mt-16">
            <?php if ($selected_attempt): ?>
                <div class="flex gap-8 mb-16" style="align-items:center;">
                    <span class="badge <?= $selected_attempt['is_correct'] ? 'badge-green' : 'badge-red' ?>" style="font-size:13px;">
                        <?= $selected_attempt['is_correct'] ? '🛡️ Correct call!' : '⚠️ Wrong call' ?>
                    </span>
                    <span class="badge <?= $selected['is_fraud'] ? 'badge-red' : 'badge-green' ?>">
                        This was <?= $selected['is_fraud'] ? 'FRAUD' : 'LEGITIMATE' ?>
                    </span>
                    <span class="badge <?= $selected_attempt['points_awarded'] >= 0 ? 'badge-green' : 'badge-red' ?>">
                        <?= ($selected_attempt['points_awarded'] >= 0 ? '+' : '') . (int)$selected_attempt['points_awarded'] ?> pts
                    </span>
                </div>
                <?php if (!empty($selected['red_flags'])): ?>
                <h4 class="mb-8">🚩 Red Flags Explained:</h4>
                <ul style="padding-left:18px;list-style:disc;">
                    <?php foreach ($selected['red_flags'] as $flag): ?>
                    <li class="text-sm mt-8" style="color:var(--text-muted);"><?= e($flag) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="text-sm text-muted">✅ This was a legitimate UPI request — safe to accept.</p>
                <?php endif; ?>
                <div class="alert alert-info mt-16" style="margin-bottom:0;">
                    <strong>💡 Key Takeaway:</strong> <?= e($selected['tip']) ?>
                </div>
            <?php else: ?>
                <h3 class="mb-8">Should you Accept or Reject?</h3>
                <p class="text-sm text-muted mb-16">Analyze the collect request above carefully — sender name, UPI ID, amount, and the message note. Then tap Reject or Accept on the phone screen.</p>
                <div class="flex gap-16" style="font-size:13px;color:var(--text-muted);">
                    <span>📋 Type: <strong><?= ucwords(str_replace('_', ' ', $selected['type'])) ?></strong></span>
                    <span>⏰ Urgency: <strong><?= ucfirst($selected['urgency']) ?></strong></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
