/* js/login.js — Login Page Features */
(function () {
    'use strict';

    /* ── Password Visibility Toggle ──────────── */
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = this.previousElementSibling || document.getElementById(this.dataset.target);
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            this.querySelector('i')?.classList.toggle('fa-eye',      !show);
            this.querySelector('i')?.classList.toggle('fa-eye-slash', show);
        });
    });

    /* ── Remember Me (localStorage) ──────────── */
    const emailInput    = document.getElementById('loginEmail');
    const rememberCheck = document.getElementById('rememberMe');

    if (emailInput && rememberCheck) {
        const saved = localStorage.getItem('vv_remember_email');
        if (saved) { emailInput.value = saved; rememberCheck.checked = true; }

        document.getElementById('loginForm')?.addEventListener('submit', function () {
            if (rememberCheck.checked) localStorage.setItem('vv_remember_email', emailInput.value.trim());
            else localStorage.removeItem('vv_remember_email');
        });
    }

    /* ── Real-time Email Validation ──────────── */
    emailInput?.addEventListener('blur', function () {
        const valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value.trim());
        this.closest('.form-group-vv')?.classList.toggle('field-error', this.value && !valid);
    });

    /* ── Enter Key Submit ─────────────────────── */
    document.getElementById('loginForm')?.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            const btn = this.querySelector('button[type=submit]');
            if (document.activeElement !== btn) btn?.click();
        }
    });

    /* ── Social Login Ripple ──────────────────── */
    document.querySelectorAll('.btn-social').forEach(btn => {
        btn.addEventListener('click', function () {
            this.classList.add('loading');
            setTimeout(() => this.classList.remove('loading'), 2000);
        });
    });

    /* ── Form Submit Loader ───────────────────── */
    document.getElementById('loginForm')?.addEventListener('submit', function () {
        const btn = this.querySelector('.btn-submit');
        if (btn) { btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in…'; btn.disabled = true; }
    });

})();
