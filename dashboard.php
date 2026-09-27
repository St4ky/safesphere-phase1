<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$user = current_user();

$welcome_flash = '';
if (!empty($_SESSION['just_registered'])) {
    $welcome_flash = "Welcome to SafeSphere, " . $user['name'] . "! Start your training by selecting a defensive module below.";
    unset($_SESSION['just_registered']);
}

$modules = [
    'phishing'  => [
        'label' => 'Phishing & Domain Spoofing',
        'svg'   => '<path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path>',
        'total' => 9,
        'url'   => 'modules/phishing.php'
    ],
    'upi'       => [
        'label' => 'UPI Fraud & Payment Scams',
        'svg'   => '<rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path>',
        'total' => 6,
        'url'   => 'modules/upi.php'
    ],
    'socialeng' => [
        'label' => 'Social Engineering & Vishing',
        'svg'   => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>',
        'total' => 4,
        'url'   => 'modules/socialeng.php'
    ],
    'network'   => [
        'label' => 'Network Hardening & Audit',
        'svg'   => '<path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line>',
        'total' => 7,
        'url'   => 'modules/network.php'
    ],
    'otp'       => [
        'label' => 'OTP Hijacking & SIM-Swap',
        'svg'   => '<path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>',
        'total' => 5,
        'url'   => 'modules/otp.php'
    ],
    'deepfake'  => [
        'label' => 'Deepfake & AI Voice Clone',
        'svg'   => '<rect width="18" height="18" x="3" y="3" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path>',
        'total' => 4,
        'url'   => 'modules/deepfake.php'
    ],
];

$stats = [];
foreach ($modules as $key => $m) {
    $stats[$key] = get_module_stats($user['id'], $key);
}

// Recent activity: last 6 attempts across all modules
$recent = get_db()->prepare(
    "SELECT * FROM attempts WHERE user_id = ? ORDER BY created_at DESC LIMIT 6"
);
$recent->execute([$user['id']]);
$recent_rows = $recent->fetchAll();

$score = (int)$user['cyber_score'];
$circumference = 2 * M_PI * 54;
$offset = $circumference * (1 - $score / 100);

$page_title = 'Command Center';
$page_subtitle = 'Defensive readiness status and tactical training simulation progress';
$active = 'dashboard';
$base = '';
include __DIR__ . '/includes/header.php';
?>

<?php if ($welcome_flash): ?>
<div class="alert alert-success" style="font-size:14.5px;">
    <?= e($welcome_flash) ?>
</div>
<?php endif; ?>

<div class="dashboard-top-grid">
    <!-- Cyber Score Gauge Card -->
    <div class="card card-pad flex-center" style="flex-direction:column;text-align:center;">
        <div class="gauge-wrap" style="position:relative;display:inline-flex;flex-direction:column;align-items:center;">
            <svg width="150" height="150" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="54" fill="none" stroke="var(--border)" stroke-width="10"/>
                <circle cx="60" cy="60" r="54" fill="none" stroke="var(--indigo)" stroke-width="10"
                        stroke-linecap="round"
                        stroke-dasharray="<?= $circumference ?>"
                        stroke-dashoffset="<?= $offset ?>"
                        transform="rotate(-90 60 60)"
                        style="transition:stroke-dashoffset 1s ease;"/>
                <text x="60" y="66" text-anchor="middle" font-size="28" font-weight="800" fill="currentColor"><?= $score ?></text>
            </svg>
            <div class="gauge-label mt-8" style="font-size:11px;font-weight:700;letter-spacing:0.06em;color:var(--text-muted);text-transform:uppercase;">DEFENSE READINESS</div>
            <div class="badge <?= $score >= 80 ? 'badge-green' : ($score >= 50 ? 'badge-amber' : 'badge-red') ?> mt-12">
                <?= $score >= 80 ? 'Exemplary Defense' : ($score >= 50 ? 'Standard Readiness' : 'Needs Reinforcement') ?>
            </div>
        </div>
    </div>

    <!-- Module Progress Grid Card -->
    <div class="card card-pad">
        <div class="flex-between mb-16">
            <h3 style="font-size:16px;font-weight:800;letter-spacing:-0.01em;">Defensive Simulation Labs</h3>
            <span class="badge badge-gray" style="font-size:11px;">Active Track</span>
        </div>
        <div class="grid grid-2" style="gap:12px;">
            <?php foreach ($modules as $key => $m):
                $s = $stats[$key];
                $pct = $m['total'] > 0 ? round(($s['attempted'] / $m['total']) * 100) : 0;
            ?>
            <a href="<?= $m['url'] ?>" class="card card-pad module-progress-card" style="display:block;padding:16px;text-decoration:none;transition:all .18s ease;">
                <div class="flex-between mb-8">
                    <span style="width:32px;height:32px;border-radius:6px;background:var(--indigo-light);color:var(--indigo);display:inline-flex;align-items:center;justify-content:center;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><?= $m['svg'] ?></svg>
                    </span>
                    <span class="badge badge-gray" style="font-size:11px;"><?= $s['attempted'] ?>/<?= $m['total'] ?></span>
                </div>
                <div class="font-bold text-sm" style="color:var(--text);"><?= e($m['label']) ?></div>
                <div class="progress-track mt-12">
                    <div class="progress-fill <?= $pct >= 80 ? 'green' : '' ?>" style="width:<?= $pct ?>%"></div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="card card-pad mt-24">
    <div class="flex-between mb-16">
        <h3 style="font-size:16px;font-weight:800;">Recent Defensive Audit Log</h3>
        <span class="text-xs text-muted">Last 6 Decisions</span>
    </div>
    <?php if (empty($recent_rows)): ?>
        <p class="text-muted text-sm" style="padding:16px 0;">No simulations recorded yet. Select any lab above to start your first triage scenario.</p>
    <?php else: ?>
        <div style="display:grid;gap:2px;">
            <?php foreach ($recent_rows as $r): ?>
            <div class="flex-between" style="padding:12px 0; border-bottom:1px solid var(--border);">
                <div class="flex gap-12" style="align-items:center;">
                    <span class="badge <?= $r['is_correct'] ? 'badge-green' : 'badge-red' ?>" style="font-size:11px;">
                        <?= $r['is_correct'] ? '✓ Passed' : '✕ Missed' ?>
                    </span>
                    <span class="text-sm font-semibold" style="color:var(--text);"><?= e(ucfirst($r['module_key'])) ?> Lab &middot; Scenario <?= e($r['scenario_id']) ?></span>
                </div>
                <span class="text-xs text-muted"><?= time_ago($r['created_at']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- National Threat Feed -->
<div class="card card-pad mt-24">
    <div class="flex-between mb-16">
        <div class="flex gap-10" style="align-items:center;">
            <span style="width:28px;height:28px;border-radius:6px;background:var(--red-light);color:var(--red);display:inline-flex;align-items:center;justify-content:center;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </span>
            <h3 style="margin:0;font-size:16px;font-weight:800;">National Threat Advisory & Intel Feed</h3>
        </div>
        <span class="badge badge-red" style="font-size:11px;">CERT-In Verified</span>
    </div>
    
    <div class="grid grid-2" style="gap:16px;">
        <div class="card card-pad" style="background:var(--bg); border-left:4px solid var(--red);">
            <div class="flex-between mb-6">
                <span class="badge badge-red" style="font-size:10px;">Critical Advisory</span>
                <span class="text-xs text-muted">MHA I4C</span>
            </div>
            <div class="font-bold text-sm mb-6">Digital Arrest Video Intimidation</div>
            <p class="text-xs text-muted" style="line-height:1.55;">Fraudsters pose as CBI / Cyber Cell on Skype demanding fund transfers to avoid arrest. Digital arrest does not exist under Indian law.</p>
        </div>

        <div class="card card-pad" style="background:var(--bg); border-left:4px solid var(--amber);">
            <div class="flex-between mb-6">
                <span class="badge badge-amber" style="font-size:10px;">APK Threat</span>
                <span class="text-xs text-muted">CERT-In Bulletin</span>
            </div>
            <div class="font-bold text-sm mb-6">Predatory APK Sideloading</div>
            <p class="text-xs text-muted" style="line-height:1.55;">SMS/WhatsApp posing as courier or power bills download malware silently intercepting 2FA banking OTPs via READ_SMS + INTERNET permissions.</p>
        </div>

        <div class="card card-pad" style="background:var(--bg); border-left:4px solid var(--blue);">
            <div class="flex-between mb-6">
                <span class="badge badge-blue" style="font-size:10px;">Payment Gateway</span>
                <span class="text-xs text-muted">NPCI Safety</span>
            </div>
            <div class="font-bold text-sm mb-6">UPI Collect Inversion Scam</div>
            <p class="text-xs text-muted" style="line-height:1.55;">Scammers claim you will "receive cashback" by typing your MPIN. Entering your UPI PIN ALWAYS debits money — it can never credit your account.</p>
        </div>

        <div class="card card-pad" style="background:var(--bg); border-left:4px solid #7c3aed;">
            <div class="flex-between mb-6">
                <span class="badge" style="font-size:10px;background:rgba(124,58,237,0.1);color:#7c3aed;border:1px solid rgba(124,58,237,0.3);">SIM-Swap Alert</span>
                <span class="text-xs text-muted">TRAI Advisory</span>
            </div>
            <div class="font-bold text-sm mb-6">Telecom SIM Hijacking Wave</div>
            <p class="text-xs text-muted" style="line-height:1.55;">Scammers call posing as Jio/Airtel, trick users into texting SIM serial numbers to short codes. Once swapped, all banking OTPs route to attacker.</p>
        </div>

        <div class="card card-pad" style="background:var(--bg); border-left:4px solid var(--red);">
            <div class="flex-between mb-6">
                <span class="badge badge-red" style="font-size:10px;">AI Threat</span>
                <span class="text-xs text-muted">CERT-In 2024</span>
            </div>
            <div class="font-bold text-sm mb-6">AI Voice Clone Extortion</div>
            <p class="text-xs text-muted" style="line-height:1.55;">ElevenLabs-style voice cloning uses 3-second Instagram audio samples to simulate family members pleading for emergency fund transfers.</p>
        </div>

        <div class="card card-pad" style="background:var(--bg); border-left:4px solid var(--green);">
            <div class="flex-between mb-6">
                <span class="badge badge-green" style="font-size:10px;">Forensic Tool</span>
                <span class="text-xs text-muted">RBI Guideline</span>
            </div>
            <div class="font-bold text-sm mb-6">SPF/DKIM Domain Spoofing</div>
            <p class="text-xs text-muted" style="line-height:1.55;">Phishing emails from fake domains pass spam filters when SPF records are absent. Always verify sender domain against brand official domains before clicking.</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
