/* admin/js/dashboard.js — Admin Dashboard Interactivity */
(function () {
    'use strict';

    /* ── Animated Stat Counters ───────────────── */
    function animateCounter(el, target, duration) {
        let start = null;
        const isCurrency = el.dataset.prefix === '₹';
        const step = ts => {
            if (!start) start = ts;
            const p = Math.min((ts - start) / duration, 1);
            const ease = 1 - Math.pow(1 - p, 4);
            const val = Math.floor(ease * target);
            el.textContent = (isCurrency ? '₹' : '') + val.toLocaleString('en-IN');
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }

    const io = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) { animateCounter(e.target, +e.target.dataset.count, 1600); io.unobserve(e.target); }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat-value[data-count]').forEach(el => io.observe(el));

    /* ── CSS Bar Chart Animate ────────────────── */
    const bars = document.querySelectorAll('.bar-fill[data-height]');
    if (bars.length) {
        const barIO = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.style.height = e.target.dataset.height + 'px';
                    barIO.unobserve(e.target);
                }
            });
        }, { threshold: 0.3 });
        bars.forEach(b => { b.style.height = '0'; barIO.observe(b); });
    }

    /* ── Status Badge Quick Update (Table) ────── */
    document.querySelectorAll('.status-quick-select').forEach(sel => {
        sel.addEventListener('change', function () {
            const orderId = this.dataset.orderId;
            const newStatus = this.value;
            const fd = new FormData();
            fd.append('order_id', orderId);
            fd.append('status', newStatus);
            fetch('../php/order_status_handler.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('Order status updated', 'success');
                        const badge = document.querySelector(`[data-order-badge="${orderId}"]`);
                        if (badge) { badge.textContent = newStatus; badge.className = `status-badge ${newStatus}`; }
                    }
                });
        });
    });

    /* ── Toast Notification ───────────────────── */
    function showToast(msg, type = 'success') {
        const t = document.createElement('div');
        t.className = `admin-toast admin-toast--${type}`;
        t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
        Object.assign(t.style, { position:'fixed', bottom:'24px', right:'24px', background: type==='success'?'#27ae60':'#e74c3c', color:'white', padding:'12px 20px', borderRadius:'12px', fontSize:'13px', fontWeight:'600', zIndex:9999, boxShadow:'0 8px 24px rgba(0,0,0,0.2)', display:'flex', gap:'8px', alignItems:'center', animation:'slideInToast 0.3s ease' });
        document.body.appendChild(t);
        if (!document.getElementById('toastStyle')) { const s = document.createElement('style'); s.id='toastStyle'; s.textContent='@keyframes slideInToast{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}'; document.head.appendChild(s); }
        setTimeout(() => { t.style.opacity='0'; t.style.transform='translateY(20px)'; t.style.transition='all 0.3s'; setTimeout(()=>t.remove(),300); }, 3000);
    }

    window.adminShowToast = showToast;

    /* ── Sidebar Active Link ──────────────────── */
    const currentPage = location.pathname.split('/').pop();
    document.querySelectorAll('.admin-sidebar-link').forEach(link => {
        link.classList.toggle('active', link.getAttribute('href') === currentPage);
    });

})();
