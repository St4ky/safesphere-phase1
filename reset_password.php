<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) { header('Location: dashboard.php'); exit; }

$error = '';
$success_msg = '';
$token_error = '';

$token = $_GET['token'] ?? '';
$reset_record = null;

if (!$token) {
    $token_error = 'Invalid or missing reset token.';
} else {
    $pdo = get_db();
    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset_record = $stmt->fetch();
    
    if (!$reset_record) {
        $token_error = 'This password reset link is invalid or has expired.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $reset_record) {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Session expired, please try again.';
    } else {
        // Re-validate token exists in DB just in case
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()");
        $stmt->execute([$token]);
        $check_record = $stmt->fetch();
        
        if (!$check_record) {
            $error = 'This password reset link is invalid or has expired.';
        } else {
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (strlen($new_password) < 8) {
                $error = 'Password must be at least 8 characters.';
            } elseif ($new_password !== $confirm_password) {
                $error = 'Passwords do not match.';
            } else {
                $hash = password_hash($new_password, PASSWORD_BCRYPT);
                
                // Update user
                $upd_user = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
                $upd_user->execute([$hash, $check_record['email']]);
                
                // Mark token used
                $upd_token = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
                $upd_token->execute([$check_record['id']]);
                
                $success_msg = 'Your password has been reset successfully. You can now log in with your new password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password · SafeSphere</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css?v=2.8">
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
        <h2>Create New Password</h2>
        <p class="text-muted text-sm mt-8 mb-24">Enter your new password below.</p>

        <?php if ($token_error): ?>
            <div class="alert alert-error"><?= e($token_error) ?></div>
            <p class="text-sm text-center mt-16"><a href="forgot_password.php" style="color:var(--indigo);font-weight:700;">Request a new link</a></p>
        <?php elseif ($success_msg): ?>
            <div class="alert alert-success"><?= e($success_msg) ?></div>
            <p class="text-sm text-center mt-16"><a href="login.php" class="btn btn-primary btn-block">Go to Log In</a></p>
        <?php else: ?>
            <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
            <form method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                
                <div class="field">
                    <div class="flex-between mb-8" style="align-items:center;">
                        <label style="margin-bottom:0;">New Password</label>
                        <a href="#" id="toggle-pw1" style="font-size:12px;color:var(--text-muted);font-weight:600;">Show</a>
                    </div>
                    <input type="password" name="new_password" id="pw-input1" required>
                    <div class="field-hint">Minimum 8 characters.</div>
                </div>
                
                <div class="field">
                    <div class="flex-between mb-8" style="align-items:center;">
                        <label style="margin-bottom:0;">Confirm Password</label>
                        <a href="#" id="toggle-pw2" style="font-size:12px;color:var(--text-muted);font-weight:600;">Show</a>
                    </div>
                    <input type="password" name="confirm_password" id="pw-input2" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg">Reset Password</button>
            </form>
        <?php endif; ?>

        <?php if (!$success_msg): ?>
        <p class="text-sm text-center mt-16"><a href="login.php" style="color:var(--text-muted);">Back to Log In</a></p>
        <?php endif; ?>
    </div>
</div>
<script src="assets/js/main.js?v=2.8"></script>
<script>
function setupToggle(inputId, btnId) {
    var pwInput = document.getElementById(inputId);
    var toggleBtn = document.getElementById(btnId);
    if (pwInput && toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var isPassword = pwInput.type === 'password';
            pwInput.type = isPassword ? 'text' : 'password';
            toggleBtn.textContent = isPassword ? 'Hide' : 'Show';
        });
    }
}
setupToggle('pw-input1', 'toggle-pw1');
setupToggle('pw-input2', 'toggle-pw2');
</script>
</body>
</html>
