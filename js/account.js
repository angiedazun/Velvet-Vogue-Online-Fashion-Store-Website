/* account.js — My Account Page */
(function () {
    'use strict';

    /* ── Tab Switching ────────────────────────── */
    const navBtns = document.querySelectorAll('.acc-nav-btn[data-tab]');
    const tabs    = document.querySelectorAll('.account-tab');

    function switchTab(tabId) {
        tabs.forEach(t => t.classList.remove('active'));
        navBtns.forEach(b => b.classList.remove('active'));
        const target = document.getElementById('tab-' + tabId);
        if (target) target.classList.add('active');
        navBtns.forEach(b => { if (b.dataset.tab === tabId) b.classList.add('active'); });
        sessionStorage.setItem('accTab', tabId);
    }

    navBtns.forEach(btn => {
        btn.addEventListener('click', () => switchTab(btn.dataset.tab));
    });

    // Restore last active tab or read hash
    const saved = sessionStorage.getItem('accTab') || 'profile';
    switchTab(saved);

    /* ── Password Strength ────────────────────── */
    const newPwdInput = document.getElementById('newPwd');
    const bar         = document.getElementById('pwdStrengthBar');
    const fill        = document.getElementById('pwdStrengthFill');
    const label       = document.getElementById('pwdStrengthLabel');

    if (newPwdInput) {
        newPwdInput.addEventListener('input', function () {
            const val = this.value;
            if (!val) { bar.style.display = 'none'; return; }
            bar.style.display = 'block';

            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const levels = [
                { w: '25%',  bg: '#e74c3c', text: 'Weak' },
                { w: '50%',  bg: '#f39c12', text: 'Fair' },
                { w: '75%',  bg: '#3498db', text: 'Good' },
                { w: '100%', bg: '#27ae60', text: 'Strong' },
            ];
            const lvl = levels[score - 1] || levels[0];
            fill.style.width      = lvl.w;
            fill.style.background = lvl.bg;
            label.textContent     = lvl.text;
            label.style.color     = lvl.bg;
        });
    }

    /* ── Password Toggle ──────────────────────── */
    window.togglePassword = function (id, btn) {
        const input = document.getElementById(id);
        if (!input) return;
        const isText = input.type === 'text';
        input.type = isText ? 'password' : 'text';
        btn.innerHTML = isText ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    };

    /* ── Confirm password match ───────────────── */
    const confirmPwd = document.getElementById('confirmPwd');
    if (confirmPwd) {
        confirmPwd.addEventListener('input', function () {
            const np = document.getElementById('newPwd')?.value;
            this.style.borderColor = this.value === np ? '#27ae60' : '#e74c3c';
        });
    }

})();
