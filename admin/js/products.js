/* admin/js/products.js — Admin Products Management */
(function () {
    'use strict';

    /* ── Open Add/Edit Modal ──────────────────── */
    function openModal(modalId) {
        document.getElementById(modalId)?.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(modalId) {
        document.getElementById(modalId)?.classList.remove('open');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('[data-modal-open]').forEach(btn => btn.addEventListener('click', () => openModal(btn.dataset.modalOpen)));
    document.querySelectorAll('[data-modal-close]').forEach(btn => btn.addEventListener('click', () => closeModal(btn.closest('.admin-modal-overlay').id)));
    document.querySelectorAll('.admin-modal-overlay').forEach(overlay => overlay.addEventListener('click', function(e) { if (e.target === this) closeModal(this.id); }));

    /* ── Edit Button Pre-fill ─────────────────── */
    document.querySelectorAll('.btn-edit-product').forEach(btn => {
        btn.addEventListener('click', function () {
            const data = JSON.parse(this.dataset.product || '{}');
            const form = document.getElementById('editProductForm');
            if (!form) return;
            Object.entries(data).forEach(([k, v]) => { const el = form.elements[k]; if (el) el.value = v; });
            openModal('editProductModal');
        });
    });

    /* ── Delete Confirm ───────────────────────── */
    let pendingDeleteId = null;
    document.querySelectorAll('.btn-delete-product').forEach(btn => {
        btn.addEventListener('click', function () {
            pendingDeleteId = this.dataset.productId;
            document.getElementById('deleteProductName').textContent = this.dataset.productName || 'this product';
            openModal('deleteConfirmModal');
        });
    });

    document.getElementById('confirmDeleteBtn')?.addEventListener('click', function () {
        if (!pendingDeleteId) return;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        this.disabled = true;
        const fd = new FormData();
        fd.append('product_id', pendingDeleteId);
        fd.append('action', 'delete');
        fetch('../php/product_delete_handler.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    document.querySelector(`[data-product-row="${pendingDeleteId}"]`)?.remove();
                    closeModal('deleteConfirmModal');
                    window.adminShowToast?.('Product deleted successfully', 'success');
                }
            })
            .finally(() => { this.innerHTML = 'Delete'; this.disabled = false; });
    });

    /* ── Image Upload Preview ─────────────────── */
    document.querySelectorAll('.product-img-upload').forEach(input => {
        input.addEventListener('change', function () {
            const preview = document.getElementById(this.dataset.preview);
            if (!preview) return;
            const file = this.files[0];
            if (file) { const reader = new FileReader(); reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; }; reader.readAsDataURL(file); }
        });
    });

    /* ── Drag & Drop Image ────────────────────── */
    document.querySelectorAll('.img-upload-area').forEach(area => {
        area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('dragover'); });
        area.addEventListener('dragleave', () => area.classList.remove('dragover'));
        area.addEventListener('drop', e => {
            e.preventDefault(); area.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                const input = area.querySelector('input[type=file]'); if (input) { const dt = new DataTransfer(); dt.items.add(file); input.files = dt.files; input.dispatchEvent(new Event('change')); }
            }
        });
    });

    /* ── Live Search Filter ───────────────────── */
    const searchInput = document.getElementById('productSearch');
    searchInput?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('[data-product-row]').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });

})();
