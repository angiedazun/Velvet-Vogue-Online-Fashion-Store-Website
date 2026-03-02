/* js/about.js — About Page Interactive Features */
(function () {
    'use strict';

    /* ── Animated Stats Counters ──────────────── */
    function animateCounter(el, target, duration) {
        let start = null;
        const step = ts => {
            if (!start) start = ts;
            const p = Math.min((ts - start) / duration, 1);
            const ease = 1 - Math.pow(1 - p, 3);
            el.textContent = Math.floor(ease * target).toLocaleString();
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }

    const statEls = document.querySelectorAll('.stat-counter[data-count]');
    if (statEls.length) {
        const io = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    animateCounter(e.target, +e.target.dataset.count, 1800);
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.6 });
        statEls.forEach(el => io.observe(el));
    }

    /* ── Scroll Reveal for Section Elements ────── */
    const revealEls = document.querySelectorAll('[data-reveal]');
    if (revealEls.length) {
        const revIO = new IntersectionObserver(entries => {
            entries.forEach((e, i) => {
                if (e.isIntersecting) {
                    const delay = e.target.dataset.revealDelay || 0;
                    setTimeout(() => {
                        e.target.style.opacity = '1';
                        e.target.style.transform = 'translateY(0)';
                    }, delay * 100);
                    revIO.unobserve(e.target);
                }
            });
        }, { threshold: 0.15 });
        revealEls.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(24px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            revIO.observe(el);
        });
    }

    /* ── Team Card Social Hover Enhanced ─────── */
    document.querySelectorAll('.team-card').forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.querySelector('.team-overlay')?.classList.add('visible');
        });
        card.addEventListener('mouseleave', function () {
            this.querySelector('.team-overlay')?.classList.remove('visible');
        });
    });

    /* ── Story Image Parallax on Scroll ──────── */
    const storyImg = document.querySelector('.about-story-img');
    if (storyImg) {
        window.addEventListener('scroll', () => {
            const rect  = storyImg.getBoundingClientRect();
            const winH  = window.innerHeight;
            const ratio = (winH - rect.top) / (winH + rect.height);
            const shift = (ratio - 0.5) * 30;
            storyImg.style.transform = `translateY(${shift}px)`;
        }, { passive: true });
    }

})();
