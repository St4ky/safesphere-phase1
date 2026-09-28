<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$user = current_user();
$pdo  = get_db();

// ── Certificate criteria (same logic as certificates.php) ──────────────────
function cert_progress_all($user_id, $pdo): array {
    $s_ph = get_module_stats($user_id, 'phishing');
    $s_up = get_module_stats($user_id, 'upi');
    $s_se = get_module_stats($user_id, 'socialeng');
    $s_ot = get_module_stats($user_id, 'otp');
    $s_df = get_module_stats($user_id, 'deepfake');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM audit_results WHERE user_id=? AND status='pass'");
    $stmt->execute([$user_id]);
    $netPass = (int)$stmt->fetchColumn();

    return [
        'phishing_specialist' => ['ok' => $s_ph['attempted'] >= 9 && $s_ph['correct'] >= 7, 'val' => $s_ph['correct'], 'max' => 7],
        'upi_guardian'        => ['ok' => $s_up['attempted'] >= 6 && $s_up['correct'] >= 5, 'val' => $s_up['correct'], 'max' => 5],
        'social_eng_proof'    => ['ok' => $s_se['attempted'] >= 4 && $s_se['correct'] >= 3, 'val' => $s_se['correct'], 'max' => 3],
        'network_defender'    => ['ok' => $netPass >= 7, 'val' => $netPass, 'max' => 7],
        'otp_defender'        => ['ok' => $s_ot['attempted'] >= 5 && $s_ot['correct'] >= 4, 'val' => $s_ot['correct'], 'max' => 4],
        'deepfake_analyst'    => ['ok' => $s_df['attempted'] >= 4 && $s_df['correct'] >= 3, 'val' => $s_df['correct'], 'max' => 3],
    ];
}

$cert_progress = cert_progress_all($user['id'], $pdo);
$certs_earned  = array_filter($cert_progress, fn($c) => $c['ok']);
$all_modules   = count($certs_earned) === 6;

// ── Module stats ───────────────────────────────────────────────────────────
$modules = [
    'phishing'  => ['label' => 'Phishing & Domain Spoofing', 'icon' => '📧', 'total' => 9],
    'upi'       => ['label' => 'UPI Fraud & Payments',       'icon' => '📱', 'total' => 6],
    'socialeng' => ['label' => 'Social Engineering & Vishing', 'icon' => '🗣️', 'total' => 4],
    'network'   => ['label' => 'Network Hardening & Audit',  'icon' => '📶', 'total' => 7],
    'otp'       => ['label' => 'OTP Hijacking & SIM-Swap',   'icon' => '📲', 'total' => 5],
    'deepfake'  => ['label' => 'Deepfake & AI Voice Clone',  'icon' => '🤖', 'total' => 4],
];
$modStats = [];
foreach ($modules as $k => $m) { $modStats[$k] = get_module_stats($user['id'], $k); }
$totalAttempted = array_sum(array_column($modStats, 'attempted'));
$totalCorrect   = array_sum(array_column($modStats, 'correct'));

// ── Forensic logs ──────────────────────────────────────────────────────────
$stmt = $pdo->prepare("SELECT COUNT(*) FROM forensic_logs WHERE user_id=?");
$stmt->execute([$user['id']]);
$forensicCount = (int)$stmt->fetchColumn();

// ── Recent activity ─────────────────────────────────────────────────────────
$recent = $pdo->prepare("SELECT * FROM attempts WHERE user_id=? ORDER BY created_at DESC LIMIT 30");
$recent->execute([$user['id']]);
$recentRows = $recent->fetchAll();

// ── Cert DB rows ────────────────────────────────────────────────────────────
$dbCerts = [];
$certRows = $pdo->prepare("SELECT * FROM certificates WHERE user_id=?");
$certRows->execute([$user['id']]);
foreach ($certRows->fetchAll() as $r) { $dbCerts[$r['cert_key']] = $r; }

$page_title    = 'My Security Report';
$page_subtitle = 'Full performance breakdown — printable';
$active        = 'reports';
$base          = '';
include __DIR__ . '/includes/header.php';
?>

<!-- Print Button -->
<div class="flex-between mb-20">
    <div class="text-sm text-muted">Generated <?= date('d M Y, H:i') ?> · Member since <?= date('d M Y', strtotime($user['created_at'])) ?></div>
    <button onclick="window.print()" class="btn btn-outline btn-sm" id="print-report-btn">🖨️ Print / Save as PDF</button>
</div>

<!-- Report Container (styled for print too) -->
<div id="report-content">

<!-- Header Banner -->
<div style="background:linear-gradient(135deg,var(--indigo) 0%,#6366f1 100%);border-radius:var(--radius-lg);padding:28px 32px;color:white;margin-bottom:24px;display:flex;justify-content:space-between;align-items:center;">
    <div>
        <div style="font-size:13px;opacity:.8;margin-bottom:4px;text-transform:uppercase;letter-spacing:.06em;">SafeSphere Security Report</div>
        <div style="font-size:28px;font-weight:800;">🛡️ <?= e($user['name']) ?></div>
        <div style="opacity:.8;margin-top:6px;font-size:14px;"><?= e($user['email']) ?> &nbsp;·&nbsp; <?= e($user['role']) ?></div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:52px;font-weight:800;line-height:1;"><?= (int)$user['cyber_score'] ?></div>
        <div style="font-size:13px;opacity:.8;">Cyber Score</div>
        <div style="margin-top:8px;">
            <span style="background:rgba(255,255,255,.2);border-radius:99px;padding:4px 12px;font-size:12px;font-weight:700;">
                🔥 <?= (int)$user['streak_count'] ?> day streak
            </span>
        </div>
    </div>
</div>

<!-- Top Stats -->
<div class="grid grid-4 mb-24">
    <div class="stat-card">
        <div class="stat-card-number"><?= $totalAttempted ?></div>
        <div class="stat-card-label">Scenarios Attempted</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number"><?= $totalAttempted > 0 ? round($totalCorrect/$totalAttempted*100) : 0 ?>%</div>
        <div class="stat-card-label">Overall Accuracy</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number"><?= count($dbCerts) ?>/7</div>
        <div class="stat-card-label">Certificates Earned</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number"><?= $forensicCount ?></div>
        <div class="stat-card-label">Forensic Analyses Run</div>
    </div>
</div>

<!-- Module Breakdown -->
<div class="card card-pad mb-24">
    <h3 class="mb-20">📊 Module Performance Breakdown</h3>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Module</th>
                <th>Completed</th>
                <th>Correct</th>
                <th>Accuracy</th>
                <th>Points</th>
                <th>Progress</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($modules as $key => $m):
            $s   = $modStats[$key];
            $acc = $s['attempted'] > 0 ? round($s['correct']/$s['attempted']*100) : 0;
            $pct = $m['total'] > 0 ? round($s['attempted']/$m['total']*100) : 0;
        ?>
        <tr style="border-top:1px solid var(--border);">
            <td style="padding:12px 14px;font-weight:700;"><?= $m['icon'] ?> <?= e($m['label']) ?></td>
            <td style="padding:12px 14px;"><?= $s['attempted'] ?>/<?= $m['total'] ?></td>
            <td style="padding:12px 14px;"><?= $s['correct'] ?></td>
            <td style="padding:12px 14px;">
                <span style="font-weight:700;color:<?= $acc >= 70 ? 'var(--green)' : ($acc >= 40 ? 'var(--amber)' : 'var(--red)') ?>;">
                    <?= $acc ?>%
                </span>
            </td>
            <td style="padding:12px 14px;font-weight:700;color:<?= $s['total_points'] >= 0 ? 'var(--green)' : 'var(--red)' ?>;">
                <?= ($s['total_points'] >= 0 ? '+' : '') . $s['total_points'] ?>
            </td>
            <td style="padding:12px 14px;min-width:120px;">
                <div class="progress-track">
                    <div class="progress-fill" style="width:<?= $pct ?>%;"></div>
                </div>
                <div style="font-size:11px;color:var(--text-faint);margin-top:3px;"><?= $pct ?>%</div>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>

<!-- Certificates -->
<div class="card card-pad mb-24">
    <h3 class="mb-16">🏅 Certificates & Credentials</h3>
    <?php
    $cert_defs = [
        ['phishing_specialist', '🏅', 'Phishing Defense Specialist', 'phishing'],
        ['upi_guardian',        '🛡️', 'UPI Guardian',               'upi'],
        ['social_eng_proof',    '🗣️', 'Social Engineer Proof',       'socialeng'],
        ['network_defender',    '📶', 'Network Defender',            'network'],
        ['otp_defender',        '📱', 'OTP & SIM-Swap Defender',     'otp'],
        ['deepfake_analyst',    '🤖', 'Synthetic Media Analyst',      'deepfake'],
        ['cyber_champion',      '🏆', 'Cyber Awareness Champion',    null],
    ];
    foreach ($cert_defs as [$key, $medal, $title, $moduleKey]):
        $earned = isset($dbCerts[$key]);
        $dbCert = $dbCerts[$key] ?? null;
    ?>
    <div class="flex-between" style="padding:12px 0;border-bottom:1px solid var(--border);align-items:center;">
        <div class="flex gap-12" style="align-items:center;">
            <span style="font-size:22px;"><?= $medal ?></span>
            <div>
                <div style="font-weight:700;font-size:14px;"><?= e($title) ?></div>
                <?php if ($earned && $dbCert): ?>
                <div style="font-size:12px;color:var(--text-muted);">
                    ID: <code style="font-family:'JetBrains Mono',monospace;"><?= e($dbCert['credential_id']) ?></code>
                    · Issued <?= date('d M Y', strtotime($dbCert['issue_date'])) ?>
                </div>
                <?php else: ?>
                <div style="font-size:12px;color:var(--text-muted);">Not yet earned</div>
                <?php endif; ?>
            </div>
        </div>
        <span class="badge <?= $earned ? 'badge-green' : 'badge-gray' ?>">
            <?= $earned ? '✓ Earned' : '🔒 Locked' ?>
        </span>
    </div>
    <?php endforeach; ?>
</div>

<!-- Activity Log (last 30) -->
<div class="card card-pad mb-24">
    <h3 class="mb-16">📋 Activity Log (Last 30 Actions)</h3>
    <?php if (empty($recentRows)): ?>
    <p class="text-sm text-muted">No activity yet.</p>
    <?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Module</th>
                <th>Scenario</th>
                <th>Result</th>
                <th>Points</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($recentRows as $r): ?>
        <tr style="border-top:1px solid var(--border);">
            <td style="padding:8px 12px;"><?= $modules[$r['module_key']]['icon'] ?? '🔷' ?> <?= e($modules[$r['module_key']]['label'] ?? $r['module_key']) ?></td>
            <td style="padding:8px 12px;font-family:monospace;font-size:12px;color:var(--text-muted);"><?= e($r['scenario_id']) ?></td>
            <td style="padding:8px 12px;">
                <span class="badge <?= $r['is_correct'] ? 'badge-green' : 'badge-red' ?>" style="font-size:10px;">
                    <?= $r['is_correct'] ? '✓ Correct' : '✕ Wrong' ?>
                </span>
            </td>
            <td style="padding:8px 12px;font-weight:700;color:<?= $r['points_awarded'] >= 0 ? 'var(--green)' : 'var(--red)' ?>;">
                <?= ($r['points_awarded'] >= 0 ? '+' : '') . (int)$r['points_awarded'] ?>
            </td>
            <td style="padding:8px 12px;color:var(--text-muted);font-size:12px;"><?= date('d M, H:i', strtotime($r['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<!-- Footer note -->
<div style="text-align:center;padding:20px;color:var(--text-faint);font-size:12px;border-top:1px solid var(--border);">
    Generated by SafeSphere &nbsp;·&nbsp; <?= date('d M Y H:i') ?> &nbsp;·&nbsp; Credential verification: safesphere.local/certificates
</div>
</div><!-- #report-content -->

<style>
@media print {
    .app-shell { display: block !important; }
    .sidebar, .topbar, .app-footer, #print-report-btn { display: none !important; }
    .main { min-height: unset; }
    .page { padding: 0 !important; }
    .card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
    body { background: white !important; }
    @page { margin: 20mm; }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
