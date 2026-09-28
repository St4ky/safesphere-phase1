<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
$user = current_user();

$scenarios = require __DIR__ . '/../includes/data/otp_data.php';
$by_id = [];
foreach ($scenarios as $s) { $by_id[$s['id']] = $s; }

$flash = '';

// Handle verdict submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $flash = 'Session expired, please try again.';
    } else {
        $sid    = $_POST['scenario_id'] ?? '';
        $choice = $_POST['choice'] ?? '';
        if (isset($by_id[$sid]) && isset($by_id[$sid]['options'][$choice])) {
            $s = $by_id[$sid];
            $correct = ($choice === $s['correct_option']);
            $points  = $correct ? 10 : -5;
            record_attempt($user['id'], 'otp', $sid, $choice, $correct, $points);
            $user = current_user(true);
        }
    }
}

$selected_id = $_GET['id'] ?? null;
if (!$selected_id || !isset($by_id[$selected_id])) {
    $selected_id = $scenarios[0]['id'];
    foreach ($scenarios as $s) {
        if (!get_attempt($user['id'], 'otp', $s['id'])) { $selected_id = $s['id']; break; }
    }
}
$selected         = $by_id[$selected_id];
$selected_attempt = get_attempt($user['id'], 'otp', $selected_id);
$stats            = get_module_stats($user['id'], 'otp');

$page_title    = 'Module 5: OTP & Voice Scam Simulator';
$page_subtitle = 'Defend against phone call vishing, SMS code hijacking, and SIM swap attempts';
$active        = 'otp';
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

<div class="grid grid-2" style="gap:24px; align-items:start;">
    <!-- Scenario List -->
    <div class="flex flex-col gap-12">
        <?php foreach ($scenarios as $s):
            $att = get_attempt($user['id'], 'otp', $s['id']);
        ?>
        <a href="?id=<?= e($s['id']) ?>" class="card card-pad <?= $s['id'] === $selected_id ? 'active' : '' ?>" style="display:block; border-left:4px solid <?= $att ? ($att['is_correct'] ? 'var(--green)' : 'var(--red)') : 'var(--indigo)' ?>; text-decoration:none;">
            <div class="flex-between mb-8">
                <span class="badge badge-gray" style="font-size:11px;"><?= e($s['channel']) ?></span>
                <span class="text-xs text-muted"><?= e($s['time']) ?></span>
            </div>
            <div class="font-bold text-sm mb-4"><?= e($s['caller_name']) ?></div>
            <div class="text-xs text-muted font-mono mb-8"><?= e($s['caller_id']) ?></div>
            <?php if ($att): ?>
                <span class="badge <?= $att['is_correct'] ? 'badge-green' : 'badge-red' ?>" style="font-size:10px;">
                    <?= $att['is_correct'] ? '✓ Passed' : '✕ Compromised' ?>
                </span>
            <?php else: ?>
                <span class="badge badge-amber" style="font-size:10px;">Pending</span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Active Scenario Interactive Device View -->
    <div class="card card-pad">
        <div class="flex-between mb-16 pb-12" style="border-bottom:1px solid var(--border);">
            <div>
                <span class="badge badge-indigo mb-6"><?= e($selected['channel']) ?></span>
                <h3 style="font-size:18px;"><?= e($selected['caller_name']) ?></h3>
                <div class="text-sm font-mono text-muted"><?= e($selected['caller_id']) ?> &middot; <?= e($selected['time']) ?></div>
            </div>
            <?php if ($selected_attempt): ?>
            <div class="badge <?= $selected['is_scam'] ? 'badge-red' : 'badge-green' ?>" style="font-size:12px;">
                <?= $selected['is_scam'] ? '⚠️ Threat Vector' : '🛡️ Legitimate Flow' ?>
            </div>
            <?php else: ?>
            <div class="badge badge-indigo" style="font-size:12px;">
                Active Simulation
            </div>
            <?php endif; ?>
        </div>

        <!-- Phone Audio/Dialogue Box -->
        <div class="card card-pad mb-16" style="background:var(--bg); border:1px solid var(--border); border-left:4px solid var(--indigo);">
            <div class="text-xs font-bold text-muted uppercase mb-8">Call / Audio Transcript:</div>
            <p style="font-size:14.5px; line-height:1.7; white-space:pre-wrap; margin:0;"><?= e($selected['dialogue']) ?></p>
        </div>

        <!-- Simulated SMS Notification Box -->
        <div class="card card-pad mb-20" style="background:var(--surface); border:2px dashed var(--amber); border-radius:var(--radius-sm);">
            <div class="flex-between mb-6">
                <span style="font-size:12px; font-weight:700; color:var(--amber);">💬 New SMS Pop-Up (From: <?= e($selected['sms_popup']['sender']) ?>)</span>
                <span class="text-xs text-muted">Just now</span>
            </div>
            <div class="font-mono text-sm" style="background:var(--bg); padding:10px 12px; border-radius:6px; color:var(--text); line-height:1.5; border:1px solid var(--border);">
                <?= e($selected['sms_popup']['text']) ?>
            </div>
        </div>

        <!-- Decision Form or Result -->
        <?php if ($selected_attempt): ?>
            <div class="verdict-panel">
                <div class="flex gap-8 mb-12" style="align-items:center;">
                    <span class="badge <?= $selected_attempt['is_correct'] ? 'badge-green' : 'badge-red' ?>">
                        <?= $selected_attempt['is_correct'] ? '✓ Safe Decision' : '✕ Security Compromise' ?>
                    </span>
                    <span class="text-sm font-bold">
                        <?= $selected_attempt['is_correct'] ? '+10 Cyber Score' : '-5 Cyber Score' ?>
                    </span>
                </div>
                <div class="text-sm mb-12" style="line-height:1.6;">
                    <strong>Insight:</strong> <?= e($selected['explanation']) ?>
                </div>
                <div class="text-xs font-bold text-muted mb-6">KEY RED FLAGS IDENTIFIED:</div>
                <ul style="padding-left:18px; margin:0; font-size:13.5px; line-height:1.6;">
                    <?php foreach ($selected['red_flags'] as $rf): ?>
                        <li class="mb-4"><?= e($rf) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="card card-pad" style="background:var(--bg);">
                <div class="font-bold text-sm mb-12">What is your immediate response?</div>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="scenario_id" value="<?= e($selected['id']) ?>">
                    <div class="flex flex-col gap-10">
                        <?php foreach ($selected['options'] as $optKey => $optLabel): ?>
                        <label class="card card-pad flex gap-12" style="cursor:pointer; align-items:center; background:var(--surface);">
                            <input type="radio" name="choice" value="<?= e($optKey) ?>" required>
                            <span class="text-sm font-bold"><?= e($optLabel) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg mt-16">Submit Decision</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
