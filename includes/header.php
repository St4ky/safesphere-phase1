<?php
// Expects: $page_title, $page_subtitle (optional), $active (nav key), $base ('' for root pages, '../' for modules/*) 
// Requires includes/auth.php already loaded and $user (current_user()) available.
$user = current_user();
$base = $base ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($page_title ?? 'SafeSphere') ?> · SafeSphere Cyber Defense</title>
<script>try{var t=localStorage.getItem('ss_theme')||'light';document.documentElement.setAttribute('data-theme',t);}catch(e){}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $base ?>assets/css/style.css?v=2.6">
</head>
<body>
<!-- Mobile sidebar overlay backdrop -->
<div id="sidebar-backdrop" class="sidebar-backdrop" onclick="closeSidebar()"></div>

<div class="app-shell">
    <aside class="sidebar" id="app-sidebar">
        <div class="sidebar-logo">
            <span class="brand-badge" style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#2563eb,#4f46e5);display:inline-flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 2px 8px rgba(37,99,235,0.35);flex-shrink:0;">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </span>
            <span>SafeSphere</span>
        </div>

        <div class="nav-section-label">Command Center</div>
        <a href="<?= $base ?>dashboard.php" class="nav-item <?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>">
            <svg class="icon-svg" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            <span>Dashboard</span>
        </a>
        <a href="<?= $base ?>leaderboard.php" class="nav-item <?= ($active ?? '') === 'leaderboard' ? 'active' : '' ?>">
            <svg class="icon-svg" viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path><path d="M4 22h16"></path><path d="M10 14.66V17c0 .55-.45 1-1 1H7c-.55 0-1-.45-1-1v-2.34"></path><path d="M14 14.66V17c0 .55.45 1 1 1h2c.55 0 1-.45 1-1v-2.34"></path><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path></svg>
            <span>Rankings</span>
        </a>

        <div class="nav-section-label">Defensive Labs</div>
        <a href="<?= $base ?>modules/phishing.php" class="nav-item <?= ($active ?? '') === 'phishing' ? 'active' : '' ?>">
            <svg class="icon-svg" viewBox="0 0 24 24"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
            <span>Phishing &amp; Domain Spoofing</span>
        </a>
        <a href="<?= $base ?>modules/upi.php" class="nav-item <?= ($active ?? '') === 'upi' ? 'active' : '' ?>">
            <svg class="icon-svg" viewBox="0 0 24 24"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path></svg>
            <span>UPI Fraud &amp; Payment Scams</span>
        </a>
        <a href="<?= $base ?>modules/socialeng.php" class="nav-item <?= ($active ?? '') === 'socialeng' ? 'active' : '' ?>">
            <svg class="icon-svg" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
            <span>Social Engineering &amp; Vishing</span>
        </a>
        <a href="<?= $base ?>modules/network.php" class="nav-item <?= ($active ?? '') === 'network' ? 'active' : '' ?>">
            <svg class="icon-svg" viewBox="0 0 24 24"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
            <span>Network Hardening &amp; Audit</span>
        </a>
        <a href="<?= $base ?>modules/otp.php" class="nav-item <?= ($active ?? '') === 'otp' ? 'active' : '' ?>">
            <svg class="icon-svg" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
            <span>OTP Hijacking &amp; SIM-Swap</span>
        </a>
        <a href="<?= $base ?>modules/deepfake.php" class="nav-item <?= ($active ?? '') === 'deepfake' ? 'active' : '' ?>">
            <svg class="icon-svg" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="2"></rect><circle cx="9" cy="9" r="2"></circle><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"></path></svg>
            <span>Deepfake &amp; AI Voice Clone</span>
        </a>

        <div class="nav-section-label">Intelligence Tools</div>
        <a href="<?= $base ?>forensics.php" class="nav-item <?= ($active ?? '') === 'forensics' ? 'active' : '' ?>">
            <svg class="icon-svg" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            <span>Forensic Toolkit</span>
        </a>
        <a href="<?= $base ?>reports.php" class="nav-item <?= ($active ?? '') === 'reports' ? 'active' : '' ?>">
            <svg class="icon-svg" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
            <span>Security Reports</span>
        </a>
        <a href="<?= $base ?>certificates.php" class="nav-item <?= ($active ?? '') === 'certificates' ? 'active' : '' ?>">
            <svg class="icon-svg" viewBox="0 0 24 24"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
            <span>Certifications</span>
        </a>

        <div class="nav-section-label">Resources</div>
        <a href="<?= $base ?>threats.php" class="nav-item">
            <svg class="icon-svg" viewBox="0 0 24 24"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            <span>Threat Radar</span>
        </a>
        <a href="<?= $base ?>about.php" class="nav-item">
            <svg class="icon-svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
            <span>Platform Info</span>
        </a>

        <div class="sidebar-footer">
            <div class="nav-item" style="cursor:default;">
                <svg class="icon-svg" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <span style="font-weight:600;"><?= e($user['name'] ?? 'User') ?></span>
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                <span class="badge badge-red" style="font-size:9px;margin-left:4px;">Admin</span>
                <?php endif; ?>
            </div>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
            <a href="<?= $base ?>admin.php" class="nav-item <?= ($active ?? '') === 'admin' ? 'active' : '' ?>" style="color:var(--red);">
                <svg class="icon-svg" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                <span>Admin Console</span>
            </a>
            <?php endif; ?>
            <a href="<?= $base ?>profile.php" class="nav-item <?= ($active ?? '') === 'profile' ? 'active' : '' ?>">
                <svg class="icon-svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                <span>Settings</span>
            </a>
            <a href="<?= $base ?>logout.php" class="nav-item">
                <svg class="icon-svg" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span>Sign Out</span>
            </a>
        </div>
    </aside>

    <div class="main">
        <div class="topbar">
            <div class="flex gap-12" style="align-items:center;">
                <!-- Hamburger button — visible on mobile only -->
                <button class="hamburger-btn" id="hamburger-btn" onclick="openSidebar()" aria-label="Open menu">
                    <span></span><span></span><span></span>
                </button>
                <div>
                    <h1><?= e($page_title ?? 'SafeSphere') ?></h1>
                    <?php if (!empty($page_subtitle)): ?>
                        <div class="subtitle"><?= e($page_subtitle) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex gap-12" style="align-items:center;">
                <!-- Theme toggle button -->
                <button type="button" class="theme-toggle-btn" onclick="toggleDarkMode()" aria-label="Toggle Theme" title="Toggle Theme" style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:8px;border:1px solid var(--border);background:var(--surface);cursor:pointer;color:var(--text-muted);padding:0;flex-shrink:0;">
                    <svg class="theme-icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                    <svg class="theme-icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                </button>

                <span class="points-chip">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    <?= (int)($user['cyber_score'] ?? 0) ?> Cyber Score
                </span>
                <span class="badge badge-indigo" style="padding:6px 10px;font-size:12px;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>
                    <?= (int)($user['streak_count'] ?? 0) ?> Day Streak
                </span>
            </div>
        </div>
        <div class="page">
