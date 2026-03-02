/* js/index.js — Home Page Interactive Features */
(function () {
    'use strict';

    /* ── Hero Particles (div-based) ──────────────── */
    const particleWrap = document.getElementById('heroParticles');
    if (particleWrap) {
        const sizes  = [4, 6, 8, 10, 14, 18];
        const delays = [0, 1, 2, 3, 4, 5, 6];
        for (let i = 0; i < 28; i++) {
            const s = document.createElement('span');
            const sz = sizes[i % sizes.length];
            s.style.cssText = [
                `width:${sz}px`, `height:${sz}px`,
                `top:${Math.random() * 100}%`,
                `left:${Math.random() * 100}%`,
                `animation-delay:${delays[i % delays.length] * 0.8}s`,
                `animation-duration:${5 + Math.random() * 5}s`,
                `background:rgba(${i % 3 === 0 ? '212,175,55' : '168,100,200'},0.35)`,
            ].join(';');
            particleWrap.appendChild(s);
        }
    }


    /* ── Animated Counters (IntersectionObserver) ─ */
    function animateCounter(el, target, duration = 2000) {
        let start = null;
        const step = (ts) => {
            if (!start) start = ts;
            const progress = Math.min((ts - start) / duration, 1);
            el.textContent = Math.floor(progress * target).toLocaleString();
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }

    const counterEls = document.querySelectorAll('[data-count]');
    if (counterEls.length) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) { animateCounter(e.target, +e.target.dataset.count); io.unobserve(e.target); }
            });
        }, { threshold: 0.5 });
        counterEls.forEach(el => io.observe(el));
    }

    /* ── Scroll Reveal (fade-in-up) ──────────────── */
    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length) {
        const revealIO = new IntersectionObserver((entries) => {
            entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('revealed'); revealIO.unobserve(e.target); } });
        }, { threshold: 0.15 });
        revealEls.forEach(el => revealIO.observe(el));
    }

    /* ── Hero Typing Effect ───────────────────── */
    const heroTagline = document.getElementById('heroTagline');
    if (heroTagline) {
        const phrases = heroTagline.dataset.phrases ? heroTagline.dataset.phrases.split('|') : [];
        let pi = 0, ci = 0, del = false;
        if (phrases.length) {
            setInterval(() => {
                const cur = phrases[pi];
                if (!del) {
                    heroTagline.textContent = cur.slice(0, ++ci);
                    if (ci === cur.length) { del = true; setTimeout(() => {}, 1200); }
                } else {
                    heroTagline.textContent = cur.slice(0, --ci);
                    if (ci === 0) { del = false; pi = (pi + 1) % phrases.length; }
                }
            }, del ? 60 : 100);
        }
    }

    /* ── Newsletter Form ──────────────────────── */
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn  = this.querySelector('button[type=submit]');
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Subscribing…';
            btn.disabled = true;
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Subscribed!';
                btn.style.background = '#27ae60';
                this.querySelector('input').value = '';
                setTimeout(() => { btn.innerHTML = orig; btn.style.background = ''; btn.disabled = false; }, 3000);
            }, 1200);
        });
    }

    /* ── Category Card Tilt ───────────────────── */
    document.querySelectorAll('.category-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width - 0.5) * 10;
            const y = ((e.clientY - rect.top)  / rect.height - 0.5) * -10;
            card.style.transform = `perspective(600px) rotateX(${y}deg) rotateY(${x}deg) scale(1.03)`;
        });
        card.addEventListener('mouseleave', () => { card.style.transform = ''; });
    });

})();
