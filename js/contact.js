/* js/contact.js — Contact Page Features */
(function () {
    'use strict';

    /* ── Character Counter for Message ──────── */
    const msgArea = document.getElementById('contactMessage');
    const charCount = document.getElementById('charCount');
    if (msgArea && charCount) {
        const update = () => { charCount.textContent = msgArea.value.length; };
        msgArea.addEventListener('input', update);
        update();
    }

    /* ── Real-time Field Validation ──────────── */
    function validateEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }
    function validatePhone(v) { return v === '' || /^[\d\s\-\+\(\)]{7,15}$/.test(v); }

    function setFieldState(wrapper, valid, msg) {
        wrapper?.classList.toggle('is-valid',   valid);
        wrapper?.classList.toggle('is-invalid', !valid);
        const fb = wrapper?.querySelector('.invalid-feedback, .valid-feedback');
        if (fb) fb.textContent = valid ? '' : msg;
    }

    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.querySelectorAll('.form-control-vv').forEach(input => {
            input.addEventListener('blur', function () {
                const wrap = this.closest('.form-group-vv');
                const v    = this.value.trim();
                if (this.required && !v)          setFieldState(wrap, false, 'This field is required.');
                else if (this.type === 'email' && !validateEmail(v)) setFieldState(wrap, false, 'Enter a valid email.');
                else if (this.name === 'phone'   && !validatePhone(v)) setFieldState(wrap, false, 'Enter a valid phone number.');
                else setFieldState(wrap, true, '');
            });
            input.addEventListener('input', function () {
                this.closest('.form-group-vv')?.classList.remove('is-invalid', 'is-valid');
            });
        });

        /* ── AJAX Form Submit ─────────────────── */
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();
            let valid = true;
            this.querySelectorAll('[required]').forEach(el => {
                if (!el.value.trim()) { el.closest('.form-group-vv')?.classList.add('is-invalid'); valid = false; }
            });
            if (!valid) return;

            const btn  = this.querySelector('button[type=submit]');
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending…';
            btn.disabled = true;

            const fd = new FormData(this);
            fetch('php/contact_handler.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    showAlert(data.success ? 'success' : 'error', data.message);
                    if (data.success) contactForm.reset();
                })
                .catch(() => showAlert('error', 'Something went wrong. Please try again.'))
                .finally(() => { btn.innerHTML = orig; btn.disabled = false; });
        });
    }

    function showAlert(type, msg) {
        const old = contactForm?.querySelector('.contact-feedback');
        old?.remove();
        const div = document.createElement('div');
        div.className = `alert-vv alert-${type} contact-feedback mb-3`;
        div.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
        contactForm?.insertAdjacentElement('beforebegin', div);
        div.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /* ── FAQ Accordion ────────────────────────── */
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', function () {
            const item = this.closest('.faq-item');
            const isOpen = item.classList.contains('open');
            document.querySelectorAll('.faq-item.open').forEach(el => el.classList.remove('open'));
            if (!isOpen) item.classList.add('open');
        });
    });

})();
