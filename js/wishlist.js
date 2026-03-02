/* wishlist.js — Wishlist Page */
(function () {
    'use strict';

    /* ── Clear All Wishlist ──────────────────── */
    window.clearWishlist = function () {
        if (!confirm('Are you sure you want to clear your entire wishlist?')) return;

        fetch('php/wishlist.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=clear_all'
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast('Wishlist cleared!', 'info');
                setTimeout(() => location.reload(), 800);
            }
        })
        .catch(() => {
            // fallback: submit hidden form
            document.getElementById('clearWishlistForm')?.submit();
        });
    };

    /* ── AJAX Remove (optional enhancement) ─── */
    document.querySelectorAll('.wishlist-remove-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            const card = this.closest('.wishlist-card');
            if (card) {
                card.style.transition = 'opacity 0.3s, transform 0.3s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
            }
        });
    });

    /* ── Add to Cart Feedback ────────────────── */
    document.querySelectorAll('.wishlist-cart-form').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = this.querySelector('button');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
                btn.disabled  = true;
            }
        });
    });

})();
