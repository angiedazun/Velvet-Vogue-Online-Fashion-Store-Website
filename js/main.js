// ============================================
// Velvet Vogue - Main JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function () {

    // ---- PRELOADER ----
    const preloader = document.getElementById('preloader');
    if (preloader) {
        setTimeout(() => preloader.classList.add('hidden'), 1800);
    }

    // ---- NAVBAR SCROLL ----
    const navbar = document.querySelector('.navbar-main');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });
    }

    // ---- MOBILE MENU ----
    const hamburger = document.querySelector('.nav-hamburger');
    const mobileMenu = document.querySelector('.mobile-menu');
    if (hamburger && mobileMenu) {
        hamburger.addEventListener('click', () => {
            mobileMenu.classList.toggle('open');
            const icon = hamburger.querySelector('i');
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        });
    }

    // ---- BACK TO TOP ----
    const btt = document.getElementById('backToTop');
    if (btt) {
        window.addEventListener('scroll', () => {
            btt.classList.toggle('show', window.scrollY > 400);
        });
        btt.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    // ---- SCROLL ANIMATIONS ----
    const animEls = document.querySelectorAll('.animate-on-scroll');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    animEls.forEach(el => observer.observe(el));

    // ---- PRODUCT FILTERS ----
    const filterBtns = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.product-card[data-category]');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const cat = btn.dataset.filter;
            productCards.forEach(card => {
                const show = cat === 'all' || card.dataset.category === cat;
                card.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                if (show) {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                    card.style.display = 'block';
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        if (card.style.opacity === '0') card.style.display = 'none';
                    }, 300);
                }
            });
        });
    });

    // ---- WISHLIST TOGGLE ----
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const pid = this.dataset.id;
            const icon = this.querySelector('i');
            fetch(`php/wishlist.php?action=toggle&id=${pid}`)
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'added') {
                        this.classList.add('active');
                        icon.classList.replace('far', 'fas');
                        showToast('Added to Wishlist ❤️', 'success');
                    } else if (data.status === 'removed') {
                        this.classList.remove('active');
                        icon.classList.replace('fas', 'far');
                        showToast('Removed from Wishlist', 'info');
                    } else if (data.status === 'login') {
                        showToast('Please login to add to wishlist', 'error');
                        setTimeout(() => window.location = 'login.php', 1500);
                    }
                })
                .catch(() => showToast('Something went wrong', 'error'));
        });
    });

    // ---- QUICK ADD TO CART ----
    document.querySelectorAll('.quick-add-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const pid = this.dataset.id;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            fetch(`php/cart.php?action=add&id=${pid}&qty=1`)
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.innerHTML = '<i class="fas fa-check"></i> Added!';
                        updateCartBadge(data.count);
                        showToast(data.message || 'Item added to cart!', 'success');
                        setTimeout(() => this.innerHTML = '<i class="fas fa-shopping-bag me-1"></i> Quick Add', 1500);
                    } else {
                        showToast(data.message || 'Error', 'error');
                        this.innerHTML = '<i class="fas fa-shopping-bag me-1"></i> Quick Add';
                    }
                });
        });
    });

    // ---- CART QUANTITY CONTROLS ----
    document.querySelectorAll('.qty-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const action = this.dataset.action;
            const itemId = this.dataset.item;
            const display = this.closest('.qty-control').querySelector('.qty-display');
            let qty = parseInt(display.textContent);
            if (action === 'plus') qty++;
            else if (action === 'minus' && qty > 1) qty--;
            display.textContent = qty;
            updateCartItem(itemId, qty);
        });
    });

    // ---- CART REMOVE ----
    document.querySelectorAll('.remove-cart-item').forEach(btn => {
        btn.addEventListener('click', function () {
            const itemId = this.dataset.item;
            if (confirm('Remove this item from cart?')) {
                fetch(`php/cart.php?action=remove&item=${itemId}`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const row = document.getElementById(`cart-item-${itemId}`);
                            if (row) row.remove();
                            updateCartBadge(data.count);
                            updateCartTotals(data.subtotal, data.total);
                            showToast('Item removed from cart', 'success');
                        }
                    });
            }
        });
    });

    // ---- SIZE SELECTOR ----
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            this.closest('.size-grid').querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const sizeInput = document.getElementById('selectedSize');
            if (sizeInput) sizeInput.value = this.dataset.size;
        });
    });

    // ---- COLOR SELECTOR ----
    document.querySelectorAll('.color-swatch').forEach(sw => {
        sw.addEventListener('click', function () {
            this.closest('.color-grid').querySelectorAll('.color-swatch').forEach(s => s.classList.remove('active'));
            this.classList.add('active');
            const colorInput = document.getElementById('selectedColor');
            if (colorInput) colorInput.value = this.dataset.color;
        });
    });

    // ---- PRODUCT GALLERY ----
    document.querySelectorAll('.product-thumbnail').forEach(thumb => {
        thumb.addEventListener('click', function () {
            document.querySelectorAll('.product-thumbnail').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const mainImg = document.querySelector('.product-main-img img');
            if (mainImg) mainImg.src = this.dataset.img;
        });
    });

    // ---- NEWSLETTER FORM ----
    const newsletterForm = document.querySelector('.newsletter-form-js');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            const btn = this.querySelector('button');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
                showToast('Thank you for subscribing! 🎉', 'success');
                this.reset();
            }, 1200);
        });
    }

    // ---- COUNTER ANIMATION ----
    const counters = document.querySelectorAll('.counter-num');
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = parseInt(entry.target.dataset.target);
                animateCounter(entry.target, target);
                counterObserver.unobserve(entry.target);
            }
        });
    });
    counters.forEach(c => counterObserver.observe(c));

    // ---- SEARCH BAR ----
    const searchToggle = document.getElementById('searchToggle');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchClose = document.getElementById('searchClose');
    if (searchToggle && searchOverlay) {
        searchToggle.addEventListener('click', () => {
            searchOverlay.classList.toggle('active');
            if (searchOverlay.classList.contains('active')) {
                document.getElementById('searchInput')?.focus();
            }
        });
        searchClose?.addEventListener('click', () => searchOverlay.classList.remove('active'));
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') searchOverlay.classList.remove('active');
        });
    }

    // Trigger search on enter
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Enter' && e.target.value.trim()) {
                window.location.href = `products.php?search=${encodeURIComponent(e.target.value.trim())}`;
            }
        });
    }

    // ---- COUPON CODE ----
    const couponBtn = document.getElementById('applyCoupon');
    if (couponBtn) {
        couponBtn.addEventListener('click', () => {
            const code = document.getElementById('couponCode').value.trim().toUpperCase();
            const coupons = { 'VELVET10': 10, 'VOGUE20': 20, 'FASHION15': 15 };
            if (coupons[code]) {
                showToast(`Coupon applied! ${coupons[code]}% discount`, 'success');
                document.getElementById('discountRow').style.display = 'flex';
                document.getElementById('discountAmt').textContent = `${coupons[code]}%`;
            } else {
                showToast('Invalid coupon code', 'error');
            }
        });
    }

    // ---- PASSWORD TOGGLE ----
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            if (input) {
                const isPass = input.type === 'password';
                input.type = isPass ? 'text' : 'password';
                this.querySelector('i').classList.toggle('fa-eye', !isPass);
                this.querySelector('i').classList.toggle('fa-eye-slash', isPass);
            }
        });
    });

    // ---- HERO PARTICLES ----
    const particlesContainer = document.querySelector('.hero-particles');
    if (particlesContainer) {
        for (let i = 0; i < 20; i++) {
            const p = document.createElement('div');
            p.className = 'particle';
            const size = Math.random() * 6 + 2;
            p.style.cssText = `
                width: ${size}px;
                height: ${size}px;
                top: ${Math.random() * 100}%;
                left: ${Math.random() * 100}%;
                animation-delay: ${Math.random() * 8}s;
                animation-duration: ${8 + Math.random() * 6}s;
            `;
            particlesContainer.appendChild(p);
        }
    }

    // ---- IMAGE ZOOM ON HOVER (Product Detail) ----
    const zoomImg = document.querySelector('.product-main-img img');
    const zoomWrapper = document.querySelector('.product-main-img');
    if (zoomImg && zoomWrapper) {
        zoomWrapper.addEventListener('mousemove', (e) => {
            const rect = zoomWrapper.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            zoomImg.style.transformOrigin = `${x}% ${y}%`;
            zoomImg.style.transform = 'scale(1.5)';
        });
        zoomWrapper.addEventListener('mouseleave', () => {
            zoomImg.style.transform = 'scale(1)';
        });
    }

    // ---- ADMIN SIDEBAR TOGGLE (mobile) ----
    const adminToggle = document.getElementById('adminSidebarToggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    if (adminToggle && adminSidebar) {
        adminToggle.addEventListener('click', () => {
            adminSidebar.classList.toggle('open');
        });
    }

    // ---- SHOW ACTIVE NAV LINK ----
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-links a, .mobile-menu a').forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPage || (currentPage === '' && href === 'index.php')) {
            link.classList.add('active');
        }
    });

    // ---- BROKEN IMAGE FALLBACK ----
    // If any product image fails to load, swap in a reliable fashion placeholder
    const imgFallbacks = [
        'https://images.unsplash.com/photo-1566479179817-c0de81f10f38?w=400&h=530&fit=crop',
        'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=400&h=530&fit=crop',
        'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=400&h=530&fit=crop',
        'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400&h=530&fit=crop',
    ];
    let _fbIdx = 0;
    document.querySelectorAll('.product-card-image img, .product-card img').forEach(function(img) {
        function applyFallback() {
            if (!this.dataset.errored) {
                this.dataset.errored = '1';
                this.src = imgFallbacks[_fbIdx % imgFallbacks.length];
                _fbIdx++;
            }
        }
        img.addEventListener('error', applyFallback);
        // Also handle images already flagged broken at DOM load time
        if (img.complete && img.naturalWidth === 0) applyFallback.call(img);
    });

});

// ============================================
// HELPER FUNCTIONS
// ============================================

function showToast(message, type = 'success') {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    const icons = { success: 'fa-check-circle', error: 'fa-times-circle', info: 'fa-info-circle' };
    const colors = { success: '#27ae60', error: '#e74c3c', info: '#6C3483' };
    toast.className = `vv-toast ${type}`;
    toast.innerHTML = `<i class="fas ${icons[type] || icons.success}" style="color:${colors[type]};font-size:1.1rem"></i> ${message}`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideInRight 0.3s ease reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

function updateCartBadge(count) {
    const badges = document.querySelectorAll('.cart-badge');
    badges.forEach(b => {
        b.textContent = count;
        b.style.display = count > 0 ? 'flex' : 'none';
    });
}

function updateCartItem(itemId, qty) {
    fetch(`php/cart.php?action=update&item=${itemId}&qty=${qty}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                updateCartTotals(data.subtotal, data.total);
                const priceEl = document.querySelector(`#cart-item-${itemId} .cart-item-price`);
                if (priceEl) priceEl.textContent = data.item_total;
            }
        });
}

function updateCartTotals(subtotal, total) {
    const subEl = document.getElementById('cartSubtotal');
    const totalEl = document.getElementById('cartTotal');
    if (subEl) subEl.textContent = subtotal;
    if (totalEl) totalEl.textContent = total;
}

function animateCounter(el, target) {
    let current = 0;
    const increment = target / 60;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            el.textContent = target.toLocaleString() + (el.dataset.suffix || '');
            clearInterval(timer);
        } else {
            el.textContent = Math.floor(current).toLocaleString() + (el.dataset.suffix || '');
        }
    }, 25);
}

function confirmDelete(message) {
    return confirm(message || 'Are you sure you want to delete this?');
}

// Rating Stars Display
function createStars(rating) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(rating)) html += '★';
        else if (i - 0.5 <= rating) html += '⭐';
        else html += '☆';
    }
    return html;
}
