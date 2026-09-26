<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$user = current_user();

// Only admins can access
if (($user['role'] ?? 'user') !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    $page_title    = 'Access Denied';
    $page_subtitle = 'You do not have permission to view this page.';
    $active        = '';
    $base          = '';
    include __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-error" style="margin-top:32px;"><strong>403 — Forbidden.</strong> Admin access only.</div>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

$pdo = get_db();

// ── Actions ───────────────────────────────────────────────────────────────────
$flash      = '';
$flash_type = 'success';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $flash      = 'CSRF validation failed.';
        $flash_type = 'error';
    } else {
        $action = $_POST['admin_action'] ?? '';

        if ($action === 'reset_user' && !empty($_POST['target_user_id'])) {
            $uid = (int)$_POST['target_user_id'];
            // Don't reset yourself
            if ($uid !== $user['id']) {
                $pdo->prepare("DELETE FROM attempts      WHERE user_id=?")->execute([$uid]);
                $pdo->prepare("DELETE FROM audit_results WHERE user_id=?")->execute([$uid]);
                $pdo->prepare("DELETE FROM forensic_logs WHERE user_id=?")->execute([$uid]);
                $pdo->prepare("DELETE FROM certificates  WHERE user_id=?")->execute([$uid]);
                $pdo->prepare("UPDATE users SET cyber_score=40, streak_count=0 WHERE id=?")->execute([$uid]);
                $flash = "User #$uid progress has been reset.";
            } else {
                $flash      = 'You cannot reset your own progress.';
                $flash_type = 'error';
            }

        } elseif ($action === 'change_role' && !empty($_POST['target_user_id']) && !empty($_POST['new_role'])) {
            $uid  = (int)$_POST['target_user_id'];
            $role = in_array($_POST['new_role'], ['user', 'admin']) ? $_POST['new_role'] : 'user';
            $pdo->prepare("UPDATE users SET role=? WHERE id=?")->execute([$role, $uid]);
            $flash = "User #$uid role changed to $role.";

        } elseif ($action === 'delete_user' && !empty($_POST['target_user_id'])) {
            $uid = (int)$_POST['target_user_id'];
            if ($uid !== $user['id']) {
                foreach (['attempts','audit_results','forensic_logs','certificates'] as $t) {
                    $pdo->prepare("DELETE FROM $t WHERE user_id=?")->execute([$uid]);
                }
                $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$uid]);
                $flash = "User #$uid deleted.";
            } else {
                $flash      = 'You cannot delete your own account.';
                $flash_type = 'error';
            }

        } elseif ($action === 'purge_logs') {
            $deleted = $pdo->exec("DELETE FROM forensic_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $flash = "Purged $deleted forensic log(s) older than 30 days.";

        } elseif ($action === 'create_scenario') {
            $modKey   = trim($_POST['module_key'] ?? '');
            $title    = trim($_POST['title'] ?? '');
            $sender   = trim($_POST['sender'] ?? '');
            $body     = trim($_POST['content_body'] ?? '');
            $isFraud  = (int)($_POST['is_fraud'] ?? 1);
            $redFlags = trim($_POST['red_flags'] ?? '');

            if ($title && $body && $sender) {
                $ins = $pdo->prepare("INSERT INTO custom_scenarios (module_key, title, sender_or_caller, content_body, is_fraud, red_flags) VALUES (?,?,?,?,?,?)");
                $ins->execute([$modKey, $title, $sender, $body, $isFraud, $redFlags]);
                $flash = "New scenario \"$title\" published successfully to CMS!";
            } else {
                $flash = "Please fill in all scenario fields.";
                $flash_type = "error";
            }

        } elseif ($action === 'delete_scenario' && !empty($_POST['scenario_id'])) {
            $scId = (int)$_POST['scenario_id'];
            $pdo->prepare("DELETE FROM custom_scenarios WHERE id=?")->execute([$scId]);
            $flash = "Custom scenario #$scId deleted.";
        }
    }
}

// ── Stats ─────────────────────────────────────────────────────────────────────
$totalUsers     = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalAttempts  = (int)$pdo->query("SELECT COUNT(*) FROM attempts")->fetchColumn();
$totalCerts     = (int)$pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn();
$totalForensics = (int)$pdo->query("SELECT COUNT(*) FROM forensic_logs")->fetchColumn();

// Active users (activity in last 7 days)
$activeUsers = (int)$pdo->query("SELECT COUNT(DISTINCT user_id) FROM attempts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

// Avg cyber score
$avgScore = round((float)$pdo->query("SELECT AVG(cyber_score) FROM users")->fetchColumn(), 1);

// Module attempt breakdown
$moduleBreakdown = $pdo->query("SELECT module_key, COUNT(*) as c, SUM(is_correct) as correct FROM attempts GROUP BY module_key")->fetchAll();

// Accuracy by module
$accByMod = [];
foreach ($moduleBreakdown as $row) {
    $accByMod[$row['module_key']] = [
        'count'   => $row['c'],
        'correct' => $row['correct'],
        'acc'     => $row['c'] > 0 ? round($row['correct']/$row['c']*100) : 0,
    ];
}

// User Pagination
$perPage = 15;
$totalUsersCount = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPages = max(1, ceil($totalUsersCount / $perPage));
$page = max(1, min($totalPages, (int)($_GET['p'] ?? 1)));
$offset = ($page - 1) * $perPage;

$recentUsers = $pdo->prepare("SELECT id, name, email, role, cyber_score, streak_count, created_at FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
$recentUsers->bindValue(1, $perPage, PDO::PARAM_INT);
$recentUsers->bindValue(2, $offset, PDO::PARAM_INT);
$recentUsers->execute();
$recentUsers = $recentUsers->fetchAll();

// Forensic verdict breakdown
$verdictBreakdown = $pdo->query("SELECT verdict, COUNT(*) as c FROM forensic_logs GROUP BY verdict")->fetchAll();

// Custom Scenarios from CMS
$customScenarios = $pdo->query("SELECT * FROM custom_scenarios ORDER BY created_at DESC")->fetchAll();

$page_title    = 'Admin Panel';
$page_subtitle = 'Platform management — users, stats, and progress data';
$active        = 'admin';
$base          = '';
include __DIR__ . '/includes/header.php';
?>

<?php if ($flash): ?><div class="alert alert-<?= e($flash_type) ?> mb-16"><?= e($flash) ?></div><?php endif; ?>

<!-- Platform stats -->
<div class="grid grid-4 mb-24">
    <div class="stat-card">
        <div class="stat-card-number"><?= $totalUsers ?></div>
        <div class="stat-card-label">Total Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number"><?= $activeUsers ?></div>
        <div class="stat-card-label">Active (7 days)</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number"><?= $totalAttempts ?></div>
        <div class="stat-card-label">Total Scenario Attempts</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-number"><?= $totalCerts ?></div>
        <div class="stat-card-label">Certificates Issued</div>
    </div>
</div>

<div class="grid grid-3 mb-24" style="gap:20px;">
    <!-- Module Accuracy -->
    <div class="card card-pad">
        <h3 class="mb-16">📊 Module Accuracy</h3>
        <?php
        $modLabels = ['phishing'=>'📧 Phishing','upi'=>'📱 UPI Fraud','socialeng'=>'🗣️ Social Eng','network'=>'📶 Network'];
        foreach ($modLabels as $key => $label):
            $d = $accByMod[$key] ?? ['count'=>0,'correct'=>0,'acc'=>0];
        ?>
        <div class="flex-between mb-4 text-sm">
            <span><?= $label ?></span>
            <span class="font-bold" style="color:<?= $d['acc'] >= 70 ? 'var(--green)' : ($d['acc'] >= 40 ? 'var(--amber)' : 'var(--red)') ?>;">
                <?= $d['acc'] ?>%
            </span>
        </div>
        <div class="progress-track mb-12">
            <div class="progress-fill" style="width:<?= $d['acc'] ?>%;"></div>
        </div>
        <?php endforeach; ?>
        <div class="text-xs text-muted mt-8">Total attempts: <?= $totalAttempts ?></div>
    </div>

    <!-- Platform Vitals -->
    <div class="card card-pad">
        <h3 class="mb-16">⚡ Platform Vitals</h3>
        <div class="flex-between text-sm mb-12" style="padding:8px 0;border-bottom:1px solid var(--border);">
            <span>Avg. Cyber Score</span>
            <strong><?= $avgScore ?>/100</strong>
        </div>
        <div class="flex-between text-sm mb-12" style="padding:8px 0;border-bottom:1px solid var(--border);">
            <span>Forensic Analyses</span>
            <strong><?= $totalForensics ?></strong>
        </div>
        <div class="flex-between text-sm mb-12" style="padding:8px 0;border-bottom:1px solid var(--border);">
            <span>Certs per User (avg)</span>
            <strong><?= $totalUsers > 0 ? round($totalCerts/$totalUsers, 1) : 0 ?></strong>
        </div>
        <h4 class="mt-16 mb-10">🔍 Forensic Verdicts</h4>
        <?php foreach ($verdictBreakdown as $v): ?>
        <div class="flex-between text-sm" style="padding:6px 0;">
            <span class="badge <?= $v['verdict'] === 'suspicious' ? 'badge-red' : ($v['verdict'] === 'warning' ? 'badge-amber' : 'badge-green') ?>"><?= e($v['verdict']) ?></span>
            <strong><?= $v['c'] ?></strong>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Quick Links -->
    <div class="card card-pad">
        <h3 class="mb-16">🔧 Admin Quick Actions</h3>
        <div class="flex flex-col gap-10">
            <a href="dashboard.php" class="btn btn-outline btn-sm" style="text-align:center;">← Go to Dashboard</a>
            <a href="reports.php"   class="btn btn-outline btn-sm" style="text-align:center;">📄 View My Report</a>
            <a href="certificates.php" class="btn btn-outline btn-sm" style="text-align:center;">🏅 Certificates</a>
            <hr class="divider">
            <div class="text-xs text-muted">DB Schema actions:</div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="admin_action" value="noop">
                <div class="field">
                    <label style="font-size:12px;">Clear forensic logs older than 30 days</label>
                    <button type="button" onclick="
                        if(confirm('Delete old forensic logs?')) {
                            fetch('admin.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
                            body: 'csrf_token=<?= csrf_token() ?>&admin_action=purge_logs'}).then(()=>location.reload());
                        }" class="btn btn-outline btn-sm" style="width:100%;margin-top:6px;">🗑️ Purge Old Logs</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- User Management Table -->
<div class="card card-pad">
    <div class="flex-between mb-16">
        <h3>👤 User Management (<?= $totalUsers ?> users)</h3>
        <input type="text" id="user-search" placeholder="Search by name or email…" class="form-input" style="width:220px;font-size:13px;padding:6px 10px;">
    </div>
    <div class="table-wrap">
    <table id="user-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name / Email</th>
                <th>Role</th>
                <th>Score</th>
                <th>Streak</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($recentUsers as $u): ?>
        <tr class="user-row" style="border-top:1px solid var(--border);" data-search="<?= strtolower(e($u['name'])) ?> <?= strtolower(e($u['email'])) ?>">
            <td style="padding:10px 12px;color:var(--text-muted);font-size:12px;">#<?= $u['id'] ?></td>
            <td style="padding:10px 12px;">
                <div style="font-weight:700;"><?= e($u['name']) ?> <?= $u['id'] == $user['id'] ? '<span class="badge badge-indigo" style="font-size:10px;">You</span>' : '' ?></div>
                <div style="font-size:12px;color:var(--text-muted);"><?= e($u['email']) ?></div>
            </td>
            <td style="padding:10px 12px;">
                <span class="badge <?= $u['role'] === 'admin' ? 'badge-red' : 'badge-gray' ?>">
                    <?= $u['role'] === 'admin' ? '👑 Admin' : '👤 User' ?>
                </span>
            </td>
            <td style="padding:10px 12px;font-weight:700;"><?= (int)$u['cyber_score'] ?></td>
            <td style="padding:10px 12px;"><?= (int)$u['streak_count'] ?>🔥</td>
            <td style="padding:10px 12px;color:var(--text-muted);font-size:12px;"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
            <td style="padding:10px 12px;">
                <?php if ($u['id'] !== $user['id']): ?>
                <div class="flex gap-6">
                    <!-- Change Role -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="admin_action" value="change_role">
                        <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                        <input type="hidden" name="new_role" value="<?= $u['role'] === 'admin' ? 'user' : 'admin' ?>">
                        <button type="submit" class="btn btn-outline btn-sm" title="Toggle role" onclick="return confirm('Change this user\'s role?')">
                            <?= $u['role'] === 'admin' ? '↓ Demote' : '↑ Admin' ?>
                        </button>
                    </form>
                    <!-- Reset Progress -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="admin_action" value="reset_user">
                        <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                        <button type="submit" class="btn btn-outline btn-sm" style="border-color:var(--amber);color:var(--amber);" onclick="return confirm('Reset all progress for <?= addslashes($u['name']) ?>?')">
                            ↩ Reset
                        </button>
                    </form>
                    <!-- Delete -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="admin_action" value="delete_user">
                        <input type="hidden" name="target_user_id" value="<?= $u['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('PERMANENTLY delete <?= addslashes($u['name']) ?> and all their data?')">
                            ✕
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <span class="text-xs text-muted">— You —</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>

    <!-- Pagination Controls -->
    <?php if ($totalPages > 1): ?>
    <div class="flex-between mt-16 pt-12" style="border-top:1px solid var(--border);">
        <div class="text-sm text-muted">Showing page <?= $page ?> of <?= $totalPages ?> (Total: <?= $totalUsersCount ?> users)</div>
        <div class="flex gap-6">
            <?php if ($page > 1): ?>
                <a href="?p=<?= $page - 1 ?>" class="btn btn-outline btn-sm">← Prev</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?p=<?= $i ?>" class="btn <?= $i === $page ? 'btn-primary' : 'btn-outline' ?> btn-sm" style="min-width:32px; padding:6px 10px;"><?= $i ?></a>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?p=<?= $page + 1 ?>" class="btn btn-outline btn-sm">Next →</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Scenario CMS Manager Section -->
<div class="card card-pad mt-24">
    <div class="flex-between mb-16">
        <div>
            <h3>✍️ Scenario CMS — Create & Deploy New Attack Scenarios</h3>
            <p class="text-xs text-muted">Author live attack simulations into the training database without touching code.</p>
        </div>
        <span class="badge badge-indigo">Dynamic CMS</span>
    </div>

    <div class="grid grid-2 mb-24" style="gap:24px; align-items:start;">
        <!-- Scenario Creator Form -->
        <div class="card card-pad" style="background:var(--bg);">
            <h4 class="mb-12">Deploy New Attack Scenario</h4>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="admin_action" value="create_scenario">
                
                <div class="field">
                    <label>Target Training Module</label>
                    <select name="module_key" required>
                        <option value="phishing">📧 Phishing Defense</option>
                        <option value="upi">📱 UPI Fraud Sim</option>
                        <option value="otp">📞 OTP & Voice Defense</option>
                        <option value="deepfake">🤖 Deepfake & AI Fraud</option>
                        <option value="socialeng">🗣️ Social Engineering</option>
                    </select>
                </div>

                <div class="field">
                    <label>Scenario Title</label>
                    <input type="text" name="title" required placeholder="e.g. TRAI Phone Disconnection Notice">
                </div>

                <div class="field">
                    <label>Sender / Caller / Channel</label>
                    <input type="text" name="sender" required placeholder="e.g. TRAI Verification Desk (+91 98210 11223)">
                </div>

                <div class="field">
                    <label>Threat Classification</label>
                    <select name="is_fraud">
                        <option value="1">⚠️ Fraud / Attack (Trainees must flag/reject)</option>
                        <option value="0">🛡️ Authentic / Safe (Trainees should trust)</option>
                    </select>
                </div>

                <div class="field">
                    <label>Scenario Content / Dialogue / SMS</label>
                    <textarea name="content_body" rows="4" required placeholder="Type the phishing message, call script, or alert text..."></textarea>
                </div>

                <div class="field">
                    <label>Key Red Flags (Comma-separated)</label>
                    <input type="text" name="red_flags" placeholder="e.g. Unsolicited contact, Artificial deadline pressure">
                </div>

                <button type="submit" class="btn btn-primary btn-block">🚀 Publish to SafeSphere</button>
            </form>
        </div>

        <!-- Custom Scenarios List -->
        <div>
            <h4 class="mb-12">Active Custom Scenarios (<?= count($customScenarios) ?>)</h4>
            <?php if (empty($customScenarios)): ?>
                <div class="card card-pad text-center" style="background:var(--bg); border:1px dashed var(--border); padding:32px 16px;">
                    <p class="text-sm text-muted">No custom scenarios published yet. Use the form on the left to deploy fresh threats!</p>
                </div>
            <?php else: ?>
                <div class="flex flex-col gap-10" style="max-height:480px; overflow-y:auto;">
                    <?php foreach ($customScenarios as $cs): ?>
                    <div class="card card-pad flex-between" style="padding:12px 16px;">
                        <div>
                            <div class="flex gap-8 mb-4" style="align-items:center;">
                                <span class="badge badge-gray" style="font-size:10px;"><?= e(strtoupper($cs['module_key'])) ?></span>
                                <span class="badge <?= $cs['is_fraud'] ? 'badge-red' : 'badge-green' ?>" style="font-size:10px;">
                                    <?= $cs['is_fraud'] ? 'Scam' : 'Legit' ?>
                                </span>
                            </div>
                            <div class="font-bold text-sm"><?= e($cs['title']) ?></div>
                            <div class="text-xs text-muted"><?= e($cs['sender_or_caller']) ?></div>
                        </div>
                        <form method="POST" onsubmit="return confirm('Delete this custom scenario?');">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="admin_action" value="delete_scenario">
                            <input type="hidden" name="scenario_id" value="<?= $cs['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm" style="padding:4px 8px; font-size:11px;">✕</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// User search filter
document.getElementById('user-search').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#user-table .user-row').forEach(function(row) {
        row.style.display = row.dataset.search.includes(q) ? '' : 'none';
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
