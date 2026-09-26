<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$user = current_user();
$pdo  = get_db();

function check_all_certs($user_id, $pdo): array {
    $s_ph = get_module_stats($user_id, 'phishing');
    $s_up = get_module_stats($user_id, 'upi');
    $s_se = get_module_stats($user_id, 'socialeng');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM audit_results WHERE user_id=? AND status='pass'");
    $stmt->execute([$user_id]);
    $netPass = (int)$stmt->fetchColumn();
    return [
        'phishing_specialist' => ['ok' => $s_ph['attempted'] >= 9 && $s_ph['correct'] >= 7, 'progress' => $s_ph['correct'], 'total' => 7, 'pct' => min(100, round($s_ph['correct']/7*100))],
        'upi_guardian'        => ['ok' => $s_up['attempted'] >= 6 && $s_up['correct'] >= 5, 'progress' => $s_up['correct'], 'total' => 5, 'pct' => min(100, round($s_up['correct']/5*100))],
        'social_eng_proof'    => ['ok' => $s_se['attempted'] >= 4 && $s_se['correct'] >= 3, 'progress' => $s_se['correct'], 'total' => 3, 'pct' => min(100, round($s_se['correct']/3*100))],
        'network_defender'    => ['ok' => $netPass >= 7, 'progress' => $netPass, 'total' => 7, 'pct' => min(100, round($netPass/7*100))],
    ];
}

$checks = check_all_certs($user['id'], $pdo);
$allFour = !in_array(false, array_column($checks, 'ok'), true);

$cert_defs = [
    [
        'key'      => 'phishing_specialist',
        'title'    => 'Phishing Defense Specialist',
        'medal'    => '🏅',
        'color'    => '#4f46e5',
        'color_bg' => '#eef2ff',
        'desc'     => 'Demonstrated ability to identify phishing emails across 9 real Indian scenarios with ≥7 correct decisions.',
        'criteria' => 'Complete 9 phishing scenarios with ≥7 correct',
        'skills'   => ['Spoofed domain detection', 'SPF/DKIM header analysis', 'Urgency tactic recognition', 'Legitimate vs phishing classification'],
    ],
    [
        'key'      => 'upi_guardian',
        'title'    => 'UPI Guardian',
        'medal'    => '🛡️',
        'color'    => '#16a34a',
        'color_bg' => '#f0fdf4',
        'desc'     => 'Successfully identified UPI collect request fraud, QR scams, and government subsidy fraud across 6 scenarios with ≥5 correct decisions.',
        'criteria' => 'Complete 6 UPI scenarios with ≥5 correct',
        'skills'   => ['Collect request fraud detection', 'QR scam awareness', 'UPI ID verification', 'Payment pressure tactic resistance'],
    ],
    [
        'key'      => 'social_eng_proof',
        'title'    => 'Social Engineer Proof',
        'medal'    => '🗣️',
        'color'    => '#d97706',
        'color_bg' => '#fffbeb',
        'desc'     => 'Successfully navigated 4 social engineering attack simulations — vishing, job fraud, tech support, and romance scams — with ≥3 correct outcomes.',
        'criteria' => 'Complete 4 social engineering scenarios with ≥3 passed',
        'skills'   => ['Vishing call identification', 'Job offer fraud recognition', 'Tech support scam resistance', 'Romance scam awareness'],
    ],
    [
        'key'      => 'network_defender',
        'title'    => 'Network Defender',
        'medal'    => '📶',
        'color'    => '#0369a1',
        'color_bg' => '#e0f2fe',
        'desc'     => 'Completed and passed all 7 home network security audit checks — router hardening, encryption, segmentation, and public Wi-Fi safety.',
        'criteria' => 'Pass all 7 network audit checks',
        'skills'   => ['Router password hardening', 'WPA3 encryption', 'Network segmentation', 'Firmware management', 'UPnP/remote management'],
    ],
    [
        'key'      => 'cyber_champion',
        'title'    => 'Cyber Awareness Champion',
        'medal'    => '🏆',
        'color'    => '#b45309',
        'color_bg' => '#fffbeb',
        'desc'     => 'The highest SafeSphere certification — awarded for demonstrating comprehensive cybersecurity awareness across all four threat domains.',
        'criteria' => 'Earn all four specialist certificates',
        'skills'   => ['Full-spectrum cyber threat awareness', 'Phishing & UPI fraud detection', 'Social engineering resistance', 'Network security hardening'],
    ],
];

// Auto-issue
$earned = [];
foreach ($cert_defs as &$cd) {
    if ($cd['key'] === 'cyber_champion') continue;
    $check = $checks[$cd['key']];
    $cd['check'] = $check;
    if ($check['ok']) {
        $earned[] = $cd['key'];
        $exists = $pdo->prepare("SELECT id FROM certificates WHERE user_id=? AND cert_key=?");
        $exists->execute([$user['id'], $cd['key']]);
        if (!$exists->fetchColumn()) {
            $credId = 'SS-' . strtoupper(bin2hex(random_bytes(5)));
            $pdo->prepare("INSERT INTO certificates (user_id, cert_key, title, credential_id, issue_date) VALUES (?,?,?,?,?)")
                ->execute([$user['id'], $cd['key'], $cd['title'], $credId, date('Y-m-d')]);
        }
    }
}
unset($cd);

$champDef = &$cert_defs[4];
$champDef['check'] = ['ok' => $allFour, 'progress' => count($earned), 'total' => 4, 'pct' => round(count($earned)/4*100)];
if ($allFour) {
    $earned[] = 'cyber_champion';
    $exists = $pdo->prepare("SELECT id FROM certificates WHERE user_id=? AND cert_key='cyber_champion'");
    $exists->execute([$user['id']]);
    if (!$exists->fetchColumn()) {
        $credId = 'SS-' . strtoupper(bin2hex(random_bytes(5)));
        $pdo->prepare("INSERT INTO certificates (user_id, cert_key, title, credential_id, issue_date) VALUES (?,?,?,?,?)")
            ->execute([$user['id'], 'cyber_champion', 'Cyber Awareness Champion', $credId, date('Y-m-d')]);
    }
}
unset($champDef);

$dbCerts = [];
$rows = $pdo->prepare("SELECT * FROM certificates WHERE user_id=?");
$rows->execute([$user['id']]);
foreach ($rows->fetchAll() as $r) { $dbCerts[$r['cert_key']] = $r; }

// Which cert to print-preview
$print_key = $_GET['print'] ?? null;
$print_cert = $print_key && isset($dbCerts[$print_key]) ? $dbCerts[$print_key] : null;
$print_def  = null;
if ($print_cert) {
    foreach ($cert_defs as $cd) {
        if ($cd['key'] === $print_key) { $print_def = $cd; break; }
    }
}

$page_title    = 'Certificates';
$page_subtitle = 'Earn verifiable credentials by completing SafeSphere training modules';
$active        = 'certificates';
$base          = '';
include __DIR__ . '/includes/header.php';
?>

<!-- Print certificate overlay (shown when ?print=key) -->
<?php if ($print_cert && $print_def): ?>
<div id="cert-print-overlay" style="position:fixed;inset:0;background:rgba(15,23,42,.8);z-index:1000;display:flex;align-items:flex-start;justify-content:center;padding:20px;overflow-y:auto;backdrop-filter:blur(4px);" onclick="if(event.target===this)this.remove()">
    <div style="max-width:760px;width:100%;margin:auto 0;">
        <div class="flex-between mb-12">
            <span class="text-sm" style="color:rgba(255,255,255,.7);">Click outside to close</span>
            <div class="flex gap-8">
                <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ Print / Save PDF</button>
                <a href="certificates.php" class="btn btn-outline btn-sm" style="background:rgba(255,255,255,.1);color:white;border-color:rgba(255,255,255,.2);">✕ Close</a>
            </div>
        </div>
        <?php include __DIR__ . '/includes/cert_template.php'; ?>
    </div>
</div>
<?php endif; ?>

<div class="text-sm text-muted mb-24">
    <?= count($earned) ?> of <?= count($cert_defs) ?> certificates earned &nbsp;·&nbsp; 
    Complete criteria to automatically unlock each certificate.
</div>

<div class="grid grid-2 mb-24" style="gap:20px;">
<?php foreach ($cert_defs as $cd):
    $isEarned = $cd['check']['ok'];
    $dbCert   = $dbCerts[$cd['key']] ?? null;
?>
<div class="cert-card <?= $isEarned ? 'earned' : 'locked' ?>">
    <?php if ($isEarned): ?>
    <div style="position:absolute;top:14px;right:14px;display:flex;gap:6px;align-items:center;">
        <span class="badge badge-green">✓ Earned</span>
    </div>
    <?php else: ?>
    <div style="position:absolute;top:14px;right:14px;"><span class="badge badge-gray">🔒 Locked</span></div>
    <?php endif; ?>

    <div class="cert-medal"><?= $cd['medal'] ?></div>
    <div class="cert-title"><?= e($cd['title']) ?></div>
    <div class="cert-desc"><?= e($cd['desc']) ?></div>

    <!-- Skills -->
    <?php if (!empty($cd['skills'])): ?>
    <div class="flex flex-wrap gap-6 mb-16" style="justify-content:center;">
        <?php foreach ($cd['skills'] as $sk): ?>
        <span class="badge badge-gray" style="font-size:10px;text-transform:none;letter-spacing:0;"><?= e($sk) ?></span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($isEarned && $dbCert): ?>
        <div class="cert-credential mb-12">
            ID: <a href="verify.php?id=<?= e($dbCert['credential_id']) ?>" target="_blank" style="color:inherit;text-decoration:underline;" title="Public Verification Link"><?= e($dbCert['credential_id']) ?></a> · Issued <?= date('d M Y', strtotime($dbCert['issue_date'])) ?>
        </div>
        <div class="flex gap-8 mb-10" style="justify-content:center;flex-wrap:wrap;">
            <span class="badge badge-green" style="font-size:11px;">🏅 SafeSphere Verified</span>
            <a href="?print=<?= e($cd['key']) ?>" class="btn btn-primary btn-sm">🖨️ View & Print Certificate</a>
        </div>
        <!-- Social Sharing -->
        <div class="flex gap-8" style="justify-content:center;align-items:center;">
            <?php 
                $shareText = urlencode("I just earned my " . $cd['title'] . " on SafeSphere! Credential ID: " . $dbCert['credential_id']);
                $shareUrl  = urlencode("http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "/safesphere-phase1/verify.php?id=" . $dbCert['credential_id']);
            ?>
            <a href="https://twitter.com/intent/tweet?text=<?= $shareText ?>&url=<?= $shareUrl ?>" target="_blank" class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 8px;" title="Share on X / Twitter">🐦 Tweet</a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareUrl ?>" target="_blank" class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 8px;" title="Share on LinkedIn">💼 LinkedIn</a>
            <a href="https://api.whatsapp.com/send?text=<?= $shareText ?>%20<?= $shareUrl ?>" target="_blank" class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 8px;" title="Share on WhatsApp">💬 WhatsApp</a>
        </div>
    <?php else: ?>
        <div class="cert-progress-wrap">
            <div class="cert-progress-label text-xs text-muted"><?= e($cd['criteria']) ?> — <?= $cd['check']['pct'] ?>% complete (<?= $cd['check']['progress'] ?>/<?= $cd['check']['total'] ?>)</div>
            <div class="progress-track mt-4">
                <div class="progress-fill" style="width:<?= $cd['check']['pct'] ?>%;"></div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>
</div>

<?php if (!empty($earned)): ?>
<div class="alert alert-success">
    🎉 You've earned <strong><?= count($earned) ?></strong> certificate<?= count($earned) > 1 ? 's' : '' ?>!
    Click "View & Print Certificate" on any earned certificate to get a printable/PDF version.
</div>
<?php else: ?>
<div class="alert alert-info">
    Complete the training modules above to automatically unlock certificates. Progress is tracked in real time.
</div>
<?php endif; ?>

<style>
@media print {
    .app-shell, .app-footer { display: block !important; }
    .sidebar, .topbar, .app-footer, .alert, h1, .subtitle, .cert-card:not(.print-target), #cert-print-overlay ~ * { display: none !important; }
    #cert-print-overlay { position: static !important; background: none !important; padding: 0 !important; display: block !important; }
    body, .main, .page { background: white !important; padding: 0 !important; }
    @page { margin: 10mm; size: A4 landscape; }
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
