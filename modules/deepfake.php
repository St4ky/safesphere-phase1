<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
$user = current_user();

$scenarios = require __DIR__ . '/../includes/data/deepfake_data.php';
$by_id = [];
foreach ($scenarios as $s) { $by_id[$s['id']] = $s; }

$flash = '';

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
            record_attempt($user['id'], 'deepfake', $sid, $choice, $correct, $points);
            $user = current_user(true);
        }
    }
}

$selected_id = $_GET['id'] ?? null;
if (!$selected_id || !isset($by_id[$selected_id])) {
    $selected_id = $scenarios[0]['id'];
    foreach ($scenarios as $s) {
        if (!get_attempt($user['id'], 'deepfake', $s['id'])) { $selected_id = $s['id']; break; }
    }
}
$selected         = $by_id[$selected_id];
$selected_attempt = get_attempt($user['id'], 'deepfake', $selected_id);
$stats            = get_module_stats($user['id'], 'deepfake');

$page_title    = 'Module 6: Deepfake & AI Fraud Awareness';
$page_subtitle = 'Recognize cloned voices, synthetic video extortion, and "Digital Arrest" threats';
$active        = 'deepfake';
$base          = '../';
include __DIR__ . '/../includes/header.php';
?>

<div class="flex-between mb-16">
    <div class="text-sm text-muted"><?= $stats['attempted'] ?> of <?= count($scenarios) ?> scenarios investigated &middot; <?= $stats['correct'] ?> correct</div>
    <div class="progress-track" style="width:200px;">
        <div class="progress-fill" style="width:<?= count($scenarios) ? round($stats['attempted']/count($scenarios)*100) : 0 ?>%"></div>
    </div>
</div>

<?php if ($flash): ?><div class="alert alert-error"><?= e($flash) ?></div><?php endif; ?>

<div class="grid grid-2" style="gap:24px; align-items:start;">
    <div class="flex flex-col gap-12">
        <?php foreach ($scenarios as $s):
            $att = get_attempt($user['id'], 'deepfake', $s['id']);
        ?>
        <a href="?id=<?= e($s['id']) ?>" class="card card-pad <?= $s['id'] === $selected_id ? 'active' : '' ?>" style="display:block; border-left:4px solid <?= $att ? ($att['is_correct'] ? 'var(--green)' : 'var(--red)') : 'var(--indigo)' ?>; text-decoration:none;">
            <div class="flex-between mb-8">
                <span class="badge badge-gray" style="font-size:11px;">🤖 <?= e($s['threat_type']) ?></span>
                <span class="badge <?= $s['urgency'] === 'Extreme' ? 'badge-red' : ($s['urgency'] === 'High' ? 'badge-amber' : 'badge-blue') ?>" style="font-size:10px;">
                    <?= e($s['urgency']) ?> Urgency
                </span>
            </div>
            <div class="font-bold text-sm mb-4"><?= e($s['title']) ?></div>
            <?php if ($att): ?>
                <span class="badge <?= $att['is_correct'] ? 'badge-green' : 'badge-red' ?>" style="font-size:10px;">
                    <?= $att['is_correct'] ? '✓ Defended' : '✕ Compromised' ?>
                </span>
            <?php else: ?>
                <span class="badge badge-amber" style="font-size:10px;">Pending Review</span>
            <?php endif; ?>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="card card-pad">
        <div class="flex-between mb-16 pb-12" style="border-bottom:1px solid var(--border);">
            <div>
                <span class="badge badge-indigo mb-6">AI Threat Simulation</span>
                <h3 style="font-size:18px;"><?= e($selected['title']) ?></h3>
                <div class="text-sm text-muted">Category: <?= e($selected['threat_type']) ?></div>
            </div>
            <?php if ($selected_attempt): ?>
            <span class="badge <?= $selected['is_scam'] ? 'badge-red' : 'badge-green' ?>">
                <?= $selected['is_scam'] ? '⚠️ Fraudulent Scenario' : '🛡️ Authentic Notification' ?>
            </span>
            <?php else: ?>
            <span class="badge badge-indigo">
                Active Simulation
            </span>
            <?php endif; ?>
        </div>

        <div class="feed-monitor mb-16">
            <div class="feed-monitor-header">
                <span>🎙️ Intercepted Media / Video Feed Stream</span>
                <span class="feed-monitor-live">● Live Simulation</span>
            </div>
            <p class="feed-monitor-body"><?= e($selected['audio_transcript']) ?></p>
        </div>

        <div class="card card-pad mb-16" style="background:var(--bg); border:1px solid var(--border);">
            <div class="text-xs font-bold text-muted mb-8 uppercase">Sensory Indicators & Behavioral Cues:</div>
            <ul style="padding-left:18px; margin:0; font-size:13px; line-height:1.6;">
                <?php foreach ($selected['media_indicators'] as $ind): ?>
                    <li class="mb-4"><?= e($ind) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <?php if ($selected_attempt): ?>
            <div class="verdict-panel">
                <div class="flex gap-8 mb-12" style="align-items:center;">
                    <span class="badge <?= $selected_attempt['is_correct'] ? 'badge-green' : 'badge-red' ?>">
                        <?= $selected_attempt['is_correct'] ? '✓ Safe Decision' : '✕ Victim Outcome' ?>
                    </span>
                    <span class="text-sm font-bold">
                        <?= $selected_attempt['is_correct'] ? '+10 Cyber Score' : '-5 Cyber Score' ?>
                    </span>
                </div>
                <div class="text-sm mb-12" style="line-height:1.6;">
                    <strong>Investigation Conclusion:</strong> <?= e($selected['explanation']) ?>
                </div>
                <div class="text-xs font-bold text-muted mb-6">DEFENSIVE PROTOCOLS:</div>
                <ul style="padding-left:18px; margin:0; font-size:13.5px; line-height:1.6;">
                    <?php foreach ($selected['safety_tips'] as $tip): ?>
                        <li class="mb-4"><?= e($tip) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="card card-pad" style="background:var(--bg);">
                <div class="font-bold text-sm mb-12">How do you respond to this situation?</div>
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
                    <button type="submit" class="btn btn-primary btn-block btn-lg mt-16">Submit Defensive Action</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
