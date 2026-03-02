<?php
$pageTitle = "Home – Premium Fashion Store";
require_once 'config/db.php';

// Featured Products
$featuredRes = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 ORDER BY p.id DESC LIMIT 8");
$featuredProducts = $featuredRes->fetch_all(MYSQLI_ASSOC);

// Trending Products
$trendRes = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.trending = 1 ORDER BY p.id DESC LIMIT 4");
$trendProducts = $trendRes->fetch_all(MYSQLI_ASSOC);

// Categories
$catRes = $conn->query("SELECT * FROM categories LIMIT 5");
$categories = $catRes->fetch_all(MYSQLI_ASSOC);

$extraCSS = '<link rel="stylesheet" href="css/index.css">';
$extraJS  = '<script src="js/index.js"></script>';
include 'includes/header.php';
?>

<!-- ============ HERO SECTION ============ -->
<section class="hero-section">
    <div class="hero-particles" id="heroParticles"></div>
    <div class="hero-glow-orb hero-orb-1"></div>
    <div class="hero-glow-orb hero-orb-2"></div>

    <div class="container">
        <div class="row align-items-center" style="min-height:100vh;padding-top:90px;padding-bottom:50px;">

            <!-- ── LEFT CONTENT ─────────────────── -->
            <div class="col-lg-6 col-xl-5">
                <div class="hero-content">

                    <!-- Season badge -->
                    <div class="hero-badge">
                        <span class="hero-badge-pulse"></span>
                        <i class="fas fa-bolt me-1"></i> SS 2026 — New Collection Live
                    </div>

                    <!-- Headline -->
                    <h1 class="hero-headline">
                        Dress to<br>
                        <span class="hero-headline-gold">Impress.</span><br>
                        Live in <em>Style.</em>
                    </h1>

                    <!-- Description -->
                    <p class="hero-description">
                        Discover curated fashion that speaks your language. From timeless classics to bold contemporary pieces — Velvet Vogue is your ultimate style destination.
                    </p>

                    <!-- Search pill -->
                    <div class="hero-search-wrap">
                        <i class="fas fa-search hero-search-ico"></i>
                        <input type="text" id="heroSearchInput" placeholder="Search dresses, blazers, accessories…" class="hero-search-field"
                            onkeydown="if(event.key==='Enter') window.location='products.php?search='+encodeURIComponent(this.value)">
                        <button class="hero-search-go" onclick="window.location='products.php?search='+encodeURIComponent(document.getElementById('heroSearchInput').value)">
                            Search
                        </button>
                    </div>

                    <!-- Popular tags -->
                    <div class="hero-tag-pills">
                        <span class="hero-tag-label">Trending:</span>
                        <a href="products.php?search=dress" class="hero-pill">Dresses</a>
                        <a href="products.php?search=blazer" class="hero-pill">Blazers</a>
                        <a href="products.php?category=sale" class="hero-pill hero-pill-hot">🔥 Sale</a>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="hero-actions">
                        <a href="products.php" class="btn-vv btn-gold">
                            <i class="fas fa-shopping-bag me-2"></i>Shop Now
                        </a>
                        <a href="products.php?new=1" class="btn-vv btn-outline-white">
                            New Arrivals <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>

                    <!-- Social proof row -->
                    <div class="hero-social-proof">
                        <div class="hero-avatar-stack">
                            <img src="https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?w=40&h=40&fit=crop&q=80" alt="">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=40&h=40&fit=crop&q=80" alt="">
                            <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=40&h=40&fit=crop&q=80" alt="">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=40&h=40&fit=crop&q=80" alt="">
                            <div class="avatar-more">+5k</div>
                        </div>
                        <div class="hero-proof-text">
                            <div class="hero-proof-stars">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <strong>4.9</strong>
                            </div>
                            <div class="hero-proof-count">Loved by <strong>5,000+</strong> happy customers</div>
                        </div>
                    </div>

                    <!-- Stats -->
                    <div class="hero-stats">
                        <div class="hero-stat-item">
                            <span class="hero-stat-num counter-num" data-target="1200" data-suffix="+">0+</span>
                            <span class="hero-stat-label">Products</span>
                        </div>
                        <div class="hero-stat-divider"></div>
                        <div class="hero-stat-item">
                            <span class="hero-stat-num counter-num" data-target="98" data-suffix="%">0%</span>
                            <span class="hero-stat-label">Satisfaction</span>
                        </div>
                        <div class="hero-stat-divider"></div>
                        <div class="hero-stat-item">
                            <span class="hero-stat-num">30</span>
                            <span class="hero-stat-label">Day Returns</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ── RIGHT IMAGE ─────────────────── -->
            <div class="col-lg-6 col-xl-7 mt-5 mt-lg-0">
                <div class="hero-image-wrap">

                    <!-- Decorative blob -->
                    <div class="hero-img-blob"></div>
                    <div class="hero-img-ring"></div>

                    <!-- Main fashion image -->
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=620&h=800&fit=crop&q=90"
                         alt="Fashion Model" class="hero-main-img" loading="eager">

                    <!-- Gradient foot fade -->
                    <div class="hero-img-fade"></div>

                    <!-- Float Card — Flash Sale -->
                    <div class="hero-fc hero-fc-sale animate-float-a">
                        <div class="hfc-left">
                            <div class="hfc-icon-wrap hfc-fire">🔥</div>
                        </div>
                        <div class="hfc-right">
                            <div class="hfc-title">Flash Sale</div>
                            <div class="hfc-value">Up to 70% Off</div>
                        </div>
                    </div>

                    <!-- Float Card — Rating -->
                    <div class="hero-fc hero-fc-rate animate-float-b">
                        <div class="hfc-stars-row">
                            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                        </div>
                        <div class="hfc-rate-num">4.9 / 5.0</div>
                        <div class="hfc-rate-sub">2,400+ reviews</div>
                    </div>

                    <!-- Float Card — Shipping -->
                    <div class="hero-fc hero-fc-ship animate-float-c">
                        <div class="hfc-icon-wrap hfc-truck">🚚</div>
                        <div class="hfc-right">
                            <div class="hfc-title">Free Shipping</div>
                            <div class="hfc-sub">Orders over Rs. 3,000</div>
                        </div>
                    </div>

                    <!-- Mini product strip -->
                    <div class="hero-mini-strip">
                        <div class="hero-mini-label">Also loved</div>
                        <a href="products.php?category=women" class="hero-mini-img">
                            <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=70&h=90&fit=crop" alt="">
                        </a>
                        <a href="products.php?category=women" class="hero-mini-img">
                            <img src="https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=70&h=90&fit=crop" alt="">
                        </a>
                        <a href="products.php?category=sale" class="hero-mini-img">
                            <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=70&h=90&fit=crop" alt="">
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Scroll indicator -->
    <div class="hero-scroll-hint">
        <div class="hero-scroll-mouse"><div class="hero-scroll-wheel"></div></div>
        <span>Scroll</span>
    </div>
</section>

<!-- ============ MARQUEE BANNER ============ -->
<div class="marquee-section">
    <div class="marquee-track">
        <?php
        $items = ['✦ FREE SHIPPING OVER RS. 3,000', '✦ NEW ARRIVALS EVERY WEEK', '✦ UPTO 70% OFF ON SALE', '✦ PREMIUM QUALITY FABRICS', '✦ EASY RETURNS IN 30 DAYS', '✦ SECURE PAYMENTS'];
        $fullItems = array_merge($items, $items); // Duplicate for seamless loop
        foreach ($fullItems as $item): ?>
            <span class="marquee-item"><span>✦</span><?= ltrim($item, '✦ ') ?></span>
        <?php endforeach; ?>
    </div>
</div>

<!-- ============ CATEGORIES SECTION ============ -->
<section class="categories-section">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll">
            <span class="section-tag">Collections</span>
            <h2 class="section-title">Shop by <span>Category</span></h2>
            <div class="divider-gold center"></div>
            <p class="section-subtitle">Explore our handpicked collections designed for every style and occasion.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-5 col-md-6 animate-on-scroll">
                <a href="products.php?category=women" class="category-card cat-women" style="aspect-ratio:2/3;">
                    <img src="https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=600&h=900&fit=crop" alt="Women" loading="lazy">
                    <div class="category-card-overlay">
                        <span class="category-card-title">Women</span>
                        <span class="category-card-count">500+ Products</span>
                        <span class="category-card-btn">Explore <i class="fas fa-arrow-right ms-1"></i></span>
                    </div>
                </a>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="d-flex flex-column gap-4 h-100">
                    <a href="products.php?category=men" class="category-card cat-men animate-on-scroll delay-100" style="flex:1;">
                        <img src="https://images.unsplash.com/photo-1617137968427-85924c800a22?w=400&h=300&fit=crop" alt="Men" loading="lazy">
                        <div class="category-card-overlay">
                            <span class="category-card-title">Men</span>
                            <span class="category-card-count">300+ Products</span>
                            <span class="category-card-btn">Explore <i class="fas fa-arrow-right ms-1"></i></span>
                        </div>
                    </a>
                    <a href="products.php?category=kids" class="category-card animate-on-scroll delay-200" style="flex:1;">
                        <img src="https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?w=400&h=300&fit=crop" alt="Kids" loading="lazy">
                        <div class="category-card-overlay">
                            <span class="category-card-title">Kids</span>
                            <span class="category-card-count">200+ Products</span>
                            <span class="category-card-btn">Explore <i class="fas fa-arrow-right ms-1"></i></span>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="d-flex flex-column gap-4 h-100">
                    <a href="products.php?category=accessories" class="category-card cat-accessories animate-on-scroll delay-300" style="flex:1.2;">
                        <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500&h=400&fit=crop" alt="Accessories" loading="lazy">
                        <div class="category-card-overlay">
                            <span class="category-card-title">Accessories</span>
                            <span class="category-card-count">150+ Products</span>
                            <span class="category-card-btn">Explore <i class="fas fa-arrow-right ms-1"></i></span>
                        </div>
                    </a>
                    <a href="products.php?category=sale" class="category-card animate-on-scroll delay-400" style="flex:0.8;background:linear-gradient(135deg,#8B0000,#c0392b);">
                        <div class="category-card-overlay" style="justify-content:center;align-items:center;background:rgba(139,0,0,0.7);">
                            <div class="text-center">
                                <div style="font-size:3rem;font-weight:900;color:#fff;font-family:'Playfair Display',serif;">SALE</div>
                                <div style="color:#FFD700;font-size:1.5rem;font-weight:700;">Up to 70% OFF</div>
                                <span class="category-card-btn" style="justify-content:center;opacity:1;transform:none;margin-top:8px;">Shop Sale <i class="fas fa-fire ms-1"></i></span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ FEATURED PRODUCTS ============ -->
<section class="products-section">
    <div class="container">
        <div class="text-center mb-2 animate-on-scroll">
            <span class="section-tag">Handpicked</span>
            <h2 class="section-title">Featured <span>Collection</span></h2>
            <div class="divider-gold center"></div>
        </div>

        <!-- Filter Buttons -->
        <div class="product-filters animate-on-scroll">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="women">Women</button>
            <button class="filter-btn" data-filter="men">Men</button>
            <button class="filter-btn" data-filter="accessories">Accessories</button>
            <button class="filter-btn" data-filter="new_arrival">New Arrivals</button>
        </div>

        <div class="product-grid">
            <?php if (empty($featuredProducts)): ?>
            <!-- Placeholder products when DB is empty -->
            <?php
            $placeholders = [
                ['name'=>'Elegant Velvet Dress','cat'=>'women','price'=>8500,'sale_price'=>6800,'rating'=>4.8,'reviews'=>124,'trending'=>1,'new_arrival'=>1],
                ['name'=>'Silk Floral Maxi','cat'=>'women','price'=>12000,'sale_price'=>null,'rating'=>4.6,'reviews'=>89,'trending'=>0,'new_arrival'=>1],
                ['name'=>'Power Blazer Set','cat'=>'women','price'=>9500,'sale_price'=>7500,'rating'=>4.7,'reviews'=>67,'trending'=>1,'new_arrival'=>1],
                ['name'=>'Classic Oxford Shirt','cat'=>'men','price'=>4500,'sale_price'=>3500,'rating'=>4.5,'reviews'=>78,'trending'=>0,'new_arrival'=>1],
                ['name'=>'Leather Biker Jacket','cat'=>'men','price'=>18000,'sale_price'=>14000,'rating'=>4.9,'reviews'=>203,'trending'=>1,'new_arrival'=>0],
                ['name'=>'Gold Chain Necklace','cat'=>'accessories','price'=>2800,'sale_price'=>null,'rating'=>4.7,'reviews'=>91,'trending'=>1,'new_arrival'=>1],
                ['name'=>'Linen Wide Leg Pants','cat'=>'women','price'=>4800,'sale_price'=>null,'rating'=>4.3,'reviews'=>33,'trending'=>1,'new_arrival'=>1],
                ['name'=>'Designer Sunglasses','cat'=>'accessories','price'=>3500,'sale_price'=>2800,'rating'=>4.5,'reviews'=>44,'trending'=>0,'new_arrival'=>1],
            ];
            $imgs = [
                'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&h=530&fit=crop',
                'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400&h=530&fit=crop',
                'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=400&h=530&fit=crop',
                'https://images.unsplash.com/photo-1603252109303-2751441dd157?w=400&h=530&fit=crop',
                'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&h=530&fit=crop',
                'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=400&h=530&fit=crop',
                'https://images.unsplash.com/photo-1509551388413-e18d0ac5d495?w=400&h=530&fit=crop',
                'https://images.unsplash.com/photo-1583744946564-b52ac1c389c8?w=400&h=530&fit=crop',
            ];
            foreach ($placeholders as $i => $p):
                $slug = strtolower(str_replace(' ', '-', $p['name']));
                $discount = $p['sale_price'] ? round((($p['price'] - $p['sale_price']) / $p['price']) * 100) : 0;
            ?>
            <div class="product-card" data-category="<?= $p['cat'] ?>">
                <div class="product-card-image">
                    <img src="<?= $imgs[$i] ?>" alt="<?= $p['name'] ?>" loading="lazy">
                    <!-- Badges -->
                    <div class="product-badges">
                        <?php if($p['new_arrival']): ?><span class="badge-pill badge-new">New</span><?php endif; ?>
                        <?php if($p['sale_price']): ?><span class="badge-pill badge-sale">-<?= $discount ?>%</span><?php endif; ?>
                        <?php if($p['trending']): ?><span class="badge-pill badge-trend">Trending</span><?php endif; ?>
                    </div>
                    <!-- Actions -->
                    <div class="product-card-actions">
                        <button class="product-action-btn wishlist-btn" data-id="<?= $i+1 ?>" title="Wishlist"><i class="far fa-heart"></i></button>
                        <a href="product-detail.php?slug=<?= $slug ?>" class="product-action-btn" title="Quick View"><i class="fas fa-eye"></i></a>
                    </div>
                    <!-- Quick Add -->
                    <button class="product-card-quick-add quick-add-btn" data-id="<?= $i+1 ?>">
                        <i class="fas fa-shopping-bag me-1"></i> Quick Add
                    </button>
                </div>
                <div class="product-card-body">
                    <div class="product-card-cat"><?= ucfirst($p['cat']) ?></div>
                    <a href="product-detail.php?slug=<?= $slug ?>" class="text-decoration-none">
                        <h3 class="product-card-name"><?= $p['name'] ?></h3>
                    </a>
                    <div class="product-rating">
                        <span class="stars">
                            <?php for($s=1;$s<=5;$s++) echo ($s <= floor($p['rating'])) ? '★' : '☆'; ?>
                        </span>
                        <span class="rating-count">(<?= $p['reviews'] ?>)</span>
                    </div>
                    <div class="product-card-price">
                        <span class="price-current"><?= formatPrice($p['sale_price'] ?? $p['price']) ?></span>
                        <?php if($p['sale_price']): ?>
                        <span class="price-old"><?= formatPrice($p['price']) ?></span>
                        <span class="price-off">-<?= $discount ?>%</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php else: foreach ($featuredProducts as $i => $p):
                $fallbackImgs = [
                    'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&h=530&fit=crop',
                    'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400&h=530&fit=crop',
                    'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=400&h=530&fit=crop',
                    'https://images.unsplash.com/photo-1603252109303-2751441dd157?w=400&h=530&fit=crop',
                    'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&h=530&fit=crop',
                    'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=400&h=530&fit=crop',
                    'https://images.unsplash.com/photo-1509551388413-e18d0ac5d495?w=400&h=530&fit=crop',
                    'https://images.unsplash.com/photo-1583744946564-b52ac1c389c8?w=400&h=530&fit=crop',
                ];
                $imgSrc = !empty($p['images']) ? $p['images'] : $fallbackImgs[$i % count($fallbackImgs)];
                $discount = ($p['sale_price'] && $p['price'] > 0) ? round((($p['price'] - $p['sale_price']) / $p['price']) * 100) : 0;
            ?>
            <div class="product-card" data-category="<?= htmlspecialchars($p['cat_name'] ?? '') ?>">
                <div class="product-card-image">
                    <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                    <div class="product-badges">
                        <?php if($p['new_arrival']): ?><span class="badge-pill badge-new">New</span><?php endif; ?>
                        <?php if($p['sale_price']): ?><span class="badge-pill badge-sale">-<?= $discount ?>%</span><?php endif; ?>
                        <?php if($p['trending']): ?><span class="badge-pill badge-trend">Trending</span><?php endif; ?>
                    </div>
                    <div class="product-card-actions">
                        <button class="product-action-btn wishlist-btn" data-id="<?= $p['id'] ?>"><i class="far fa-heart"></i></button>
                        <a href="product-detail.php?slug=<?= $p['slug'] ?>" class="product-action-btn"><i class="fas fa-eye"></i></a>
                    </div>
                    <button class="product-card-quick-add quick-add-btn" data-id="<?= $p['id'] ?>">
                        <i class="fas fa-shopping-bag me-1"></i> Quick Add
                    </button>
                </div>
                <div class="product-card-body">
                    <div class="product-card-cat"><?= htmlspecialchars($p['cat_name'] ?? 'Fashion') ?></div>
                    <a href="product-detail.php?slug=<?= $p['slug'] ?>" class="text-decoration-none">
                        <h3 class="product-card-name"><?= htmlspecialchars($p['name']) ?></h3>
                    </a>
                    <div class="product-rating">
                        <span class="stars">
                            <?php for($s=1;$s<=5;$s++) echo ($s <= floor($p['rating'])) ? '★' : '☆'; ?>
                        </span>
                        <span class="rating-count">(<?= $p['reviews_count'] ?>)</span>
                    </div>
                    <div class="product-card-price">
                        <span class="price-current"><?= formatPrice($p['sale_price'] ?? $p['price']) ?></span>
                        <?php if($p['sale_price']): ?>
                        <span class="price-old"><?= formatPrice($p['price']) ?></span>
                        <span class="price-off">-<?= $discount ?>%</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>

        <div class="text-center mt-5 animate-on-scroll">
            <a href="products.php" class="btn-vv btn-primary-vv">
                <i class="fas fa-th-large me-2"></i> View All Products
            </a>
        </div>
    </div>
</section>

<!-- ============ PROMO BANNER ============ -->
<section style="padding:0 0 100px;">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 animate-on-scroll">
                <div style="border-radius:20px;overflow:hidden;position:relative;min-height:280px;background:linear-gradient(135deg,#1a0a2e,#4a2060);">
                    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&h=300&fit=crop" alt="Women Sale" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;opacity:0.4;">
                    <div style="position:relative;z-index:1;padding:48px 40px;">
                        <span style="font-size:12px;letter-spacing:3px;color:#D4AF37;text-transform:uppercase;">Women's Collection</span>
                        <h3 style="font-family:'Playfair Display',serif;font-size:2.5rem;color:white;margin:12px 0;">Up to <span style="color:#D4AF37;">50% Off</span></h3>
                        <p style="color:rgba(255,255,255,0.7);margin-bottom:24px;">Exclusive deals on premium women's fashion</p>
                        <a href="products.php?category=women" class="btn-vv btn-gold">Shop Women <i class="fas fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 animate-on-scroll delay-200">
                <div style="border-radius:20px;overflow:hidden;position:relative;min-height:280px;background:linear-gradient(135deg,#0a1628,#1a3a5c);">
                    <img src="https://images.unsplash.com/photo-1617137968427-85924c800a22?w=600&h=300&fit=crop" alt="Men Collection" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0;opacity:0.35;">
                    <div style="position:relative;z-index:1;padding:48px 40px;">
                        <span style="font-size:12px;letter-spacing:3px;color:#D4AF37;text-transform:uppercase;">Men's Collection</span>
                        <h3 style="font-family:'Playfair Display',serif;font-size:2.5rem;color:white;margin:12px 0;">New <span style="color:#D4AF37;">Arrivals</span></h3>
                        <p style="color:rgba(255,255,255,0.7);margin-bottom:24px;">Fresh styles for the modern gentleman</p>
                        <a href="products.php?category=men" class="btn-vv btn-gold">Shop Men <i class="fas fa-arrow-right ms-2"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ FEATURES SECTION ============ -->
<section class="features-section">
    <div class="container">
        <div class="row g-4">
            <?php
            $features = [
                ['icon'=>'fa-truck','title'=>'Free Delivery','desc'=>'Free shipping on orders over Rs. 3,000 across Pakistan'],
                ['icon'=>'fa-undo','title'=>'Easy Returns','desc'=>'30-day hassle-free return policy on all products'],
                ['icon'=>'fa-shield-alt','title'=>'Secure Payment','desc'=>'100% secure payment with SSL encryption'],
                ['icon'=>'fa-headset','title'=>'24/7 Support','desc'=>'Round the clock customer support via chat & call'],
            ];
            foreach ($features as $f): ?>
            <div class="col-lg-3 col-sm-6 animate-on-scroll">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas <?= $f['icon'] ?>"></i></div>
                    <h5 class="feature-title"><?= $f['title'] ?></h5>
                    <p class="feature-desc"><?= $f['desc'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="testimonials-section">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll">
            <span class="section-tag">Reviews</span>
            <h2 class="section-title">What Our <span>Customers</span> Say</h2>
            <div class="divider-gold center"></div>
        </div>
        <div class="row g-4">
            <?php
            $testimonials = [
                ['name'=>'Aisha Khan','role'=>'Fashion Blogger','review'=>'Velvet Vogue has completely transformed my wardrobe! The quality of their pieces is absolutely outstanding. I get compliments everywhere I go.','rating'=>5,'init'=>'AK'],
                ['name'=>'Sara Ahmed','role'=>'Stylist','review'=>'I\'ve shopped at many online stores, but Velvet Vogue stands out. Fast delivery, premium packaging, and the clothes are even better in person!','rating'=>5,'init'=>'SA'],
                ['name'=>'Fatima Ali','role'=>'Entrepreneur','review'=>'The velvet dress I ordered was for my wedding anniversary, and it was perfection. My husband was speechless! Thank you Velvet Vogue.','rating'=>5,'init'=>'FA'],
            ];
            foreach ($testimonials as $t): ?>
            <div class="col-md-4 animate-on-scroll">
                <div class="testimonial-card">
                    <div class="testimonial-quote">"</div>
                    <p class="testimonial-text"><?= $t['review'] ?></p>
                    <div style="color:#D4AF37;font-size:14px;margin-bottom:16px;"><?= str_repeat('★',$t['rating']) ?></div>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar"><?= $t['init'] ?></div>
                        <div>
                            <div class="testimonial-name"><?= $t['name'] ?></div>
                            <div class="testimonial-role"><?= $t['role'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ NEWSLETTER SECTION ============ -->
<section class="newsletter-section">
    <div class="container">
        <div class="row justify-content-center text-center animate-on-scroll">
            <div class="col-lg-7">
                <span class="section-tag" style="background:rgba(212,175,55,0.15);color:#D4AF37;">Newsletter</span>
                <h2 class="section-title" style="color:white;">Stay in <span style="color:#D4AF37;">Style</span></h2>
                <p style="color:rgba(255,255,255,0.6);margin-bottom:36px;">Subscribe to our newsletter and get 10% off your first order, plus exclusive access to new collections and special offers.</p>
                <form class="newsletter-form newsletter-form-js" onsubmit="return false;">
                    <input type="email" class="newsletter-input" placeholder="Enter your email address..." required>
                    <button type="submit" class="btn-vv btn-gold">Subscribe <i class="fas fa-paper-plane ms-1"></i></button>
                </form>
                <small style="color:rgba(255,255,255,0.3);font-size:12px;margin-top:12px;display:block;">No spam, unsubscribe anytime. We respect your privacy.</small>
            </div>
        </div>
    </div>
</section>

<!-- ============ INSTAGRAM SECTION ============ -->
<section style="padding:80px 0;">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll">
            <span class="section-tag">Social</span>
            <h2 class="section-title">Follow Us on <span>Instagram</span></h2>
            <p class="section-subtitle">@velvetvogue — Tag us in your photos for a chance to be featured!</p>
        </div>
        <div class="insta-grid animate-on-scroll">
            <?php
            $instaImgs = [
                'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=300&h=300&fit=crop',
                'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=300&h=300&fit=crop',
                'https://images.unsplash.com/photo-1540221652346-e5dd6b50f3e7?w=300&h=300&fit=crop',
                'https://images.unsplash.com/photo-1487222477894-8943e31ef7b2?w=300&h=300&fit=crop',
                'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=300&h=300&fit=crop',
                'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=300&h=300&fit=crop',
            ];
            foreach ($instaImgs as $img): ?>
            <a href="#" class="insta-item">
                <img src="<?= $img ?>" alt="Instagram" loading="lazy">
                <div class="insta-overlay"><i class="fab fa-instagram"></i></div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="https://instagram.com" target="_blank" class="btn-vv btn-outline-primary-vv">
                <i class="fab fa-instagram me-2"></i> Follow @velvetvogue
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
