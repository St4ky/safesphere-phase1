<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$user = current_user();

// ─── Rule-based analysis functions ────────────────────────────────────────────

function analyze_email_header(string $raw): array {
    $findings = [];
    $verdict  = 'clean';

    // Extract From header
    if (preg_match('/^From:\s*(.+)$/im', $raw, $m)) {
        $from = $m[1];
        if (preg_match('/([^<]+)<([^>]+)>/', $from, $parts)) {
            $display = trim($parts[1]);
            $email   = trim($parts[2]);
            $emailDomain = preg_replace('/.*@/', '', $email);
            $knownBrands = ['sbi','hdfc','icici','axis','paytm','amazon','flipkart','google','microsoft','irctc','phonepe'];
            foreach ($knownBrands as $brand) {
                if (stripos($display, $brand) !== false && stripos($emailDomain, $brand) === false) {
                    $findings[] = ['level' => 'red', 'text' => "Display name \"$display\" claims to be $brand but sending domain is \"$emailDomain\" — classic spoofing signature."];
                    $verdict = 'suspicious';
                }
            }
        }
    }

    // SPF check
    if (preg_match('/spf=(pass|fail|softfail|neutral|none)/i', $raw, $m)) {
        $spf = strtolower($m[1]);
        if ($spf === 'pass') {
            $findings[] = ['level' => 'green', 'text' => 'SPF: PASS — sending server is cryptographically authorized by the domain SPF record.'];
        } else {
            $findings[] = ['level' => 'red', 'text' => "SPF: $spf — sending mail server is NOT authorized. High spoofing/phishing risk."];
            $verdict = 'suspicious';
        }
    } else {
        $findings[] = ['level' => 'amber', 'text' => 'SPF: Not detected in headers — sender authentication cannot be verified.'];
        if ($verdict === 'clean') $verdict = 'warning';
    }

    // DKIM check
    if (preg_match('/dkim=(pass|fail|neutral|none)/i', $raw, $m)) {
        $dkim = strtolower($m[1]);
        if ($dkim === 'pass') {
            $findings[] = ['level' => 'green', 'text' => 'DKIM: PASS — cryptographic signature is valid. Message body was not altered in transit.'];
        } else {
            $findings[] = ['level' => 'red', 'text' => "DKIM: $dkim — digital signature invalid or failed verification."];
            $verdict = 'suspicious';
        }
    } else {
        $findings[] = ['level' => 'amber', 'text' => 'DKIM: Not detected — no cryptographic signature attached.'];
    }

    // DMARC check
    if (preg_match('/dmarc=(pass|fail|none)/i', $raw, $m)) {
        $dmarc = strtolower($m[1]);
        if ($dmarc === 'pass') {
            $findings[] = ['level' => 'green', 'text' => 'DMARC: PASS — domain alignment verified between From and SPF/DKIM identifiers.'];
        } else {
            $findings[] = ['level' => 'red', 'text' => "DMARC: $dmarc — domain alignment failed. Sender domain cannot be authenticated."];
            $verdict = 'suspicious';
        }
    }

    // Received chain timezone anomaly
    preg_match_all('/Received:.*?(\+[\d]{4}|-[\d]{4})/is', $raw, $tz_matches);
    if (!empty($tz_matches[1])) {
        $tzs = array_unique($tz_matches[1]);
        if (count($tzs) > 2) {
            $findings[] = ['level' => 'amber', 'text' => 'Multiple conflicting timezones in Received relay chain (' . implode(', ', $tzs) . ') — mail passed through unusual intermediate hops.'];
            if ($verdict === 'clean') $verdict = 'warning';
        }
    }

    // Reply-To mismatch
    if (preg_match('/^Reply-To:\s*(.+)$/im', $raw, $rto) && preg_match('/^From:\s*(.+)$/im', $raw, $frm)) {
        $replyDomain = preg_replace('/.*@([^>\s]+).*/i', '$1', $rto[1]);
        $fromDomain  = preg_replace('/.*@([^>\s]+).*/i', '$1', $frm[1]);
        if (strtolower(trim($replyDomain)) !== strtolower(trim($fromDomain))) {
            $findings[] = ['level' => 'red', 'text' => "Reply-To address ($replyDomain) does not match From address ($fromDomain). Replies are routed to an external inbox."];
            $verdict = 'suspicious';
        }
    }

    if (empty($findings)) {
        $findings[] = ['level' => 'green', 'text' => 'No anomalies found in header structure. Basic verification passes.'];
    }

    return ['verdict' => $verdict, 'findings' => $findings];
}

function analyze_url(string $url): array {
    $findings = [];
    $verdict  = 'clean';

    $url = trim($url);
    if (!preg_match('~^https?://~i', $url)) {
        $url = 'https://' . $url;
    }
    $parsed = parse_url($url);
    $host = strtolower($parsed['host'] ?? $url);

    // IP as host
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        $findings[] = ['level' => 'red', 'text' => "Direct IP ($host) used instead of domain name. Legitimate corporate services use registered domain names."];
        $verdict = 'suspicious';
    }

    // URL shorteners
    $shorteners = ['bit.ly','tinyurl.com','t.co','goo.gl','ow.ly','tiny.cc','is.gd','buff.ly','adf.ly','rb.gy','cutt.ly','shorturl.at'];
    foreach ($shorteners as $sh) {
        if (str_ends_with($host, $sh) || $host === $sh) {
            $findings[] = ['level' => 'amber', 'text' => "URL shortener detected ($sh) — masks final target destination."];
            if ($verdict === 'clean') $verdict = 'warning';
        }
    }

    // Suspicious TLDs
    $suspTLDs = ['.xyz','.tk','.ml','.ga','.cf','.pw','.top','.click','.download','.zip','.mov','.icu','.buzz','.live'];
    foreach ($suspTLDs as $tld) {
        if (str_ends_with($host, $tld)) {
            $findings[] = ['level' => 'amber', 'text' => "High-abuse TLD \"$tld\" detected — heavily utilized in disposable phishing kits."];
            if ($verdict === 'clean') $verdict = 'warning';
        }
    }

    // Brand in subdomain but not root
    $brands = ['sbi','hdfc','icici','axis','paytm','amazon','flipkart','google','microsoft','irctc','phonepe','npci'];
    $parts  = explode('.', $host);
    $tld2   = implode('.', array_slice($parts, -2));
    foreach ($brands as $brand) {
        if (str_contains($host, $brand) && !str_contains($tld2, $brand)) {
            $findings[] = ['level' => 'red', 'text' => "Brand token \"$brand\" detected in subdomain but not in root domain (\"$tld2\"). Typical spoofing layout."];
            $verdict = 'suspicious';
        }
    }

    // Punycode / Homoglyph
    if (str_starts_with($host, 'xn--')) {
        $findings[] = ['level' => 'red', 'text' => "Punycode domain (\"$host\") detected — frequently employed for lookalike visual deception."];
        $verdict = 'suspicious';
    }

    // Excessive subdomains
    if (count($parts) > 4) {
        $findings[] = ['level' => 'amber', 'text' => count($parts) . " subdomain levels detected — legitimate services rarely exceed 3 levels."];
        if ($verdict === 'clean') $verdict = 'warning';
    }

    // Live DNS query via Cloudflare DoH
    $dohUrl = 'https://cloudflare-dns.com/dns-query?name=' . urlencode($host) . '&type=A';
    $ctx = stream_context_create([
        'http' => ['header' => 'Accept: application/dns-json', 'timeout' => 3],
        'ssl'  => ['verify_peer' => false, 'verify_peer_name' => false]
    ]);
    $doh = @file_get_contents($dohUrl, false, $ctx);
    if ($doh) {
        $dohData = json_decode($doh, true);
        if (isset($dohData['Answer']) && !empty($dohData['Answer'])) {
            $ips = [];
            foreach ($dohData['Answer'] as $ans) {
                if (($ans['type'] ?? 0) === 1 && isset($ans['data'])) $ips[] = $ans['data'];
            }
            if (!empty($ips)) {
                $findings[] = ['level' => 'green', 'text' => 'Live Cloudflare DoH resolution verified: ' . implode(', ', array_slice($ips, 0, 3))];
            }
        } else {
            $findings[] = ['level' => 'amber', 'text' => 'Live Cloudflare DoH lookup: Domain has no active A records (NXDOMAIN/Inactive).'];
            if ($verdict === 'clean') $verdict = 'warning';
        }
    }

    if (empty($findings)) {
        $findings[] = ['level' => 'green', 'text' => 'Domain structure appears standard. Verify URL bar before providing credentials.'];
    }

    return ['verdict' => $verdict, 'findings' => $findings, 'host' => $host];
}

function analyze_permissions(array $perms): array {
    $findings = [];
    $verdict  = 'clean';
    $riskScore = 0;

    $dangerSingle = ['READ_CALL_LOG','RECORD_AUDIO','READ_CONTACTS','SEND_SMS','READ_SMS','RECEIVE_SMS','CAMERA','ACCESS_FINE_LOCATION','PROCESS_OUTGOING_CALLS','GET_ACCOUNTS'];
    $detected = [];
    foreach ($dangerSingle as $p) {
        if (in_array($p, $perms)) { $detected[] = $p; $riskScore += 2; }
    }
    if (!empty($detected)) {
        $findings[] = ['level' => 'amber', 'text' => 'Dangerous runtime permissions requested: ' . implode(', ', $detected)];
    }

    if (in_array('READ_SMS', $perms) && in_array('RECEIVE_SMS', $perms) && in_array('INTERNET', $perms)) {
        $findings[] = ['level' => 'red', 'text' => 'CRITICAL COMBO: SMS Interception + Internet access enables silent OTP interception and exfiltration.'];
        $verdict = 'suspicious';
        $riskScore += 8;
    }

    if (in_array('RECORD_AUDIO', $perms) && in_array('INTERNET', $perms)) {
        $findings[] = ['level' => 'red', 'text' => 'Microphone recording paired with Internet permission — potential spyware signature.'];
        $verdict = 'suspicious';
        $riskScore += 5;
    }

    if (in_array('READ_CONTACTS', $perms) && in_array('READ_EXTERNAL_STORAGE', $perms)) {
        $findings[] = ['level' => 'amber', 'text' => 'Contact list and storage access matches permissions harvested by predatory instant-loan apps.'];
        $riskScore += 4;
        if ($verdict === 'clean') $verdict = 'warning';
    }

    if ($riskScore >= 10) $verdict = 'suspicious';
    elseif ($riskScore >= 5 && $verdict === 'clean') $verdict = 'warning';

    return ['verdict' => $verdict, 'findings' => $findings, 'risk_score' => $riskScore];
}

// Handle POST submissions
$active_tab  = $_POST['tab'] ?? $_GET['tab'] ?? 'url';
$input_value = '';
$result      = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $flash_error = 'Session expired, please try again.';
    } else {
        if ($active_tab === 'header') {
            $raw = trim($_POST['header_input'] ?? '');
            $input_value = $raw;
            if ($raw) {
                $result = analyze_email_header($raw);
                get_db()->prepare("INSERT INTO forensic_logs (user_id, tool_type, input_sample, verdict) VALUES (?,?,?,?)")
                        ->execute([$user['id'], 'header', substr($raw, 0, 255), $result['verdict']]);
            }
        } elseif ($active_tab === 'url') {
            $url = trim($_POST['url_input'] ?? '');
            $input_value = $url;
            if ($url) {
                $result = analyze_url($url);
                get_db()->prepare("INSERT INTO forensic_logs (user_id, tool_type, input_sample, verdict) VALUES (?,?,?,?)")
                        ->execute([$user['id'], 'url', substr($url, 0, 255), $result['verdict']]);
            }
        } elseif ($active_tab === 'apk') {
            $perms = $_POST['perms'] ?? [];
            $input_value = implode(',', $perms);
            if ($perms) {
                $result = analyze_permissions($perms);
                get_db()->prepare("INSERT INTO forensic_logs (user_id, tool_type, input_sample, verdict) VALUES (?,?,?,?)")
                        ->execute([$user['id'], 'apk_permission', substr($input_value, 0, 255), $result['verdict']]);
            }
        }
    }
}

// Recent logs
$logs = get_db()->prepare("SELECT * FROM forensic_logs WHERE user_id=? ORDER BY created_at DESC LIMIT 6");
$logs->execute([$user['id']]);
$log_rows = $logs->fetchAll();

$all_perms = [
    'READ_SMS','SEND_SMS','RECEIVE_SMS','READ_CONTACTS','WRITE_CONTACTS','RECORD_AUDIO',
    'CAMERA','ACCESS_FINE_LOCATION','ACCESS_COARSE_LOCATION','READ_CALL_LOG',
    'PROCESS_OUTGOING_CALLS','GET_ACCOUNTS','READ_EXTERNAL_STORAGE','WRITE_EXTERNAL_STORAGE',
    'INTERNET','BLUETOOTH','NFC','VIBRATE','RECEIVE_BOOT_COMPLETED','FOREGROUND_SERVICE',
];

$page_title    = 'Forensic Toolkit';
$page_subtitle = 'Live API domain inspection, HaveIBeenPwned breach verification, IP intelligence & header analysis';
$active        = 'forensics';
$base          = '';
include __DIR__ . '/includes/header.php';
?>

<div data-tabs>
    <div class="tab-bar">
        <button class="tab-btn <?= $active_tab === 'url'    ? 'active' : '' ?>" data-tab="url">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
            URL & DoH Domain Inspector
        </button>
        <button class="tab-btn <?= $active_tab === 'pwd'    ? 'active' : '' ?>" data-tab="pwd">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            HIBP Password Breach Scanner
        </button>
        <button class="tab-btn <?= $active_tab === 'ip'     ? 'active' : '' ?>" data-tab="ip">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12.55a11 11 0 0 1 14.08 0"></path><path d="M1.42 9a16 16 0 0 1 21.16 0"></path><path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path><line x1="12" y1="20" x2="12.01" y2="20"></line></svg>
            IP & Geo-Intelligence
        </button>
        <button class="tab-btn <?= $active_tab === 'header' ? 'active' : '' ?>" data-tab="header">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"></path><path d="M22 2 11 13"></path></svg>
            Email Header Analyzer
        </button>
        <button class="tab-btn <?= $active_tab === 'apk'    ? 'active' : '' ?>" data-tab="apk">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"></rect><path d="M12 18h.01"></path></svg>
            APK Permission Audit
        </button>
    </div>

    <!-- TAB: URL Inspector -->
    <div class="tab-panel <?= $active_tab === 'url' ? 'active' : '' ?>" id="tab-url">
        <div class="grid grid-2" style="gap:20px;align-items:start;">
            <div>
                <div class="card card-pad">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <h3 style="font-size:17px;font-weight:800;">URL & Domain Intelligence Inspector</h3>
                        <span class="badge badge-indigo">Live Cloudflare DoH</span>
                    </div>
                    <p class="text-sm text-muted mb-16">
                        Paste suspicious URLs to analyze homoglyphs, brand spoofing, suspicious TLDs, and query live Cloudflare DNS over HTTPS records for active IP resolution.
                    </p>
                    <form method="POST" class="forensic-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="tab" value="url">
                        <input type="text" name="url_input" class="scanner-input" style="font-family:'JetBrains Mono',monospace;"
                               placeholder="e.g. sbi-kyc-update.xyz or netbanking.secure-sbi.top"
                               value="<?= e($active_tab === 'url' ? $input_value : 'sbi-kyc-update.xyz') ?>" required>
                        <button type="submit" class="btn btn-primary mt-12 forensic-submit" style="width:100%;">
                            Inspect URL via Live DNS API →
                        </button>
                    </form>
                    <div class="mt-16">
                        <div class="text-xs text-muted font-bold mb-8">QUICK TEST PRESETS:</div>
                        <div class="flex flex-col gap-6">
                            <code class="text-xs" style="background:var(--bg);padding:6px 10px;border-radius:4px;border:1px solid var(--border);cursor:pointer;" onclick="document.querySelector('[name=url_input]').value='http://sbi-netbanking.secure-kyc.xyz/login'">http://sbi-netbanking.secure-kyc.xyz/login</code>
                            <code class="text-xs" style="background:var(--bg);padding:6px 10px;border-radius:4px;border:1px solid var(--border);cursor:pointer;" onclick="document.querySelector('[name=url_input]').value='https://bit.ly/free-recharge-india'">https://bit.ly/free-recharge-india</code>
                            <code class="text-xs" style="background:var(--bg);padding:6px 10px;border-radius:4px;border:1px solid var(--border);cursor:pointer;" onclick="document.querySelector('[name=url_input]').value='https://192.168.1.1/admin'">https://192.168.1.1/admin</code>
                        </div>
                    </div>
                </div>

                <?php if (!empty($log_rows)): ?>
                <div class="card card-pad mt-16">
                    <div class="font-bold text-sm mb-12">Recent Forensic Audits</div>
                    <?php foreach ($log_rows as $log): ?>
                    <div class="flex-between text-sm" style="padding:6px 0;border-bottom:1px solid var(--border);">
                        <span class="text-muted"><?= e(ucfirst($log['tool_type'])) ?> — <?= e(substr($log['input_sample'], 0, 30)) ?>…</span>
                        <span class="badge <?= $log['verdict'] === 'suspicious' ? 'badge-red' : ($log['verdict'] === 'warning' ? 'badge-amber' : 'badge-green') ?>">
                            <?= e(strtoupper($log['verdict'])) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div>
                <?php if ($result && $active_tab === 'url'): ?>
                <div class="card card-pad">
                    <div class="mb-12 text-sm font-mono" style="word-break:break-all;background:var(--bg);padding:10px;border-radius:6px;border:1px solid var(--border);"><?= e($input_value) ?></div>
                    <div class="flex-between mb-16">
                        <span class="badge <?= $result['verdict'] === 'suspicious' ? 'badge-red' : ($result['verdict'] === 'warning' ? 'badge-amber' : 'badge-green') ?>" style="font-size:13px;padding:6px 12px;">
                            <?= $result['verdict'] === 'suspicious' ? 'HIGH RISK PHISHING SIGNATURE' : ($result['verdict'] === 'warning' ? 'ANOMALIES DETECTED — CAUTION' : 'VERIFIED CLEAN STRUCTURE') ?>
                        </span>
                    </div>
                    <div style="display:grid;gap:10px;">
                        <?php foreach ($result['findings'] as $f): ?>
                        <div style="padding:10px 14px;border-radius:6px;background:var(--bg);border-left:3px solid <?= $f['level'] === 'red' ? 'var(--red)' : ($f['level'] === 'amber' ? 'var(--amber)' : 'var(--green)') ?>;border:1px solid var(--border);border-left-width:3px;">
                            <div style="font-size:13.5px;color:var(--text);line-height:1.5;"><?= e($f['text']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="card card-pad" style="background:var(--bg);border-style:dashed;">
                    <p class="text-sm text-muted text-center" style="padding:40px 0;">Enter a URL and click Inspect to view live DNS resolution and threat heuristics.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- TAB: Password Hygiene & HIBP API Tester -->
    <div class="tab-panel <?= $active_tab === 'pwd' ? 'active' : '' ?>" id="tab-pwd">
        <div class="grid grid-2" style="gap:20px;align-items:start;">
            <div>
                <div class="card card-pad">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <h3 style="font-size:17px;font-weight:800;">HIBP Real Breach & Entropy Scanner</h3>
                        <span class="badge badge-indigo">k-Anonymity SHA-1</span>
                    </div>
                    <p class="text-sm text-muted mb-16">
                        Tests password entropy and queries the <strong>HaveIBeenPwned (HIBP)</strong> database of 800+ million compromised credentials using cryptographically secure k-Anonymity (only 5 characters of SHA-1 hash are sent).
                    </p>
                    
                    <div class="field">
                        <label>Enter Password to Audit</label>
                        <input type="text" id="pwd-inspector-input" class="scanner-input" style="font-family:'JetBrains Mono',monospace;" placeholder="Type password (e.g. Rahul@123, India2024)...">
                    </div>

                    <button type="button" id="hibp-check-btn" class="btn btn-primary btn-block mt-8" onclick="checkHIBPPassword()">
                        Audit Password Against 800M+ Leaked Breaches →
                    </button>

                    <div class="card card-pad mt-16" style="background:var(--bg); border:1px solid var(--border);">
                        <div class="text-xs font-bold text-muted mb-8 uppercase">Common Indian Weak Patterns Evaluated:</div>
                        <ul style="padding-left:18px; margin:0; font-size:12.5px; line-height:1.6; color:var(--text-muted);">
                            <li>First Name / Nickname + <code>@123</code> or <code>1234</code></li>
                            <li>Birth years (e.g., <code>1998</code>, <code>2002</code>) or 10-digit mobile phone sequences</li>
                            <li>Religious/National tokens (e.g., <code>Krishna@1</code>, <code>India@2024</code>, <code>Hanuman</code>)</li>
                            <li>Sequential keyboard rows (e.g., <code>qwerty</code>, <code>asdfgh</code>)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div>
                <div class="card card-pad" id="pwd-result-box">
                    <div class="flex-between mb-12">
                        <span class="badge badge-gray" id="pwd-verdict" style="font-size:13px;padding:6px 12px;">Awaiting Password Input</span>
                    </div>
                    <div class="text-sm text-muted mb-12" id="pwd-crack-time">Estimated crack time: &mdash;</div>
                    <div class="risk-meter mb-16">
                        <div class="risk-meter-fill" id="pwd-strength-bar" style="width:0%; background:var(--red);"></div>
                    </div>
                    
                    <!-- HIBP Breach Box -->
                    <div id="hibp-result-area" style="margin-bottom:14px;display:none;"></div>

                    <div id="pwd-findings-list" class="flex flex-col gap-8">
                        <div class="alert alert-info" style="font-size:12.5px;">
                            Zero-knowledge architecture: HIBP queries transmit only the first 5 characters of the SHA-1 hash. Full plaintext is never exposed.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: IP & Geo-Intelligence Scanner -->
    <div class="tab-panel <?= $active_tab === 'ip' ? 'active' : '' ?>" id="tab-ip">
        <div class="grid grid-2" style="gap:20px;align-items:start;">
            <div>
                <div class="card card-pad">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                        <h3 style="font-size:17px;font-weight:800;">IP & Network Geo-Intelligence</h3>
                        <span class="badge badge-indigo">Real-Time ASN API</span>
                    </div>
                    <p class="text-sm text-muted mb-16">
                        Inspect sender IP addresses from email headers or investigate remote servers to pinpoint country, ISP, Autonomous System (ASN), and detect VPN/datacenter relays.
                    </p>
                    
                    <div class="field">
                        <label>IP Address to Audit</label>
                        <input type="text" id="ip-lookup-input" class="scanner-input" placeholder="e.g. 8.8.8.8 or leave empty for your current IP" value="8.8.8.8">
                    </div>
                    <div class="flex gap-10">
                        <button type="button" class="btn btn-primary" style="flex:1;" onclick="scanIPAddress()">Inspect IP Coordinates →</button>
                        <button type="button" class="btn btn-outline" onclick="scanMyIP()">Audit My Network</button>
                    </div>
                </div>
            </div>
            <div>
                <div class="card card-pad" id="ip-result-card" style="display:none;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                        <h4 style="font-size:16px;font-weight:800;" id="ip-res-title">IP Intelligence Report</h4>
                        <span class="badge badge-green" id="ip-res-badge">Active</span>
                    </div>
                    <div id="ip-res-body" style="font-size:13.5px;line-height:1.6;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB: Email Header -->
    <div class="tab-panel <?= $active_tab === 'header' ? 'active' : '' ?>" id="tab-header">
        <div class="grid grid-2" style="gap:20px;align-items:start;">
            <div>
                <div class="card card-pad">
                    <h3 class="mb-8" style="font-size:17px;font-weight:800;">Email Header Forensics</h3>
                    <p class="text-sm text-muted mb-16">Paste raw MIME email headers to verify cryptographic SPF/DKIM/DMARC status, display-name spoofing, and Reply-To redirection traps.</p>
                    <form method="POST" class="forensic-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="tab" value="header">
                        <textarea class="scanner-input" name="header_input" style="height:140px;font-family:'JetBrains Mono',monospace;font-size:12.5px;" placeholder="From: State Bank of India <alerts@sbi-update-kyc.xyz>&#10;Reply-To: scammer99@gmail.com&#10;Received-SPF: fail (domain does not designate)&#10;Authentication-Results: dkim=fail"><?= e($active_tab === 'header' ? $input_value : '') ?></textarea>
                        <button type="submit" class="btn btn-primary mt-12 forensic-submit" style="width:100%;">Audit Headers →</button>
                    </form>
                </div>
            </div>
            <div>
                <?php if ($result && $active_tab === 'header'): ?>
                <div class="card card-pad">
                    <div class="mb-12">
                        <span class="badge <?= $result['verdict'] === 'suspicious' ? 'badge-red' : ($result['verdict'] === 'warning' ? 'badge-amber' : 'badge-green') ?>" style="font-size:13px;padding:6px 12px;">
                            <?= $result['verdict'] === 'suspicious' ? 'SUSPICIOUS HEADERS — SPOOFING DETECTED' : ($result['verdict'] === 'warning' ? 'SECURITY ANOMALIES PRESENT' : 'AUTHENTICATED HEADERS') ?>
                        </span>
                    </div>
                    <div style="display:grid;gap:10px;">
                        <?php foreach ($result['findings'] as $f): ?>
                        <div style="padding:10px 14px;border-radius:6px;background:var(--bg);border-left:3px solid <?= $f['level'] === 'red' ? 'var(--red)' : ($f['level'] === 'amber' ? 'var(--amber)' : 'var(--green)') ?>;border:1px solid var(--border);border-left-width:3px;">
                            <div style="font-size:13.5px;color:var(--text);line-height:1.5;"><?= e($f['text']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="card card-pad" style="background:var(--bg);border-style:dashed;">
                    <p class="text-sm text-muted text-center" style="padding:40px 0;">Paste headers and click Audit Headers to see results here.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- TAB: APK Permissions -->
    <div class="tab-panel <?= $active_tab === 'apk' ? 'active' : '' ?>" id="tab-apk">
        <div class="grid grid-2" style="gap:20px;align-items:start;">
            <div>
                <div class="card card-pad">
                    <h3 class="mb-8" style="font-size:17px;font-weight:800;">APK Permission Risk Profiler</h3>
                    <p class="text-sm text-muted mb-16">Select runtime permissions requested by an Android APK to detect predatory loan app signatures and SMS OTP harvesters.</p>
                    <form method="POST" class="forensic-form">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="tab" value="apk">
                        <div class="perm-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:6px;max-height:220px;overflow-y:auto;padding:8px;background:var(--bg);border:1px solid var(--border);border-radius:6px;">
                            <?php foreach ($all_perms as $p): ?>
                            <label style="display:flex;align-items:center;gap:6px;font-size:12px;font-family:'JetBrains Mono',monospace;cursor:pointer;">
                                <input type="checkbox" name="perms[]" value="<?= e($p) ?>"
                                       <?= (in_array($p, $_POST['perms'] ?? []) && $active_tab === 'apk') ? 'checked' : '' ?>>
                                <?= e($p) ?>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <button type="submit" class="btn btn-primary mt-12 forensic-submit" style="width:100%;">Evaluate APK Permissions →</button>
                    </form>
                </div>
            </div>
            <div>
                <?php if ($result && $active_tab === 'apk'): ?>
                <div class="card card-pad">
                    <div class="mb-12">
                        <span class="badge <?= $result['verdict'] === 'suspicious' ? 'badge-red' : ($result['verdict'] === 'warning' ? 'badge-amber' : 'badge-green') ?>" style="font-size:13px;padding:6px 12px;">
                            <?= $result['verdict'] === 'suspicious' ? 'HIGH RISK SPYWARE PERMISSION PROFILE' : ($result['verdict'] === 'warning' ? 'MODERATE PERMISSION RISK' : 'ACCEPTABLE PERMISSION PROFILE') ?>
                        </span>
                    </div>
                    <div style="display:grid;gap:10px;">
                        <?php foreach ($result['findings'] as $f): ?>
                        <div style="padding:10px 14px;border-radius:6px;background:var(--bg);border-left:3px solid <?= $f['level'] === 'red' ? 'var(--red)' : ($f['level'] === 'amber' ? 'var(--amber)' : 'var(--green)') ?>;border:1px solid var(--border);border-left-width:3px;">
                            <div style="font-size:13.5px;color:var(--text);line-height:1.5;"><?= e($f['text']) ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="card card-pad" style="background:var(--bg);border-style:dashed;">
                    <p class="text-sm text-muted text-center" style="padding:40px 0;">Select permissions and click Evaluate to calculate risk profile.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function checkHIBPPassword() {
    var input = document.getElementById('pwd-inspector-input');
    var val = input.value.trim();
    if (!val) { alert('Please enter a password to audit.'); return; }

    var btn = document.getElementById('hibp-check-btn');
    var resArea = document.getElementById('hibp-result-area');
    btn.disabled = true;
    btn.textContent = 'Querying HaveIBeenPwned API…';
    resArea.style.display = 'block';
    resArea.innerHTML = '<div class="alert alert-info">Checking 800M+ breach records via k-Anonymity SHA-1...</div>';

    fetch('api/threat_intel.php?action=check_password', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'password=' + encodeURIComponent(val)
    })
    .then(r => r.json())
    .then(data => {
        btn.disabled = false;
        btn.textContent = 'Audit Password Against 800M+ Leaked Breaches →';

        if (data.is_pwned) {
            resArea.innerHTML = '<div class="alert alert-error" style="border-left-width:4px;">'
                + '<strong>CRITICAL COMPROMISE DETECTED:</strong> This exact password was found <strong>' + data.breach_count.toLocaleString() + ' times</strong> in real public corporate breaches! Attackers can crack this instantly via credential stuffing.'
                + '</div>';
        } else {
            resArea.innerHTML = '<div class="alert alert-success" style="border-left-width:4px;">'
                + '<strong>ZERO BREACH MATCHES:</strong> This password was NOT found in the 800M+ HaveIBeenPwned breached credentials database.'
                + '</div>';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Audit Password Against 800M+ Leaked Breaches →';
        resArea.innerHTML = '<div class="alert alert-error">Unable to contact HIBP API. Check internet connection.</div>';
    });
}

function scanIPAddress() {
    var ip = document.getElementById('ip-lookup-input').value.trim();
    runIPScan(ip);
}
function scanMyIP() {
    document.getElementById('ip-lookup-input').value = '';
    runIPScan('');
}
function runIPScan(ip) {
    var card = document.getElementById('ip-result-card');
    var body = document.getElementById('ip-res-body');
    var badge = document.getElementById('ip-res-badge');
    card.style.display = 'block';
    body.innerHTML = 'Querying IP Geo-Intelligence…';

    fetch('api/threat_intel.php?action=ip_lookup&ip=' + encodeURIComponent(ip))
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            body.innerHTML = '<div class="alert alert-error">' + (data.error || 'Failed to resolve IP.') + '</div>';
            return;
        }

        badge.className = 'badge ' + (data.is_hosting_provider ? 'badge-amber' : 'badge-green');
        badge.textContent = data.is_hosting_provider ? 'DATACENTER / VPN' : 'CONSUMER ISP';

        var html = '<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">'
            + '<div><strong>IP Address:</strong> ' + data.ip + '</div>'
            + '<div><strong>Country:</strong> ' + data.country + ' (' + data.country_code + ')</div>'
            + '<div><strong>City/Region:</strong> ' + data.city + ', ' + data.region + '</div>'
            + '<div><strong>Internet Provider:</strong> ' + data.isp + '</div>'
            + '<div><strong>AS Network:</strong> ' + data.as + '</div>'
            + '<div><strong>Organization:</strong> ' + data.org + '</div>'
            + '</div>';

        html += '<div class="alert ' + (data.is_hosting_provider ? 'alert-error' : 'alert-info') + '" style="font-size:13px;">'
            + '<strong>Perimeter Assessment:</strong> ' + data.threat_assessment
            + '</div>';

        body.innerHTML = html;
    })
    .catch(() => {
        body.innerHTML = '<div class="alert alert-error">Failed to query IP intelligence service.</div>';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var pInput = document.getElementById('pwd-inspector-input');
    if (!pInput) return;

    var commonPatterns = [
        { regex: /^[0-9]{10}$/, text: 'Entirely a 10-digit Indian mobile number — harvested in bulk telecom leaks.' },
        { regex: /(123|1234|12345|123456)$/, text: 'Ends in predictable numeric ladder sequence (123, 1234).' },
        { regex: /@(123|1234|2024|2025|2026)$/i, text: 'Uses ubiquitous Indian suffix pattern "@123" or recent year.' },
        { regex: /(india|rahul|amit|priya|rohit|kumar|sharma|patel|singh)/i, text: 'Contains prevalent Indian surname/name found in common rainbow tables.' },
        { regex: /(god|ram|krishna|shiva|ganesh|allah|jesus)/i, text: 'Contains common devotional token frequently targeted in cultural dictionary lists.' },
        { regex: /(qwerty|asdfgh|password|admin)/i, text: 'Contains generic high-frequency keyboard walk.' }
    ];

    pInput.addEventListener('input', function() {
        var val = this.value;
        var verdict = document.getElementById('pwd-verdict');
        var crackTime = document.getElementById('pwd-crack-time');
        var bar = document.getElementById('pwd-strength-bar');
        var list = document.getElementById('pwd-findings-list');

        if (!val) {
            verdict.className = 'badge badge-gray';
            verdict.textContent = 'Awaiting Password Input';
            crackTime.textContent = 'Estimated crack time: —';
            bar.style.width = '0%';
            list.innerHTML = '<div class="alert alert-info" style="font-size:12.5px;">Zero-knowledge check: Checked via HIBP k-Anonymity protocol.</div>';
            return;
        }

        var score = 0;
        var findings = [];

        if (val.length >= 8) score += 20;
        if (val.length >= 12) score += 25;
        if (val.length >= 16) score += 15;
        if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score += 15;
        if (/[0-9]/.test(val)) score += 10;
        if (/[^a-zA-Z0-9]/.test(val)) score += 15;

        commonPatterns.forEach(function(p) {
            if (p.regex.test(val)) {
                score = Math.max(10, score - 25);
                findings.push({ level: 'red', text: p.text });
            }
        });

        if (val.length < 8) {
            findings.push({ level: 'red', text: 'Length is less than 8 characters — vulnerable to instant brute force.' });
        } else {
            findings.push({ level: 'green', text: 'Meets minimum recommended length of 8+ characters.' });
        }

        var crackEst = 'Less than 1 second';
        var badgeClass = 'badge-red';
        var verdictText = 'CRITICAL WEAKNESS';
        var barColor = 'var(--red)';

        if (score >= 80) {
            badgeClass = 'badge-green';
            verdictText = 'STRONG COMPLEXITY';
            barColor = 'var(--green)';
            crackEst = 'Several centuries with consumer hardware';
        } else if (score >= 50) {
            badgeClass = 'badge-amber';
            verdictText = 'MODERATE RESILIENCE';
            barColor = 'var(--amber)';
            crackEst = 'A few hours to several days';
        }

        verdict.className = 'badge ' + badgeClass;
        verdict.textContent = verdictText;
        crackTime.textContent = 'Estimated crack time: ' + crackEst + ' (Score: ' + score + '/100)';
        bar.style.width = score + '%';
        bar.style.background = barColor;

        var html = '';
        findings.forEach(function(f) {
            html += '<div style="padding:8px 12px;border-radius:6px;background:var(--bg);border-left:3px solid ' + (f.level==='red'?'var(--red)':(f.level==='amber'?'var(--amber)':'var(--green)')) + ';border:1px solid var(--border);border-left-width:3px;font-size:13px;">'
                 + f.text + '</div>';
        });
        list.innerHTML = html;
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
