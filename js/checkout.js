/* js/checkout.js — Checkout Page Features */
(function () {
    'use strict';

    /* ── Step Indicator ───────────────────────── */
    const steps = document.querySelectorAll('.checkout-step');
    function goToStep(n) {
        steps.forEach((s, i) => {
            s.classList.toggle('active', i === n);
            s.classList.toggle('done',   i < n);
        });
        document.querySelectorAll('.checkout-phase').forEach((p, i) => {
            p.style.display = i === n ? '' : 'none';
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.querySelectorAll('[data-checkout-next]').forEach(btn => {
        btn.addEventListener('click', function () {
            const cur = +this.closest('.checkout-phase').dataset.phase;
            if (validatePhase(cur)) goToStep(cur + 1);
        });
    });

    document.querySelectorAll('[data-checkout-back]').forEach(btn => {
        btn.addEventListener('click', function () {
            const cur = +this.closest('.checkout-phase').dataset.phase;
            goToStep(cur - 1);
        });
    });

    /* ── Simple Phase Validation ──────────────── */
    function validatePhase(phase) {
        const phaseEl = document.querySelector(`.checkout-phase[data-phase="${phase}"]`);
        if (!phaseEl) return true;
        let valid = true;
        phaseEl.querySelectorAll('[required]').forEach(el => {
            if (!el.value.trim()) {
                el.closest('.form-group-vv')?.classList.add('is-invalid');
                el.style.borderColor = '#e74c3c';
                valid = false;
            } else {
                el.closest('.form-group-vv')?.classList.remove('is-invalid');
                el.style.borderColor = '';
            }
        });
        if (!valid) { phaseEl.querySelector('[required]:invalid, [required][style*="border-color: rgb(231"]')?.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
        return valid;
    }

    /* ── Payment Method Selection ─────────────── */
    document.querySelectorAll('.payment-option').forEach(opt => {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('active'));
            document.querySelectorAll('.card-detail-panel').forEach(p => p.classList.remove('show'));
            this.classList.add('active');
            const panelId = this.dataset.panel;
            if (panelId) document.getElementById(panelId)?.classList.add('show');
            const hidden = document.getElementById('selectedPayment');
            if (hidden) hidden.value = this.dataset.method || '';
        });
    });

    /* ── Place Order Button ───────────────────── */
    document.getElementById('placeOrderBtn')?.addEventListener('click', function (e) {
        if (!document.querySelector('.payment-option.active')) {
            alert('Please select a payment method.');
            e.preventDefault();
            return;
        }
        const btn = this;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing…';
        btn.disabled = true;
    });

    /* ── Order Confirmation Confetti ─────────── */
    if (document.getElementById('orderConfirmation')) {
        launchConfetti();
        setTimeout(launchConfetti, 1500);
    }

    function launchConfetti() {
        const colors = ['#6C3483','#D4AF37','#8e44ad','#f39c12','#ffffff'];
        for (let i = 0; i < 60; i++) {
            const el = document.createElement('span');
            Object.assign(el.style, {
                position: 'fixed',
                top: '-10px',
                left: Math.random() * 100 + 'vw',
                width: (Math.random() * 8 + 6)  + 'px',
                height: (Math.random() * 8 + 6) + 'px',
                borderRadius: Math.random() > 0.5 ? '50%' : '2px',
                background: colors[Math.floor(Math.random() * colors.length)],
                animation: `confettiFall ${Math.random() * 2 + 1.5}s ease ${Math.random() * 0.8}s forwards`,
                zIndex: 9999,
                pointerEvents: 'none',
            });
            document.body.appendChild(el);
            setTimeout(() => el.remove(), 4000);
        }
    }

    /* Inject confetti keyframe once */
    if (!document.getElementById('confettiStyle')) {
        const style = document.createElement('style');
        style.id = 'confettiStyle';
        style.textContent = `@keyframes confettiFall { 0%{opacity:1;transform:translateY(0) rotate(0deg)} 100%{opacity:0;transform:translateY(100vh) rotate(720deg)} }`;
        document.head.appendChild(style);
    }

})();
