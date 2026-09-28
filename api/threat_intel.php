<?php
/**
 * SafeSphere — Live Threat Intelligence & Security API
 * Provides live domain DNS/SPF checking via Cloudflare DoH,
 * breach checking via HaveIBeenPwned k-Anonymity API,
 * real-time IP/Geo-intelligence, and Indian cyber fraud heuristic analysis.
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if (!function_exists('safe_curl_get')) {
    function safe_curl_get(string $url, array $headers = [], int $timeout = 4): ?string {
        if (!function_exists('curl_init')) {
            $opts = [
                'http' => [
                    'method' => 'GET',
                    'header' => implode("\r\n", array_merge(['User-Agent: SafeSphere-CyberIntel/2.0'], $headers)),
                    'timeout' => $timeout,
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ];
            $ctx = stream_context_create($opts);
            $res = @file_get_contents($url, false, $ctx);
            return $res !== false ? $res : null;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'SafeSphere-CyberIntel/2.0 (Security Scanner)');
        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $output = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code >= 200 && $code < 400 && $output !== false) ? $output : null;
    }
}

if (!function_exists('execute_url_scan')) {
    function execute_url_scan(string $rawUrl) {
        if (empty($rawUrl)) {
            echo json_encode(['success' => false, 'error' => 'Please provide a URL to scan.']);
            exit;
        }

        if (!preg_match('~^https?://~i', $rawUrl)) {
            $testUrl = 'https://' . $rawUrl;
        } else {
            $testUrl = $rawUrl;
        }

        $parsed = parse_url($testUrl);
        $host = strtolower($parsed['host'] ?? '');

        if (empty($host)) {
            echo json_encode(['success' => false, 'error' => 'Invalid domain or URL format.']);
            exit;
        }

        $findings = [];
        $riskScore = 0;

        // 1. IP Hostname Check
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $findings[] = [
                'type' => 'danger',
                'title' => 'Direct IP Address Used in Hostname',
                'desc' => "The link points directly to an IP ($host) rather than a verified domain name. Legitimate services virtually never use raw IP URLs."
            ];
            $riskScore += 45;
        }

        // 2. High-Risk TLD check
        $suspiciousTlds = ['.xyz', '.top', '.tk', '.ml', '.ga', '.cf', '.gq', '.icu', '.club', '.buzz', '.work', '.click', '.live'];
        foreach ($suspiciousTlds as $tld) {
            if (substr($host, -strlen($tld)) === $tld) {
                $findings[] = [
                    'type' => 'warning',
                    'title' => 'High-Abuse Top-Level Domain (TLD)',
                    'desc' => "The domain ends with $tld, which is disproportionately favored by disposable phishing campaigns due to low-cost bulk registrations."
                ];
                $riskScore += 25;
                break;
            }
        }

        // 3. Indian Brand Impersonation / Typosquatting heuristics
        $targetedBrands = [
            'sbi' => ['sbi', 'onlinesbi', 'statebank'],
            'hdfc' => ['hdfc', 'hdfcbank'],
            'icici' => ['icici', 'icicibank'],
            'paytm' => ['paytm', 'paytmbank'],
            'phonepe' => ['phonepe'],
            'gpay' => ['googlepay', 'gpay'],
            'irctc' => ['irctc'],
            'incometax' => ['incometax', 'incometaxindia', 'epfo'],
            'uidai' => ['uidai', 'aadhaar', 'm-aadhaar'],
            'electricity' => ['bses', 'mahavitaran', 'tneb', 'bijli', 'powercorp'],
            'telecom' => ['jio', 'airtel', 'vodafone', 'vi-sim']
        ];

        $suspiciousKeywords = ['kyc', 'update', 'verify', 'support', 'helpdesk', 'claim', 'refund', 'login', 'reward', 'blocked', 'suspend', 'apk', 'bonus', 'cashback'];

        foreach ($targetedBrands as $brandKey => $keywords) {
            foreach ($keywords as $kw) {
                if (strpos($host, $kw) !== false) {
                    $officialDomains = [
                        'sbi.co.in', 'onlinesbi.sbi', 'onlinesbi.com',
                        'hdfcbank.com', 'icicibank.com', 'paytm.com', 'phonepe.com',
                        'irctc.co.in', 'incometax.gov.in', 'uidai.gov.in',
                        'jio.com', 'airtel.in', 'myvi.in'
                    ];

                    $isOfficial = false;
                    foreach ($officialDomains as $official) {
                        if ($host === $official || substr($host, -strlen('.' . $official)) === '.' . $official) {
                            $isOfficial = true;
                            break;
                        }
                    }

                    if (!$isOfficial) {
                        $findings[] = [
                            'type' => 'danger',
                            'title' => 'Brand Impersonation Detected (' . strtoupper($brandKey) . ')',
                            'desc' => "The domain contains \"$kw\" but is NOT an official domain of $brandKey. This matches spoofing templates designed to steal login credentials and OTPs."
                        ];
                        $riskScore += 50;
                    }
                    break 2;
                }
            }
        }

        foreach ($suspiciousKeywords as $skw) {
            if (strpos($host, $skw) !== false && !in_array($host, ['login.gov', 'accounts.google.com', 'login.microsoftonline.com'])) {
                $findings[] = [
                    'type' => 'warning',
                    'title' => 'Suspicious Keyword Pattern',
                    'desc' => "Domain contains credential-harvesting trigger \"$skw\"."
                ];
                $riskScore += 20;
                break;
            }
        }

        // 4. Live DNS Lookup via Cloudflare DNS over HTTPS API
        $dohUrl = 'https://cloudflare-dns.com/dns-query?name=' . urlencode($host) . '&type=A';
        $dohResponse = safe_curl_get($dohUrl, ['Accept: application/dns-json']);
        $dnsData = $dohResponse ? json_decode($dohResponse, true) : null;

        $resolvedIps = [];
        $domainExists = false;

        if ($dnsData && isset($dnsData['Answer'])) {
            $domainExists = true;
            foreach ($dnsData['Answer'] as $ans) {
                if (($ans['type'] ?? 0) === 1 && isset($ans['data'])) {
                    $resolvedIps[] = $ans['data'];
                }
            }
        }

        $mxUrl = 'https://cloudflare-dns.com/dns-query?name=' . urlencode($host) . '&type=MX';
        $mxResponse = safe_curl_get($mxUrl, ['Accept: application/dns-json']);
        $mxData = $mxResponse ? json_decode($mxResponse, true) : null;
        $hasMx = !empty($mxData['Answer']);

        $txtUrl = 'https://cloudflare-dns.com/dns-query?name=' . urlencode($host) . '&type=TXT';
        $txtResponse = safe_curl_get($txtUrl, ['Accept: application/dns-json']);
        $txtData = $txtResponse ? json_decode($txtResponse, true) : null;
        $hasSpf = false;
        if ($txtData && !empty($txtData['Answer'])) {
            foreach ($txtData['Answer'] as $txt) {
                if (stripos($txt['data'] ?? '', 'v=spf1') !== false) {
                    $hasSpf = true;
                    break;
                }
            }
        }

        if (!$domainExists && empty($resolvedIps) && !filter_var($host, FILTER_VALIDATE_IP)) {
            $findings[] = [
                'type' => 'warning',
                'title' => 'Unresolvable / Dead Domain',
                'desc' => "Live Cloudflare DoH lookup returned NXDOMAIN (no active A record). Domain might be taken down or newly crafted."
            ];
            $riskScore += 15;
        } else if (!empty($resolvedIps)) {
            $findings[] = [
                'type' => 'info',
                'title' => 'Live DNS Resolution (Cloudflare DoH)',
                'desc' => "Domain resolves to: " . implode(', ', array_slice($resolvedIps, 0, 3)) . ($hasMx ? ' | Valid MX Mail Exchange' : ' | No MX records') . ($hasSpf ? ' | SPF configured' : '')
            ];
        }

        $riskScore = min(100, $riskScore);
        if (empty($findings) || $riskScore === 0) {
            $verdict = 'CLEAN';
            $verdictClass = 'green';
            $findings[] = [
                'type' => 'success',
                'title' => 'No High-Risk Patterns Detected',
                'desc' => 'Domain structure appears standard. Always verify URLs directly in your browser address bar before providing credentials.'
            ];
        } elseif ($riskScore >= 45) {
            $verdict = 'DANGEROUS';
            $verdictClass = 'red';
        } else {
            $verdict = 'SUSPICIOUS';
            $verdictClass = 'amber';
        }

        echo json_encode([
            'success' => true,
            'target' => $rawUrl,
            'host' => $host,
            'risk_score' => $riskScore,
            'verdict' => $verdict,
            'verdict_class' => $verdictClass,
            'domain_exists' => $domainExists,
            'resolved_ips' => $resolvedIps,
            'has_mx' => $hasMx,
            'has_spf' => $hasSpf,
            'findings' => $findings,
            'checked_at' => date('Y-m-d H:i:s')
        ]);
        exit;
    }
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'scan_url':
        $rawUrl = trim($_GET['url'] ?? $_POST['url'] ?? '');
        execute_url_scan($rawUrl);
        break;

    case 'check_password':
        $password = $_POST['password'] ?? $_GET['password'] ?? '';
        if (empty($password)) {
            echo json_encode(['success' => false, 'error' => 'No password provided for breach check.']);
            exit;
        }

        $sha1 = strtoupper(sha1($password));
        $prefix = substr($sha1, 0, 5);
        $suffix = substr($sha1, 5);

        $hibpUrl = 'https://api.pwnedpasswords.com/range/' . $prefix;
        $hibpResponse = safe_curl_get($hibpUrl, ['Add-Padding: true']);

        $breachCount = 0;
        if ($hibpResponse) {
            $lines = explode("\n", str_replace("\r", "", $hibpResponse));
            foreach ($lines as $line) {
                $parts = explode(':', trim($line));
                if (count($parts) === 2 && strtoupper($parts[0]) === $suffix) {
                    $breachCount = (int)$parts[1];
                    break;
                }
            }
        }

        $isPwned = $breachCount > 0;
        $verdict = $isPwned ? 'COMPROMISED' : 'SAFE';
        $verdictClass = $isPwned ? 'red' : 'green';

        echo json_encode([
            'success' => true,
            'is_pwned' => $isPwned,
            'breach_count' => $breachCount,
            'sha1_prefix' => $prefix . '...',
            'verdict' => $verdict,
            'verdict_class' => $verdictClass,
            'security_notice' => 'Zero-knowledge check: Checked via HIBP k-Anonymity protocol. Only 5 characters of SHA-1 hash were transmitted.'
        ]);
        exit;

    case 'ip_lookup':
        $rawIp = trim($_GET['ip'] ?? $_POST['ip'] ?? '');
        $isMyNetwork = empty($rawIp) || in_array(strtolower($rawIp), ['my', 'self', 'me', 'local'], true);

        // 1. Check if user explicitly asked for private / loopback IP
        $isPrivate = false;
        $isLoopback = false;
        if (!$isMyNetwork) {
            if ($rawIp === '127.0.0.1' || $rawIp === '::1' || strtolower($rawIp) === 'localhost') {
                $isLoopback = true;
            } elseif (filter_var($rawIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false && filter_var($rawIp, FILTER_VALIDATE_IP)) {
                $isPrivate = true;
            }
        }

        // If user explicitly queried a private or loopback IP, return a specialized LAN audit assessment
        if ($isLoopback || $isPrivate) {
            echo json_encode([
                'success' => true,
                'ip' => $rawIp,
                'country' => 'Local Subnet',
                'country_code' => 'LAN',
                'city' => 'Internal Network',
                'region' => 'Private Address Space',
                'isp' => 'Internal LAN / Router Subnet',
                'org' => 'Private Network (RFC 1918 Gateway)',
                'as' => 'Unrouted Private Space',
                'is_hosting_provider' => false,
                'is_private_lan' => true,
                'threat_assessment' => 'Internal LAN Address: This IP operates exclusively inside your local Wi-Fi or office network behind NAT (Network Address Translation). It cannot be probed directly from the public internet. Ensure your router admin password (e.g. at 192.168.1.1) is changed from the factory default.'
            ]);
            exit;
        }

        // 2. Lookup public IP or detect caller public IP without hardcoded fallbacks
        $apiUrl = 'http://ip-api.com/json/';
        if (!$isMyNetwork && filter_var($rawIp, FILTER_VALIDATE_IP)) {
            $apiUrl .= urlencode($rawIp);
        }
        $apiUrl .= '?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,query';

        $res = safe_curl_get($apiUrl);
        $data = $res ? json_decode($res, true) : null;

        // Resilient fallback: if empty/local query returned an issue or if ip-api needed fallback
        if ((!$data || ($data['status'] ?? '') !== 'success') && $isMyNetwork) {
            $publicIp = safe_curl_get('https://api.ipify.org');
            if ($publicIp && filter_var(trim($publicIp), FILTER_VALIDATE_IP)) {
                $res2 = safe_curl_get('http://ip-api.com/json/' . trim($publicIp) . '?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,query');
                $data = $res2 ? json_decode($res2, true) : null;
            }
        }

        if ($data && ($data['status'] ?? '') === 'success') {
            $isHosting = false;
            $asDesc = strtolower(($data['as'] ?? '') . ' ' . ($data['org'] ?? ''));
            $hostingKeywords = ['amazon', 'aws', 'cloudflare', 'digitalocean', 'linode', 'ovh', 'hetzner', 'google llc', 'microsoft', 'azure', 'vultr', 'alibaba', 'fastly', 'leaseweb'];
            foreach ($hostingKeywords as $hkw) {
                if (strpos($asDesc, $hkw) !== false) {
                    $isHosting = true;
                    break;
                }
            }

            echo json_encode([
                'success' => true,
                'ip' => $data['query'] ?? $rawIp,
                'country' => $data['country'] ?? 'Unknown',
                'country_code' => $data['countryCode'] ?? '',
                'city' => $data['city'] ?? 'Unknown',
                'region' => $data['regionName'] ?? '',
                'isp' => $data['isp'] ?? 'Unknown',
                'org' => $data['org'] ?? '',
                'as' => $data['as'] ?? '',
                'is_hosting_provider' => $isHosting,
                'is_private_lan' => false,
                'threat_assessment' => $isHosting
                    ? 'Datacenter / Hosting / VPN relay detected. If you are not intentionally using a VPN, your traffic may be routed through an intermediate proxy.'
                    : 'Consumer Broadband / Cellular ISP: Standard residential allocation. Ensure your Wi-Fi uses WPA2/WPA3 and router firmware is up to date.'
            ]);
            exit;
        }

        echo json_encode([
            'success' => false,
            'error' => 'Unable to resolve network intelligence at this time. Please check your internet connection.',
            'fallback_ip' => $rawIp
        ]);
        exit;

    case 'quick_scan':
        $input = trim($_POST['input'] ?? $_GET['input'] ?? '');
        if (empty($input)) {
            echo json_encode(['success' => false, 'error' => 'Please paste a link, SMS message, or UPI handle.']);
            exit;
        }

        $isUrl = preg_match('~^https?://|[a-z0-9-]+\.(com|in|org|net|xyz|top|site|app|co\.in|gov\.in)~i', $input);
        $isUpi = preg_match('/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/', $input);

        if ($isUrl) {
            execute_url_scan($input);
            exit;
        }

        if ($isUpi) {
            $handle = strtolower($input);
            list($userPart, $vpaProvider) = explode('@', $handle);

            $legitBanks = ['okhdfcbank', 'okaxis', 'oksbi', 'okicici', 'paytm', 'ybl', 'ibl', 'axl', 'apl', 'upi', 'barodampay', 'cnrb'];
            $suspiciousTokens = ['refund', 'customercare', 'support', 'helpdesk', 'reward', 'cashback', 'kyc', 'lottery', 'winner', 'loan'];

            $isSuspicious = false;
            $detected = [];
            foreach ($suspiciousTokens as $st) {
                if (strpos($userPart, $st) !== false) {
                    $isSuspicious = true;
                    $detected[] = "Keyword \"$st\" matches fake customer care / refund scam patterns.";
                }
            }

            $riskScore = $isSuspicious ? 80 : 15;
            $verdict = $isSuspicious ? 'HIGH_RISK_UPI' : 'STANDARD_VPA';

            echo json_encode([
                'success' => true,
                'type' => 'upi',
                'target' => $handle,
                'vpa_bank' => '@' . $vpaProvider,
                'risk_score' => $riskScore,
                'verdict' => $verdict,
                'verdict_class' => $isSuspicious ? 'red' : 'green',
                'findings' => [
                    [
                        'type' => $isSuspicious ? 'danger' : 'info',
                        'title' => $isSuspicious ? 'High-Risk UPI Handle Pattern' : 'Valid Format UPI Virtual Address',
                        'desc' => $isSuspicious
                            ? implode(' ', $detected) . ' Remember: You NEVER need to enter your UPI PIN or accept a collect request to RECEIVE money!'
                            : 'Valid UPI format detected. Remember that genuine banks and companies never use personal Gmail/standard UPI accounts for official settlements.'
                    ]
                ]
            ]);
            exit;
        }

        // Fraud Message / SMS text heuristic engine
        $text = strtolower($input);
        $score = 0;
        $reasons = [];

        $patterns = [
            'electricity_scam' => [
                'regex' => '/(electricity|power|bijli).*?(disconnect|cut off|bill|9:30|8:30|officer)/i',
                'title' => 'Electricity Disconnection Scam Pattern',
                'desc' => 'Matches the rampant Indian power disconnection fraud. State DISCOMs never send personal mobile numbers or threaten immediate disconnection within hours.',
                'weight' => 40
            ],
            'digital_arrest' => [
                'regex' => '/(cbi|customs|narcol|police|arrest|ed|enforcement|cyber cell|warrant|fedex|parcel)/i',
                'title' => 'Digital Arrest / Fake Law Enforcement Intimidation',
                'desc' => 'Law enforcement in India never initiates interrogations via Skype, WhatsApp video, or phone calls demanding money transfers to clear charges.',
                'weight' => 50
            ],
            'part_time_job' => [
                'regex' => '/(telegram|part.?time|daily earnings|per day|like youtube|review google|task|₹[\d,]+|rs\.?\s*[\d,]+)/i',
                'title' => 'Telegram Part-Time Task Fraud',
                'desc' => 'Classic task scam promising ₹2,000–₹10,000/day for liking videos or Google reviews, escalating to frozen funds in fake investment portals.',
                'weight' => 35
            ],
            'apk_loan' => [
                'regex' => '/(download apk|install apk|\.apk|instant loan|instant cash|pan card only)/i',
                'title' => 'Malicious APK / Predatory Loan App',
                'desc' => 'Direct APK downloads bypass Google Play Protect to harvest contacts, photos, and SMS messages for extortion.',
                'weight' => 45
            ],
            'bank_urgency' => [
                'regex' => '/(account.*?(block|suspend|deactivate|freeze)|kyc.*?(update|expire|mandatory)|pan.*?(link|update))/i',
                'title' => 'Urgent Banking Panic Trigger',
                'desc' => 'Manufactures artificial urgency to force you to act before thinking critically.',
                'weight' => 35
            ]
        ];

        foreach ($patterns as $key => $p) {
            if (preg_match($p['regex'], $input)) {
                $score += $p['weight'];
                $reasons[] = [
                    'type' => 'danger',
                    'title' => $p['title'],
                    'desc' => $p['desc']
                ];
            }
        }

        $score = min(100, $score);
        if ($score === 0) {
            $reasons[] = [
                'type' => 'info',
                'title' => 'No Documented Indian Fraud Signatures Found',
                'desc' => 'Text does not match common automated phishing templates. Always remain vigilant with unsolicited messages.'
            ];
        }

        echo json_encode([
            'success' => true,
            'type' => 'message',
            'target' => mb_strimwidth($input, 0, 80, '...'),
            'risk_score' => $score,
            'verdict' => $score >= 40 ? 'HIGH_RISK_FRAUD' : ($score > 0 ? 'SUSPICIOUS' : 'LOW_RISK'),
            'verdict_class' => $score >= 40 ? 'red' : ($score > 0 ? 'amber' : 'green'),
            'findings' => $reasons
        ]);
        exit;

    default:
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action parameter.',
            'available_actions' => ['scan_url', 'check_password', 'ip_lookup', 'quick_scan']
        ]);
        exit;
}
