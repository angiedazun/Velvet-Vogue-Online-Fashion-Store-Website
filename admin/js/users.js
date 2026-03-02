/* admin/js/users.js — Admin Users Management */
(function () {
    'use strict';

    /* ── User Drawer Open/Close ───────────────── */
    const drawer  = document.getElementById('userDrawer');
    const overlay = document.getElementById('userDrawerOverlay');

    function set(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val ?? '—';
    }

    function openUserDrawer(userId) {
        fetch(`../php/user_detail_handler.php?user_id=${userId}`)
            .then(r => r.json())
            .then(data => {
                if (!drawer) return;
                set('drawerUserName',   data.name);
                set('drawerUserEmail',  data.email);
                set('drawerUserPhone',  data.phone);
                set('drawerUserJoined',data.created_at);
                set('drawerTotalOrders', data.total_orders ?? 0);
                set('drawerTotalSpent', 'Rs. ' + Number(data.total_spent || 0).toLocaleString());
                set('drawerDelivered',  data.delivered ?? 0);

                const avatarEl = document.getElementById('drawerAvatar');
                if (avatarEl && data.name) {
                    const parts = data.name.trim().split(' ');
                    avatarEl.textContent = (parts[0]?.[0] || '') + (parts[1]?.[0] || '');
                }

                // Last Order
                const lastOrderBlock = document.getElementById('drawerLastOrder');
                if (data.last_order && lastOrderBlock) {
                    lastOrderBlock.style.display = 'block';
                    set('drawerLastOrderNum',    data.last_order.number);
                    set('drawerLastOrderDate',   data.last_order.date);
                    set('drawerLastOrderAmount', 'Rs. ' + Number(data.last_order.amount).toLocaleString());
                    const statusEl = document.getElementById('drawerLastOrderStatus');
                    if (statusEl) {
                        statusEl.textContent = data.last_order.status;
                        const colors = { Pending:'#f39c12', Processing:'#3498db', Shipped:'#9b59b6', Delivered:'#27ae60', Cancelled:'#e74c3c' };
                        const c = colors[data.last_order.status] || '#666';
                        statusEl.style.background = c + '20';
                        statusEl.style.color      = c;
                    }
                } else if (lastOrderBlock) {
                    lastOrderBlock.style.display = 'none';
                }

                // Update View Orders link
                const orderLink = document.getElementById('drawerViewOrdersLink');
                if (orderLink) orderLink.href = `orders.php?customer=${data.id}`;

                drawer.classList.add('open');
                overlay?.classList.add('show');
                document.body.style.overflow = 'hidden';
            })
            .catch(err => console.error('Drawer error:', err));
    }

    function closeUserDrawer() {
        drawer?.classList.remove('open');
        overlay?.classList.remove('show');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.btn-view-user').forEach(btn => {
        btn.addEventListener('click', () => openUserDrawer(btn.dataset.userId));
    });

    overlay?.addEventListener('click', closeUserDrawer);
    document.getElementById('drawerCloseBtn')?.addEventListener('click', closeUserDrawer);

    /* ── Ban / Unban Toggle ───────────────────── */
    document.querySelectorAll('.btn-ban, .btn-unban').forEach(btn => {
        btn.addEventListener('click', function () {
            const userId = this.dataset.userId;
            const action = this.classList.contains('btn-ban') ? 'ban' : 'unban';
            if (!confirm(`Are you sure you want to ${action} this user?`)) return;
            const fd = new FormData();
            fd.append('user_id', userId);
            fd.append('action', action);
            fetch('../php/user_status_handler.php', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => { if (data.success) { location.reload(); } });
        });
    });

    /* ── Live Search ──────────────────────────── */
    document.getElementById('userSearch')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('[data-user-row]').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

    /* ── Role Filter ──────────────────────────── */
    document.getElementById('roleFilter')?.addEventListener('change', function () {
        const role = this.value;
        document.querySelectorAll('[data-user-row]').forEach(row => {
            row.style.display = (!role || row.dataset.role === role) ? '' : 'none';
        });
    });

})();
