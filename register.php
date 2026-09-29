<?php
require_once __DIR__ . '/includes/auth.php';
if (is_logged_in()) { header('Location: dashboard.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = 'Session expired, please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim(strtolower($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters.';
        } else {
            $pdo = get_db();
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);
            if ($check->fetch()) {
                $error = 'An account with this email already exists.';
            } else {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, cyber_score, streak_count, last_active_date) VALUES (?, ?, ?, 40, 1, ?)");
                $stmt->execute([$name, $email, $hash, date('Y-m-d')]);
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['just_registered'] = true;
                header('Location: dashboard.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<script>try{var t=localStorage.getItem('ss_theme')||'light';document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account · SafeSphere</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="icon" type="image/png" href="assets/images/logo.png">
<link rel="stylesheet" href="assets/css/style.css?v=2.9">
</head>
<body>
<?php include __DIR__ . '/includes/public_nav.php'; ?>
<div class="auth-wrap">
    <div class="card auth-card">
        <div class="auth-logo">
            <img src="assets/images/logo.png" alt="SafeSphere Logo" style="width:52px;height:52px;object-fit:contain;border-radius:8px;">
            SafeSphere
        </div>
        <h2>Create your account</h2>
        <p class="text-muted text-sm mt-8 mb-24">Start training against real-world scam scenarios.</p>

        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

        <form method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div class="field">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>
            </div>
            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="field">
                <label>Password</label>
                <input type="password" name="password" required>
                <div class="field-hint">Minimum 6 characters.</div>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Create Account</button>
        </form>

        <p class="text-sm text-center mt-16">Already have an account? <a href="login.php" style="color:var(--indigo);font-weight:700;">Log in</a></p>
        <p class="text-sm text-center mt-8"><a href="index.php" style="color:var(--text-muted);">← Back to home</a></p>
    </div>
</div>
<script src="assets/js/main.js?v=2.9"></script>
</body>
</html>
