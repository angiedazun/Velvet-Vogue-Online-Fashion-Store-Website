/* js/cart.js — Cart Page Features */
(function () {
    'use strict';

    /* ── Quantity Update with Debounce ────────── */
    let debounceTimers = {};

    document.querySelectorAll('.qty-input').forEach(input => {
        const id = input.dataset.cartId;

        document.getElementById(`qtyMinus_${id}`)?.addEventListener('click', () => {
            if (+input.value > 1) { input.value = +input.value - 1; triggerUpdate(id, input.value, input); }
        });
        document.getElementById(`qtyPlus_${id}`)?.addEventListener('click', () => {
            const max = +(input.max || 99);
            if (+input.value < max) { input.value = +input.value + 1; triggerUpdate(id, input.value, input); }
        });
        input.addEventListener('change', () => triggerUpdate(id, input.value, input));
    });

    function triggerUpdate(cartId, qty, inputEl) {
        clearTimeout(debounceTimers[cartId]);
        debounceTimers[cartId] = setTimeout(() => updateCartItem(cartId, +qty, inputEl), 600);
    }

    function updateCartItem(cartId, qty, inputEl) {
        const row = inputEl.closest('.cart-item');
        row?.classList.add('updating');
        fetch(`php/cart_handler.php?action=update&cart_id=${cartId}&quantity=${qty}`, { method: 'POST' })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    updateSubtotal(cartId, data.subtotal);
                    updateCartTotals(data.cartTotal, data.discount, data.shipping);
                }
            })
            .finally(() => row?.classList.remove('updating'));
    }

    function updateSubtotal(cartId, subtotal) {
        const el = document.getElementById(`subtotal_${cartId}`);
        if (el && subtotal !== undefined) el.textContent = '₹' + Number(subtotal).toLocaleString('en-IN');
    }

    function updateCartTotals(total, discount, shipping) {
        if (total    !== undefined) document.getElementById('cartTotal')?.textContent    = '₹' + Number(total).toLocaleString('en-IN');
        if (discount !== undefined) document.getElementById('cartDiscount')?.textContent = '-₹' + Number(discount).toLocaleString('en-IN');
        if (shipping !== undefined) document.getElementById('cartShipping')?.textContent = shipping == 0 ? 'Free' : '₹' + Number(shipping).toLocaleString('en-IN');
    }

    /* ── Remove Item ──────────────────────────── */
    document.querySelectorAll('.cart-remove-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const cartId = this.dataset.cartId;
            const row    = this.closest('.cart-item');
            if (!cartId || !row) return;

            row.classList.add('removing');
            setTimeout(() => {
                fetch(`php/cart_handler.php?action=remove&cart_id=${cartId}`, { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        row.remove();
                        updateCartTotals(data.cartTotal, data.discount, data.shipping);
                        if (data.count === 0) showEmptyCart();
                        updateNavCartCount(data.count);
                    });
            }, 360);
        });
    });

    function showEmptyCart() {
        const table = document.getElementById('cartTable');
        const empty = document.getElementById('emptyCart');
        if (table) table.style.display = 'none';
        if (empty) empty.style.display = '';
    }

    function updateNavCartCount(count) {
        document.querySelectorAll('.cart-badge').forEach(b => b.textContent = count);
    }

    /* ── Coupon Code Apply ────────────────────── */
    document.getElementById('applyCouponBtn')?.addEventListener('click', function () {
        const code = document.getElementById('couponInput')?.value.trim();
        if (!code) return;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        this.disabled = true;
        const fd = new FormData();
        fd.append('coupon', code);
        fetch('php/cart_handler.php?action=apply_coupon', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                const msg = document.getElementById('couponResult');
                if (msg) { msg.textContent = data.message; msg.className = `coupon-result ${data.success ? 'success' : 'error'}`; }
                if (data.success) updateCartTotals(data.cartTotal, data.discount, data.shipping);
            })
            .finally(() => { this.innerHTML = 'Apply'; this.disabled = false; });
    });

})();
