/* js/products.js — Products Listing Page Features */
(function () {
    'use strict';

    /* ── View Toggle (Grid / List) ────────────── */
    const viewBtns = document.querySelectorAll('.view-btn');
    const productsGrid = document.getElementById('productsGrid');
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const view = this.dataset.view;
            if (productsGrid) {
                productsGrid.classList.toggle('products-list-view', view === 'list');
            }
        });
    });

    /* ── Sort Select Auto Submit ──────────────── */
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    }

    /* ── Price Range Dual Input ───────────────── */
    const priceMin = document.getElementById('priceMin');
    const priceMax = document.getElementById('priceMax');
    if (priceMin && priceMax) {
        const applyPrice = () => {
            const url = new URL(window.location.href);
            if (priceMin.value) url.searchParams.set('min_price', priceMin.value);
            if (priceMax.value) url.searchParams.set('max_price', priceMax.value);
            window.location.href = url.toString();
        };
        [priceMin, priceMax].forEach(el => {
            el.addEventListener('keydown', e => { if (e.key === 'Enter') applyPrice(); });
        });
        document.getElementById('applyPriceBtn')?.addEventListener('click', applyPrice);
    }

    /* ── Mobile Filter Sidebar Toggle ────────── */
    const filterToggleBtn = document.getElementById('filterToggleBtn');
    const filterSidebar   = document.getElementById('filterSidebar');
    const filterOverlay   = document.getElementById('filterOverlay');

    filterToggleBtn?.addEventListener('click', () => {
        filterSidebar?.classList.add('open');
        filterOverlay?.classList.add('show');
        document.body.style.overflow = 'hidden';
    });

    const closeFilter = () => {
        filterSidebar?.classList.remove('open');
        filterOverlay?.classList.remove('show');
        document.body.style.overflow = '';
    };

    filterOverlay?.addEventListener('click', closeFilter);
    document.getElementById('filterCloseBtn')?.addEventListener('click', closeFilter);

    /* ── Quick Filter Tags Active State ──────── */
    document.querySelectorAll('.quick-filter-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const url = new URL(window.location.href);
            url.searchParams.set('quick_filter', this.dataset.filter);
            window.location.href = url.toString();
        });
    });

    /* ── Product Card Hover — Lazy Load Images ─ */
    const lazyImgs = document.querySelectorAll('img[data-src]');
    if (lazyImgs.length) {
        const lazyIO = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.src = e.target.dataset.src;
                    lazyIO.unobserve(e.target);
                }
            });
        });
        lazyImgs.forEach(img => lazyIO.observe(img));
    }

    /* ── Wishlist Toggle on Product Card ─────── */
    document.querySelectorAll('.product-wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            icon?.classList.toggle('fas');
            icon?.classList.toggle('far');
        });
    });

})();
