<?php
// Certificate print template — used by certificates.php via ?print=key
// Requires: $print_def (cert definition array), $print_cert (DB row), $user (current user array)
$cert_date = isset($print_cert['issue_date']) ? date('d F Y', strtotime($print_cert['issue_date'])) : date('d F Y');
$bg   = $print_def['color_bg'] ?? '#eef2ff';
$clr  = $print_def['color']    ?? '#4f46e5';
?>
<div class="cert-printable" style="
    background: <?= $bg ?>;
    border: 3px solid <?= $clr ?>;
    border-radius: 20px;
    padding: 48px 56px;
    text-align: center;
    position: relative;
    overflow: hidden;
    font-family: 'Plus Jakarta Sans', sans-serif;
">
    <!-- Corner decorations -->
    <div style="position:absolute;top:-40px;left:-40px;width:120px;height:120px;background:<?= $clr ?>;opacity:.06;border-radius:50%;"></div>
    <div style="position:absolute;bottom:-40px;right:-40px;width:160px;height:160px;background:<?= $clr ?>;opacity:.06;border-radius:50%;"></div>
    <div style="position:absolute;top:-20px;right:-20px;width:80px;height:80px;background:<?= $clr ?>;opacity:.04;border-radius:50%;"></div>

    <!-- Header -->
    <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:28px;position:relative;">
        <div style="width:34px;height:34px;border-radius:9px;background:linear-gradient(135deg,<?= $clr ?>,<?= $clr ?>aa);display:flex;align-items:center;justify-content:center;color:white;font-size:16px;">◆</div>
        <div style="font-size:18px;font-weight:800;color:<?= $clr ?>;">SafeSphere</div>
        <div style="background:<?= $clr ?>;color:white;font-size:10px;font-weight:700;padding:3px 10px;border-radius:99px;letter-spacing:.05em;text-transform:uppercase;">Verified</div>
    </div>

    <!-- Title -->
    <div style="font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:<?= $clr ?>;opacity:.7;margin-bottom:12px;position:relative;">
        Certificate of Completion
    </div>

    <!-- Medal -->
    <div style="font-size:64px;margin:8px 0 16px;position:relative;"><?= $print_def['medal'] ?></div>

    <!-- Cert Title -->
    <div style="font-size:32px;font-weight:800;color:<?= $clr ?>;letter-spacing:-0.02em;margin-bottom:16px;position:relative;">
        <?= e($print_def['title']) ?>
    </div>

    <!-- Awarded to -->
    <div style="font-size:14px;color:#64748b;margin-bottom:8px;position:relative;">This certifies that</div>
    <div style="font-size:26px;font-weight:800;color:#0f172a;margin-bottom:8px;position:relative;">
        <?= e($user['name']) ?>
    </div>
    <div style="font-size:13px;color:#64748b;margin-bottom:24px;position:relative;"><?= e($user['email']) ?></div>

    <!-- Description -->
    <div style="font-size:14.5px;color:#475569;line-height:1.7;max-width:520px;margin:0 auto 28px;position:relative;">
        <?= e($print_def['desc']) ?>
    </div>

    <!-- Skills -->
    <?php if (!empty($print_def['skills'])): ?>
    <div style="display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin-bottom:28px;position:relative;">
        <?php foreach ($print_def['skills'] as $sk): ?>
        <span style="background:<?= $clr ?>22;color:<?= $clr ?>;border:1px solid <?= $clr ?>44;border-radius:99px;padding:4px 12px;font-size:12px;font-weight:700;">
            <?= e($sk) ?>
        </span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Divider -->
    <div style="border-top:2px solid <?= $clr ?>33;margin:0 auto 24px;max-width:400px;position:relative;"></div>

    <!-- Footer row -->
    <div style="display:flex;justify-content:space-between;align-items:flex-end;position:relative;flex-wrap:wrap;gap:16px;">
        <div style="text-align:left;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:4px;">Issue Date</div>
            <div style="font-size:15px;font-weight:700;color:#0f172a;"><?= $cert_date ?></div>
        </div>
        <div style="text-align:center;">
            <div style="font-size:28px;font-weight:800;color:<?= $clr ?>;"><?= e($user['cyber_score'] ?? '—') ?></div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;">Cyber Score</div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#94a3b8;margin-bottom:4px;">Credential ID</div>
            <div style="font-family:'JetBrains Mono',monospace;font-size:14px;font-weight:700;color:#0f172a;"><?= e($print_cert['credential_id']) ?></div>
        </div>
    </div>
</div>
