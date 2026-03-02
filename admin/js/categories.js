/* admin/js/categories.js */
(function () {
    'use strict';

    /* ── Open Add Modal ──────────────────────── */
    window.openAddModal = function () {
        document.getElementById('addModalBackdrop').style.display = 'flex';
        document.getElementById('addNameInput').focus();
    };

    /* ── Open Edit Modal ─────────────────────── */
    window.openEditModal = function (id, data) {
        document.getElementById('editCatId').value      = id;
        document.getElementById('editName').value       = data.name || '';
        document.getElementById('editSlug').value       = data.slug || '';
        document.getElementById('editDesc').value       = data.description || '';
        const imgInput   = document.getElementById('editImageUrl');
        const imgPreview = document.getElementById('editImgPreview');
        imgInput.value   = data.image_url || '';
        if (data.image_url) {
            imgPreview.src  = data.image_url;
            imgPreview.style.display = 'block';
        } else {
            imgPreview.style.display = 'none';
        }
        document.getElementById('editModalBackdrop').style.display = 'flex';
        document.getElementById('editName').focus();
    };

    /* ── Image preview on edit ───────────────── */
    document.getElementById('editImageUrl')?.addEventListener('input', function () {
        const preview = document.getElementById('editImgPreview');
        if (this.value) {
            preview.src = this.value;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });

    /* ── Close Modals ────────────────────────── */
    window.closeModals = function () {
        document.getElementById('addModalBackdrop').style.display  = 'none';
        document.getElementById('editModalBackdrop').style.display = 'none';
    };

    /* Close on backdrop click */
    ['addModalBackdrop', 'editModalBackdrop'].forEach(id => {
        document.getElementById(id)?.addEventListener('click', function (e) {
            if (e.target === this) closeModals();
        });
    });

    /* Close on Escape */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModals();
    });

    /* ── Auto Slug ───────────────────────────── */
    window.autoSlug = function (nameInput, slugInputId) {
        const slugInput = document.getElementById(slugInputId);
        if (!slugInput || slugInput.dataset.manual) return;
        slugInput.value = nameInput.value
            .toLowerCase()
            .replace(/[^a-z0-9 -]/g, '')
            .trim()
            .replace(/\s+/g, '-');
    };

    /* Mark slug as manually edited */
    document.getElementById('addSlugInput')?.addEventListener('input', function () {
        this.dataset.manual = '1';
    });

})();
