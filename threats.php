<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cyber Threat Intelligence — SafeSphere India</title>
<script>try{var t=localStorage.getItem('ss_theme')||'light';document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
<meta name="description" content="Learn about India's most pressing cyber threats — UPI fraud, phishing, social engineering, and network attacks — with real statistics and case studies.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.9">
</head>
<body>
<?php $nav_active = 'threats'; include __DIR__ . '/includes/public_nav.php'; ?>

<section class="page-hero">
    <div class="container">
        <span class="badge badge-red">Threat Intelligence</span>
        <h1 class="mt-16">India's Most Dangerous<br>Cyber Threats in 2024–25</h1>
        <p>Understand what you're up against — real statistics, real techniques, and exactly how each attack works so you can recognize and resist them.</p>
    </div>
</section>

<!-- Threat stats overview -->
<div style="background:var(--surface);border-bottom:1px solid var(--border);">
    <div class="container">
        <div class="stat-strip">
            <div class="stat-strip-item">
                <div class="stat-number" data-counter="11333" data-suffix="Cr">0</div>
                <div class="stat-label">₹ Lost to UPI fraud FY24</div>
            </div>
            <div class="stat-strip-item">
                <div class="stat-number" data-counter="1100000" data-suffix="+">0</div>
                <div class="stat-label">Cybercrime complaints 2023</div>
            </div>
            <div class="stat-strip-item">
                <div class="stat-number" data-counter="300" data-suffix="%">0</div>
                <div class="stat-label">UPI fraud growth 2021–24</div>
            </div>
            <div class="stat-strip-item">
                <div class="stat-number" data-counter="60" data-suffix="%">0</div>
                <div class="stat-label">Victims under 40 years old</div>
            </div>
        </div>
    </div>
</div>

<!-- Threat 1: Phishing -->
<section class="info-section">
    <div class="container">
        <div class="grid grid-2" style="gap:48px;align-items:start;">
            <div>
                <span class="badge badge-red mb-12">Threat #1</span>
                <h2 style="font-size:28px;font-weight:800;letter-spacing:-0.02em;margin-bottom:14px;">📧 Phishing Emails</h2>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    Phishing emails impersonate trusted institutions — banks, the Income Tax Department, IRCTC, Amazon — to trick you into revealing credentials or clicking malicious links.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:20px;">
                    Modern phishing uses real logos, genuine-looking URLs (e.g., sbi-netbanking-secure.xyz instead of sbi.co.in), and urgent language like "your account will be suspended in 24 hours."
                </p>
                <h4 style="margin-bottom:10px;">Common Indian phishing targets:</h4>
                <ul style="list-style:disc;padding-left:18px;color:var(--text-muted);font-size:14.5px;line-height:2;">
                    <li>SBI, HDFC, ICICI, Axis Bank — fake KYC and account suspension notices</li>
                    <li>IRCTC — fake refund notifications</li>
                    <li>Income Tax Department — fake refund or notice emails</li>
                    <li>Amazon / Flipkart — fake order confirmation with malicious links</li>
                    <li>NPCI / UPI — "verify your UPI ID" phishing</li>
                </ul>
            </div>
            <div>
                <div class="card card-pad" style="background:var(--red-light);border:1px solid rgba(239,68,68,0.3);border-left:4px solid var(--red);">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--red);margin-bottom:12px;">Example Phishing Email</div>
                    <div style="font-size:13px;font-family:'JetBrains Mono',monospace;line-height:1.7;color:var(--text);">
                        <strong>From:</strong> SBI Alert &lt;noreply@sbi-kyc-update.xyz&gt;<br>
                        <strong>Subject:</strong> URGENT: Your SBI account will be blocked<br><br>
                        Dear Valued Customer,<br><br>
                        Your SBI NetBanking account requires immediate KYC update as per RBI circular. Failure to update within 24 hours will result in account suspension.<br><br>
                        Click here to update: <span style="text-decoration:underline;color:var(--red);">http://sbi-kyc-verify.online/update</span><br><br>
                        State Bank of India Customer Care
                    </div>
                </div>
                <div class="card card-pad mt-12">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--green);margin-bottom:10px;">🟢 How to spot it</div>
                    <ul style="list-style:none;padding:0;font-size:13.5px;line-height:1.8;">
                        <li>🔴 Sender domain: <code>sbi-kyc-update.xyz</code> ≠ <code>sbi.co.in</code></li>
                        <li>🔴 Link domain: <code>sbi-kyc-verify.online</code> — not SBI's real domain</li>
                        <li>🔴 Artificial urgency: "24 hours" pressure tactic</li>
                        <li>🔴 "KYC via email" — banks never do this</li>
                        <li>🟢 Real SBI emails come from @sbi.co.in only</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Threat 2: UPI Fraud -->
<section class="info-section" style="background:var(--surface);">
    <div class="container">
        <div class="grid grid-2" style="gap:48px;align-items:start;">
            <div>
                <div class="card card-pad" style="background:var(--amber-light);border:1px solid rgba(245,158,11,0.3);border-left:4px solid var(--amber);">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--amber);margin-bottom:12px;">The #1 UPI Scam Script</div>
                    <div style="font-size:14px;line-height:1.8;color:var(--text);">
                        <strong>Scammer (as "SBI KYC"):</strong><br>
                        "Dear customer, please accept this ₹1 request to verify your account. Once verified, your account will remain active."<br><br>
                        <strong>What actually happens:</strong><br>
                        You enter your UPI PIN → ₹1 goes to them → they now have confirmed your UPI is active → they escalate to larger fraud.
                    </div>
                </div>
                <div class="card card-pad mt-12" style="border-left:4px solid var(--amber);">
                    <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--green);margin-bottom:10px;">🟢 The golden rule</div>
                    <p style="font-size:14.5px;line-height:1.7;color:var(--text-muted);">
                        <strong>Collecting a request = you SEND money.</strong> There is no "accept to receive" mechanism in UPI. Any request telling you to "accept to receive your refund/reward/subsidy" is 100% fraud.
                    </p>
                </div>
            </div>
            <div>
                <span class="badge badge-amber mb-12">Threat #2</span>
                <h2 style="font-size:28px;font-weight:800;letter-spacing:-0.02em;margin-bottom:14px;">📱 UPI Payment Fraud</h2>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    UPI fraud is India's fastest-growing cybercrime category. With 300+ million UPI users and 10+ billion monthly transactions, attackers have a massive attack surface.
                </p>
                <h4 style="margin-bottom:10px;">Most common UPI scam types:</h4>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <?php $upiFrauds = [
                        ['Collect Request Scam', 'Pretends to send you money — actually requesting payment from you'],
                        ['Fake QR Code Scan', '"Scan to receive refund/cashback" — QR always means YOU pay'],
                        ['KYC Verification Scam', 'Calls claiming your UPI/bank needs ₹1 verification'],
                        ['OLX/Quikr Buyer Fraud', 'Sends you QR or collect request as fake "payment proof"'],
                        ['Government Scheme Fraud', 'Fake PM-KISAN, subsidy, or ration card collect requests'],
                        ['Vishing + UPI', 'Phone call claims your number will be de-linked unless you share OTP'],
                    ];
                    foreach ($upiFrauds as $f): ?>
                    <div style="display:flex;gap:10px;padding:10px 12px;background:var(--bg);border-radius:var(--radius-sm);border:1px solid var(--border);">
                        <span style="color:var(--red);font-size:16px;flex-shrink:0;">⚠️</span>
                        <div>
                            <div style="font-weight:700;font-size:13.5px;"><?= $f[0] ?></div>
                            <div style="font-size:12.5px;color:var(--text-muted);margin-top:2px;"><?= $f[1] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Threat 3: Social Engineering -->
<section class="info-section">
    <div class="container">
        <div class="section-header">
            <span class="badge badge-indigo">Threat #3</span>
            <h2 class="mt-12">🗣️ Social Engineering</h2>
            <p>The most dangerous attacks exploit human psychology — not technical vulnerabilities. No software patch can fix being manipulated.</p>
        </div>
        <div class="grid grid-4" style="gap:18px;">
            <?php $seTypes = [
                ['Vishing', '📞', 'Phone calls impersonating bank officers, government officials, or tech support. Creates urgency and requests OTPs, PINs, or remote access.'],
                ['Pretexting', '🎭', 'Attacker builds a detailed fake identity — fake HR manager, fake army officer, fake bank employee — to manipulate over multiple interactions.'],
                ['Romance Scam', '💕', 'Long-term fake relationship built online, ending in fabricated emergency requiring money transfer. Emotionally devastating and financially catastrophic.'],
                ['Job Offer Scam', '💼', 'Fake job offers from large Indian IT companies, requiring advance "registration fees" or "security deposits." Common on LinkedIn and WhatsApp.'],
            ];
            foreach ($seTypes as $s): ?>
            <div class="feature-card">
                <div class="feature-icon"><?= $s[1] ?></div>
                <h3><?= $s[0] ?></h3>
                <p><?= $s[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="card card-pad mt-24" style="background:var(--indigo-light);border-color:var(--indigo);">
            <strong>🛡️ Universal defence:</strong> Slow down. Urgency is always manufactured. Hang up, independently verify using official numbers, and never share OTPs — with anyone, for any reason.
        </div>
    </div>
</section>

<!-- Threat 4: Network -->
<section class="info-section" style="background:var(--surface);">
    <div class="container">
        <div class="grid grid-2" style="gap:48px;align-items:center;">
            <div>
                <span class="badge badge-blue mb-12">Threat #4</span>
                <h2 style="font-size:28px;font-weight:800;letter-spacing:-0.02em;margin-bottom:14px;">📶 Home Network & Wi-Fi Attacks</h2>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">
                    Most home routers in India run years-old firmware, use default passwords, and are exposed to the internet via remote management. A compromised router means every device in your home — and every transaction you make — is visible to the attacker.
                </p>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">
                    Public Wi-Fi at cafes, airports, and railway stations is a particularly high-risk environment. Attackers set up identical "evil twin" hotspots using common names like "Airport_Free_WiFi" and intercept all traffic passing through.
                </p>
                <a href="register.php" class="btn btn-primary">Run Your Network Audit →</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px;">
                <?php $netRisks = [
                    ['🔑', 'Default Router Passwords', 'Still used by 60%+ of Indian home routers. Takes 30 seconds to change.'],
                    ['📶', 'WEP/WPA Encryption', 'Crackable in minutes with free tools. Upgrade to WPA2 or WPA3.'],
                    ['🌐', 'Remote Management On', 'Your router admin panel exposed to the entire internet.'],
                    ['☕', 'Public Wi-Fi Risk', 'Evil twin attacks, traffic interception, fake login pages.'],
                ];
                foreach ($netRisks as $r): ?>
                <div style="display:flex;gap:14px;padding:14px;background:var(--bg);border-radius:var(--radius);border:1px solid var(--border);align-items:flex-start;">
                    <span style="font-size:24px;flex-shrink:0;"><?= $r[0] ?></span>
                    <div>
                        <div style="font-weight:700;font-size:14.5px;margin-bottom:4px;"><?= $r[1] ?></div>
                        <div style="font-size:13px;color:var(--text-muted);"><?= $r[2] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Threat 5: OTP / SIM Swap -->
<section class="info-section">
    <div class="container">
        <div class="grid grid-2" style="gap:48px;align-items:start;">
            <div>
                <span class="badge badge-indigo mb-12">Threat #5</span>
                <h2 style="font-size:28px;font-weight:800;letter-spacing:-0.02em;margin-bottom:14px;">📲 OTP Hijacking & SIM-Swap</h2>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    Cybercriminals use social engineering to trick you into sharing your OTP, or they bypass you entirely by cloning or swapping your SIM card through your mobile operator.
                </p>
                <h4 style="margin-bottom:10px;">Common Tactics:</h4>
                <ul style="list-style:disc;padding-left:18px;color:var(--text-muted);font-size:14.5px;line-height:2;">
                    <li>Call forwarding scams ("Dial *401*... to activate 5G")</li>
                    <li>WhatsApp account takeovers via 6-digit verification code requests</li>
                    <li>SIM swap attacks resulting in all banking SMS OTPs being routed to attackers</li>
                </ul>
            </div>
            <div class="card card-pad" style="background:var(--indigo-light);border-left:4px solid var(--indigo);">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--indigo);margin-bottom:12px;">The Defense</div>
                <p style="font-size:14px;line-height:1.8;color:var(--text);">
                    Never dial MMI codes (like *401*) suggested by unknown callers. If your phone suddenly loses cellular service in a location with normally good coverage, contact your carrier immediately — it may be a SIM swap in progress.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Threat 6: Deepfakes -->
<section class="info-section" style="background:var(--surface);">
    <div class="container">
        <div class="grid grid-2" style="gap:48px;align-items:start;">
            <div class="card card-pad" style="background:var(--red-light);border-left:4px solid var(--red);">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--red);margin-bottom:12px;">Visual & Audio Clues</div>
                <p style="font-size:14px;line-height:1.8;color:var(--text);">
                    Look for unnatural blinking, mismatched lip-sync, and robotic or emotionally flat intonations in voice clones. AI models struggle with complex lighting and background textures. Always establish a safe word with family members.
                </p>
            </div>
            <div>
                <span class="badge badge-red mb-12">Threat #6</span>
                <h2 style="font-size:28px;font-weight:800;letter-spacing:-0.02em;margin-bottom:14px;">🤖 Deepfakes & AI Voice Clones</h2>
                <p style="font-size:15px;color:var(--text-muted);line-height:1.7;margin-bottom:14px;">
                    Threat actors use advanced AI tools to clone the voices of family members or executives. This technology is increasingly used in "virtual kidnapping" and CEO fraud.
                </p>
                <h4 style="margin-bottom:10px;">Common AI Scams:</h4>
                <ul style="list-style:disc;padding-left:18px;color:var(--text-muted);font-size:14.5px;line-height:2;">
                    <li>Virtual kidnapping: "We have your child, send ransom" using cloned audio</li>
                    <li>CEO fraud: Fabricated video calls directing finance teams to wire funds</li>
                    <li>Synthetic media for fake investment endorsements</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- What to do -->
<section class="info-section">
    <div class="container">
        <div class="section-header">
            <h2>What to Do If You're Targeted</h2>
            <p>Quick-reference action guide for when something feels wrong.</p>
        </div>
        <div class="grid grid-3" style="gap:18px;">
            <?php $actions = [
                ['🚫', 'Stop Immediately', 'The moment something feels off — stop. Don\'t click, don\'t share, don\'t transfer. Scammers rely on momentum. Break it.'],
                ['📞', 'Call Your Bank', 'Use the number on the back of your card or your bank\'s official website. Never call a number given to you by the suspicious party.'],
                ['🔒', 'Secure Your Accounts', 'Change UPI MPIN, app login password, and net banking password from a trusted device on a trusted network.'],
                ['📝', 'Report the Fraud', 'File a complaint at cybercrime.gov.in or call 1930 (National Cyber Helpline). The sooner you report, the higher the chance of recovery.'],
                ['🏦', 'Contact NPCI', 'For UPI fraud, raise a dispute directly in your UPI app and contact NPCI at npci.org.in for escalation.'],
                ['📸', 'Preserve Evidence', 'Screenshot everything — the suspicious message, transaction ID, caller ID. This is crucial for the police complaint.'],
            ];
            foreach ($actions as $a): ?>
            <div class="card card-pad">
                <div style="font-size:28px;margin-bottom:12px;"><?= $a[0] ?></div>
                <h3 class="mb-8"><?= $a[1] ?></h3>
                <p class="text-sm text-muted"><?= $a[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container" style="position:relative;">
        <h2>Train Before You're Targeted</h2>
        <p>Every scenario on SafeSphere is based on real attacks. The best time to practice was yesterday. The second-best time is now.</p>
        <div class="flex gap-16 flex-center mt-32">
            <a href="register.php" class="btn btn-white btn-lg">Start Free Training</a>
            <a href="about.php" class="btn btn-ghost btn-lg">Learn About Us →</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/public_footer.php'; ?>
<script src="assets/js/main.js?v=2.9"></script>
</body>
</html>
