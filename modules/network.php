<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
$user = current_user();

$scenarios = require __DIR__ . '/../includes/data/network_data.php';
$by_id = [];
foreach ($scenarios as $s) { $by_id[$s['check_id']] = $s; }

// Handle AJAX status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ajax'])) {
    header('Content-Type: application/json');
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        echo json_encode(['error' => 'csrf']); exit;
    }
    $check_id = $_POST['check_id'] ?? '';
    $status   = $_POST['status'] ?? '';
    if (isset($by_id[$check_id]) && in_array($status, ['pass','fail','pending'], true)) {
        $pdo  = get_db();
        // Get previous status
        $prev = $pdo->prepare("SELECT status FROM audit_results WHERE user_id=? AND check_id=?");
        $prev->execute([$user['id'], $check_id]);
        $old_status = $prev->fetchColumn() ?: 'pending';

        // Upsert
        $stmt = $pdo->prepare(
            "INSERT INTO audit_results (user_id, check_id, status) VALUES (?,?,?)
             ON DUPLICATE KEY UPDATE status=VALUES(status)"
        );
        $stmt->execute([$user['id'], $check_id, $status]);

        // Award points only if first time marking (was 'pending')
        $item = $by_id[$check_id];
        if ($old_status === 'pending') {
            $pts = $status === 'pass' ? $item['points_pass'] : ($status === 'fail' ? $item['points_fail'] : 0);
            if ($pts !== 0) { award_points($user['id'], $pts); }
        }

        // Return updated audit score + cyber score
        $audit_score = $pdo->prepare("SELECT SUM(CASE WHEN ar.status='pass' THEN nd.points ELSE 0 END) FROM audit_results ar WHERE ar.user_id=?");
        // simpler: just count pass * 3
        $passCount = $pdo->prepare("SELECT COUNT(*) FROM audit_results WHERE user_id=? AND status='pass'");
        $passCount->execute([$user['id']]);
        $score = (int)$passCount->fetchColumn() * 3;

        $csRow = $pdo->prepare("SELECT cyber_score FROM users WHERE id=?");
        $csRow->execute([$user['id']]);
        $cs = (int)$csRow->fetchColumn();

        echo json_encode(['score' => $score, 'cyber_score' => $cs]);
    } else {
        echo json_encode(['error' => 'invalid']);
    }
    exit;
}

// Load current audit status for all checks
$statusMap = [];
$rows = get_db()->prepare("SELECT check_id, status FROM audit_results WHERE user_id=?");
$rows->execute([$user['id']]);
foreach ($rows->fetchAll() as $r) {
    $statusMap[$r['check_id']] = $r['status'];
}

// Compute audit score
$passCount = 0;
foreach ($statusMap as $st) { if ($st === 'pass') $passCount++; }
$auditScore    = $passCount * 3;
$auditTotal    = count($scenarios) * 3;
$auditPct      = $auditTotal > 0 ? round($auditScore / $auditTotal * 100) : 0;
$auditAnswered = count(array_filter($statusMap, fn($s) => $s !== 'pending'));

$page_title    = 'Module 4: Network Self-Audit';
$page_subtitle = 'Check your home network security and fix vulnerabilities step by step';
$active        = 'network';
$base          = '../';
include __DIR__ . '/../includes/header.php';
?>

<meta name="csrf_meta" content="<?= e(csrf_token()) ?>">

<div class="flex-between mb-16">
    <div class="text-sm text-muted"><?= $auditAnswered ?> of <?= count($scenarios) ?> checks completed</div>
    <div class="progress-track" style="width:200px;">
        <div class="progress-fill <?= $auditPct >= 70 ? 'green' : '' ?>" style="width:<?= $auditAnswered/count($scenarios)*100 ?>%"></div>
    </div>
</div>

<div class="audit-grid">

    <!-- Sidebar: Score card -->
    <div>
        <div class="card card-pad audit-progress-card">
            <div class="audit-score-circle">
                <div class="audit-score-num" id="audit-total-score"><?= $auditScore ?></div>
                <div class="audit-score-label">/ <?= $auditTotal ?> Audit Points</div>
                <div class="progress-track mt-16">
                    <div class="progress-fill <?= $auditPct >= 70 ? 'green' : ($auditPct >= 40 ? '' : 'red') ?>" style="width:<?= $auditPct ?>%"></div>
                </div>
                <div class="text-xs text-muted mt-8"><?= $auditPct ?>% complete</div>
            </div>
            <hr class="divider">
            <div class="text-sm font-bold mb-8">Checklist Summary</div>
            <?php foreach ($scenarios as $s):
                $st = $statusMap[$s['check_id']] ?? 'pending';
            ?>
            <div class="flex-between text-sm" style="padding:6px 0;border-bottom:1px solid var(--border);">
                <span><?= e($s['icon']) ?> <?= e($s['title']) ?></span>
                <span class="badge <?= $st === 'pass' ? 'badge-green' : ($st === 'fail' ? 'badge-red' : 'badge-gray') ?>" style="font-size:10px;">
                    <?= $st === 'pass' ? '✓ Pass' : ($st === 'fail' ? '✕ Fail' : 'Pending') ?>
                </span>
            </div>
            <?php endforeach; ?>
            <div class="mt-16 alert alert-info" style="margin-bottom:0;font-size:12.5px;">
                Each <strong>Pass</strong> = +<?= $scenarios[0]['points_pass'] ?> pts · Each <strong>Fail</strong> = <?= $scenarios[0]['points_fail'] ?> pts (first-time only)
            </div>
        </div>

        <!-- Live Network Diagnostics Widget -->
        <div class="card card-pad mt-16" id="live-net-widget">
            <div class="flex-between mb-8">
                <span class="badge badge-indigo">Live Perimeter Audit</span>
                <span class="badge badge-green" id="net-live-badge">Querying…</span>
            </div>
            <h4 style="font-size:14.5px;font-weight:700;margin-bottom:8px;">Your Connection Profile</h4>
            <div id="net-live-details" style="font-size:12.5px;color:var(--text-muted);line-height:1.6;">
                Detecting public IP and carrier…
            </div>
        </div>
    </div>

    <!-- Main: Accordion Checklist -->
    <div>
        <?php foreach ($scenarios as $s):
            $st = $statusMap[$s['check_id']] ?? 'pending';
            $risk_class = 'risk-' . $s['risk'];
        ?>
        <div class="accordion-item" id="acc-<?= e($s['check_id']) ?>">
            <button class="accordion-trigger" type="button">
                <span class="accordion-icon"><?= e($s['icon']) ?></span>
                <div class="accordion-title-wrap">
                    <div class="accordion-title"><?= e($s['title']) ?></div>
                    <span class="accordion-risk <?= $risk_class ?>"><?= ucfirst($s['risk']) ?> risk</span>
                </div>
                <span class="accordion-status-badge badge <?= $st === 'pass' ? 'badge-green' : ($st === 'fail' ? 'badge-red' : 'badge-gray') ?>">
                    <?= $st === 'pass' ? '✓ Pass' : ($st === 'fail' ? '✕ Fail' : 'Pending') ?>
                </span>
                <span class="accordion-arrow">▼</span>
            </button>
            <div class="accordion-body">
                <div class="accordion-body-section">
                    <h4>What is this?</h4>
                    <p><?= e($s['description']) ?></p>
                </div>
                <div class="accordion-body-section">
                    <h4>Why it matters</h4>
                    <p><?= e($s['why_matters']) ?></p>
                </div>
                <div class="accordion-body-section">
                    <h4>How to fix it</h4>
                    <div class="howto-steps"><?= e($s['how_to']) ?></div>
                </div>
                <div class="audit-actions">
                    <button class="btn btn-success btn-sm audit-action-btn"
                            data-check="<?= e($s['check_id']) ?>" data-status="pass"
                            <?= $st === 'pass' ? 'disabled' : '' ?>>
                        ✓ Mark as Pass
                    </button>
                    <button class="btn btn-danger btn-sm audit-action-btn"
                            data-check="<?= e($s['check_id']) ?>" data-status="fail"
                            <?= $st === 'fail' ? 'disabled' : '' ?>>
                        ✕ Mark as Fail / Not Done
                    </button>
                    <?php if ($st !== 'pending'): ?>
                    <button class="btn btn-outline btn-sm audit-action-btn"
                            data-check="<?= e($s['check_id']) ?>" data-status="pending">
                        ↩ Reset
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var details = document.getElementById('net-live-details');
    var badge = document.getElementById('net-live-badge');
    if (!details || !badge) return;

    fetch('../api/threat_intel.php?action=ip_lookup')
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                details.textContent = 'Perimeter scan unavailable in local sandbox mode.';
                badge.textContent = 'Localhost';
                badge.className = 'badge badge-gray';
                return;
            }
            badge.className = 'badge ' + (data.is_hosting_provider ? 'badge-amber' : 'badge-green');
            badge.textContent = data.is_hosting_provider ? 'Datacenter / VPN' : 'Consumer Broadband';

            details.innerHTML = '<div><strong>Public IP:</strong> <code style="font-family:\'JetBrains Mono\',monospace;font-weight:700;">' + data.ip + '</code></div>'
                + '<div><strong>ISP:</strong> ' + data.isp + '</div>'
                + '<div><strong>Location:</strong> ' + data.city + ', ' + data.country + '</div>'
                + '<div style="margin-top:6px;font-size:11.5px;color:var(--text-muted);line-height:1.5;">' + data.threat_assessment + '</div>';
        })
        .catch(() => {
            details.textContent = 'Unable to reach IP intelligence API.';
            badge.textContent = 'Offline';
            badge.className = 'badge badge-gray';
        });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
