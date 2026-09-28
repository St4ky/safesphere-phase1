<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
require_login();
$user = current_user();

$all_scenarios = require __DIR__ . '/../includes/data/socialeng_data.php';
$by_id = [];
foreach ($all_scenarios as $s) { $by_id[$s['id']] = $s; }

// Handle AJAX node recording
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ajax'])) {
    header('Content-Type: application/json');
    // We record the final outcome only (terminal nodes) — tracked by scenario completion
    $scenario_id = $_POST['scenario_id'] ?? '';
    $is_correct  = !empty($_POST['is_correct']) && $_POST['is_correct'] === '1';
    $points      = (int)($_POST['points'] ?? 0);
    // Only record at terminal nodes (handled client-side tracking)
    echo json_encode(['ok' => 1]);
    exit;
}

// Handle final scenario outcome submission (form POST from JS)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['scenario_id']) && !empty($_POST['outcome'])) {
    if (verify_csrf($_POST['csrf_token'] ?? '')) {
        $sid     = $_POST['scenario_id'];
        $outcome = $_POST['outcome']; // 'pass' or 'fail'
        if (isset($by_id[$sid])) {
            $correct = ($outcome === 'pass');
            $points  = $correct ? 10 : -5;
            record_attempt($user['id'], 'socialeng', $sid, $outcome, $correct, $points);
            $user = current_user(true);
        }
    }
    // Redirect to same page to avoid re-submit
    header('Location: socialeng.php?id=' . urlencode($_POST['scenario_id'] ?? ''));
    exit;
}

// Selected scenario
$selected_id = $_GET['id'] ?? $all_scenarios[0]['id'];
if (!isset($by_id[$selected_id])) $selected_id = $all_scenarios[0]['id'];
$selected         = $by_id[$selected_id];
$selected_attempt = get_attempt($user['id'], 'socialeng', $selected_id);
$stats            = get_module_stats($user['id'], 'socialeng');

$page_title    = 'Module 3: Social Engineering';
$page_subtitle = 'Navigate branching scam conversations — your choices change the outcome';
$active        = 'socialeng';
$base          = '../';
include __DIR__ . '/../includes/header.php';
?>

<meta name="csrf_meta" content="<?= e(csrf_token()) ?>">

<div class="flex-between mb-16">
    <div class="text-sm text-muted"><?= $stats['attempted'] ?> of <?= count($all_scenarios) ?> scenarios completed &middot; <?= $stats['correct'] ?> passed</div>
    <div class="progress-track" style="width:200px;">
        <div class="progress-fill" style="width:<?= count($all_scenarios) ? round($stats['attempted']/count($all_scenarios)*100) : 0 ?>%"></div>
    </div>
</div>

<div class="se-layout">

    <!-- LEFT: Scenario List -->
    <div class="se-scenario-list">
        <?php foreach ($all_scenarios as $s):
            $attempt = get_attempt($user['id'], 'socialeng', $s['id']);
        ?>
        <a href="?id=<?= e($s['id']) ?>" class="se-scenario-item <?= $s['id'] === $selected_id ? 'active' : '' ?>" style="text-decoration:none;display:block;">
            <div class="se-scenario-icon"><?= $s['icon'] ?></div>
            <div class="se-scenario-title"><?= e($s['title']) ?></div>
            <div class="se-scenario-desc"><?= e($s['description']) ?></div>
            <div class="se-scenario-outcome">
                <?php if ($attempt): ?>
                    <span class="badge <?= $attempt['is_correct'] ? 'badge-green' : 'badge-red' ?>" style="font-size:10px;">
                        <?= $attempt['is_correct'] ? '✓ Passed' : '✕ Failed' ?>
                    </span>
                <?php else: ?>
                    <span class="badge badge-gray" style="font-size:10px;">Not started</span>
                <?php endif; ?>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- RIGHT: Chat Window -->
    <div>
        <?php if ($selected_attempt): ?>
        <!-- Scenario already completed — show result + replay option -->
        <div class="card card-pad">
            <div class="flex gap-8 mb-16" style="align-items:center;">
                <span class="badge <?= $selected_attempt['is_correct'] ? 'badge-green' : 'badge-red' ?>" style="font-size:14px;padding:6px 14px;">
                    <?= $selected_attempt['is_correct'] ? '🛡️ You handled it correctly!' : '⚠️ You fell for the scam' ?>
                </span>
                <span class="badge <?= $selected_attempt['points_awarded'] >= 0 ? 'badge-green' : 'badge-red' ?>">
                    <?= ($selected_attempt['points_awarded'] >= 0 ? '+' : '') . (int)$selected_attempt['points_awarded'] ?> pts
                </span>
            </div>
            <h3 class="mb-8"><?= e($selected['icon']) ?> <?= e($selected['title']) ?></h3>
            <p class="text-sm text-muted mb-16"><?= e($selected['description']) ?></p>
            <div class="alert alert-info">
                <strong>Scenario completed.</strong> You can replay by clicking "Replay" — but your score won't change (recorded once).
            </div>
            <div class="flex gap-8 mt-16">
                <a href="?id=<?= e($selected_id) ?>&replay=1" class="btn btn-primary">↩ Replay Scenario</a>
                <?php
                $next_id = null;
                foreach ($all_scenarios as $i => $sc) {
                    if ($sc['id'] === $selected_id && isset($all_scenarios[$i+1])) {
                        $next_id = $all_scenarios[$i+1]['id'];
                    }
                }
                ?>
                <?php if ($next_id): ?>
                <a href="?id=<?= e($next_id) ?>" class="btn btn-outline">Next Scenario →</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php
        $show_chat = !$selected_attempt || !empty($_GET['replay']);
        ?>

        <?php if ($show_chat): ?>
        <div class="chat-window">
            <div class="chat-header">
                <div class="chat-persona-avatar" style="background:<?= e($selected['persona']['color']) ?>;">
                    <?= e($selected['persona']['avatar']) ?>
                </div>
                <div>
                    <div class="chat-persona-name"><?= e($selected['persona']['name']) ?></div>
                    <div class="chat-persona-status">● Online</div>
                </div>
                <div style="margin-left:auto;" class="text-xs text-muted">Simulation — safe environment</div>
            </div>

            <div class="chat-messages" id="chat-messages"></div>

            <div class="chat-choices" id="chat-choices"
                 data-scenario="<?= e($selected_id) ?>"
                 data-se-id="<?= e($selected_id) ?>">
                <div class="text-xs text-muted">Chat loading…</div>
            </div>
        </div>

        <!-- Hidden outcome form — submitted via JS at terminal node -->
        <form id="outcome-form" method="POST" style="display:none;">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="scenario_id" value="<?= e($selected_id) ?>">
            <input type="hidden" name="outcome" id="outcome-value" value="">
        </form>

        <script>
        // Inject scenario data and flag that dedicated engine is active
        window._SE_CUSTOM_ENGINE = true;
        window.SE_DATA = {
            nodes: <?= json_encode($selected['nodes'], JSON_UNESCAPED_UNICODE) ?>,
            currentNode: 'start'
        };

        // Dedicated branching chat engine with realistic delays and automatic outcome recording
        document.addEventListener('DOMContentLoaded', function() {
            const chatMessages = document.getElementById('chat-messages');
            const chatChoices  = document.getElementById('chat-choices');
            if (!chatMessages || !chatChoices || !window.SE_DATA) return;

            const nodes = window.SE_DATA.nodes;
            let submitted = false;

            function appendBubble(text, cls) {
                const div = document.createElement('div');
                div.className = 'chat-bubble ' + cls;
                div.textContent = text;
                chatMessages.appendChild(div);
                chatMessages.scrollTop = chatMessages.scrollHeight;
                return div;
            }

            function renderChoices(nodeId) {
                const node = nodes[nodeId];
                if (!node) return;
                chatChoices.innerHTML = '';

                const choices = node.choices || [];
                if (choices.length === 0) {
                    // Terminal
                    const outcome = node.outcome || 'pass';
                    if (!submitted) {
                        submitted = true;
                        const outcomeVal = document.getElementById('outcome-value');
                        if (outcomeVal) {
                            outcomeVal.value = outcome;
                            setTimeout(function() {
                                document.getElementById('outcome-form').submit();
                            }, 4500);
                        }
                    }
                    const info = document.createElement('div');
                    info.className = 'text-xs text-muted';
                    info.style.textAlign = 'center';
                    info.style.padding = '8px';
                    info.textContent = 'Submitting result…';
                    chatChoices.appendChild(info);
                    return;
                }

                const label = document.createElement('div');
                label.className = 'chat-choices-label';
                label.textContent = 'Your response:';
                chatChoices.appendChild(label);

                choices.forEach(function(choice) {
                    const btn = document.createElement('button');
                    btn.className = 'choice-btn';
                    btn.textContent = choice.label;
                    btn.addEventListener('click', function() {
                        chatChoices.querySelectorAll('.choice-btn').forEach(function(b) { b.disabled = true; b.style.opacity = '0.5'; });
                        appendBubble(choice.label, 'bubble-user');
                        setTimeout(function() { showNode(choice.next); }, 500);
                    });
                    chatChoices.appendChild(btn);
                });
            }

            function showNode(nodeId) {
                const node = nodes[nodeId];
                if (!node) return;
                chatChoices.innerHTML = '';
                let delay = 0;
                const msgs = node.messages || [];
                msgs.forEach(function(msg, i) {
                    delay += i === 0 ? 500 : 1400;
                    setTimeout(function() {
                        // Determine bubble class: outcome nodes get color-coded
                        let cls = 'bubble-attacker';
                        if (node.outcome === 'pass' && i === 0) cls = 'bubble-outcome-pass';
                        if (node.outcome === 'fail' && i === 0) cls = 'bubble-outcome-fail';
                        if (node.outcome) cls = node.outcome === 'pass' ? 'bubble-outcome-pass' : 'bubble-outcome-fail';
                        appendBubble(msg, cls);
                        if (i === msgs.length - 1) {
                            setTimeout(function() { renderChoices(nodeId); }, 700);
                        }
                    }, delay);
                });
            }

            showNode('start');
        });
        </script>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
