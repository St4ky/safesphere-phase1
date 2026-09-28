<?php
require_once __DIR__ . '/includes/auth.php';

$_live_stats = ['users' => 0, 'attempts' => 0, 'certs' => 0];
try {
    $pdo = get_db();
    $_live_stats['users']    = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $_live_stats['attempts'] = (int)$pdo->query("SELECT COUNT(*) FROM attempts")->fetchColumn();
    $_live_stats['certs']    = (int)$pdo->query("SELECT COUNT(*) FROM certificates")->fetchColumn();
} catch (Exception $e) { /* silently fail */ }

$nav_active = 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<script>try{var t=localStorage.getItem('ss_theme')||'light';document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SafeSphere — National Cyber Resilience & Interactive Defense Platform</title>
<meta name="description" content="Simulate real Indian cyber fraud attacks: UPI scams, Digital Arrest extortion, phishing links, and social engineering in a secure interactive sandbox.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.9">
</head>
<body>

<!-- Navigation -->
<?php include __DIR__ . '/includes/public_nav.php'; ?>

<!-- Hero Section -->
<section class="landing-hero" style="padding-bottom: 40px;">
    <div style="display:inline-flex;align-items:center;gap:8px;padding:5px 14px;border-radius:999px;background:var(--indigo-light);color:var(--indigo-dark);font-size:12.5px;font-weight:700;margin-bottom:16px;border:1px solid rgba(79,70,229,0.2);">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        <span>National Cyber Threat Simulation & Defense Architecture</span>
    </div>
    <h1 style="max-width: 860px; margin: 0 auto 18px;">
        Don't Just Read About Cyber Fraud.<br>
        <span style="background:linear-gradient(135deg,#2563eb,#7c3aed);-webkit-background-clip:text;-webkit-text-fill-color:transparent;">Train to Defend in Real Time.</span>
    </h1>
    <p style="max-width: 680px; font-size: 16.5px; line-height: 1.65;">
        Master the techniques used by modern threat actors targeting India. Hands-on simulations of UPI collect scams, Digital Arrest extortion, spoofed banking portals, and AI deepfake fraud — backed by live forensic APIs.
    </p>
    <div class="flex gap-16 flex-center mt-32">
        <a href="register.php" class="btn btn-primary btn-lg" id="hero-cta">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
            Launch Free Training Lab
        </a>
        <a href="#quick-scanner" class="btn btn-outline btn-lg">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            Live Threat Sandbox
        </a>
    </div>
    <div class="flex gap-24 flex-center mt-24 text-sm text-muted">
        <span>✓ 100% Free for Individual Learners</span>
        <span>✓ Zero Installation or Setup</span>
        <span>✓ Verifiable Cyber Certification</span>
    </div>
</section>

<!-- LIVE THREAT SANDBOX (GUEST TOOL) -->
<section id="quick-scanner" style="padding: 20px 0 60px; background: var(--bg);">
    <div class="container" style="max-width: 900px;">
        <div class="scanner-card">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px;flex-wrap:wrap;gap:12px;">
                <div>
                    <span class="badge badge-indigo" style="margin-bottom:6px;">Live Threat Intelligence API</span>
                    <h2 style="font-size:22px;font-weight:800;letter-spacing:-0.02em;">Instant Scam & Threat Analyzer</h2>
                    <p style="font-size:13.5px;color:var(--text-muted);margin-top:4px;">
                        Paste any suspicious URL, UPI ID, or SMS message text to inspect DNS integrity, brand spoofing, and fraud heuristics in real time.
                    </p>
                </div>
                <span class="badge badge-gray" style="font-size:11px;">Powered by Cloudflare DoH & Heuristics</span>
            </div>

            <div class="scanner-tabs">
                <button type="button" class="scanner-tab active" onclick="setScannerPreset('url', this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    <span>Suspicious Link / Domain</span>
                </button>
                <button type="button" class="scanner-tab" onclick="setScannerPreset('sms', this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    <span>Electricity / Job SMS Text</span>
                </button>
                <button type="button" class="scanner-tab" onclick="setScannerPreset('upi', this)">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path></svg>
                    <span>UPI Virtual Address (VPA)</span>
                </button>
            </div>

            <form id="quick-scanner-form">
                <div class="scanner-box">
                    <input type="text" id="quick-scanner-input" class="scanner-input" placeholder="e.g. sbi-kyc-verification.xyz or paste SMS text..." value="sbi-kyc-update.xyz" required autocomplete="off">
                    <button type="submit" id="quick-scanner-btn" class="btn btn-primary" style="white-space:nowrap;display:inline-flex;align-items:center;justify-content:center;gap:8px;">Inspect Risk →</button>
                </div>
            </form>

            <div id="quick-scanner-result" class="scanner-result"></div>
        </div>
    </div>
</section>

<!-- Live Stat Metrics Strip -->
<div class="stat-strip stat-strip-5">
    <div class="stat-strip-item">
        <div class="stat-number" data-counter="<?= max(1, (int)$_live_stats['users']) ?>" data-suffix="<?= $_live_stats['users'] > 1 ? '+' : '' ?>">0</div>
        <div class="stat-label">Active Cyber Trainees</div>
    </div>
    <div class="stat-strip-item">
        <div class="stat-number" data-counter="35" data-suffix="">0</div>
        <div class="stat-label">Interactive Lab Scenarios</div>
    </div>
    <div class="stat-strip-item">
        <div class="stat-number" data-counter="8" data-suffix="">0</div>
        <div class="stat-label">Specialized Threat Modules</div>
    </div>
    <div class="stat-strip-item">
        <div class="stat-number" data-counter="11333" data-suffix="Cr+">0</div>
        <div class="stat-label">₹ Lost to UPI Fraud in India (FY24)</div>
    </div>
    <div class="stat-strip-item">
        <div class="stat-number" data-counter="<?= (int)$_live_stats['certs'] ?>" data-suffix="">0</div>
        <div class="stat-label">Verifiable Certificates Issued</div>
    </div>
</div>

<!-- 8 CORE SIMULATION LABS -->
<section class="section" style="background:var(--bg);">
    <div class="container">
        <div class="section-header">
            <span class="badge badge-indigo mb-8">Simulation Curriculum</span>
            <h2>8 Specialized Cyber Defense Modules</h2>
            <p>Every scenario is reverse-engineered from real incidents documented by Indian cybersecurity authorities, RBI advisories, and CERT-In bulletins.</p>
        </div>
        <div class="grid grid-4" style="gap:20px;">
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(37,99,235,0.1);color:var(--indigo);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
                </div>
                <h3>Email Phishing & Domain Spoofing</h3>
                <p>Inspect realistic Indian mail streams: fake SBI KYC suspensions, IRCTC refund traps, and Income Tax Department demands. Learn to identify header spoofs, SPF/DKIM/DMARC failures, homoglyph domains, and lookalike TLDs.</p>
                <div class="mt-16 text-xs text-muted" style="border-top:1px solid var(--border);padding-top:12px;">9 Scenarios · Live Header Analysis · Scored</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(16,185,129,0.1);color:var(--green);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path></svg>
                </div>
                <h3>UPI Collect-Request & Reverse-Payment Fraud</h3>
                <p>Interactive simulations of PhonePe, GPay, and Paytm collect requests. Master collect inversion, QR scams, MPIN risks, and the golden rule: entering your UPI PIN always DEBITS funds from your account.</p>
                <div class="mt-16 text-xs text-muted" style="border-top:1px solid var(--border);padding-top:12px;">6 Scenarios · QR Reverse Scams · PIN Safety</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(245,158,11,0.1);color:var(--amber);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <h3>Social Engineering & Vishing Resistance</h3>
                <p>Dynamic branching dialogues with simulated scammers on WhatsApp and Telegram. Encounter AI-scripted vishing, courier fraud, digital arrest calls, fake HR recruiters, and tech support takeovers.</p>
                <div class="mt-16 text-xs text-muted" style="border-top:1px solid var(--border);padding-top:12px;">Interactive Chat Engine · Branching Decisions</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(59,130,246,0.1);color:var(--blue);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
                </div>
                <h3>Network Hardening & Self-Audit Protocol</h3>
                <p>Evaluate your home and office router defenses: WPA3 encryption protocols, remote admin ports, UPnP exposures, DNS leaks, router exploits, and public Wi-Fi attacks.</p>
                <div class="mt-16 text-xs text-muted" style="border-top:1px solid var(--border);padding-top:12px;">7 Audit Points · Live IP Assessment · Remediation Guides</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(124,58,237,0.1);color:#7c3aed;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </div>
                <h3>OTP Hijacking & SIM-Swap Defense</h3>
                <p>Master the mechanics of OTP social engineering — vishing calls posing as bank officials, WhatsApp account takeover via 6-digit code sharing, and SIM swap attacks that route all your banking SMS OTPs to attackers.</p>
                <div class="mt-16 text-xs text-muted" style="border-top:1px solid var(--border);padding-top:12px;">5 Scenarios · SIM Swap · Vishing Call Defense</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(239,68,68,0.1);color:var(--red);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                </div>
                <h3>Deepfake & AI Voice-Clone Recognition</h3>
                <p>Recognize AI-cloned executive voices, manipulated video calls, and synthetic face artifacts utilized in modern CEO fraud, executive impersonation, and virtual kidnapping extortion.</p>
                <div class="mt-16 text-xs text-muted" style="border-top:1px solid var(--border);padding-top:12px;">AI Voice Analysis · Visual Artifact Spotting</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(147,51,234,0.1);color:#9333ea;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <h3>Forensic Inspection & Password Entropy</h3>
                <p>Equipped with live security APIs: verify domain DNS/SPF/MX integrity via Cloudflare DoH, test password exposure against HaveIBeenPwned breaches, trace sender IPs, and perform APK permission analysis.</p>
                <div class="mt-16 text-xs text-muted" style="border-top:1px solid var(--border);padding-top:12px;">Live Cloudflare DoH · HIBP Breach API · IP Intel</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(16,185,129,0.1);color:var(--green);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                </div>
                <h3>Tamper-Evident Credential Verification</h3>
                <p>SafeSphere certificates embed cryptographically unique 10-character hex credential IDs. Any third party can verify authenticity on the public attestation portal — no central database required.</p>
                <div class="mt-16 text-xs text-muted" style="border-top:1px solid var(--border);padding-top:12px;">6 Certificates · Public Verify Portal · PDF Export</div>
            </div>
        </div>
    </div>
</section>

<!-- INTERACTIVE 60-SECOND CYBER VULNERABILITY ASSESSMENT -->
<section class="section" style="background:var(--surface);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
    <div class="container" style="max-width:860px;">
        <div class="section-header">
            <span class="badge badge-indigo mb-8">Self-Diagnostic</span>
            <h2>60-Second Cyber Risk Diagnostic</h2>
            <p>Answer 3 quick questions to benchmark your personal susceptibility to common digital fraud patterns.</p>
        </div>

        <form id="risk-quiz-form" style="background:var(--bg);border:1px solid var(--border);border-radius:var(--radius-lg);padding:30px;">
            <div style="margin-bottom:24px;">
                <label style="font-weight:700;font-size:15px;display:block;margin-bottom:10px;">
                    1. Someone on OLX/Marketplace offers to send you money via UPI and sends a QR code asking you to scan it and enter your UPI PIN. What happens?
                </label>
                <div style="display:grid;gap:8px;">
                    <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer;">
                        <input type="radio" name="q_upi" value="good" required>
                        <span style="font-size:14px;">Money will be DEDUCTED from my bank account. Entering a PIN is only for sending money.</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer;">
                        <input type="radio" name="q_upi" value="bad">
                        <span style="font-size:14px;">Money will be deposited directly into my bank account after verification.</span>
                    </label>
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label style="font-weight:700;font-size:15px;display:block;margin-bottom:10px;">
                    2. Do you reuse the same password or slight variations across your personal email and banking apps?
                </label>
                <div style="display:grid;gap:8px;">
                    <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer;">
                        <input type="radio" name="q_pwd" value="good" required>
                        <span style="font-size:14px;">No, I use unique credentials with a password manager and have 2FA enabled.</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer;">
                        <input type="radio" name="q_pwd" value="bad">
                        <span style="font-size:14px;">Yes, I reuse 1–2 master passwords to remember them easily.</span>
                    </label>
                </div>
            </div>

            <div style="margin-bottom:24px;">
                <label style="font-weight:700;font-size:15px;display:block;margin-bottom:10px;">
                    3. You receive a video call claiming to be the Mumbai Police/CBI saying a parcel containing illegal contraband in your name was seized and you are under "Digital Arrest". What is the first thing you should do?
                </label>
                <div style="display:grid;gap:8px;">
                    <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer;">
                        <input type="radio" name="q_call" value="good" required>
                        <span style="font-size:14px;">Disconnect immediately. Indian law enforcement never conducts arrests or inquiries over Skype/WhatsApp video calls.</span>
                    </label>
                    <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid var(--border);border-radius:6px;background:var(--surface);cursor:pointer;">
                        <input type="radio" name="q_call" value="bad">
                        <span style="font-size:14px;">Cooperate and transfer security deposit funds to their designated RBI clearing account to avoid arrest.</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Calculate My Vulnerability Rating →</button>
            <div id="risk-quiz-result" style="display:none;"></div>
        </form>
    </div>
</section>

<!-- EMERGENCY GOLDEN HOUR PROTOCOL -->
<section class="section" style="background:var(--bg);">
    <div class="container">
        <div class="grid grid-2" style="gap:48px;align-items:center;">
            <div>
                <span class="badge badge-red mb-12">Emergency Incident Response</span>
                <h2 style="font-size:32px;font-weight:800;letter-spacing:-0.02em;margin-bottom:16px;">The "Golden Hour" Protocol for Cyber Fraud Victims</h2>
                <p style="font-size:15.5px;color:var(--text-muted);line-height:1.7;margin-bottom:20px;">
                    If you or a colleague have transferred funds or shared banking OTPs, the first <strong>60 to 120 minutes</strong> determine whether lost money can be frozen before the attacker withdraws it through layered mule accounts.
                </p>
                <div style="display:grid;gap:12px;">
                    <div style="display:flex;gap:14px;align-items:flex-start;">
                        <div style="width:28px;height:28px;border-radius:50%;background:var(--indigo-light);color:var(--indigo);display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">1</div>
                        <div>
                            <strong style="font-size:15px;">Dial 1930 Immediately</strong>
                            <div style="font-size:13.5px;color:var(--text-muted);margin-top:2px;">The National Cybercrime Helpline connects directly to Indian bank nodal fraud officers to block beneficiary accounts.</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:14px;align-items:flex-start;">
                        <div style="width:28px;height:28px;border-radius:50%;background:var(--indigo-light);color:var(--indigo);display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">2</div>
                        <div>
                            <strong style="font-size:15px;">Register Complaint on cybercrime.gov.in</strong>
                            <div style="font-size:13.5px;color:var(--text-muted);margin-top:2px;">File within 2 hours. Keep bank statement, UTR reference numbers, SMS screenshots, and scammer phone numbers ready.</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:14px;align-items:flex-start;">
                        <div style="width:28px;height:28px;border-radius:50%;background:var(--indigo-light);color:var(--indigo);display:flex;align-items:center;justify-content:center;font-weight:800;flex-shrink:0;">3</div>
                        <div>
                            <strong style="font-size:15px;">Invoke RBI Zero-Liability Protection</strong>
                            <div style="font-size:13.5px;color:var(--text-muted);margin-top:2px;">Under RBI Circular DBR.No.Leg.BC.78/09.07.005/2017-18, reporting third-party breach within 3 working days protects you from financial liability.</div>
                        </div>
                    </div>
                </div>
            </div>
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:32px;box-shadow:var(--shadow-md);">
                <h3 style="font-size:20px;font-weight:800;margin-bottom:14px;">National Cybercrime Helplines</h3>
                <div style="display:grid;gap:12px;margin-bottom:24px;">
                    <div style="padding:14px;border-radius:8px;background:var(--bg);border:1px solid var(--border);">
                        <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;font-weight:700;">Financial Cyber Fraud Toll-Free</div>
                        <div style="font-size:24px;font-weight:800;color:var(--red);margin-top:2px;">1930</div>
                        <div style="font-size:12.5px;color:var(--text-muted);margin-top:2px;">Active 24/7 across all Indian States & UTs</div>
                    </div>
                    <div style="padding:14px;border-radius:8px;background:var(--bg);border:1px solid var(--border);">
                        <div style="font-size:12px;color:var(--text-muted);text-transform:uppercase;font-weight:700;">Official Portal</div>
                        <div style="font-size:16px;font-weight:700;color:var(--indigo);margin-top:2px;">cybercrime.gov.in</div>
                        <div style="font-size:12.5px;color:var(--text-muted);margin-top:2px;">Ministry of Home Affairs (MHA), Government of India</div>
                    </div>
                </div>
                <a href="threats.php" class="btn btn-outline btn-block">Explore Full Threat Radar & Intel →</a>
            </div>
        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="cta-section">
    <div class="container" style="position:relative;">
        <span class="badge badge-indigo" style="background:rgba(255,255,255,0.15);color:#fff;border-color:rgba(255,255,255,0.3);margin-bottom:12px;">Start Defensive Drills Today</span>
        <h2 style="font-size:clamp(28px, 4vw, 42px);font-weight:800;">Build Instincts That Prevent Real Financial Loss</h2>
        <p style="max-width:600px;margin:16px auto 0;color:rgba(255,255,255,0.85);font-size:16px;line-height:1.6;">
            Free account setup takes 30 seconds. Track your Cyber Score from 40 to 100 and earn verifiable certificates.
        </p>
        <div class="flex gap-16 flex-center mt-32">
            <a href="register.php" class="btn btn-white btn-lg" id="footer-cta">Create Free Account</a>
            <a href="login.php" class="btn btn-ghost btn-lg">Sign In to Dashboard →</a>
        </div>
    </div>
</section>

<script>
function setScannerPreset(type, el) {
    document.querySelectorAll('.scanner-tab').forEach(t => t.classList.remove('active'));
    if (el) el.classList.add('active');
    const input = document.getElementById('quick-scanner-input');
    if (type === 'url') {
        input.value = 'sbi-kyc-update.xyz';
        input.placeholder = 'e.g. sbi-kyc-verification.xyz or netbanking-secure.online';
    } else if (type === 'sms') {
        input.value = 'Dear customer your electricity power will be disconnect tonight at 9:30 pm from electricity office call 9876543210';
        input.placeholder = 'Paste suspicious SMS message text...';
    } else if (type === 'upi') {
        input.value = 'customercare-refund-paytm@okaxis';
        input.placeholder = 'e.g. refund-desk@okicici';
    }
}
</script>

<?php include __DIR__ . '/includes/public_footer.php'; ?>
<script src="assets/js/main.js?v=2.9"></script>
</body>
</html>
