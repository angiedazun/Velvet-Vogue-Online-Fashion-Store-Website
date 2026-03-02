/* js/register.js — Registration Page Features */
(function () {
    'use strict';

    /* ── Password Strength Meter ──────────────── */
    const pwdInput   = document.getElementById('regPassword');
    const strengthFill  = document.getElementById('strengthFill');
    const strengthLabel = document.getElementById('strengthLabel');
    const rules = {
        length:  { el: document.getElementById('ruleLength'),  test: v => v.length >= 8 },
        upper:   { el: document.getElementById('ruleUpper'),   test: v => /[A-Z]/.test(v) },
        number:  { el: document.getElementById('ruleNumber'),  test: v => /[0-9]/.test(v) },
        special: { el: document.getElementById('ruleSpecial'), test: v => /[^A-Za-z0-9]/.test(v) },
    };

    const strengthLabels = ['', 'Weak', 'Fair', 'Good', 'Strong'];

    function updateStrength(val) {
        let score = 0;
        Object.values(rules).forEach(rule => {
            const met = rule.test(val);
            if (met) score++;
            rule.el?.classList.toggle('met', met);
            const icon = rule.el?.querySelector('i');
            if (icon) { icon.className = met ? 'fas fa-check' : 'fas fa-times'; }
        });
        if (strengthFill) { strengthFill.className = `strength-bar-fill strength-${score}`; }
        if (strengthLabel) {
            strengthLabel.className = `strength-label strength-${score}`;
            strengthLabel.textContent = strengthLabels[score] || '';
        }
    }

    pwdInput?.addEventListener('input', function () { updateStrength(this.value); checkMatch(); });

    /* ── Password Confirm Match ───────────────── */
    const confirmInput = document.getElementById('regConfirmPassword');
    const matchMsg     = document.getElementById('matchMsg');

    function checkMatch() {
        if (!confirmInput || !matchMsg || !confirmInput.value) return;
        const match = pwdInput?.value === confirmInput.value;
        matchMsg.className = `confirm-match-msg ${match ? 'match' : 'no-match'}`;
        matchMsg.innerHTML = match
            ? '<i class="fas fa-check-circle"></i> Passwords match'
            : '<i class="fas fa-times-circle"></i> Passwords do not match';
    }

    confirmInput?.addEventListener('input', checkMatch);

    /* ── Password Visibility Toggle (both fields) ─ */
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target) || this.previousElementSibling;
            if (!input) return;
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            this.querySelector('i')?.classList.toggle('fa-eye',      !show);
            this.querySelector('i')?.classList.toggle('fa-eye-slash', show);
        });
    });

    /* ── Terms Checkbox Visual ────────────────── */
    document.getElementById('termsCheck')?.addEventListener('change', function () {
        this.closest('.terms-check-wrap')?.classList.toggle('agreed', this.checked);
    });

    /* ── Form Submit Validation ───────────────── */
    document.getElementById('registerForm')?.addEventListener('submit', function (e) {
        if (!document.getElementById('termsCheck')?.checked) {
            e.preventDefault();
            alert('Please agree to the Terms & Conditions to continue.');
            return;
        }
        if (pwdInput?.value !== confirmInput?.value) {
            e.preventDefault();
            confirmInput?.focus();
            return;
        }
        const btn = this.querySelector('.btn-submit');
        if (btn) { btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating account…'; btn.disabled = true; }
    });

})();
