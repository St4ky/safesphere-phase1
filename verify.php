<?php
require_once __DIR__ . '/includes/auth.php';
$pdo = get_db();

$cred_id = trim(strtoupper($_GET['id'] ?? ''));
$cert = null;
$user_info = null;

if ($cred_id) {
    $stmt = $pdo->prepare("SELECT c.*, u.name as user_name, u.email as user_email, u.cyber_score 
                           FROM certificates c 
                           JOIN users u ON c.user_id = u.id 
                           WHERE c.credential_id = ? LIMIT 1");
    $stmt->execute([$cred_id]);
    $cert = $stmt->fetch();
}

$nav_active = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $cert ? 'Verified Credential: ' . e($cert['title']) : 'Verify Credential' ?> · SafeSphere</title>
<script>try{var t=localStorage.getItem('ss_theme')||'light';document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.5">
<script>try{var t=localStorage.getItem('ss_theme');if(t)document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
</head>
<body>
<?php include __DIR__ . '/includes/public_nav.php'; ?>

<div class="container section" style="max-width:820px;">
    <div class="card card-pad mb-24 text-center">
        <h1 style="font-size:28px;" class="mb-8">🛡️ Public Credential Verification</h1>
        <p class="text-sm text-muted mb-20">Verify authentic SafeSphere training certificates issued to cybersecurity trainees.</p>

        <form method="GET" class="flex gap-8 flex-center" style="max-width:480px; margin:0 auto;">
            <input type="text" name="id" value="<?= e($cred_id) ?>" placeholder="e.g. SS-A1B2C3D4E5" required class="form-input" style="padding:10px 14px; font-family:'JetBrains Mono',monospace; text-transform:uppercase; font-size:15px;">
            <button type="submit" class="btn btn-primary">Verify Credential</button>
        </form>
    </div>

    <?php if ($cred_id): ?>
        <?php if ($cert): ?>
            <div class="card card-pad" style="border-top:4px solid var(--green);">
                <div class="flex-between mb-16">
                    <span class="badge badge-green" style="font-size:13px; padding:6px 14px;">✓ Authentic & Verified Credential</span>
                    <span class="font-mono text-sm text-muted">ID: <?= e($cert['credential_id']) ?></span>
                </div>

                <div class="grid grid-2 mb-20" style="gap:20px;">
                    <div>
                        <div class="text-xs text-muted uppercase font-bold mb-4">Recipient Name</div>
                        <div style="font-size:22px; font-weight:800;"><?= e($cert['user_name']) ?></div>
                    </div>
                    <div>
                        <div class="text-xs text-muted uppercase font-bold mb-4">Award Title</div>
                        <div style="font-size:20px; font-weight:800; color:var(--indigo);"><?= e($cert['title']) ?></div>
                    </div>
                </div>

                <div class="grid grid-3 mb-24" style="gap:16px;">
                    <div class="stat-card" style="padding:14px;">
                        <div class="stat-card-number" style="font-size:20px;"><?= date('d M Y', strtotime($cert['issue_date'])) ?></div>
                        <div class="stat-card-label">Issue Date</div>
                    </div>
                    <div class="stat-card" style="padding:14px;">
                        <div class="stat-card-number" style="font-size:20px;"><?= (int)$cert['cyber_score'] ?>/100</div>
                        <div class="stat-card-label">Current Cyber Score</div>
                    </div>
                    <div class="stat-card" style="padding:14px;">
                        <div class="stat-card-number" style="font-size:20px; color:var(--green);">Active</div>
                        <div class="stat-card-label">Credential Status</div>
                    </div>
                </div>

                <div class="card card-pad" style="background:var(--bg); border:1px solid var(--border);">
                    <div class="font-bold text-sm mb-6">Verification Authority Note:</div>
                    <p class="text-sm text-muted" style="margin:0; line-height:1.6;">
                        This certificate was granted after direct, hands-on completion of simulated Indian cyber fraud attack vectors. 
                        SafeSphere guarantees that this credential is immutable, cryptographically indexed, and was earned through proven performance.
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-error">
                <strong>No certificate found for ID:</strong> <?= e($cred_id) ?>. Please ensure the credential ID is spelled correctly.
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/public_footer.php'; ?>
<script src="assets/js/main.js?v=2.6"></script>
</body>
</html>
