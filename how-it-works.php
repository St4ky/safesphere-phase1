<?php $nav_active = 'howitworks'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>How SafeSphere Works — Cyber Awareness Training India</title>
<script>try{var t=localStorage.getItem('ss_theme')||'light';document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
<meta name="description" content="See how SafeSphere's phishing simulator, UPI fraud simulator, social engineering chat, network audit, and forensic toolkit work together to build real cyber resilience.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.7">
</head>
<body>
<?php include __DIR__ . '/includes/public_nav.php'; ?>

<section class="page-hero">
    <div class="container">
        <span class="badge badge-indigo">Platform Guide</span>
        <h1 class="mt-16">How SafeSphere Works</h1>
        <p>A complete walkthrough of every module, tool, and feature — so you know exactly what to expect when you start training.</p>
    </div>
</section>

<!-- Getting Started Steps -->
<section class="info-section" style="background:var(--surface);">
    <div class="container">
        <div class="section-header">
            <h2>Get Started in 3 Steps</h2>
            <p>You go from zero to training in under 2 minutes. No apps to install, no credit card, no setup.</p>
        </div>
        <div class="steps-grid">
            <div class="step-item">
                <div class="step-num">1</div>
                <h3>Create Free Account</h3>
                <p>Sign up with just your name and email. Your Cyber Score starts at 40 — a neutral baseline — so you have room to go up and motivation to improve.</p>
            </div>
            <div class="step-item">
                <div class="step-num">2</div>
                <h3>Pick a Module</h3>
                <p>Start with Phishing Defense (recommended), or jump straight to the UPI Simulator if that's your concern. Each module is completely independent.</p>
            </div>
            <div class="step-item">
                <div class="step-num">3</div>
                <h3>Make Decisions, Learn Fast</h3>
                <p>Every decision is scored in real time. Right answer = +10 points and a detailed explanation. Wrong answer = −5 points and an even more detailed explanation of why.</p>
            </div>
        </div>
    </div>
</section>

<!-- Module Walkthroughs -->
<section class="info-section">
    <div class="container">
        <div class="section-header">
            <h2>The Training Modules</h2>
            <p>8 specialized modules, each targeting a different attack vector. Together they cover the full spectrum of threats Indian internet users face.</p>
        </div>

        <!-- Module 1: Phishing -->
        <div class="grid grid-2" style="gap:40px;align-items:center;margin-bottom:56px;">
            <div>
                <div class="feature-icon" style="background:var(--indigo-light);width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:16px;">📧</div>
                <h3 style="font-size:24px;font-weight:800;margin-bottom:12px;">Module 1: Phishing Defense</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">
                    You're presented with a realistic email inbox containing 9 messages — a mix of phishing attempts and legitimate emails from Indian banks, e-commerce sites, and government departments.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:20px;">
                    Your job: sort each email as phishing or legitimate. After each verdict, you see exactly which red flags to look for — spoofed domains, mismatched Reply-To headers, urgency tactics, and fake links.
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="badge badge-indigo">9 scenarios</span>
                    <span class="badge badge-gray">SBI • IRCTC • HDFC • Amazon</span>
                    <span class="badge badge-green">+10 / −5 pts per decision</span>
                </div>
            </div>
            <div class="card" style="overflow:hidden;">
                <div style="background:var(--bg);padding:12px 16px;border-bottom:1px solid var(--border);font-size:13px;font-weight:700;color:var(--text-muted);display:flex;gap:6px;align-items:center;">
                    <span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                    <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                    <span style="width:10px;height:10px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                    <span style="margin-left:8px;">Inbox Simulator</span>
                </div>
                <div style="padding:18px;">
                    <?php $sample_emails = [
                        ['SBI Alert', 'noreply@sbi-kyc-update.xyz', '⚠️ URGENT: KYC Update Required', true],
                        ['IRCTC Official', 'tickets@irctc.co.in', 'Your PNR 4521837821 confirmed', false],
                        ['Income Tax Dept', 'it.refund@incometax-refunds.net', 'IT Refund ₹8,240 pending', true],
                    ];
                    foreach ($sample_emails as $i => $em): ?>
                    <div style="display:flex;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);align-items:center;">
                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#a5b4fc,#6366f1);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:12px;flex-shrink:0;">
                            <?= strtoupper(substr($em[0],0,1)) ?>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-weight:700;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= $em[0] ?></div>
                            <div style="font-size:11px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= $em[2] ?></div>
                        </div>
                        <span class="badge <?= $em[3] ? 'badge-red' : 'badge-green' ?>" style="font-size:9px;">
                            <?= $em[3] ? 'PHISHING' : 'LEGIT' ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Module 2: UPI -->
        <div class="grid grid-2" style="gap:40px;align-items:center;margin-bottom:56px;">
            <div class="card card-pad" style="background:#1a1a2e;border-color:#333;color:white;">
                <div style="font-size:11px;font-weight:700;letter-spacing:.08em;color:#94a3b8;margin-bottom:8px;">PHONEPE COLLECT REQUEST</div>
                <div style="text-align:center;padding:16px 0;">
                    <div style="font-size:11px;color:#94a3b8;margin-bottom:4px;">REQUEST AMOUNT</div>
                    <div style="font-size:42px;font-weight:800;color:#ef4444;">₹1</div>
                    <div style="font-size:13px;color:#94a3b8;margin-top:4px;">from <strong style="color:white;">SBI Bank KYC</strong></div>
                </div>
                <div style="background:#0f172a;border-radius:8px;padding:12px;font-size:12.5px;color:#94a3b8;line-height:1.6;margin-bottom:16px;">
                    "KYC verification — accept ₹1 to confirm your bank account is active. Account will be deactivated in 24h if not verified."
                </div>
                <div style="display:flex;gap:8px;">
                    <div style="flex:1;background:#dc2626;border-radius:8px;padding:10px;text-align:center;font-weight:700;font-size:14px;color:white;">✕ Reject</div>
                    <div style="flex:1;background:#16a34a;border-radius:8px;padding:10px;text-align:center;font-weight:700;font-size:14px;color:white;">✓ Accept</div>
                </div>
            </div>
            <div>
                <div class="feature-icon" style="background:var(--green-light);width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:16px;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path></svg>
                </div>
                <h3 style="font-size:24px;font-weight:800;margin-bottom:12px;">Module 2: UPI Fraud Simulator</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">
                    A phone-style interface presents UPI collect requests and QR code scenarios. You see exactly what a fraud attempt looks like on your GPay or PhonePe screen.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:20px;">
                    The key insight most people learn: <strong>accepting a collect request means you send money</strong> — not receive it. After each decision, you get a full breakdown of why the request was fraud (or legitimate).
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="badge badge-green">6 scenarios</span>
                    <span class="badge badge-gray">GPay • PhonePe • Paytm</span>
                    <span class="badge badge-amber">Collect requests • QR scams</span>
                </div>
            </div>
        </div>

        <!-- Module 3: Social Eng -->
        <div class="grid grid-2" style="gap:40px;align-items:center;margin-bottom:56px;">
            <div>
                <div class="feature-icon" style="background:var(--amber-light);width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:16px;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <h3 style="font-size:24px;font-weight:800;margin-bottom:12px;">Module 3: Social Engineering Chat</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">
                    A WhatsApp-style chat interface puts you in conversation with a simulated scammer. You choose how to respond — and the conversation branches based on your decisions.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:20px;">
                    Scenarios include: a fake bank KYC call, a job offer scam, a tech support takeover, and a romance scam asking for money. Each tests your ability to identify manipulation and disengage.
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="badge badge-amber">4 scenarios</span>
                    <span class="badge badge-gray">Branching chat engine</span>
                    <span class="badge badge-indigo">Multiple decision points each</span>
                </div>
            </div>
            <div class="card" style="overflow:hidden;">
                <div style="background:#075e54;padding:12px 16px;display:flex;align-items:center;gap:10px;">
                    <div style="width:36px;height:36px;border-radius:50%;background:#dc2626;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;">AX</div>
                    <div>
                        <div style="color:white;font-weight:700;font-size:14px;">KYC Support — Axis Bank</div>
                        <div style="color:rgba(255,255,255,0.7);font-size:12px;">● Online</div>
                    </div>
                </div>
                <div style="padding:16px;background:var(--bg);display:flex;flex-direction:column;gap:10px;">
                    <div style="background:var(--surface);color:var(--text);padding:10px 12px;border-radius:8px;border-bottom-left-radius:2px;font-size:13.5px;max-width:80%;line-height:1.6;border:1px solid var(--border);box-shadow:var(--shadow-sm);">
                        Your account will be suspended in 2 hours unless you complete KYC re-verification now.
                    </div>
                    <div style="background:var(--indigo-light);color:var(--indigo-dark);padding:10px 12px;border-radius:8px;border-bottom-right-radius:2px;font-size:13.5px;max-width:80%;align-self:flex-end;line-height:1.6;border:1px solid rgba(99,102,241,0.25);box-shadow:var(--shadow-sm);">
                        I'll call the bank's official number myself.
                    </div>
                    <div style="background:var(--surface);color:var(--text);padding:10px 12px;border-radius:8px;border-bottom-left-radius:2px;font-size:13.5px;max-width:80%;line-height:1.6;border:1px solid var(--border);box-shadow:var(--shadow-sm);">
                        I cannot give you a callback number for security reasons...
                    </div>
                </div>
            </div>
        </div>

        <!-- Module 4: Network -->
        <div class="grid grid-2" style="gap:40px;align-items:center;">
            <div class="card card-pad">
                <div style="font-size:14px;font-weight:700;margin-bottom:14px;">🛡️ Network Security Audit</div>
                <?php $checks = [
                    ['🔑', 'Change Router Default Password', 'pass'],
                    ['📶', 'Use WPA2 or WPA3 Encryption', 'pass'],
                    ['🏠', 'Guest Network for IoT Devices', 'pending'],
                    ['🔄', 'Keep Router Firmware Updated', 'fail'],
                    ['🔌', 'Disable UPnP', 'pending'],
                ];
                foreach ($checks as $c): ?>
                <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border);">
                    <span><?= $c[0] ?></span>
                    <span style="flex:1;font-size:13.5px;"><?= $c[1] ?></span>
                    <span class="badge <?= $c[2] === 'pass' ? 'badge-green' : ($c[2] === 'fail' ? 'badge-red' : 'badge-gray') ?>" style="font-size:10px;">
                        <?= $c[2] === 'pass' ? '✓ Pass' : ($c[2] === 'fail' ? '✕ Fail' : 'Pending') ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <div>
                <div class="feature-icon" style="background:var(--red-light);width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:16px;">📶</div>
                <h3 style="font-size:24px;font-weight:800;margin-bottom:12px;">Module 4: Network Self-Audit</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">
                    A 7-item interactive checklist that walks you through your home router security settings. Each item explains the risk, why it matters, and gives step-by-step instructions to fix it.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:20px;">
                    Unlike other modules, your audit status persists across sessions — so you can fix one thing, come back tomorrow, and continue where you left off.
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="badge badge-red">7 security checks</span>
                    <span class="badge badge-gray">Persistent status tracking</span>
                    <span class="badge badge-indigo">Step-by-step fix guides</span>
                </div>
            </div>
        </div>
    </div>
        <!-- Module 5: OTP -->
        <div class="grid grid-2" style="gap:40px;align-items:center;margin-top:56px;margin-bottom:56px;">
            <div>
                <div class="feature-icon" style="background:rgba(124,58,237,0.1);color:#7c3aed;width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:16px;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </div>
                <h3 style="font-size:24px;font-weight:800;margin-bottom:12px;">Module 5: Multi-Factor Authentication (OTP) Hijacking & SIM-Swap Countermeasures</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">
                    Master the mechanics of OTP social engineering. Encounter simulations of vishing calls posing as bank officials and SIM swap attacks that route all your banking SMS OTPs to attackers.
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="badge badge-indigo">5 Scenarios</span>
                    <span class="badge badge-gray">SIM Swap</span>
                    <span class="badge badge-amber">Vishing Call Defense</span>
                </div>
            </div>
            <div class="card card-pad">
                <div style="font-size:14px;font-weight:700;margin-bottom:14px;">📱 OTP Hijacking Defense</div>
                <p style="font-size:13px;color:var(--text-muted);line-height:1.6;">Learn to recognize the signs of a compromised phone number and how to safely respond to unprompted OTP requests.</p>
            </div>
        </div>

        <!-- Module 6: Deepfake -->
        <div class="grid grid-2" style="gap:40px;align-items:center;margin-bottom:56px;">
            <div class="card card-pad" style="background:#1e1e1e;color:white;border-color:#333;">
                <div style="font-size:14px;font-weight:700;margin-bottom:14px;color:#ef4444;">🎙️ AI Voice Clone Alert</div>
                <p style="font-size:13px;color:#94a3b8;line-height:1.6;">Detecting unnatural pauses, robotic inflections, and emotional urgency in simulated executive deepfake audio.</p>
            </div>
            <div>
                <div class="feature-icon" style="background:rgba(239,68,68,0.1);color:var(--red);width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:16px;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                </div>
                <h3 style="font-size:24px;font-weight:800;margin-bottom:12px;">Module 6: Synthetic Media (Deepfake) & AI Voice-Clone Threat Recognition</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">
                    Recognize AI-cloned executive voices, manipulated video calls, and synthetic face artifacts utilized in modern CEO fraud, executive impersonation, and virtual kidnapping extortion.
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="badge badge-red">AI Voice Analysis</span>
                    <span class="badge badge-gray">Visual Artifact Spotting</span>
                </div>
            </div>
        </div>

        <!-- Module 7: Forensic -->
        <div class="grid grid-2" style="gap:40px;align-items:center;margin-bottom:56px;">
            <div>
                <div class="feature-icon" style="background:rgba(147,51,234,0.1);color:#9333ea;width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:16px;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <h3 style="font-size:24px;font-weight:800;margin-bottom:12px;">Module 7: Rule-Based Client-Side Forensic Inspection & Password Entropy Analysis</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">
                    Equipped with live security APIs: verify domain DNS/SPF/MX integrity via Cloudflare DoH, test password exposure against HaveIBeenPwned breaches, trace sender IPs, and perform APK permission analysis.
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="badge badge-indigo">Live Cloudflare DoH</span>
                    <span class="badge badge-gray">HIBP Breach API</span>
                    <span class="badge badge-green">IP Intel</span>
                </div>
            </div>
            <div class="card card-pad">
                <div style="font-size:14px;font-weight:700;margin-bottom:14px;">🔍 Live Inspection</div>
                <p style="font-size:13px;color:var(--text-muted);line-height:1.6;">Use the built-in forensic tools to actively dissect malicious URLs, email headers, and app permissions in real-time.</p>
            </div>
        </div>

        <!-- Module 8: Credential -->
        <div class="grid grid-2" style="gap:40px;align-items:center;">
            <div class="card card-pad" style="background:var(--green-light);border-color:var(--green);">
                <div style="font-size:14px;font-weight:700;margin-bottom:14px;color:var(--green);">✅ Verified Attestation</div>
                <p style="font-size:13px;color:var(--text-muted);line-height:1.6;">Your training results are permanently secured. Generate a unique ID to prove your completion status to employers.</p>
            </div>
            <div>
                <div class="feature-icon" style="background:rgba(16,185,129,0.1);color:var(--green);width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:26px;margin-bottom:16px;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                </div>
                <h3 style="font-size:24px;font-weight:800;margin-bottom:12px;">Module 8: Tamper-Evident Credential Architecture & Public Attestation Verification</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">
                    SafeSphere certificates embed cryptographically unique 10-character hex credential IDs. Any third party can verify authenticity on the public attestation portal — no central database required.
                </p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="badge badge-green">6 Certificates</span>
                    <span class="badge badge-gray">Public Verify Portal</span>
                    <span class="badge badge-indigo">PDF Export</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scoring + Certificates -->
<section class="info-section" style="background:var(--surface);">
    <div class="container">
        <div class="section-header">
            <h2>Scoring, Streaks, and Certificates</h2>
            <p>Your progress is measured, tracked, and recognized — because learning without accountability fades quickly.</p>
        </div>
        <div class="grid grid-3" style="gap:20px;">
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Cyber Score (0–100)</h3>
                <p>Your score reflects your cumulative decision-making quality across all modules. +10 for correct, −5 for wrong. Capped at 100 and floored at 0. Visible in the header at all times.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔥</div>
                <h3>Day Streaks</h3>
                <p>Logging in and training on consecutive days builds your streak counter. This rewards consistency — cybersecurity awareness needs regular reinforcement, not one-time cramming.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🏅</div>
                <h3>Auto-Issued Certificates</h3>
                <p>When you meet the criteria for a module (e.g., 7/9 correct in Phishing), a certificate is automatically issued with a unique credential ID. The Champion certificate requires all four.</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ -->
<section class="info-section">
    <div class="container">
        <div class="section-header">
            <h2>Frequently Asked Questions</h2>
        </div>
        <div style="max-width:780px;margin:0 auto;">
            <?php $faqs = [
                ['Is SafeSphere really free?', 'Yes — completely free for individual users. All 8 training modules, the Forensic Toolkit, Reports, and Certificates are free. We don\'t sell your data or show ads. Our mission is cybersecurity awareness at scale, not revenue.'],
                ['Do I need any technical knowledge?', 'No. SafeSphere is built for everyday internet users — not IT professionals. If you use UPI apps, Gmail, or WhatsApp, you have all the background you need. The explanations are written in plain language.'],
                ['Can I retake scenarios?', 'Yes — you can replay any scenario, but your score is recorded only on your first attempt. Replaying is great for review without pressure.'],
                ['Are the scenarios based on real attacks?', 'Yes. Every scenario in SafeSphere is derived from documented Indian cybercrime cases, phishing campaigns targeting Indian banks, and fraud patterns reported by NPCI, CERT-In, and RBI.'],
                ['How does the Forensic Toolkit work?', 'The toolkit uses rule-based analysis written in PHP — no external APIs. It checks email headers for SPF/DKIM/DMARC failures, URLs for brand spoofing and suspicious TLDs, and APK permissions for dangerous combinations. The same logic patterns used by real security analysts.'],
                ['What happens to my data?', 'Your training data (scores, attempts) is stored in the local database running on your server. SafeSphere is designed to be self-hosted. Your personal data never leaves your server.'],
                ['Can companies use SafeSphere for employee training?', 'SafeSphere is open-source and self-hosted, making it ideal for internal corporate awareness programs. You can deploy it on your own server and enroll your team.'],
            ];
            foreach ($faqs as $faq): ?>
            <div class="faq-item">
                <button class="faq-question">
                    <?= $faq[0] ?>
                    <span class="faq-arrow">▼</span>
                </button>
                <div class="faq-answer"><?= $faq[1] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container" style="position:relative;">
        <h2>Ready to Start?</h2>
        <p>Create your free account and begin your first simulation in under 2 minutes.</p>
        <div class="flex gap-16 flex-center mt-32">
            <a href="register.php" class="btn btn-white btn-lg">Create Free Account</a>
            <a href="threats.php" class="btn btn-ghost btn-lg">View Threat Intelligence →</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/public_footer.php'; ?>
<script src="assets/js/main.js?v=2.6"></script>
</body>
</html>
