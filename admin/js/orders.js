/* admin/js/orders.js — Admin Orders Management */
(function () {
    'use strict';

    /* ── Status Update ────────────────────────── */
    document.querySelectorAll('.status-select').forEach(sel => {
        const original = sel.value;
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
                        window.adminShowToast?.(`Order #${orderId} updated to "${newStatus}"`, 'success');
                        const badge = this.closest('tr')?.querySelector('.status-badge');
                        if (badge) { badge.className = `status-badge ${newStatus}`; badge.textContent = newStatus; }
                    } else { this.value = original; window.adminShowToast?.('Failed to update status', 'error'); }
                })
                .catch(() => { this.value = original; });
        });
    });

    /* ── Order Row Click → Detail Expand ───────── */
    document.querySelectorAll('.order-row-expandable').forEach(row => {
        row.addEventListener('click', function () {
            const orderId = this.dataset.orderId;
            const detailRow = document.getElementById(`order-detail-${orderId}`);
            if (!detailRow) return;
            const isOpen = detailRow.style.display !== 'none';
            detailRow.style.display = isOpen ? 'none' : '';
            this.querySelector('.expand-icon')?.classList.toggle('rotated', !isOpen);
        });
    });

    /* ── Date Range Filter ────────────────────── */
    document.getElementById('applyDateFilter')?.addEventListener('click', function () {
        const from = document.getElementById('dateFrom')?.value;
        const to   = document.getElementById('dateTo')?.value;
        if (!from || !to) return;
        const url = new URL(window.location.href);
        url.searchParams.set('from', from);
        url.searchParams.set('to', to);
        window.location.href = url.toString();
    });

    /* ── Bulk Status Update ───────────────────── */
    const selectAll = document.getElementById('selectAllOrders');
    selectAll?.addEventListener('change', function () {
        document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkBar();
    });

    document.querySelectorAll('.order-checkbox').forEach(cb => cb.addEventListener('change', updateBulkBar));

    function updateBulkBar() {
        const checked = document.querySelectorAll('.order-checkbox:checked');
        const bar = document.getElementById('bulkBar');
        if (bar) bar.style.display = checked.length ? '' : 'none';
        const count = document.getElementById('bulkCount');
        if (count) count.textContent = `${checked.length} order(s) selected`;
    }

    document.getElementById('bulkStatusApply')?.addEventListener('click', function () {
        const status = document.getElementById('bulkStatusSelect')?.value;
        const ids = [...document.querySelectorAll('.order-checkbox:checked')].map(cb => cb.value);
        if (!ids.length || !status) return;
        const fd = new FormData();
        ids.forEach(id => fd.append('order_ids[]', id));
        fd.append('status', status);
        fetch('../php/order_status_handler.php?bulk=1', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => { if (data.success) location.reload(); });
    });

    /* ── Print Invoice ────────────────────────── */
    document.querySelectorAll('.btn-print-order').forEach(btn => {
        btn.addEventListener('click', function () { window.print(); });
    });

})();
