<?php
require_once __DIR__ . '/includes/auth.php';
$nav_active = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Privacy Policy · SafeSphere</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.5">
<script>try{var t=localStorage.getItem('ss_theme');if(t)document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
</head>
<body>
<?php include __DIR__ . '/includes/public_nav.php'; ?>

<div class="container section" style="max-width:860px;">
    <div class="card card-pad">
        <h1 class="mb-16">Privacy Policy</h1>
        <p class="text-sm text-muted mb-24">Last updated: <?= date('F Y') ?></p>
        
        <div style="line-height:1.75; font-size:15px;" class="flex flex-col gap-16">
            <p>At <strong>SafeSphere</strong>, your privacy and data autonomy are fundamental principles. This privacy policy outlines how we handle data within our educational cyber defense platform.</p>
            
            <h3 class="mt-12">1. Information We Collect</h3>
            <p>SafeSphere collects minimal operational data needed to deliver training exercises:</p>
            <ul style="padding-left:20px; list-style:disc;">
                <li>Account credentials: Name, email, and cryptographically hashed passwords (bcrypt).</li>
                <li>Simulation progress: User choices, score telemetry, streak counters, and earned certificate metadata.</li>
                <li>Forensic analysis logs: Header and URL samples submitted to local rule engines (retained only for learning review).</li>
            </ul>

            <h3 class="mt-12">2. How We Use Information</h3>
            <p>Your data is used solely to track learning outcomes, generate progress reports, verify certificates, and provide educational feedback. We never sell or share user data with third-party advertisers or data brokers.</p>

            <h3 class="mt-12">3. Data Retention & Deletion</h3>
            <p>You maintain complete control of your account. You can permanently delete your profile, exercise history, and certificates at any time via your <a href="profile.php" style="color:var(--indigo);font-weight:700;">Profile Settings</a>.</p>

            <h3 class="mt-12">4. Educational Sandbox Safety</h3>
            <p>All phone numbers, banking links, and threat scenarios inside our simulations are simulated educational fixtures designed to mimic real threats safely without routing traffic to live malicious domains.</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/public_footer.php'; ?>
<script src="assets/js/main.js?v=2.6"></script>
</body>
</html>
