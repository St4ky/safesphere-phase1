<?php
require_once __DIR__ . '/includes/auth.php';
$nav_active = '';

$sent = false;
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $msg = 'Security token expired. Please try again.';
    } else {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if ($name === '' || $email === '' || $message === '') {
            $msg = 'Please complete all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = 'Please enter a valid email address.';
        } else {
            $sent = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact SafeSphere · Support & Inquiries</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.7">
<script>try{var t=localStorage.getItem('ss_theme');if(t)document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
</head>
<body>
<?php include __DIR__ . '/includes/public_nav.php'; ?>

<div class="container section" style="max-width:800px;">
    <div class="card card-pad">
        <h1 class="mb-8">Contact SafeSphere</h1>
        <p class="text-sm text-muted mb-24">Have feedback, want to report a new Indian scam vector, or inquire about academic / institutional partnerships?</p>
        
        <?php if ($sent): ?>
            <div class="alert alert-success">
                Thank you for getting in touch! We've received your note and will review it promptly.
            </div>
            <a href="index.php" class="btn btn-primary mt-12">Return to Home</a>
        <?php else: ?>
            <?php if ($msg): ?>
                <div class="alert alert-error"><?= e($msg) ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="field">
                    <label>Your Name</label>
                    <input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Email Address</label>
                    <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>Inquiry Topic</label>
                    <select name="subject">
                        <option value="scam_report">Report a new Scam / Attack pattern</option>
                        <option value="partnership">School / Organization Training Inquiry</option>
                        <option value="technical">Bug Report / Technical Issue</option>
                        <option value="feedback">General Platform Feedback</option>
                    </select>
                </div>
                <div class="field">
                    <label>Message</label>
                    <textarea name="message" rows="5" required style="resize:vertical;"><?= e($_POST['message'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Send Message</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/public_footer.php'; ?>
<script src="assets/js/main.js?v=2.6"></script>
</body>
</html>
