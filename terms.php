<?php
require_once __DIR__ . '/includes/auth.php';
$nav_active = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms of Use · SafeSphere</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.8">
<script>try{var t=localStorage.getItem('ss_theme');if(t)document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
</head>
<body>
<?php include __DIR__ . '/includes/public_nav.php'; ?>

<div class="container section" style="max-width:860px;">
    <div class="card card-pad">
        <h1 class="mb-16">Terms of Use</h1>
        <p class="text-sm text-muted mb-24">Effective date: <?= date('F Y') ?></p>
        
        <div style="line-height:1.75; font-size:15px;" class="flex flex-col gap-16">
            <p>Welcome to <strong>SafeSphere</strong>. By accessing or utilizing this cyber defense awareness platform, you agree to comply with and be bound by the following conditions.</p>
            
            <h3 class="mt-12">1. Educational Purpose Exclusively</h3>
            <p>SafeSphere is constructed purely for educational, defensive awareness, and training purposes. The tactics, technical indicators, and scenarios provided are intended to enable users to detect and protect against fraud.</p>
            
            <h3 class="mt-12">2. Prohibited Exploitative Use</h3>
            <p>Users are strictly forbidden from utilizing any knowledge or material obtained on SafeSphere for offensive reconnaissance, social engineering attacks, credential harvesting, unauthorized penetration testing, or malicious exploits.</p>

            <h3 class="mt-12">3. Institutional Disclaimer</h3>
            <p>SafeSphere is an independent educational training sandbox. It is not officially affiliated with, endorsed by, or representing the Reserve Bank of India (RBI), NPCI, CERT-In, State Bank of India, or any commercial entity simulated herein.</p>

            <h3 class="mt-12">4. Credential Verification & Fair Play</h3>
            <p>Certificates issued by SafeSphere attest to completion of simulated modules within our sandbox. Manipulation of database attempts or falsification of credential IDs is prohibited and grounds for profile termination.</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/public_footer.php'; ?>
<script src="assets/js/main.js?v=2.8"></script>
</body>
</html>
