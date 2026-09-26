<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
$user = current_user();

$scenarios = require __DIR__ . '/../includes/data/phishing_data.php';
$by_id = [];
foreach ($scenarios as $s) { $by_id[$s['id']] = $s; }

$flash_error = '';
$just_answered_id = null;

// Handle verdict submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $flash_error = 'Session expired, please try again.';
    } else {
        $sid = $_POST['scenario_id'] ?? '';
        $choice = $_POST['verdict'] ?? ''; // 'phishing' or 'legit'
        if (isset($by_id[$sid]) && in_array($choice, ['phishing', 'legit'], true)) {
            $correct = ($by_id[$sid]['is_phishing'] && $choice === 'phishing')
                    || (!$by_id[$sid]['is_phishing'] && $choice === 'legit');
            $points = $correct ? 10 : -5;
            record_attempt($user['id'], 'phishing', $sid, $choice, $correct, $points);
            $just_answered_id = $sid;
            $user = current_user(true); // force refresh so header shows updated score
        }
    }
}

// Selected email to view (default: first unanswered, else first)
$selected_id = $_GET['id'] ?? $just_answered_id ?? null;
if (!$selected_id || !isset($by_id[$selected_id])) {
    $selected_id = $scenarios[0]['id'];
    foreach ($scenarios as $s) {
        if (!get_attempt($user['id'], 'phishing', $s['id'])) { $selected_id = $s['id']; break; }
    }
}
$selected = $by_id[$selected_id];
$selected_attempt = get_attempt($user['id'], 'phishing', $selected_id);

$stats = get_module_stats($user['id'], 'phishing');

$page_title = 'Module 1: Phishing Defense';
$page_subtitle = 'Sort your inbox — flag phishing, leave legitimate mail alone';
$active = 'phishing';
$base = '../';
include __DIR__ . '/../includes/header.php';
?>

<div class="flex-between mb-16">
    <div class="text-sm text-muted"><?= $stats['attempted'] ?> of <?= count($scenarios) ?> emails reviewed &middot; <?= $stats['correct'] ?> correct</div>
    <div class="progress-track" style="width:200px;">
        <div class="progress-fill" style="width:<?= count($scenarios) ? round($stats['attempted']/count($scenarios)*100) : 0 ?>%"></div>
    </div>
</div>

<?php if ($flash_error): ?><div class="alert alert-error"><?= e($flash_error) ?></div><?php endif; ?>

<div class="inbox-layout">
    <div class="inbox-list" id="inbox-list">
        <?php foreach ($scenarios as $s):
            $answered = get_attempt($user['id'], 'phishing', $s['id']);
            $initials = strtoupper(substr($s['sender_name'], 0, 1));
        ?>
        <a href="?id=<?= e($s['id']) ?>" class="inbox-row <?= $s['id'] === $selected_id ? 'active' : '' ?> <?= $answered ? 'answered' : '' ?>" style="text-decoration:none;color:inherit;">
            <div class="avatar"><?= e($initials) ?></div>
            <div class="inbox-row-body">
                <div class="inbox-row-top">
                    <span class="inbox-sender"><?= e($s['sender_name']) ?></span>
                    <span class="inbox-time"><?= e($s['time']) ?></span>
                </div>
                <div class="inbox-subject"><?= e($s['subject']) ?></div>
                <div class="inbox-preview"><?= e($s['preview']) ?></div>
                <?php if ($answered): ?>
                    <span class="channel-badge" style="background:<?= $answered['is_correct'] ? 'var(--green-light)' : 'var(--red-light)' ?>; color:<?= $answered['is_correct'] ? 'var(--green)' : 'var(--red)' ?>; border:1px solid <?= $answered['is_correct'] ? 'rgba(34,197,94,0.3)' : 'rgba(239,68,68,0.3)' ?>;">
                        <?= $answered['is_correct'] ? '✓ Correct' : '✕ Missed' ?>
                    </span>
                <?php else: ?>
                    <span class="channel-badge">Email · Unread</span>
                <?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="email-detail" id="email-view">
        <div class="email-detail-header">
            <div class="flex-between mb-8" style="align-items:flex-start;">
                <div class="email-detail-subject" style="margin-bottom:0;"><?= e($selected['subject']) ?></div>
                <a href="#inbox-list" class="btn btn-outline btn-sm" style="display:none;font-size:11px;padding:4px 8px;" id="mobile-back-btn">↑ Inbox</a>
            </div>
            <div class="email-meta-row"><strong><?= e($selected['sender_name']) ?></strong> &lt;<?= e($selected['sender_email']) ?>&gt;</div>
            <div class="email-meta-row">To: <?= e($user['email']) ?></div>
            <div class="email-meta-row"><?= e($selected['time']) ?></div>
        </div>

        <div class="email-body"><?= nl2br(e($selected['body'])) ?></div>

        <!-- Live Forensic Inspection Drawer -->
        <div style="margin: 16px 0; padding: 12px 16px; border-radius: 8px; background: var(--bg); border: 1px solid var(--border);">
            <div class="flex-between">
                <div style="font-size: 13px; font-weight: 700; color: var(--text-muted); display:flex; align-items:center; gap:6px;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Forensic Inspection Assistance
                </div>
                <button type="button" class="btn btn-outline btn-sm" id="live-dns-inspect-btn" onclick="inspectSenderDomain('<?= e(preg_replace('/.*@/', '', $selected['sender_email'])) ?>')">
                    Query Sender DNS & SPF →
                </button>
            </div>
            <div id="phishing-dns-result" style="display:none; margin-top:12px; font-size:13px; border-top:1px solid var(--border); padding-top:10px;"></div>
        </div>

        <?php if ($selected_attempt): ?>
            <div class="verdict-panel">
                <div class="flex gap-8" style="align-items:center;">
                    <span class="badge <?= $selected_attempt['is_correct'] ? 'badge-green' : 'badge-red' ?>">
                        <?= $selected_attempt['is_correct'] ? 'Verdict: Correct' : 'Verdict: Incorrect' ?>
                    </span>
                    <span class="badge <?= $selected['is_phishing'] ? 'badge-red' : 'badge-green' ?>">
                        Classification: <?= $selected['is_phishing'] ? 'Malicious Phishing' : 'Legitimate Traffic' ?>
                    </span>
                </div>
                <h4 class="mt-16 mb-8">Threat Indicators & Forensic Analysis:</h4>
                <ul style="padding-left:18px; list-style:disc;">
                    <?php foreach ($selected['red_flags'] as $flag): ?>
                        <li class="text-sm mt-8"><?= e($flag) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else: ?>
            <div class="verdict-panel">
                <div class="font-bold text-sm mb-8">Triage Decision: Is this email phishing or legitimate?</div>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="scenario_id" value="<?= e($selected['id']) ?>">
                    <div class="verdict-actions">
                        <button type="submit" name="verdict" value="phishing" class="btn btn-danger">Flag as Phishing</button>
                        <button type="submit" name="verdict" value="legit" class="btn btn-outline">Verify as Legitimate</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function inspectSenderDomain(domain) {
    var resDiv = document.getElementById('phishing-dns-result');
    var btn = document.getElementById('live-dns-inspect-btn');
    btn.disabled = true;
    btn.textContent = 'Querying DoH…';
    resDiv.style.display = 'block';
    resDiv.innerHTML = '<span class="text-muted">Resolving live DNS over HTTPS for ' + domain + '…</span>';

    fetch('../api/threat_intel.php?action=scan_url&url=' + encodeURIComponent(domain))
        .then(r => r.json())
        .then(data => {
            btn.disabled = false;
            btn.textContent = 'Query Sender DNS & SPF →';
            if (!data.success) {
                resDiv.innerHTML = '<div class="alert alert-error" style="font-size:12px;">' + (data.error || 'Lookup failed.') + '</div>';
                return;
            }
            var bColor = data.verdict_class === 'red' ? 'badge-red' : (data.verdict_class === 'amber' ? 'badge-amber' : 'badge-green');
            var html = '<div class="flex-between mb-8"><strong>Domain: ' + data.host + '</strong><span class="badge ' + bColor + '">' + data.verdict + '</span></div>';
            if (data.resolved_ips && data.resolved_ips.length > 0) {
                html += '<div style="color:var(--text-muted);font-size:12.5px;margin-bottom:4px;">Resolved IPs: ' + data.resolved_ips.join(', ') + '</div>';
            }
            html += '<div style="color:var(--text-muted);font-size:12.5px;">MX Mail Record: ' + (data.has_mx ? 'Configured' : 'Missing') + ' | SPF Record: ' + (data.has_spf ? 'Found' : 'Missing') + '</div>';
            resDiv.innerHTML = html;
        })
        .catch(() => {
            btn.disabled = false;
            btn.textContent = 'Query Sender DNS & SPF →';
            resDiv.innerHTML = '<span class="text-danger">Unable to reach DNS service.</span>';
        });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
