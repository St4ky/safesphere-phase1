<?php $nav_active = 'howitworks'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SafeSphere Curriculum — 8 National Cyber Defense Simulation Modules</title>
<script>try{var t=localStorage.getItem('ss_theme')||'light';document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
<meta name="description" content="Explore the 8 defensive simulation modules in SafeSphere: Email Phishing, UPI Fraud, Social Engineering, Network Hardening, OTP Hijacking, Deepfakes, Forensics, and Credential Attestation.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.8">
</head>
<body>
<?php include __DIR__ . '/includes/public_nav.php'; ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <span class="badge badge-indigo">National Curriculum Guide</span>
        <h1 class="mt-16">The SafeSphere Defensive Curriculum</h1>
        <p>A tactical walkthrough of all 8 specialized cyber simulation modules, forensic APIs, and verifiable certifications engineered for Indian threat vectors.</p>
    </div>
</section>

<!-- Getting Started in 3 Steps -->
<section class="info-section" style="background:var(--surface);">
    <div class="container">
        <div class="section-header">
            <h2>Get Started in 3 Steps</h2>
            <p>You go from zero to live defensive triage in under 2 minutes. No apps to install, no credit card, no technical setup.</p>
        </div>
        <div class="steps-grid">
            <div class="step-item">
                <div class="step-num">1</div>
                <h3>Create Free Account</h3>
                <p>Register with your name and email. Your personal Cyber Score starts at 40 (neutral baseline) — ready to track every defensive win as you learn.</p>
            </div>
            <div class="step-item">
                <div class="step-num">2</div>
                <h3>Select a Defensive Lab</h3>
                <p>Start with Phishing Defense or jump straight to the UPI Simulator and Deepfake Recognition. Each module is self-paced and fully independent.</p>
            </div>
            <div class="step-item">
                <div class="step-num">3</div>
                <h3>Triage Threats &amp; Earn Proof</h3>
                <p>Decisions are evaluated in real time: +10 pts for correct defense, −5 pts for missed threats with detailed forensic breakdowns. Earn verifiable credentials.</p>
            </div>
        </div>
    </div>
</section>

<!-- The 8 Core Defensive Modules -->
<section class="info-section">
    <div class="container">
        <div class="section-header">
            <span class="badge badge-indigo" style="margin-bottom:8px;">Interactive Laboratories</span>
            <h2>Eight Comprehensive Simulation Labs</h2>
            <p>Every module targets a real attack vector reverse-engineered from incidents documented by CERT-In, RBI advisories, and the Indian Cyber Crime Coordination Centre (I4C).</p>
        </div>

        <!-- MODULE 1: Phishing -->
        <div class="curriculum-module-row">
            <div class="module-text-col">
                <div class="module-icon-wrap" style="background:var(--indigo-light);color:var(--indigo);">📧</div>
                <h3 style="font-size:clamp(20px, 3.5vw, 24px);font-weight:800;margin-bottom:12px;">Module 1: Email Phishing &amp; Domain Spoofing Detection</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    Step into an active triage inbox containing 9 high-fidelity Indian email streams: SBI account suspensions, IRCTC refund traps, Income Tax department notices, and genuine Amazon delivery alerts.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:18px;">
                    Inspect sender domains against real brand domains, decode Punycode homoglyphs, check SPF/DKIM authentication failures, and detect artificial urgency triggers before clicking.
                </p>
                <div class="flex gap-8 flex-wrap">
                    <span class="badge badge-indigo">9 Scenarios</span>
                    <span class="badge badge-gray">SBI • IRCTC • HDFC • Amazon</span>
                    <span class="badge badge-green">+10 / −5 pts per decision</span>
                </div>
            </div>
            <div class="module-preview-col">
                <div class="curriculum-preview-card">
                    <div class="curriculum-preview-header">
                        <div style="display:flex;gap:6px;align-items:center;">
                            <span style="width:10px;height:10px;border-radius:50%;background:#ef4444;display:inline-block;"></span>
                            <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                            <span style="width:10px;height:10px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
                            <span style="margin-left:8px;">Live Inbox Simulator Preview</span>
                        </div>
                        <span class="badge badge-indigo" style="font-size:10px;">Scored Lab</span>
                    </div>
                    <div class="curriculum-preview-body" style="padding:14px 18px;">
                        <?php 
                        $sample_emails = [
                            ['SBI Security Alert', 'alerts@sbi-kyc-update.xyz', '⚠️ URGENT: Account Suspension Notice', true],
                            ['IRCTC Official', 'tickets@irctc.co.in', 'Your PNR 4827193056 Confirmed', false],
                            ['Income Tax Dept', 'refund@incometax-gov-in.net', 'Tax Refund ₹15,480 Approved – Claim', true],
                        ];
                        foreach ($sample_emails as $em): ?>
                        <div style="display:flex;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);align-items:center;">
                            <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#a5b4fc,#6366f1);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:13px;flex-shrink:0;">
                                <?= strtoupper(substr($em[0],0,1)) ?>
                            </div>
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:700;font-size:13.5px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= $em[0] ?></div>
                                <div style="font-size:12px;color:var(--text-muted);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= $em[2] ?></div>
                            </div>
                            <span class="badge <?= $em[3] ? 'badge-red' : 'badge-green' ?>" style="font-size:10px;flex-shrink:0;">
                                <?= $em[3] ? 'PHISHING' : 'LEGIT' ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODULE 2: UPI -->
        <div class="curriculum-module-row reverse-on-mobile">
            <div class="module-preview-col">
                <div class="curriculum-preview-card" style="background:#090d16;border-color:#1e293b;">
                    <div class="curriculum-preview-header" style="background:#111827;border-bottom-color:#1e293b;color:#94a3b8;">
                        <span>PHONEPE / GPAY IN-APP NOTIFICATION</span>
                        <span class="badge badge-red" style="font-size:10px;">Scam Simulator</span>
                    </div>
                    <div class="curriculum-preview-body" style="color:white;text-align:center;padding:24px;">
                        <div style="font-size:11px;font-weight:700;letter-spacing:.08em;color:#94a3b8;margin-bottom:6px;">COLLECT REQUEST RECEIVED</div>
                        <div style="font-size:40px;font-weight:900;color:#ef4444;line-height:1;margin-bottom:6px;">₹1</div>
                        <div style="font-size:13.5px;color:#cbd5e1;margin-bottom:14px;">from <strong style="color:white;">State Bank KYC Desk (customercare@okhdfcbank)</strong></div>
                        <div style="background:#172033;border-radius:8px;padding:12px;font-size:12.5px;color:#94a3b8;line-height:1.55;margin-bottom:18px;border:1px solid #1e293b;text-align:left;">
                            "Enter your UPI PIN to approve receipt of ₹2,500 government festival subsidy directly into your bank."
                        </div>
                        <div style="display:flex;gap:10px;">
                            <div style="flex:1;background:#dc2626;border-radius:8px;padding:10px;font-weight:700;font-size:13.5px;color:white;cursor:pointer;">✕ Decline &amp; Report</div>
                            <div style="flex:1;background:#334155;border-radius:8px;padding:10px;font-weight:700;font-size:13.5px;color:#94a3b8;cursor:not-allowed;">Pay ₹1 (Trap)</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="module-text-col">
                <div class="module-icon-wrap" style="background:var(--green-light);color:var(--green);">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path></svg>
                </div>
                <h3 style="font-size:clamp(20px, 3.5vw, 24px);font-weight:800;margin-bottom:12px;">Module 2: UPI Collect-Request &amp; Reverse-Payment Fraud</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    Simulates actual PhonePe, Google Pay, and Paytm payment screens. Encounter the insidious "Collect Inversion" scam where fraudsters tell victims they must "scan QR or type PIN to receive refund money".
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:18px;">
                    Reinforces the non-negotiable rule of India's UPI infrastructure: <strong>entering your UPI PIN ALWAYS debits funds from your account</strong>. No system in India requires a PIN to credit funds.
                </p>
                <div class="flex gap-8 flex-wrap">
                    <span class="badge badge-green">6 Scenarios</span>
                    <span class="badge badge-gray">GPay • PhonePe • Paytm</span>
                    <span class="badge badge-amber">Reverse QR • ₹1 Trap</span>
                </div>
            </div>
        </div>

        <!-- MODULE 3: Social Engineering -->
        <div class="curriculum-module-row">
            <div class="module-text-col">
                <div class="module-icon-wrap" style="background:var(--amber-light);color:var(--amber);">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                </div>
                <h3 style="font-size:clamp(20px, 3.5vw, 24px);font-weight:800;margin-bottom:12px;">Module 3: Conversational Social Engineering &amp; Vishing Sandbox</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    Engage in live dynamic branching dialogues against simulated threat actors on WhatsApp and Telegram. The conversation changes based on your answers: panic, cooperate, or apply out-of-band verification.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:18px;">
                    Experience real scenarios: fake FedEx customs extortion, Telegram part-time video review task scams, and fake Axis Bank KYC WhatsApp takeovers.
                </p>
                <div class="flex gap-8 flex-wrap">
                    <span class="badge badge-amber">4 Scenarios</span>
                    <span class="badge badge-gray">Branching Dialogue Engine</span>
                    <span class="badge badge-indigo">Out-of-Band Verification</span>
                </div>
            </div>
            <div class="module-preview-col">
                <div class="curriculum-preview-card">
                    <div style="background:#075e54;padding:12px 18px;display:flex;align-items:center;gap:12px;color:white;">
                        <div style="width:36px;height:36px;border-radius:50%;background:#dc2626;display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:13px;flex-shrink:0;">CBI</div>
                        <div>
                            <div style="font-weight:800;font-size:14px;letter-spacing:-0.01em;">Cyber Crime Cell — Inspector Sharma</div>
                            <div style="font-size:11.5px;color:rgba(255,255,255,0.75);">● Active Interrogation Session</div>
                        </div>
                    </div>
                    <div style="padding:18px;background:var(--bg);display:flex;flex-direction:column;gap:10px;">
                        <div style="background:var(--surface);color:var(--text);padding:10px 14px;border-radius:10px;border-bottom-left-radius:2px;font-size:13px;max-width:85%;line-height:1.55;border:1px solid var(--border);box-shadow:var(--shadow-sm);">
                            "Your Aadhaar is linked to parcel seized in Mumbai with illegal contraband. You are under Digital Arrest right now on Skype."
                        </div>
                        <div style="background:var(--indigo);color:white;padding:10px 14px;border-radius:10px;border-bottom-right-radius:2px;font-size:13px;max-width:85%;align-self:flex-end;line-height:1.55;box-shadow:var(--shadow-sm);">
                            "Indian law has no provision for 'Digital Arrest'. I am disconnecting and dialling 1930."
                        </div>
                        <div style="background:var(--green-light);border:1px solid rgba(34,197,94,0.3);color:var(--green);padding:8px 12px;border-radius:6px;font-size:12px;font-weight:700;text-align:center;">
                            ✓ Disinformation Neutralized (+10 Points Awarded)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODULE 4: Network Hardening -->
        <div class="curriculum-module-row reverse-on-mobile">
            <div class="module-preview-col">
                <div class="curriculum-preview-card">
                    <div class="curriculum-preview-header">
                        <span>Router &amp; Wi-Fi Hardening Matrix</span>
                        <span class="badge badge-green" style="font-size:10px;">Interactive Audit</span>
                    </div>
                    <div class="curriculum-preview-body" style="padding:16px 20px;">
                        <?php 
                        $audit_checks = [
                            ['🔑', 'Change Default Router Admin Credentials', 'pass'],
                            ['📶', 'Upgrade Encryption from WPA/WPA2 to WPA3', 'pass'],
                            ['🏠', 'Isolate Smart IoT Devices on Dedicated VLAN', 'pending'],
                            ['🔌', 'Disable Vulnerable UPnP Service Exposure', 'fail'],
                            ['🛡️', 'Disable WAN Remote Router Admin Ports', 'pass'],
                        ];
                        foreach ($audit_checks as $c): ?>
                        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid var(--border);">
                            <span style="font-size:18px;"><?= $c[0] ?></span>
                            <span style="flex:1;font-size:13px;font-weight:600;color:var(--text);"><?= $c[1] ?></span>
                            <span class="badge <?= $c[2] === 'pass' ? 'badge-green' : ($c[2] === 'fail' ? 'badge-red' : 'badge-gray') ?>" style="font-size:10px;flex-shrink:0;">
                                <?= $c[2] === 'pass' ? '✓ Pass' : ($c[2] === 'fail' ? '✕ Vulnerable' : 'Pending') ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="module-text-col">
                <div class="module-icon-wrap" style="background:var(--blue-light);color:var(--blue);">📶</div>
                <h3 style="font-size:clamp(20px, 3.5vw, 24px);font-weight:800;margin-bottom:12px;">Module 4: Decentralized Consumer Network Hardening &amp; Self-Audit</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    A 7-item interactive checklist that inspects and hardens your home and small office wireless perimeter. Explains the exact exploitation vectors behind default router credentials, rogue public Wi-Fi twin towers, and UPnP worm attacks.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:18px;">
                    Your audit results are saved persistently in the database across sessions so you can audit, apply router fixes, and return to complete the full audit.
                </p>
                <div class="flex gap-8 flex-wrap">
                    <span class="badge badge-blue">7 Checkpoints</span>
                    <span class="badge badge-gray">Persistent Status Tracker</span>
                    <span class="badge badge-indigo">Remediation Step Guides</span>
                </div>
            </div>
        </div>

        <!-- MODULE 5: OTP & SIM-Swap -->
        <div class="curriculum-module-row">
            <div class="module-text-col">
                <div class="module-icon-wrap" style="background:rgba(124,58,237,0.1);color:#7c3aed;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </div>
                <h3 style="font-size:clamp(20px, 3.5vw, 24px);font-weight:800;margin-bottom:12px;">Module 5: Multi-Factor Authentication (OTP) Hijacking &amp; SIM-Swap Countermeasures</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    Demystifies the deceptive mechanics of telecom social engineering. Learn how attackers execute SIM swaps by pretending to be Jio/Airtel 5G upgrade support desks, and how courier delivery PINs differ from banking OTPs.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:18px;">
                    Interactive simulations challenge you to distinguish between legitimate e-commerce delivery PINs and fraudulent WhatsApp 6-digit registration account takeover codes.
                </p>
                <div class="flex gap-8 flex-wrap">
                    <span class="badge" style="background:rgba(124,58,237,0.1);color:#7c3aed;border:1px solid rgba(124,58,237,0.3);">5 Scenarios</span>
                    <span class="badge badge-gray">SIM-Swap Wave</span>
                    <span class="badge badge-amber">Vishing Defense</span>
                </div>
            </div>
            <div class="module-preview-col">
                <div class="curriculum-preview-card">
                    <div class="curriculum-preview-header">
                        <span>Simulated SMS &amp; Telecom Intercept</span>
                        <span class="badge badge-red" style="font-size:10px;">Vishing Alert</span>
                    </div>
                    <div class="curriculum-preview-body" style="padding:20px;">
                        <div style="background:var(--bg);border:1px solid var(--border);border-left:4px solid #7c3aed;padding:12px 16px;border-radius:6px;margin-bottom:14px;">
                            <div style="font-weight:700;font-size:13px;color:var(--text);margin-bottom:4px;">SMS from AD-AIRTEL:</div>
                            <div style="font-size:12.5px;color:var(--text-muted);font-family:'JetBrains Mono',monospace;">"Request received for SIM swap/eSIM replacement for 98210-XXXXX. If not requested by you, call 198 immediately."</div>
                        </div>
                        <div style="font-size:13px;color:var(--text-muted);line-height:1.55;margin-bottom:14px;">
                            <strong>Caller on line:</strong> "Sir, please send the 20-digit SIM serial code to 121 to complete mandatory 5G upgrade, or your outgoing calls will be disconnected tonight."
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button type="button" class="btn btn-danger btn-sm" style="flex:1;">Reject &amp; Report to 198</button>
                            <button type="button" class="btn btn-outline btn-sm" style="flex:1;">Send SIM Code (Trap)</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODULE 6: Deepfake & AI Voice -->
        <div class="curriculum-module-row reverse-on-mobile">
            <div class="module-preview-col">
                <div class="feed-monitor">
                    <div class="feed-monitor-header">
                        <span class="feed-monitor-live">● AI AUDIO SPECTRUM ANALYZER</span>
                        <span style="font-size:10px;color:#ef4444;font-weight:700;">CONFIDENCE: 98.4% SYNTHETIC</span>
                    </div>
                    <div class="feed-monitor-body" style="font-size:12.5px;margin-bottom:12px;">
[AUDIO INGEST] WhatsApp Voice Note: "Aryan (Son)"
[SPECTRUM LOG] Zero natural micro-breathing detected
[SPECTRUM LOG] Unnatural 85ms neural latency artifact
[ANALYSIS] Cloned via 4-second Instagram reel voice sample
[DEMAND] "Papa, police have arrested me... GPay ₹75,000 right now"
                    </div>
                    <div style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);padding:10px 12px;border-radius:6px;font-size:12px;color:#fca5a5;">
                        <strong>Defensive Action:</strong> Verify with confidential family safe-word or call son's phone directly from a second device.
                    </div>
                </div>
            </div>
            <div class="module-text-col">
                <div class="module-icon-wrap" style="background:var(--red-light);color:var(--red);">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
                </div>
                <h3 style="font-size:clamp(20px, 3.5vw, 24px);font-weight:800;margin-bottom:12px;">Module 6: Synthetic Media (Deepfake) &amp; AI Voice-Clone Threat Recognition</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    Modern scammers use generative AI voice cloning (ElevenLabs) to synthesize 95% accurate voice clones of loved ones in distress or corporate executives requesting urgent RTGS wire transfers.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:18px;">
                    Learn to detect the subtle hallmarks of synthetic generation: unnatural cadence, robotic breathing gaps, high emotional manipulation, and establish protocols like confidential family safe words.
                </p>
                <div class="flex gap-8 flex-wrap">
                    <span class="badge badge-red">4 Scenarios</span>
                    <span class="badge badge-gray">Voice Clone Spectrum</span>
                    <span class="badge badge-indigo">CEO Wire Fraud Protocol</span>
                </div>
            </div>
        </div>

        <!-- MODULE 7: Forensics & Password Entropy -->
        <div class="curriculum-module-row">
            <div class="module-text-col">
                <div class="module-icon-wrap" style="background:rgba(147,51,234,0.1);color:#9333ea;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </div>
                <h3 style="font-size:clamp(20px, 3.5vw, 24px);font-weight:800;margin-bottom:12px;">Module 7: Rule-Based Client-Side Forensic Inspection &amp; Password Entropy Analysis</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    Hands-on digital forensics toolkit powered by live security APIs: verify domain DNS/SPF/MX records via Cloudflare DNS over HTTPS (DoH), trace sender IPs via ASN geolocation, and evaluate Android APK dangerous permissions.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:18px;">
                    Includes zero-knowledge <strong>HaveIBeenPwned (HIBP)</strong> k-anonymity password breach checking, testing against 800M+ compromised credentials without exposing plaintext passwords.
                </p>
                <div class="flex gap-8 flex-wrap">
                    <span class="badge" style="background:rgba(147,51,234,0.1);color:#9333ea;border:1px solid rgba(147,51,234,0.3);">5 Forensic Tools</span>
                    <span class="badge badge-indigo">Cloudflare DoH API</span>
                    <span class="badge badge-green">HIBP k-Anonymity</span>
                </div>
            </div>
            <div class="module-preview-col">
                <div class="curriculum-preview-card">
                    <div class="curriculum-preview-header font-mono" style="font-size:12px;">
                        <span>DoH &amp; HIBP Forensic Terminal</span>
                        <span class="badge badge-indigo" style="font-size:9px;">Live API</span>
                    </div>
                    <div class="curriculum-preview-body font-mono" style="font-size:12.5px;line-height:1.7;padding:16px;">
                        <div style="color:var(--indigo);font-weight:700;margin-bottom:6px;">&gt; query_doh(sbi-netbanking-kyc.top)</div>
                        <div style="color:var(--text-muted);margin-bottom:6px;">Status: NXDOMAIN | A Record: Missing | SPF: None</div>
                        <div style="color:var(--red);font-weight:700;margin-bottom:14px;">Risk Assessment: 90/100 (HIGH RISK SPOOF)</div>

                        <div style="color:var(--green);font-weight:700;margin-bottom:6px;">&gt; hibp_audit_hash(5D414...)</div>
                        <div style="color:var(--text-muted);">Matches: 48,129 Breaches in Public Dumps</div>
                        <div style="color:var(--amber);font-weight:700;">Entropy: Compromised in Rainbow Tables</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODULE 8: Credential Attestation -->
        <div class="curriculum-module-row reverse-on-mobile">
            <div class="module-preview-col">
                <div class="curriculum-preview-card" style="border:2px solid rgba(79,70,229,0.3);background:radial-gradient(circle at 50% 10%, rgba(99,102,241,0.06), transparent 70%);">
                    <div class="curriculum-preview-header">
                        <span style="font-weight:800;color:var(--indigo);">Official Attestation Credential Preview</span>
                        <span class="badge badge-green" style="font-size:9px;">Verifiable</span>
                    </div>
                    <div class="curriculum-preview-body" style="text-align:center;padding:24px;">
                        <div style="font-size:40px;margin-bottom:8px;">🏅</div>
                        <div style="font-size:11px;font-weight:800;color:var(--indigo);letter-spacing:0.1em;text-transform:uppercase;">Certificate of Defensive Mastery</div>
                        <div style="font-size:18px;font-weight:800;margin:6px 0;color:var(--text);">Phishing Defense Specialist</div>
                        <div style="font-size:13px;color:var(--text-muted);margin-bottom:14px;">Awarded to Candidate <strong>Rehan Khan</strong></div>
                        <div style="background:var(--bg);border:1px solid var(--border);border-radius:6px;padding:8px 12px;font-family:'JetBrains Mono',monospace;font-size:11px;color:var(--text-muted);word-break:break-all;margin-bottom:12px;">
                            ID: SS-A8E2B1C9F4 &bull; SHA256: E3B0C44298FC1C...
                        </div>
                        <a href="verify.php?id=SS-A8E2B1C9F4" class="btn btn-outline btn-sm" style="font-size:12px;">Inspect Public Attestation Proof →</a>
                    </div>
                </div>
            </div>
            <div class="module-text-col">
                <div class="module-icon-wrap" style="background:var(--green-light);color:var(--green);">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                </div>
                <h3 style="font-size:clamp(20px, 3.5vw, 24px);font-weight:800;margin-bottom:12px;">Module 8: Tamper-Evident Credential Architecture &amp; Public Attestation</h3>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    SafeSphere issues cryptographically indexed certifications stamped with unique 10-hex credential identifiers and deterministic SHA-256 integrity fingerprints.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:18px;">
                    Recruiters, educational institutions, and compliance auditors can verify candidate authentications on the public registry with zero authentication or paywalls required.
                </p>
                <div class="flex gap-8 flex-wrap">
                    <span class="badge badge-green">7 Certificates</span>
                    <span class="badge badge-gray">Public Verify Portal</span>
                    <span class="badge badge-indigo">A4 PDF Print Export</span>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Scoring, Streaks, and Certification -->
<section class="info-section" style="background:var(--surface);">
    <div class="container">
        <div class="section-header">
            <h2>Scoring, Daily Streaks, and Certification</h2>
            <p>Your performance is measured, tracked, and rewarded — because learning without accountability fades quickly.</p>
        </div>
        <div class="grid grid-3" style="gap:20px;">
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(79,70,229,0.1);color:var(--indigo);">⚡</div>
                <h3>Dynamic Cyber Score (0–100)</h3>
                <p>Your score reflects real-time decision quality across all simulations: +10 for identifying traps, −5 for missed vectors. Capped at 100, visible continuously in your Command Center.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(245,158,11,0.1);color:var(--amber);">🔥</div>
                <h3>Daily Threat Defense Streaks</h3>
                <p>Consecutive daily training maintains your defense streak. Regular small drills build instinctive suspicion reflexes that prevent real-world financial loss under pressure.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:rgba(16,185,129,0.1);color:var(--green);">🏅</div>
                <h3>Automated Certificates</h3>
                <p>Meeting passing thresholds (e.g. 7/9 in Phishing, 5/6 in UPI, 4/5 in OTP) auto-issues a permanent credential. Earning all 6 specialist certificates unlocks the Sovereign Cyber Champion.</p>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Accordion -->
<section class="info-section">
    <div class="container">
        <div class="section-header">
            <h2>Frequently Asked Questions</h2>
            <p>Common questions about SafeSphere's training platform, data security, and curriculum.</p>
        </div>
        <div style="max-width:780px;margin:0 auto;">
            <?php 
            $faqs = [
                ['Is SafeSphere really 100% free?', 'Yes — completely free for all individual users. All 8 training simulation labs, the live Forensic Toolkit, progress reports, and cryptographic certificates are free. We never sell your data or display ads.'],
                ['Do I need technical or programming knowledge?', 'No. SafeSphere is engineered for everyday internet users — students, working professionals, and seniors. If you use UPI apps like GPay/PhonePe or check email, the scenarios will feel completely natural and the feedback is written in plain, accessible language.'],
                ['Can I replay scenarios if I make a mistake?', 'Yes — you can replay any simulation scenario unlimited times to review red flags and explanations. However, to maintain competitive leaderboard integrity, your Cyber Score is updated only on your first attempt.'],
                ['Are the scenarios based on real Indian cybercrime cases?', 'Yes. Every scenario in SafeSphere is reverse-engineered directly from documented Indian police FIRs, CERT-In bulletins, NPCI payment fraud statistics, and RBI consumer awareness advisories.'],
                ['How does the Forensic Toolkit work without server uploads?', 'The toolkit runs client-side and server-side rule engines with direct queries to authoritative DNS-over-HTTPS (Cloudflare DoH) and k-anonymity breach APIs (HaveIBeenPwned). No plaintext passwords or private messages are ever stored.'],
                ['Where is my training progress stored?', 'All attempt logs, module scores, and credential IDs are stored locally on your database server. SafeSphere is self-hostable and respects sovereign data privacy.'],
                ['Can universities or organizations use SafeSphere for awareness?', 'Yes! SafeSphere has built-in organizational group management and an Admin Console, allowing companies, colleges, and IT departments to onboard cohorts and monitor team defense resilience.'],
            ];
            foreach ($faqs as $faq): ?>
            <div class="faq-item">
                <button type="button" class="faq-question">
                    <span><?= e($faq[0]) ?></span>
                    <span class="faq-arrow">▼</span>
                </button>
                <div class="faq-answer"><?= e($faq[1]) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section">
    <div class="container" style="position:relative;">
        <h2>Ready to Test Your Cyber Defense Reflexes?</h2>
        <p>Create your free account in 30 seconds and start your first interactive simulation drill.</p>
        <div class="flex gap-16 flex-center mt-32">
            <a href="register.php" class="btn btn-white btn-lg">Launch Free Training Lab</a>
            <a href="threats.php" class="btn btn-ghost btn-lg">Explore Threat Radar →</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/public_footer.php'; ?>
<script src="assets/js/main.js?v=2.8"></script>
</body>
</html>
