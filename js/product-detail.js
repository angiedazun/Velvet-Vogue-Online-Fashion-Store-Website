/* js/product-detail.js — Product Detail Page Features */
(function () {
    'use strict';

    /* ── Gallery Thumbnail Switcher ──────────── */
    const mainImg = document.getElementById('galleryMain');
    document.querySelectorAll('.gallery-thumb').forEach(thumb => {
        thumb.addEventListener('click', function () {
            if (mainImg) mainImg.src = this.dataset.full || this.src;
            document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    /* ── Image Zoom on Mousemove ──────────────── */
    if (mainImg) {
        mainImg.addEventListener('mousemove', function (e) {
            const rect = this.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width)  * 100;
            const y = ((e.clientY - rect.top)  / rect.height) * 100;
            this.style.transformOrigin = `${x}% ${y}%`;
            this.style.transform = 'scale(1.6)';
        });
        mainImg.addEventListener('mouseleave', function () {
            this.style.transform = '';
            this.style.transformOrigin = '';
        });
    }

    /* ── Size Selector ────────────────────────── */
    document.querySelectorAll('.size-btn:not([disabled])').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            if (document.getElementById('selectedSize')) {
                document.getElementById('selectedSize').value = this.dataset.size || this.textContent;
            }
        });
    });

    /* ── Color Swatch Selector ────────────────── */
    document.querySelectorAll('.color-swatch').forEach(swatch => {
        swatch.addEventListener('click', function () {
            document.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
            this.classList.add('active');
            if (document.getElementById('selectedColor')) {
                document.getElementById('selectedColor').value = this.dataset.color || '';
            }
        });
    });

    /* ── Quantity Control ─────────────────────── */
    const qtyInput = document.getElementById('productQty');
    document.getElementById('qtyMinus')?.addEventListener('click', () => {
        if (qtyInput && +qtyInput.value > 1) qtyInput.value = +qtyInput.value - 1;
    });
    document.getElementById('qtyPlus')?.addEventListener('click', () => {
        if (qtyInput) {
            const max = +qtyInput.max || 99;
            if (+qtyInput.value < max) qtyInput.value = +qtyInput.value + 1;
        }
    });

    /* ── Custom Tab Switcher ──────────────────── */
    document.querySelectorAll('.product-tab-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const target = this.dataset.target;
            document.querySelectorAll('.product-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.product-tab-content').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(target)?.classList.add('active');
        });
    });

    /* ── Review Rating Star Picker ────────────── */
    const ratePickers = document.querySelectorAll('.star-picker');
    ratePickers.forEach(pickerWrap => {
        const stars = pickerWrap.querySelectorAll('[data-star]');
        const input = pickerWrap.nextElementSibling;
        stars.forEach(star => {
            star.addEventListener('mouseenter', function () {
                const val = +this.dataset.star;
                stars.forEach((s, i) => s.classList.toggle('hovered', i < val));
            });
            star.addEventListener('click', function () {
                const val = +this.dataset.star;
                if (input) input.value = val;
                stars.forEach((s, i) => s.classList.toggle('selected', i < val));
            });
        });
        pickerWrap.addEventListener('mouseleave', () => stars.forEach(s => s.classList.remove('hovered')));
    });

    /* ── Sticky "Add to Cart" bar on mobile ────── */
    const stickyBar = document.getElementById('stickyAddBar');
    const productInfo = document.querySelector('.product-info-panel');
    if (stickyBar && productInfo) {
        const io = new IntersectionObserver(entries => {
            stickyBar.classList.toggle('show', !entries[0].isIntersecting);
        }, { threshold: 0 });
        io.observe(productInfo);
    }

})();
