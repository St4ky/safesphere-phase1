<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';
require_login();
$user = current_user();

$db = get_db();
// 1. Top 20 all time
$stmt = $db->query("SELECT id, name, cyber_score, streak_count, role, created_at FROM users ORDER BY cyber_score DESC LIMIT 20");
$all_time = $stmt->fetchAll();

// 2. Weekly leaders
$stmt2 = $db->query("
    SELECT u.id, u.name, u.cyber_score, u.streak_count, COUNT(a.id) as week_attempts 
    FROM users u 
    LEFT JOIN attempts a ON a.user_id = u.id AND a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
    WHERE u.cyber_score > 0 
    GROUP BY u.id 
    ORDER BY week_attempts DESC, u.cyber_score DESC 
    LIMIT 20
");
$this_week = $stmt2->fetchAll();

// 3. User global rank
$stmt3 = $db->prepare("SELECT COUNT(*) + 1 as rank FROM users WHERE cyber_score > (SELECT cyber_score FROM users WHERE id = ?)");
$stmt3->execute([$user['id']]);
$user_rank = $stmt3->fetchColumn();

$page_title = 'Leaderboard';
$page_subtitle = 'Top cybersecurity defenders on SafeSphere';
$active = 'leaderboard';
$base = '';
include __DIR__ . '/includes/header.php';
?>
<div class="grid grid-3 mb-24">
    <div class="stat-card card card-pad">
        <div class="stat-card-label">Your Rank</div>
        <div class="stat-card-number">#<?= $user_rank ?> globally</div>
    </div>
    <div class="stat-card card card-pad">
        <div class="stat-card-label">Your Score</div>
        <div class="stat-card-number"><?= e($user['cyber_score']) ?></div>
    </div>
    <div class="stat-card card card-pad">
        <div class="stat-card-label">Your Streak</div>
        <div class="stat-card-number"><?= e($user['streak_count']) ?> days</div>
    </div>
</div>

<?php if ($user_rank <= 3 && $user['cyber_score'] > 40): ?>
<div class="alert alert-success mb-24">
    You're on the podium! 🎉 Keep up the great work!
</div>
<?php endif; ?>

<div class="card card-pad">
    <div class="flex gap-12 mb-24">
        <button class="btn btn-primary" onclick="showTab('all-time', this)">All Time</button>
        <button class="btn btn-outline" onclick="showTab('this-week', this)">This Week</button>
    </div>

    <div id="tab-all-time">
        <?php if (empty($all_time)): ?>
            <p class="text-muted">No users found.</p>
        <?php else: ?>
            <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="padding: 12px;">Rank</th>
                        <th style="padding: 12px;">Defender</th>
                        <th style="padding: 12px;">Score</th>
                        <th style="padding: 12px;">Streak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($all_time as $u): 
                        $is_me = ($u['id'] == $user['id']);
                        $bg = $is_me ? 'background-color: var(--indigo-light); border-left: 4px solid var(--indigo); border-bottom: 1px solid var(--border);' : 'border-bottom: 1px solid var(--border);';
                        
                        $rank_display = $rank;
                        if ($rank == 1) $rank_display = '🥇';
                        else if ($rank == 2) $rank_display = '🥈';
                        else if ($rank == 3) $rank_display = '🥉';
                    ?>
                    <tr style="<?= $bg ?>">
                        <td style="padding: 16px 12px; font-weight: bold; font-size: 1.2rem;"><?= $rank_display ?></td>
                        <td style="padding: 16px 12px;">
                            <div class="flex gap-12 flex-center" style="justify-content: flex-start;">
                                <?php 
                                    $initials = strtoupper(substr($u['name'], 0, 2));
                                ?>
                                <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg, var(--indigo), var(--blue)); color:white; display:flex; align-items:center; justify-content:center; font-weight:bold;">
                                    <?= e($initials) ?>
                                </div>
                                <div>
                                    <div class="font-bold"><?= e($u['name']) ?> <?php if ($is_me): ?><span class="badge badge-indigo">You</span><?php endif; ?></div>
                                    <div class="text-sm text-muted">Member since <?= date('M Y', strtotime($u['created_at'])) ?></div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px 12px; width: 30%;">
                            <div class="flex-between mb-4">
                                <span class="badge <?= $u['cyber_score'] >= 80 ? 'badge-green' : ($u['cyber_score'] >= 50 ? 'badge-amber' : 'badge-red') ?>"><?= $u['cyber_score'] ?></span>
                            </div>
                            <div class="progress-track" style="height: 6px;">
                                <div class="progress-fill" style="width: <?= $u['cyber_score'] ?>%;"></div>
                            </div>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span class="badge badge-amber">🔥 <?= $u['streak_count'] ?></span>
                        </td>
                    </tr>
                    <?php $rank++; endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>

    <div id="tab-this-week" style="display:none;">
        <?php if (empty($this_week)): ?>
            <p class="text-muted">No users found.</p>
        <?php else: ?>
            <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="padding: 12px;">Rank</th>
                        <th style="padding: 12px;">Defender</th>
                        <th style="padding: 12px;">Score</th>
                        <th style="padding: 12px;">Streak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    foreach ($this_week as $u): 
                        $is_me = ($u['id'] == $user['id']);
                        $bg = $is_me ? 'background-color: var(--indigo-light); border-left: 4px solid var(--indigo); border-bottom: 1px solid var(--border);' : 'border-bottom: 1px solid var(--border);';
                        
                        $rank_display = $rank;
                        if ($rank == 1) $rank_display = '🥇';
                        else if ($rank == 2) $rank_display = '🥈';
                        else if ($rank == 3) $rank_display = '🥉';
                    ?>
                    <tr style="<?= $bg ?>">
                        <td style="padding: 16px 12px; font-weight: bold; font-size: 1.2rem;"><?= $rank_display ?></td>
                        <td style="padding: 16px 12px;">
                            <div class="flex gap-12 flex-center" style="justify-content: flex-start;">
                                <?php 
                                    $initials = strtoupper(substr($u['name'], 0, 2));
                                ?>
                                <div style="width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg, var(--indigo), var(--blue)); color:white; display:flex; align-items:center; justify-content:center; font-weight:bold;">
                                    <?= e($initials) ?>
                                </div>
                                <div>
                                    <div class="font-bold"><?= e($u['name']) ?> <?php if ($is_me): ?><span class="badge badge-indigo">You</span><?php endif; ?></div>
                                    <div class="text-sm text-muted"><?= $u['week_attempts'] ?> attempts this week</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 16px 12px; width: 30%;">
                            <div class="flex-between mb-4">
                                <span class="badge <?= $u['cyber_score'] >= 80 ? 'badge-green' : ($u['cyber_score'] >= 50 ? 'badge-amber' : 'badge-red') ?>"><?= $u['cyber_score'] ?></span>
                            </div>
                            <div class="progress-track" style="height: 6px;">
                                <div class="progress-fill" style="width: <?= $u['cyber_score'] ?>%;"></div>
                            </div>
                        </td>
                        <td style="padding: 16px 12px;">
                            <span class="badge badge-amber">🔥 <?= $u['streak_count'] ?></span>
                        </td>
                    </tr>
                    <?php $rank++; endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function showTab(id, btn) {
    document.getElementById('tab-all-time').style.display = 'none';
    document.getElementById('tab-this-week').style.display = 'none';
    document.getElementById('tab-' + id).style.display = 'block';
    
    const btns = btn.parentElement.querySelectorAll('button');
    btns.forEach(b => {
        b.classList.remove('btn-primary');
        b.classList.add('btn-outline');
    });
    btn.classList.remove('btn-outline');
    btn.classList.add('btn-primary');
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
