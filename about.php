<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About SafeSphere — India's Cyber Awareness Training Platform</title>
<script>try{var t=localStorage.getItem('ss_theme')||'light';document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
<meta name="description" content="SafeSphere is India's hands-on cybersecurity awareness platform — built to help everyday internet users recognize phishing, UPI fraud, social engineering, and network threats through realistic simulations.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.5">
</head>
<body>
<?php $nav_active = 'about'; include __DIR__ . '/includes/public_nav.php'; ?>

<!-- Hero -->
<section class="page-hero">
    <div class="container">
        <span class="badge badge-indigo">Our Mission</span>
        <h1 class="mt-16">We Train India to Fight Back<br>Against Cyber Fraud</h1>
        <p>SafeSphere exists because cybercrime in India is a crisis that no one adequately prepares everyday users for. We change that — with hands-on simulations, not boring slide decks.</p>
    </div>
</section>

<!-- Mission Section -->
<section class="info-section">
    <div class="container">
        <div class="grid grid-2" style="gap:48px;align-items:center;">
            <div>
                <span class="badge badge-red mb-16">The Problem</span>
                <h2 style="font-size:30px;font-weight:800;letter-spacing:-0.02em;margin-bottom:16px;">₹1 Lakh Crore+ Lost to Cybercrime in India Annually</h2>
                <p style="font-size:16px;color:var(--text-muted);line-height:1.7;margin-bottom:16px;">
                    India added 200+ million new internet users in the last three years — most without any cybersecurity awareness training. The result: UPI fraud cases grew 300% between 2021 and 2024. Phishing attacks targeting Indian banks send over 1 crore fraudulent emails every month.
                </p>
                <p style="font-size:16px;color:var(--text-muted);line-height:1.7;">
                    The victims are not just the elderly or tech-illiterate — young professionals, students, and small business owners are targeted daily. The knowledge gap is the vulnerability.
                </p>
            </div>
            <div class="grid grid-2" style="gap:14px;">
                <div class="threat-stat-card">
                    <div class="threat-stat-number">₹11,333 Cr</div>
                    <div class="threat-stat-label">Lost to UPI fraud in FY 2023–24</div>
                    <div class="threat-stat-source">Source: NPCI Annual Report</div>
                </div>
                <div class="threat-stat-card amber">
                    <div class="threat-stat-number">1.1 Mn</div>
                    <div class="threat-stat-label">Cybercrime complaints in 2023</div>
                    <div class="threat-stat-source">Source: NCRB Data</div>
                </div>
                <div class="threat-stat-card indigo">
                    <div class="threat-stat-number">76%</div>
                    <div class="threat-stat-label">Attacks start with social engineering</div>
                    <div class="threat-stat-source">Source: Verizon DBIR 2024</div>
                </div>
                <div class="threat-stat-card">
                    <div class="threat-stat-number">3 min</div>
                    <div class="threat-stat-label">Avg time before a victim acts on phishing</div>
                    <div class="threat-stat-source">Source: Proofpoint Research</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Approach -->
<section class="info-section" style="background:var(--surface);">
    <div class="container">
        <div class="section-header">
            <h2>Our Approach: Learn by Doing</h2>
            <p>Reading about phishing doesn't make you immune to it. Surviving a simulation does. Every SafeSphere module is built around realistic scenarios drawn from actual attacks targeting Indian users.</p>
        </div>
        <div class="grid grid-3" style="gap:20px;">
            <div class="feature-card">
                <div class="feature-icon">🎯</div>
                <h3>India-Specific Content</h3>
                <p>Every scenario uses real Indian brands, payment systems (UPI, NEFT), regulatory language (RBI, UIDAI), and attack patterns documented in Indian cyberfraud cases — not generic Western examples.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⚡</div>
                <h3>Instant Feedback</h3>
                <p>Right or wrong, you immediately see why — with detailed red-flag explanations, the attacker's psychological technique, and a specific, actionable lesson. No waiting, no PDF reports.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📊</div>
                <h3>Measurable Progress</h3>
                <p>Your Cyber Score (0–100) tracks every decision across all modules. Streaks reward consistent training. Certificates verify your knowledge to employers or institutions.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🛡️</div>
                <h3>Safe Environment</h3>
                <p>All simulations are entirely sandboxed. No real money, no real data, no real risk. You can make mistakes here — that's the whole point. The lessons stick because you lived them.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <h3>Real Forensic Tools</h3>
                <p>The Forensic Toolkit uses real detection logic — not just checkboxes. Paste a suspicious URL or email header and get a genuine technical analysis using the same rules that security professionals use.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🆓</div>
                <h3>Always Free for Individuals</h3>
                <p>SafeSphere is free for individual learners. Our mission is awareness at scale — not revenue. No ads, no paywalls for core modules, no selling your data.</p>
            </div>
        </div>
    </div>
</section>

<!-- What Makes Us Different -->
<section class="info-section">
    <div class="container">
        <div class="section-header">
            <h2>What Makes SafeSphere Different</h2>
        </div>
        <div class="grid grid-2" style="gap:16px;">
            <?php
            $comparisons = [
                ['them' => 'Generic phishing examples from US/EU banks', 'us' => 'Real SBI, HDFC, IRCTC, Paytm, NPCI scenarios'],
                ['them' => 'Multiple-choice quizzes on theory', 'us' => 'Live inbox simulation — you sort real vs. fake emails'],
                ['them' => 'One-time awareness training certificate', 'us' => 'Ongoing training with streak tracking and score'],
                ['them' => 'No feedback on wrong answers', 'us' => 'Instant red-flag breakdown explaining every decision'],
                ['them' => 'No UPI-specific fraud training', 'us' => 'Dedicated UPI collect request simulator — collect vs. pay'],
                ['them' => 'Social engineering = reading a PDF', 'us' => 'Branching chat scenarios — your choices change the outcome'],
            ];
            foreach ($comparisons as $c):
            ?>
            <div class="grid grid-2" style="border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;gap:0;">
                <div style="padding:16px;background:var(--red-light);border-right:1px solid var(--border);">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--red);margin-bottom:6px;">Others</div>
                    <div style="font-size:13.5px;color:var(--text);line-height:1.5;"><?= $c['them'] ?></div>
                </div>
                <div style="padding:16px;background:var(--green-light);">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;color:var(--green);margin-bottom:6px;">SafeSphere</div>
                    <div style="font-size:13.5px;color:var(--text);line-height:1.5;font-weight:600;"><?= $c['us'] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container" style="position:relative;">
        <h2>Start Your Training Today</h2>
        <p>Free. No credit card. Ready in 30 seconds.</p>
        <div class="flex gap-16 flex-center mt-32">
            <a href="register.php" class="btn btn-white btn-lg">Create Free Account</a>
            <a href="how-it-works.php" class="btn btn-ghost btn-lg">See How It Works →</a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/public_footer.php'; ?>
<script src="assets/js/main.js"></script>
</body>
</html>
