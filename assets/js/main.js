// SafeSphere — Enhanced JS: counters, chat engine, tabs, accordion, FAQ

// ── Mobile sidebar hamburger (global, called from onclick in header.php) ────
function openSidebar() {
    var sidebar = document.getElementById('app-sidebar');
    var backdrop = document.getElementById('sidebar-backdrop');
    if (sidebar) sidebar.classList.add('open');
    if (backdrop) backdrop.classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeSidebar() {
    var sidebar = document.getElementById('app-sidebar');
    var backdrop = document.getElementById('sidebar-backdrop');
    if (sidebar) sidebar.classList.remove('open');
    if (backdrop) backdrop.classList.remove('active');
    document.body.style.overflow = '';
}

// ── Theme Management ──────────────────────────────────────────────────────────
function initDarkMode() {
    var saved = localStorage.getItem('ss_theme');
    var theme = saved ? saved : 'light';
    document.documentElement.setAttribute('data-theme', theme);
    updateThemeToggleUI(theme);
}

function toggleDarkMode() {
    var current = document.documentElement.getAttribute('data-theme') || 'light';
    var next = (current === 'dark') ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    try {
        localStorage.setItem('ss_theme', next);
    } catch(e) {}
    updateThemeToggleUI(next);
}

function updateThemeToggleUI(theme) {
    var isDark = theme === 'dark';
    document.querySelectorAll('.theme-toggle-btn').forEach(function(btn) {
        btn.setAttribute('aria-label', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
        btn.setAttribute('title', isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode');
        var sun = btn.querySelector('.theme-icon-sun');
        var moon = btn.querySelector('.theme-icon-moon');
        if (sun) sun.style.display = isDark ? 'inline-block' : 'none';
        if (moon) moon.style.display = isDark ? 'none' : 'inline-block';
    });
}
initDarkMode(); // run immediately

document.addEventListener('DOMContentLoaded', function () {

    // ── Auto-dismiss alerts ──────────────────────────────────────────
    document.querySelectorAll('.alert').forEach(function (el) {
        setTimeout(function () {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(function () { el.remove(); }, 400);
        }, 6000);
    });

    // ── Close mobile sidebar when a nav item is clicked ──────────────
    document.querySelectorAll('#app-sidebar .nav-item').forEach(function(item) {
        item.addEventListener('click', function() { closeSidebar(); });
    });

    // ── Animated stat counters ────────────────────────────────────────

    const counters = document.querySelectorAll('[data-counter]');
    if (counters.length > 0) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    animateCounter(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });
        counters.forEach(function (el) { observer.observe(el); });
    }

    function animateCounter(el) {
        const target = parseInt(el.getAttribute('data-counter'), 10);
        const suffix = el.getAttribute('data-suffix') || '';
        const duration = 1800;
        const start = performance.now();
        function update(now) {
            const elapsed = now - start;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(eased * target).toLocaleString('en-IN') + suffix;
            if (progress < 1) requestAnimationFrame(update);
        }
        requestAnimationFrame(update);
    }

    // ── Tab switcher ─────────────────────────────────────────────────
    document.querySelectorAll('.tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const group = btn.closest('[data-tabs]') || btn.closest('.tab-bar').parentElement;
            const target = btn.getAttribute('data-tab');
            group.querySelectorAll('.tab-btn').forEach(function (b) { b.classList.remove('active'); });
            group.querySelectorAll('.tab-panel').forEach(function (p) { p.classList.remove('active'); });
            btn.classList.add('active');
            const panel = group.querySelector('#tab-' + target);
            if (panel) panel.classList.add('active');
        });
    });

    // ── Accordion ─────────────────────────────────────────────────────
    document.querySelectorAll('.accordion-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            const item = trigger.closest('.accordion-item');
            const isOpen = item.classList.contains('open');
            // optionally close others in same parent:
            // item.parentElement.querySelectorAll('.accordion-item.open').forEach(i => i.classList.remove('open'));
            item.classList.toggle('open', !isOpen);
        });
    });

    // ── FAQ accordion ─────────────────────────────────────────────────
    document.querySelectorAll('.faq-question').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const item = btn.closest('.faq-item');
            item.classList.toggle('open');
        });
    });

    // ── Social Engineering Chat Engine ────────────────────────────────
    const chatWindow = document.getElementById('chat-messages');
    const chatChoices = document.getElementById('chat-choices');
    const chatData = window.SE_DATA || null; // injected by PHP

    if (chatWindow && chatData) {
        initChat(chatData.currentNode || 'start');
    }

    function initChat(nodeId) {
        if (!chatData || !chatData.nodes) return;
        const node = chatData.nodes[nodeId];
        if (!node) return;

        // Clear choices while messages appear
        if (chatChoices) chatChoices.innerHTML = '';

        // Show attacker messages with typing delay
        let delay = 0;
        (node.messages || []).forEach(function (msg, i) {
            delay += i === 0 ? 300 : 1200;
            setTimeout(function () {
                appendBubble(chatWindow, msg, 'bubble-attacker');
                chatWindow.scrollTop = chatWindow.scrollHeight;

                // After last message, show choices
                if (i === node.messages.length - 1) {
                    setTimeout(function () {
                        renderChoices(node.choices, node.outcome);
                    }, 600);
                }
            }, delay);
        });
    }

    function appendBubble(container, text, cls) {
        const div = document.createElement('div');
        div.className = 'chat-bubble ' + cls;
        div.textContent = text;
        container.appendChild(div);
        return div;
    }

    function renderChoices(choices, outcome) {
        if (!chatChoices) return;
        chatChoices.innerHTML = '';

        if (!choices || choices.length === 0) {
            // Terminal node — show restart option
            const btn = document.createElement('button');
            btn.className = 'btn btn-primary';
            btn.textContent = '↩ Try a Different Scenario';
            btn.addEventListener('click', function () {
                const scId = chatChoices.getAttribute('data-scenario');
                window.location.href = '?id=' + scId;
            });
            chatChoices.appendChild(btn);
            return;
        }

        const label = document.createElement('div');
        label.className = 'chat-choices-label';
        label.textContent = 'Your response:';
        chatChoices.appendChild(label);

        choices.forEach(function (choice) {
            const btn = document.createElement('button');
            btn.className = 'choice-btn';
            btn.textContent = choice.label;
            btn.addEventListener('click', function () {
                // Show user bubble
                appendBubble(chatWindow, choice.label, 'bubble-user');
                chatWindow.scrollTop = chatWindow.scrollHeight;

                // Disable all choices
                chatChoices.querySelectorAll('.choice-btn').forEach(function (b) { b.disabled = true; b.style.opacity = '0.5'; });

                // Record via AJAX
                const scId = chatChoices.getAttribute('data-scenario');
                const seId = chatChoices.getAttribute('data-se-id');
                if (scId && seId) {
                    fetch(window.location.href, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'ajax=1&scenario_id=' + encodeURIComponent(scId)
                              + '&node_id=' + encodeURIComponent(choice.next || '')
                              + '&is_correct=' + (choice.correct ? '1' : '0')
                              + '&points=' + (choice.points || 0)
                              + '&csrf_token=' + encodeURIComponent(document.querySelector('[name=csrf_meta]')?.content || '')
                    });
                }

                // Continue chat
                setTimeout(function () {
                    initChat(choice.next);
                }, 400);
            });
            chatChoices.appendChild(btn);
        });
    }

    // ── Network Audit status toggle ───────────────────────────────────
    document.querySelectorAll('.audit-action-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const checkId = btn.getAttribute('data-check');
            const status  = btn.getAttribute('data-status');
            const csrf    = document.querySelector('[name=csrf_meta]')?.content || '';
            // Optimistic UI update
            const item = btn.closest('.accordion-item');
            const badge = item.querySelector('.accordion-status-badge');
            if (badge) {
                badge.className = 'accordion-status-badge badge ' + (status === 'pass' ? 'badge-green' : (status === 'fail' ? 'badge-red' : 'badge-gray'));
                badge.textContent = status === 'pass' ? '✓ Pass' : (status === 'fail' ? '✕ Fail' : 'Pending');
            }
            // Update score display
            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'ajax=1&check_id=' + encodeURIComponent(checkId)
                      + '&status=' + encodeURIComponent(status)
                      + '&csrf_token=' + encodeURIComponent(csrf)
            }).then(function (r) { return r.json(); })
              .then(function (data) {
                  if (data.score !== undefined) {
                      const scoreEl = document.getElementById('audit-total-score');
                      if (scoreEl) scoreEl.textContent = data.score;
                  }
                  if (data.cyber_score !== undefined) {
                      const chip = document.querySelector('.points-chip');
                      if (chip) chip.innerHTML = '⚡ ' + data.cyber_score + ' Cyber Score';
                  }
              }).catch(function () {});
        });
    });

    // ── Forensic tool analysis ─────────────────────────────────────────
    const forensicForms = document.querySelectorAll('.forensic-form');
    forensicForms.forEach(function (form) {
        form.addEventListener('submit', function (e) {
            const btn = form.querySelector('.forensic-submit');
            if (btn) { btn.textContent = 'Analyzing…'; btn.disabled = true; }
        });
    });

    // ── UPI scenario card click ────────────────────────────────────────
    document.querySelectorAll('.upi-notification').forEach(function (card) {
        card.addEventListener('click', function () {
            const id = card.getAttribute('data-id');
            if (!id) return;
            // Highlight active
            document.querySelectorAll('.upi-notification').forEach(function (c) { c.classList.remove('active'); });
            card.classList.add('active');
            // Show detail
            document.querySelectorAll('.upi-detail').forEach(function (d) { d.classList.add('hidden'); });
            const detail = document.getElementById('upi-detail-' + id);
            if (detail) detail.classList.remove('hidden');
        });
    });

    // ── Live Threat & Scam Sandbox Engine ─────────────────────────────
    const quickScanForm = document.getElementById('quick-scanner-form');
    const quickScanInput = document.getElementById('quick-scanner-input');
    const quickScanBtn = document.getElementById('quick-scanner-btn');
    const quickScanResult = document.getElementById('quick-scanner-result');

    if (quickScanForm && quickScanInput && quickScanBtn && quickScanResult) {
        quickScanForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const val = quickScanInput.value.trim();
            if (!val) return;

            quickScanBtn.disabled = true;
            quickScanBtn.innerHTML = 'Scanning Live…';
            quickScanResult.style.display = 'block';
            quickScanResult.innerHTML = '<div style="display:flex;align-items:center;gap:10px;color:var(--text-muted);"><span class="badge badge-indigo">Querying Cloudflare DoH & Threat Heuristics…</span></div>';

            fetch('api/threat_intel.php?action=quick_scan', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'input=' + encodeURIComponent(val)
            })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                quickScanBtn.disabled = false;
                quickScanBtn.innerHTML = 'Inspect Risk →';

                if (!data.success) {
                    quickScanResult.innerHTML = '<div class="alert alert-error">' + (data.error || 'Unable to scan.') + '</div>';
                    return;
                }

                let badgeColor = data.verdict_class === 'red' ? 'badge-red' : (data.verdict_class === 'amber' ? 'badge-amber' : 'badge-green');
                let meterFill = data.verdict_class;
                let riskScore = data.risk_score || 0;

                let html = '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">'
                    + '<div><strong style="font-size:15px;">Target: ' + (data.host || data.target || val) + '</strong></div>'
                    + '<span class="badge ' + badgeColor + '">' + (data.verdict || 'ANALYZED') + ' (' + riskScore + '/100 RISK)</span>'
                    + '</div>';

                html += '<div class="risk-meter"><div class="risk-meter-fill ' + meterFill + '" style="width:' + Math.max(5, riskScore) + '%"></div></div>';

                if (data.findings && data.findings.length > 0) {
                    html += '<div style="display:grid;gap:8px;">';
                    data.findings.forEach(function(f) {
                        let fColor = f.type === 'danger' ? '#ef4444' : (f.type === 'warning' ? '#f59e0b' : '#10b981');
                        html += '<div style="padding:10px 14px;border-radius:6px;background:var(--surface);border-left:3px solid ' + fColor + ';border:1px solid var(--border);border-left-width:3px;">'
                            + '<div style="font-weight:700;font-size:13.5px;color:var(--text);margin-bottom:3px;">' + f.title + '</div>'
                            + '<div style="font-size:13px;color:var(--text-muted);line-height:1.5;">' + f.desc + '</div>'
                            + '</div>';
                    });
                    html += '</div>';
                }

                quickScanResult.innerHTML = html;
            })
            .catch(function(err) {
                quickScanBtn.disabled = false;
                quickScanBtn.innerHTML = 'Inspect Risk →';
                quickScanResult.innerHTML = '<div class="alert alert-error">Network error scanning target. Please try again.</div>';
            });
        });
    }

    // ── Homepage 60-Second Cyber Risk Assessment ───────────────────────
    const riskQuizForm = document.getElementById('risk-quiz-form');
    if (riskQuizForm) {
        riskQuizForm.addEventListener('submit', function (e) {
            e.preventDefault();
            let q1 = document.querySelector('input[name="q_upi"]:checked');
            let q2 = document.querySelector('input[name="q_pwd"]:checked');
            let q3 = document.querySelector('input[name="q_call"]:checked');

            if (!q1 || !q2 || !q3) {
                alert('Please answer all 3 questions to calculate your risk rating.');
                return;
            }

            let risk = 0;
            if (q1.value === 'bad') risk += 35;
            if (q2.value === 'bad') risk += 35;
            if (q3.value === 'bad') risk += 30;

            let resultCard = document.getElementById('risk-quiz-result');
            if (resultCard) {
                resultCard.style.display = 'block';
                let rating = risk >= 60 ? 'HIGH EXPOSURE RISK' : (risk >= 30 ? 'MODERATE VULNERABILITY' : 'CYBER VIGILANT');
                let badgeClass = risk >= 60 ? 'badge-red' : (risk >= 30 ? 'badge-amber' : 'badge-green');
                let desc = risk >= 60
                    ? 'Your current digital habits leave you susceptible to modern UPI collect scams and credential stuffing attacks. We strongly advise completing the Phishing and UPI Defense modules.'
                    : (risk >= 30
                        ? 'You have basic security instincts, but psychological urgency tricks could still catch you off guard. Practice hands-on scenarios to sharpen your reflexes.'
                        : 'Great job! You have solid foundational security awareness. Take our advanced Deepfake and Forensic Toolkit drills to stay ahead of sophisticated AI threats.');

                resultCard.innerHTML = '<div style="padding:24px;border-radius:12px;background:var(--surface);border:1px solid var(--border);box-shadow:var(--shadow-md);margin-top:20px;">'
                    + '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">'
                    + '<h4 style="font-size:18px;font-weight:800;">Your Cyber Risk Profile</h4>'
                    + '<span class="badge ' + badgeClass + '">' + rating + '</span>'
                    + '</div>'
                    + '<p style="font-size:14.5px;color:var(--text-muted);line-height:1.6;margin-bottom:18px;">' + desc + '</p>'
                    + '<a href="register.php" class="btn btn-primary">Start Interactive Training to Fix Weaknesses →</a>'
                    + '</div>';
            }
        });
    }

});
