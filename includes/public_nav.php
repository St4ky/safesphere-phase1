<?php
// Public nav — session-aware. Shows Dashboard button if logged in, Login/Register if not.
// Include BEFORE outputting any HTML. Requires includes/auth.php already loaded.
require_once __DIR__ . '/auth.php';
$_nav_logged_in = is_logged_in();
$_nav_user      = $_nav_logged_in ? current_user() : null;
$_nav_active    = $nav_active ?? '';
?>
<nav class="landing-nav">
    <div class="flex gap-10" style="align-items:center;">
        <a href="index.php" style="display:inline-flex;align-items:center;gap:10px;font-weight:800;font-size:18px;color:inherit;letter-spacing:-0.02em;">
            <span class="brand-badge" style="width:34px;height:34px;border-radius:8px;background:linear-gradient(135deg,#2563eb,#4f46e5);display:inline-flex;align-items:center;justify-content:center;color:#fff;box-shadow:0 2px 8px rgba(37,99,235,0.35);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </span>
            SafeSphere
        </a>
    </div>

    <div class="nav-links">
        <a href="index.php"        class="nav-link <?= $_nav_active === 'home'       ? 'active' : '' ?>">Home</a>
        <a href="about.php"        class="nav-link <?= $_nav_active === 'about'      ? 'active' : '' ?>">Platform</a>
        <a href="how-it-works.php" class="nav-link <?= $_nav_active === 'howitworks' ? 'active' : '' ?>">Curriculum</a>
        <a href="threats.php"      class="nav-link <?= $_nav_active === 'threats'    ? 'active' : '' ?>">Threat Radar</a>
        <a href="verify.php"       class="nav-link <?= $_nav_active === 'verify'     ? 'active' : '' ?>">Verify Credential</a>
    </div>

    <div class="flex gap-12" style="align-items:center;">
        <!-- Theme Toggle Button -->
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

        <!-- Mobile Menu Toggle Button (Hidden on desktop) -->
        <button type="button" class="landing-nav-toggle" onclick="document.querySelector('.landing-nav .nav-links').classList.toggle('open')" aria-label="Toggle Navigation Menu" title="Toggle Navigation Menu">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"></line>
                <line x1="3" y1="12" x2="21" y2="12"></line>
                <line x1="3" y1="18" x2="21" y2="18"></line>
            </svg>
        </button>

        <?php if ($_nav_logged_in && $_nav_user): ?>
            <span class="points-chip" style="font-size:12px;padding:6px 10px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                <?= (int)$_nav_user['cyber_score'] ?> pts
            </span>
            <a href="dashboard.php" class="btn btn-primary btn-sm">Command Center →</a>
        <?php else: ?>
            <a href="login.php"    class="btn btn-outline btn-sm">Sign In</a>
            <a href="register.php" class="btn btn-primary btn-sm">Get Started</a>
        <?php endif; ?>
    </div>
</nav>
