<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) { header('Location: dashboard.php'); exit; }

$error = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Session expired, please try again.';
    } else {
        $email = trim(strtolower($_POST['email'] ?? ''));
        
        if ($email === '') {
            $error = 'Please enter an email address.';
        } else {
            $pdo = get_db();
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            $token = bin2hex(random_bytes(32));
            
            if ($user) {
                $expires = date('Y-m-d H:i:s', time() + 3600);
                $ins = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
                $ins->execute([$email, $token, $expires]);
            }
            
            $success_msg = "If an account exists for that email, a password reset link has been sent.<br><br>Reset link (dev mode — in production this would be emailed): <a href=\"reset_password.php?token=" . e($token) . "\">reset_password.php?token=" . e($token) . "</a>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password · SafeSphere</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.7">
</head>
<body>
<?php include __DIR__ . '/includes/public_nav.php'; ?>
<div class="auth-wrap">
    <div class="card auth-card">
        <div class="auth-logo">
            <span class="brand-badge" style="width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,#2563eb,#4f46e5);display:inline-flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 2px 8px rgba(37,99,235,0.35);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </span>
            SafeSphere
        </div>
        <h2>Reset Password</h2>
        <p class="text-muted text-sm mt-8 mb-24">Enter your email and we'll send you a link to reset your password.</p>

        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
        <?php if ($success_msg): ?><div class="alert alert-info"><?= $success_msg ?></div><?php else: ?>

        <form method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Send Reset Link</button>
        </form>
        <?php endif; ?>

        <p class="text-sm text-center mt-16"><a href="login.php" style="color:var(--indigo);font-weight:700;">Return to Log In</a></p>
    </div>
</div>
<script src="assets/js/main.js?v=2.6"></script>
</body>
</html>
