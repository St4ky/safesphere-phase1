<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$user = current_user();
$db = get_db();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $error = "Invalid form submission.";
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'update_name') {
            $name = trim($_POST['name'] ?? '');
            if (empty($name)) {
                $error = "Name cannot be empty.";
            } elseif (strlen($name) > 100) {
                $error = "Name is too long.";
            } else {
                $stmt = $db->prepare("UPDATE users SET name=? WHERE id=?");
                $stmt->execute([$name, $user['id']]);
                $success = "Name updated successfully.";
                $user = current_user(true); // refresh
            }
        } elseif ($action === 'change_password') {
            $current = $_POST['current_password'] ?? '';
            $new_pass = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            
            if (empty($current) || empty($new_pass) || empty($confirm)) {
                $error = "All password fields are required.";
            } elseif (!password_verify($current, $user['password_hash'])) {
                $error = "Current password is incorrect.";
            } elseif (strlen($new_pass) < 8) {
                $error = "New password must be at least 8 characters.";
            } elseif ($new_pass !== $confirm) {
                $error = "New passwords do not match.";
            } else {
                $hash = password_hash($new_pass, PASSWORD_BCRYPT);
                $stmt = $db->prepare("UPDATE users SET password_hash=? WHERE id=?");
                $stmt->execute([$hash, $user['id']]);
                $success = "Password changed successfully.";
            }
        } elseif ($action === 'delete_account') {
            $confirm_text = $_POST['confirm_text'] ?? '';
            if ($confirm_text !== 'DELETE MY ACCOUNT') {
                $error = "You must type 'DELETE MY ACCOUNT' to confirm.";
            } else {
                // Delete associated data
                $db->prepare("DELETE FROM attempts WHERE user_id=?")->execute([$user['id']]);
                $db->prepare("DELETE FROM audit_results WHERE user_id=?")->execute([$user['id']]);
                $db->prepare("DELETE FROM forensic_logs WHERE user_id=?")->execute([$user['id']]);
                $db->prepare("DELETE FROM certificates WHERE user_id=?")->execute([$user['id']]);
                // Delete user
                $db->prepare("DELETE FROM users WHERE id=?")->execute([$user['id']]);
                
                session_destroy();
                header("Location: index.php");
                exit;
            }
        }
    }
}

$page_title = 'Profile Settings';
$page_subtitle = 'Manage your account';
$active = 'profile';
$base = '';
include __DIR__ . '/includes/header.php';
?>

<?php if ($success): ?>
<div class="alert alert-success mb-24"><?= e($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-error mb-24"><?= e($error) ?></div>
<?php endif; ?>

<div class="card card-pad mb-24 text-center flex-center" style="flex-direction: column;">
    <?php $initials = strtoupper(substr($user['name'], 0, 2)); ?>
    <div style="width:80px; height:80px; border-radius:50%; background:linear-gradient(135deg, var(--indigo), var(--blue)); color:white; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:2rem; margin-bottom: 16px;">
        <?= e($initials) ?>
    </div>
    <h2 style="margin:0 0 8px 0;"><?= e($user['name']) ?></h2>
    <div class="text-muted mb-12"><?= e($user['email']) ?></div>
    <div class="flex gap-12 flex-center">
        <span class="badge badge-indigo"><?= e(ucfirst($user['role'])) ?></span>
        <span class="badge <?= $user['cyber_score'] >= 80 ? 'badge-green' : ($user['cyber_score'] >= 50 ? 'badge-amber' : 'badge-red') ?>">⚡ <?= $user['cyber_score'] ?> Score</span>
        <span class="badge badge-amber">🔥 <?= $user['streak_count'] ?> Streak</span>
    </div>
    <div class="text-sm text-muted mt-16 mb-16">Member since <?= date('F j, Y', strtotime($user['created_at'])) ?></div>
    
    <!-- Achievements & Badges -->
    <?php
    $earned_badges = get_user_badges($user['id']);
    $badge_catalog = [
        'first_step'     => ['icon' => '🎯', 'title' => 'First Defense',   'desc' => 'Answered first simulation scenario'],
        'sharpshooter'   => ['icon' => '🏹', 'title' => 'Sharpshooter',    'desc' => '10+ accurate fraud detections'],
        'sentinel'       => ['icon' => '🛡️', 'title' => 'High Sentinel',   'desc' => 'Maintained Cyber Score >= 80'],
        'streak_master'  => ['icon' => '🔥', 'title' => 'Streak Master',   'desc' => 'Consecutive daily activity for 3+ days'],
        'forensic_scout' => ['icon' => '🔬', 'title' => 'Forensic Scout',  'desc' => 'Conducted 3+ threat forensic tests']
    ];
    ?>
    <div style="width:100%; border-top:1px solid var(--border); padding-top:16px; margin-top:8px;">
        <div class="text-xs font-bold text-muted uppercase mb-12">Earned Achievements & Badges</div>
        <div class="flex gap-12 flex-center flex-wrap">
            <?php foreach ($badge_catalog as $bKey => $bDef): 
                $unlocked = isset($earned_badges[$bKey]);
            ?>
            <div class="card card-pad flex gap-8" style="padding:10px 14px; align-items:center; opacity:<?= $unlocked ? '1' : '0.45' ?>; background:<?= $unlocked ? 'var(--bg)' : 'transparent' ?>; border:1px solid var(--border);" title="<?= e($bDef['desc']) ?>">
                <span style="font-size:20px;"><?= $bDef['icon'] ?></span>
                <div style="text-align:left;">
                    <div style="font-size:12px; font-weight:700;"><?= e($bDef['title']) ?></div>
                    <div style="font-size:10px; color:var(--text-muted);"><?= $unlocked ? 'Unlocked' : 'Locked' ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="grid grid-2 gap-24">
    <div class="card card-pad">
        <h3 class="mb-16">Update Name</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="update_name">
            <div class="field">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= e($user['name']) ?>" required class="form-input" maxlength="100">
            </div>
            <button type="submit" class="btn btn-primary">Update Name</button>
        </form>
    </div>

    <div class="card card-pad">
        <h3 class="mb-16">Change Password</h3>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="change_password">
            <div class="field">
                <label>Current Password</label>
                <input type="password" name="current_password" required class="form-input">
            </div>
            <div class="field">
                <label>New Password</label>
                <input type="password" name="new_password" required minlength="8" class="form-input">
            </div>
            <div class="field">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required minlength="8" class="form-input">
            </div>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>

<div class="card card-pad mt-24" style="border: 1px solid var(--red);">
    <h3 class="mb-16" style="color: var(--red);">Danger Zone</h3>
    <div class="alert alert-error mb-16">
        Warning: Deleting your account is permanent. All your simulations, certificates, and scores will be erased.
    </div>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="delete_account">
        <div class="field">
            <label>Type 'DELETE MY ACCOUNT' to confirm</label>
            <input type="text" name="confirm_text" required class="form-input" autocomplete="off">
        </div>
        <button type="submit" class="btn btn-danger">Delete Account</button>
    </form>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
