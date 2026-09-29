<?php
// Public footer — included on all public-facing pages (not the app shell)
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <!-- Brand -->
            <div class="footer-brand-col">
                <div class="logo-wrap" style="display:flex;align-items:center;gap:10px;">
                    <img src="assets/images/logo.png" alt="SafeSphere" style="width:40px;height:40px;object-fit:contain;border-radius:6px;">
                    <span style="font-size:18px;font-weight:800;">SafeSphere</span>
                </div>
                <div class="footer-tagline">India's hands-on cyber awareness platform — train against real phishing, UPI fraud, and social engineering scenarios.</div>
                <div class="footer-badge">🇮🇳 Built for Indian Internet Users</div>
            </div>

            <!-- Platform -->
            <div class="footer-col">
                <div class="footer-col-title">Platform</div>
                <a href="index.php">Home</a>
                <a href="about.php">About SafeSphere</a>
                <a href="how-it-works.php">How It Works</a>
                <a href="threats.php">Threat Intelligence</a>
                <a href="verify.php">Verify Credentials</a>
                <a href="contact.php">Contact & Feedback</a>
                <a href="register.php">Start Training Free</a>
                <a href="login.php">Sign In</a>
            </div>

            <!-- Modules -->
            <div class="footer-col">
                <div class="footer-col-title">Training Modules</div>
                <a href="login.php">Phishing Defense</a>
                <a href="login.php">UPI Fraud Simulator</a>
                <a href="login.php">Social Engineering</a>
                <a href="login.php">Network Self-Audit</a>
                <a href="login.php">Forensic Toolkit</a>
                <a href="login.php">Certificates</a>
            </div>

            <!-- Resources -->
            <div class="footer-col">
                <div class="footer-col-title">Resources</div>
                <a href="https://cybercrime.gov.in" target="_blank" rel="noopener">Report Cybercrime →</a>
                <a href="https://www.npci.org.in" target="_blank" rel="noopener">NPCI UPI Safety →</a>
                <a href="https://www.cert-in.org.in" target="_blank" rel="noopener">CERT-In Advisories →</a>
                <a href="https://www.india.gov.in/cyber-suraksha" target="_blank" rel="noopener">Cyber Suraksha →</a>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-text">© <?= date('Y') ?> SafeSphere. Educational platform — not affiliated with any bank or government body.</div>
            <div class="flex gap-16">
                <a href="privacy.php">Privacy Policy</a>
                <a href="terms.php">Terms of Use</a>
                <a href="contact.php">Contact</a>
            </div>
        </div>
    </div>
</footer>
