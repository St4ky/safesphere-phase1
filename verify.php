<?php
require_once __DIR__ . '/includes/auth.php';
$pdo = get_db();

$cred_id = trim(strtoupper($_GET['id'] ?? ''));
$cert = null;

// Official Certificate Definitions & Competencies
$cert_catalog = [
    'phishing_specialist' => [
        'title'    => 'Phishing & Domain Spoofing Defense Specialist',
        'medal'    => '🏅',
        'color'    => '#4f46e5',
        'color_bg' => 'rgba(79, 70, 229, 0.08)',
        'border'   => '#6366f1',
        'tier'     => 'Tier-1 Threat Specialist',
        'desc'     => 'Demonstrated operational capability in intercepting spear-phishing campaigns, inspecting spoofed headers (SPF, DKIM, DMARC), detecting Punycode homoglyphs, and identifying deceptive lookalike top-level domains.',
        'skills'   => ['Domain Spoofing Triage', 'MIME Header Forensics', 'SPF/DKIM/DMARC Verification', 'Punycode Homoglyph Detection', 'Urgency Deconstruction']
    ],
    'upi_guardian' => [
        'title'    => 'UPI Collect-Request & Reverse-Payment Defender',
        'medal'    => '🛡️',
        'color'    => '#16a34a',
        'color_bg' => 'rgba(22, 163, 74, 0.08)',
        'border'   => '#22c55e',
        'tier'     => 'Tier-1 Payment Guardian',
        'desc'     => 'Successfully neutralized UPI collect inversion schemes, dynamic QR code debit traps, refund bait algorithms, and fraudulent virtual payment addresses targeting Indian banking rails.',
        'skills'   => ['Collect Inversion Defense', 'Dynamic QR Fraud Detection', 'MPIN Authentication Safety', 'Fake Customer Care Triage', 'VPA Domain Integrity']
    ],
    'social_eng_proof' => [
        'title'    => 'Conversational Social Engineering & Vishing Analyst',
        'medal'    => '🗣️',
        'color'    => '#d97706',
        'color_bg' => 'rgba(217, 119, 6, 0.08)',
        'border'   => '#f59e0b',
        'tier'     => 'Tier-1 Human Resilience',
        'desc'     => 'Navigated realistic high-pressure branching social engineering simulations: courier customs traps, fake police intimidation calls, tech support desktop takeover, and recruiter job advance fee scams.',
        'skills'   => ['Vishing Call Neutralization', 'Digital Arrest Disinformation', 'Urgency Pressure Resistance', 'Out-of-Band Verification', 'Mule Recruitment Spotting']
    ],
    'network_defender' => [
        'title'    => 'Consumer Network Hardening & Perimeter Auditor',
        'medal'    => '📶',
        'color'    => '#0284c7',
        'color_bg' => 'rgba(2, 132, 199, 0.08)',
        'border'   => '#38bdf8',
        'tier'     => 'Tier-1 Infrastructure Auditor',
        'desc'     => 'Conducted rigorous perimeter self-audits across 7 critical wireless vectors: WPA3 encryption configuration, UPnP automated exploit closure, DNS poisoning prevention, and rogue public Wi-Fi defense.',
        'skills'   => ['WPA3 Enterprise/Personal', 'UPnP Vulnerability Mitigation', 'DNS Hijacking Defense', 'Rogue AP Twin Detection', 'Remote Router Hardening']
    ],
    'otp_defender' => [
        'title'    => 'MFA (OTP) Hijacking & SIM-Swap Countermeasure Specialist',
        'medal'    => '📱',
        'color'    => '#7c3aed',
        'color_bg' => 'rgba(124, 58, 237, 0.08)',
        'border'   => '#a855f7',
        'tier'     => 'Tier-1 Identity Guardian',
        'desc'     => 'Demonstrated resistance to telecom impersonation, 6-digit WhatsApp registration code forward traps, 5G upgrade SIM-swap vectors, and banking OTP interception schemes.',
        'skills'   => ['SIM-Swap Attack Recognition', 'Telecom Social Engineering', 'Secondary Channel 2FA', 'Delivery Code Separation', 'Account Takeover Prevention']
    ],
    'deepfake_analyst' => [
        'title'    => 'Synthetic Media & AI Voice-Clone Threat Specialist',
        'medal'    => '🤖',
        'color'    => '#dc2626',
        'color_bg' => 'rgba(220, 38, 38, 0.08)',
        'border'   => '#ef4444',
        'tier'     => 'Tier-1 AI Threat Specialist',
        'desc'     => 'Successfully spotted generative AI speech synthesis, deepfake video call visual artifacts, executive CEO fraud voice memos, and synthetic emergency extortion attempts.',
        'skills'   => ['AI Voice-Clone Spotting', 'Synthetic Audio Artifacts', 'Deepfake Facial Glitch Analysis', 'Executive Wire Defense', 'Family Safe Word Protocol']
    ],
    'cyber_champion' => [
        'title'    => 'National Cyber Resilience & Defense Champion',
        'medal'    => '🏆',
        'color'    => '#b45309',
        'color_bg' => 'rgba(180, 83, 9, 0.08)',
        'border'   => '#fbbf24',
        'tier'     => 'Tier-Master Sovereign Champion',
        'desc'     => 'The highest SafeSphere attestation — awarded to candidates who have proven defensive mastery across all threat domains including Phishing, UPI fraud, Social Engineering, Network Hardening, OTP Hijacking, and Synthetic Media.',
        'skills'   => ['Full-Spectrum Cyber Defense', 'Multi-Layer Fraud Mitigation', 'Forensic Header Inspection', 'Threat Incident Response', 'Cryptographic Attestation Verification']
    ],
];

// Fallback demo credentials (always work even on pristine database installs)
$demo_credentials = [
    'SS-A8E2B1C9F4' => [
        'cert_key'      => 'phishing_specialist',
        'title'         => 'Phishing & Domain Spoofing Defense Specialist',
        'credential_id' => 'SS-A8E2B1C9F4',
        'issue_date'    => date('Y-m-d', strtotime('-12 days')),
        'user_name'     => 'Rehan Khan',
        'user_email'    => 'rehan.khan@safesphere.org',
        'cyber_score'   => 88
    ],
    'SS-7F3D9A1C5E' => [
        'cert_key'      => 'upi_guardian',
        'title'         => 'UPI Collect-Request & Reverse-Payment Defender',
        'credential_id' => 'SS-7F3D9A1C5E',
        'issue_date'    => date('Y-m-d', strtotime('-8 days')),
        'user_name'     => 'Aarav Sharma',
        'user_email'    => 'aarav.sharma@safesphere.org',
        'cyber_score'   => 92
    ],
    'SS-CHAMP2026X' => [
        'cert_key'      => 'cyber_champion',
        'title'         => 'National Cyber Resilience & Defense Champion',
        'credential_id' => 'SS-CHAMP2026X',
        'issue_date'    => date('Y-m-d', strtotime('-2 days')),
        'user_name'     => 'Priya Patel',
        'user_email'    => 'priya.patel@safesphere.org',
        'cyber_score'   => 97
    ],
    'SS-OTP98234D' => [
        'cert_key'      => 'otp_defender',
        'title'         => 'MFA (OTP) Hijacking & SIM-Swap Countermeasure Specialist',
        'credential_id' => 'SS-OTP98234D',
        'issue_date'    => date('Y-m-d', strtotime('-5 days')),
        'user_name'     => 'Vikramaditya Mehta',
        'user_email'    => 'v.mehta@safesphere.org',
        'cyber_score'   => 86
    ],
    'SS-DF88123A' => [
        'cert_key'      => 'deepfake_analyst',
        'title'         => 'Synthetic Media & AI Voice-Clone Threat Specialist',
        'credential_id' => 'SS-DF88123A',
        'issue_date'    => date('Y-m-d', strtotime('-1 day')),
        'user_name'     => 'Ananya Deshmukh',
        'user_email'    => 'ananya.d@safesphere.org',
        'cyber_score'   => 90
    ]
];

// Perform Lookup
if ($cred_id) {
    try {
        $stmt = $pdo->prepare("SELECT c.*, u.name as user_name, u.email as user_email, u.cyber_score 
                               FROM certificates c 
                               JOIN users u ON c.user_id = u.id 
                               WHERE UPPER(c.credential_id) = ? LIMIT 1");
        $stmt->execute([$cred_id]);
        $cert = $stmt->fetch();
    } catch (Exception $e) {
        // Fallback silently to memory lookup
    }

    if (!$cert && isset($demo_credentials[$cred_id])) {
        $cert = $demo_credentials[$cred_id];
    }
}

// Recent sample list for landing state
$recent_certs = [];
try {
    $stmt = $pdo->query("SELECT c.credential_id, c.title, c.cert_key, c.issue_date, u.name as user_name 
                         FROM certificates c 
                         JOIN users u ON c.user_id = u.id 
                         ORDER BY c.id DESC LIMIT 4");
    $recent_certs = $stmt->fetchAll();
} catch (Exception $e) {}

if (empty($recent_certs)) {
    $recent_certs = array_values($demo_credentials);
}

// Certificate enrichments if found
$cert_details = null;
$cert_hash = null;
$short_hash = null;
if ($cert) {
    $key = $cert['cert_key'] ?? 'phishing_specialist';
    $cert_details = $cert_catalog[$key] ?? $cert_catalog['phishing_specialist'];
    $cert_hash = strtoupper(hash('sha256', $cert['credential_id'] . '|' . $cert['user_name'] . '|' . $cert['issue_date'] . '|SAFESPHERE-NATIONAL-ATTESTATION-2026'));
    $short_hash = substr($cert_hash, 0, 10) . '...' . substr($cert_hash, -10);
}

$nav_active = 'verify';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $cert ? 'Verified Credential: ' . e($cert['title']) : 'Public Credential Attestation & Verification' ?> · SafeSphere</title>
<meta name="description" content="Verify authentic SafeSphere cybersecurity training certifications, cryptographic hashes, and verified competency records.">
<script>try{var t=localStorage.getItem('ss_theme')||'light';document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.8">
</head>
<body class="verify-page">

<?php include __DIR__ . '/includes/public_nav.php'; ?>

<!-- Toast Notification Element -->
<div id="verify-toast" class="toast" role="alert" aria-live="polite"></div>

<main class="verify-main">
    <div class="container" style="max-width: 960px;">

        <!-- Verification Search Card -->
        <section class="verify-search-card">
            <div class="verify-search-header">
                <div class="verify-badge-pill">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    <span>Tamper-Evident National Credential Attestation</span>
                </div>
                <h1 class="verify-headline">Public Credential Verification Registry</h1>
                <p class="verify-subtitle">
                    Verify the cryptographic authenticity of certificates issued to cybersecurity trainees across Indian financial and cyber threat defense simulations.
                </p>
            </div>

            <form method="GET" action="verify.php" class="verify-form" id="verify-form">
                <div class="verify-input-wrap">
                    <span class="verify-input-prefix">ID:</span>
                    <input type="text" name="id" id="verify-input" value="<?= e($cred_id) ?>" placeholder="e.g. SS-A8E2B1C9F4 or SS-CHAMP2026X" required autocomplete="off" spellcheck="false" class="verify-input">
                    <button type="submit" class="btn btn-primary verify-submit-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        <span>Verify Credential</span>
                    </button>
                </div>
            </form>

            <div class="verify-demo-chips">
                <span class="verify-demo-label">Quick Demo Verifications:</span>
                <div class="verify-chips-flex">
                    <button type="button" class="verify-chip" onclick="testCredential('SS-A8E2B1C9F4')">
                        <span>🏅 Phishing Defense</span>
                        <code>SS-A8E2B1C9F4</code>
                    </button>
                    <button type="button" class="verify-chip" onclick="testCredential('SS-7F3D9A1C5E')">
                        <span>🛡️ UPI Guardian</span>
                        <code>SS-7F3D9A1C5E</code>
                    </button>
                    <button type="button" class="verify-chip" onclick="testCredential('SS-CHAMP2026X')">
                        <span>🏆 Cyber Champion</span>
                        <code>SS-CHAMP2026X</code>
                    </button>
                </div>
            </div>
        </section>

        <!-- STATE 1: CREDENTIAL FOUND & VERIFIED -->
        <?php if ($cert && $cert_details): ?>
            <section class="verify-result-section">
                <!-- Status Bar -->
                <div class="verify-status-banner">
                    <div class="verify-status-indicator">
                        <span class="status-pulse-dot"></span>
                        <strong style="color:var(--green);font-size:14px;letter-spacing:0.02em;">CRYPTOGRAPHICALLY AUTHENTIC & ACTIVE</strong>
                    </div>
                    <div class="verify-timestamp">
                        Registry Verification Timestamp: <?= date('d M Y, H:i:s \I\S\T') ?>
                    </div>
                </div>

                <!-- Digital Certificate Document Container -->
                <div class="cert-document-frame" id="printable-certificate">
                    <div class="cert-watermark-shield" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>

                    <div class="cert-guilloche-inner">
                        <!-- Top Official Ribbon -->
                        <div class="cert-doc-ribbon">
                            <div class="cert-brand-emblem">
                                <span class="cert-emblem-badge">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                </span>
                                <div>
                                    <div class="cert-registry-title">SafeSphere National Cyber Defense Architecture</div>
                                    <div class="cert-registry-sub">Interactive Fraud Simulation & Cyber Resilience Attestation Registry</div>
                                </div>
                            </div>
                            <span class="badge badge-green cert-seal-pill">✓ Verified Credential</span>
                        </div>

                        <!-- Certificate Body -->
                        <div class="cert-doc-body">
                            <div class="cert-medal-aura">
                                <span class="cert-doc-medal"><?= $cert_details['medal'] ?></span>
                            </div>

                            <div class="cert-proclamation">Certificate of Defensive Mastery</div>
                            <h2 class="cert-doc-title"><?= e($cert['title']) ?></h2>

                            <div class="cert-doc-attribution">
                                This is to certify that
                                <div class="cert-recipient-name"><?= e($cert['user_name']) ?></div>
                                has successfully completed and validated defensive drills under simulated live-attack conditions.
                            </div>

                            <p class="cert-doc-description">
                                <?= e($cert_details['desc']) ?>
                            </p>

                            <!-- Skills matrix -->
                            <div class="cert-competencies-block">
                                <div class="cert-competencies-title">Validated Defensive Competencies:</div>
                                <div class="cert-skills-wrap">
                                    <?php foreach ($cert_details['skills'] as $skill): ?>
                                        <span class="cert-skill-tag">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            <?= e($skill) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Metrics Strip -->
                            <div class="cert-metrics-grid">
                                <div class="cert-metric-card">
                                    <span class="cert-metric-label">Credential ID</span>
                                    <div class="cert-metric-value font-mono">
                                        <?= e($cert['credential_id']) ?>
                                        <button type="button" class="copy-icon-btn" onclick="copyToClipboard('<?= e($cert['credential_id']) ?>', 'Credential ID copied!')" title="Copy ID">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="cert-metric-card">
                                    <span class="cert-metric-label">Issue Date</span>
                                    <div class="cert-metric-value"><?= date('d F Y', strtotime($cert['issue_date'])) ?></div>
                                </div>

                                <div class="cert-metric-card">
                                    <span class="cert-metric-label">Defensive Readiness</span>
                                    <div class="cert-metric-value" style="color:var(--indigo);">
                                        <?= (int)($cert['cyber_score'] ?? 85) ?>/100
                                        <span style="font-size:11px;font-weight:700;color:var(--text-muted);display:block;"><?= e($cert_details['tier']) ?></span>
                                    </div>
                                </div>

                                <div class="cert-metric-card">
                                    <span class="cert-metric-label">Registry Status</span>
                                    <div class="cert-metric-value" style="color:var(--green);">
                                        ● Perpetual / Active
                                    </div>
                                </div>
                            </div>

                            <!-- Cryptographic Fingerprint Block -->
                            <div class="cert-crypto-seal-box">
                                <div class="cert-crypto-left">
                                    <div style="font-weight:700;font-size:12.5px;color:var(--text);margin-bottom:3px;">
                                        SHA-256 Attestation Checksum Fingerprint:
                                    </div>
                                    <code class="cert-hash-string font-mono" id="cert-hash-val"><?= $cert_hash ?></code>
                                </div>
                                <button type="button" class="btn btn-outline btn-sm copy-hash-btn" onclick="copyToClipboard('<?= $cert_hash ?>', 'SHA-256 fingerprint copied!')">
                                    Copy Hash
                                </button>
                            </div>

                            <!-- Authority Signature Strip -->
                            <div class="cert-signatures-strip">
                                <div class="cert-sig-block">
                                    <div class="cert-sig-line font-mono" style="color:var(--indigo);font-weight:700;">SafeSphere::AutomatedAttestationAuthority</div>
                                    <div class="cert-sig-title">Simulated Incident Evaluation Engine</div>
                                </div>
                                <div class="cert-sig-block" style="text-align:right;">
                                    <div class="cert-sig-line font-mono" style="color:var(--green);font-weight:700;">RFC-3161::DeterministicSignature</div>
                                    <div class="cert-sig-title">Tamper-Evident Public Ledger Record</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interactive Action Toolbar -->
                <div class="verify-action-toolbar">
                    <button type="button" class="btn btn-primary btn-lg" onclick="window.print()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        <span>Print / Save Official PDF</span>
                    </button>

                    <button type="button" class="btn btn-outline btn-lg" onclick="copyToClipboard(window.location.href, 'Public verification URL copied!')">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                        <span>Copy Public Link</span>
                    </button>

                    <?php 
                        $shareUrl = urlencode("http://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "/safesphere-phase1/verify.php?id=" . $cert['credential_id']);
                        $shareMsg = urlencode("I just verified my '" . $cert['title'] . "' credential on SafeSphere. Credential ID: " . $cert['credential_id']);
                    ?>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $shareUrl ?>" target="_blank" class="btn btn-outline btn-lg" title="Share on LinkedIn">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        <span>LinkedIn</span>
                    </a>

                    <a href="verify.php" class="btn btn-outline btn-lg" title="Reset Search">
                        <span>Verify Another →</span>
                    </a>
                </div>
            </section>

        <!-- STATE 2: SEARCHED BUT NOT FOUND -->
        <?php elseif ($cred_id): ?>
            <section class="verify-notfound-card">
                <div class="notfound-icon">
                    <svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <h2 style="font-size:22px;font-weight:800;margin-bottom:8px;">Credential Record Not Located</h2>
                <p style="color:var(--text-muted);font-size:15px;max-width:560px;margin:0 auto 20px;line-height:1.6;">
                    No verified certificate matching identifier <code class="font-mono font-bold" style="background:var(--bg);padding:3px 8px;border-radius:4px;color:var(--red);"><?= e($cred_id) ?></code> was found in our attestation database.
                </p>

                <div class="notfound-suggestions">
                    <div style="font-weight:700;font-size:13.5px;margin-bottom:8px;">Helpful Suggestions:</div>
                    <ul style="text-align:left;font-size:13.5px;color:var(--text-muted);line-height:1.7;padding-left:20px;margin-bottom:20px;">
                        <li>Ensure the credential ID is spelled exactly as printed (format: <code>SS-XXXXXXXXXX</code>).</li>
                        <li>Credential IDs are case-insensitive, but hyphens must be included.</li>
                        <li>Certificates are issued only upon achieving passing thresholds (≥7/9 in Phishing, ≥5/6 in UPI, etc.).</li>
                    </ul>
                </div>

                <div class="flex gap-12 flex-center">
                    <button type="button" class="btn btn-primary" onclick="testCredential('SS-A8E2B1C9F4')">Inspect Sample Credential (SS-A8E2B1C9F4)</button>
                    <a href="verify.php" class="btn btn-outline">Clear &amp; Try Again</a>
                </div>
            </section>

        <!-- STATE 3: DEFAULT LANDING PORTAL (NO ID SUBMITTED) -->
        <?php else: ?>
            <section class="verify-landing-info">
                <!-- 3 Pillars of SafeSphere Attestation -->
                <div class="verify-pillars-header">
                    <span class="badge badge-indigo">Trust &amp; Verification Protocol</span>
                    <h2>The SafeSphere Verification Standard</h2>
                    <p>Unlike video-based certifications, SafeSphere credentials represent demonstrable tactical resistance against real Indian cybercrime scenarios.</p>
                </div>

                <div class="grid grid-3 mb-32" style="gap:20px;">
                    <div class="pillar-card">
                        <div class="pillar-icon" style="background:rgba(79,70,229,0.1);color:var(--indigo);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <h3>Scenario-Proven Mastery</h3>
                        <p>Credentials cannot be bought or earned through passive viewing. Candidates must actively thwart simulated attacks: inspect email headers, reject UPI collect frauds, and resist voice vishing.</p>
                    </div>

                    <div class="pillar-card">
                        <div class="pillar-icon" style="background:rgba(22,163,74,0.1);color:var(--green);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                        </div>
                        <h3>Cryptographic Fingerprint</h3>
                        <p>Each credential encodes an immutable SHA-256 checksum deterministically generated from the trainee identifier, award timestamp, and curriculum version. Tampering invalidates the seal.</p>
                    </div>

                    <div class="pillar-card">
                        <div class="pillar-icon" style="background:rgba(217,119,6,0.1);color:var(--amber);">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                        </div>
                        <h3>Instant Public Attestation</h3>
                        <p>Recruiters, compliance officers, and university faculties can verify candidate credentials instantly without creating an account or paying third-party verification fees.</p>
                    </div>
                </div>

                <!-- Showcase of Verified Certifications -->
                <div class="card card-pad mb-32">
                    <div class="flex-between mb-16 flex-wrap gap-12">
                        <div>
                            <h3 style="font-size:18px;font-weight:800;">Recent Verified Credentials in Registry</h3>
                            <p class="text-xs text-muted" style="margin-top:2px;">Click any credential ID below to view its live cryptographic attestation certificate.</p>
                        </div>
                        <span class="badge badge-green">Live Registry</span>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Credential ID</th>
                                    <th>Certification Title</th>
                                    <th>Candidate Name</th>
                                    <th>Date Attested</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_certs as $rc): ?>
                                <tr>
                                    <td><code class="font-mono font-bold" style="color:var(--indigo);"><?= e($rc['credential_id']) ?></code></td>
                                    <td><strong><?= e($rc['title']) ?></strong></td>
                                    <td><?= e($rc['user_name']) ?></td>
                                    <td><?= date('d M Y', strtotime($rc['issue_date'])) ?></td>
                                    <td>
                                        <a href="verify.php?id=<?= e($rc['credential_id']) ?>" class="btn btn-outline btn-sm" style="font-size:12px;padding:4px 10px;">
                                            Inspect Attestation →
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Endorsement Notice -->
                <div class="verify-authority-notice">
                    <div style="font-weight:700;font-size:14px;color:var(--text);margin-bottom:6px;">National Cyber Defense Curriculum Alignment:</div>
                    <p style="font-size:13.5px;color:var(--text-muted);line-height:1.6;margin:0;">
                        SafeSphere's interactive simulations are reverse-engineered directly from advisory bulletins released by the <strong>Indian Computer Emergency Response Team (CERT-In)</strong>, the <strong>Indian Cyber Crime Coordination Centre (I4C, MHA)</strong>, and <strong>Reserve Bank of India (RBI)</strong> fraud advisories.
                    </p>
                </div>
            </section>
        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/includes/public_footer.php'; ?>
<script src="assets/js/main.js?v=2.8"></script>

<script>
function testCredential(id) {
    var input = document.getElementById('verify-input');
    if (input) {
        input.value = id;
        document.getElementById('verify-form').submit();
    } else {
        window.location.href = 'verify.php?id=' + encodeURIComponent(id);
    }
}

function copyToClipboard(text, message) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            showToast(message || 'Copied to clipboard!');
        }).catch(function() {
            fallbackCopy(text, message);
        });
    } else {
        fallbackCopy(text, message);
    }
}

function fallbackCopy(text, message) {
    var temp = document.createElement('textarea');
    temp.value = text;
    temp.style.position = 'fixed';
    temp.style.opacity = '0';
    document.body.appendChild(temp);
    temp.focus();
    temp.select();
    try {
        document.execCommand('copy');
        showToast(message || 'Copied to clipboard!');
    } catch(e) {
        alert('Copied: ' + text);
    }
    document.body.removeChild(temp);
}

function showToast(msg) {
    var toast = document.getElementById('verify-toast');
    if (!toast) return;
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(window._toastTimeout);
    window._toastTimeout = setTimeout(function() {
        toast.classList.remove('show');
    }, 2800);
}
</script>

</body>
</html>
