<?php
require_once 'config/db.php';

$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : '';
$product = null;

if ($slug) {
    $res = $conn->query("SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = '$slug' LIMIT 1");
    if ($res) $product = $res->fetch_assoc();
}

// Placeholder if not found or DB empty
if (!$product) {
    $product = [
        'id' => 1,
        'name' => 'Elegant Velvet Dress',
        'slug' => 'elegant-velvet-dress',
        'cat_name' => 'Women',
        'cat_slug' => 'women',
        'description' => 'Luxurious deep purple velvet midi dress with cinched waist and flowing skirt. Crafted from premium Italian velvet, this piece features a flattering A-line silhouette, hidden side zipper, and delicate fabric-covered buttons at the back. Perfect for evening occasions, formal events, and special celebrations.',
        'price' => 8500.00,
        'sale_price' => 6800.00,
        'stock' => 25,
        'sizes' => 'XS,S,M,L,XL,XXL',
        'colors' => 'Deep Purple,Wine Red,Midnight Blue,Forest Green',
        'featured' => 1,
        'trending' => 1,
        'new_arrival' => 1,
        'rating' => 4.8,
        'reviews_count' => 124,
    ];
}

$pageTitle = htmlspecialchars($product['name']);

// Related products
$catSlug = $product['cat_slug'] ?? '';
$relRes = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE c.slug = '$catSlug' AND p.id != {$product['id']} LIMIT 4");
$relProducts = $relRes ? $relRes->fetch_all(MYSQLI_ASSOC) : [];

// Reviews
$reviewsRes = $conn->query("SELECT * FROM reviews WHERE product_id = {$product['id']} ORDER BY created_at DESC LIMIT 10");
$reviews = $reviewsRes ? $reviewsRes->fetch_all(MYSQLI_ASSOC) : [];

$sizes = explode(',', $product['sizes'] ?? 'XS,S,M,L,XL,XXL');
$colors = explode(',', $product['colors'] ?? 'Black,White,Red,Blue');

$discount = ($product['sale_price'] && $product['price'] > 0) ? round((($product['price'] - $product['sale_price']) / $product['price']) * 100) : 0;

$_fallbackImgs = [
    "https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=700&h=900&fit=crop",
    "https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=700&h=900&fit=crop",
    "https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=700&h=900&fit=crop",
    "https://images.unsplash.com/photo-1583744946564-b52ac1c389c8?w=700&h=900&fit=crop",
];
$_mainImg = !empty($product['images']) ? $product['images'] : $_fallbackImgs[0];
$productImages = array_merge([$_mainImg], array_filter($_fallbackImgs, fn($img) => $img !== $_mainImg));

$extraCSS = '<link rel="stylesheet" href="css/product-detail.css">';
$extraJS  = '<script src="js/product-detail.js"></script>';
include 'includes/header.php';
?>

<!-- Breadcrumb -->
<div style="background:white;border-bottom:1px solid var(--border);padding:16px 0;">
    <div class="container">
        <div class="breadcrumb-vv" style="--gold:#D4AF37;">
            <a href="index.php" style="color:var(--text-muted);">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <a href="products.php" style="color:var(--text-muted);">Shop</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <a href="products.php?category=<?= $product['cat_slug'] ?? '' ?>" style="color:var(--text-muted);"><?= htmlspecialchars($product['cat_name'] ?? '') ?></a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-current"><?= htmlspecialchars($product['name']) ?></span>
        </div>
    </div>
</div>

<section class="product-detail-section">
    <div class="container">
        <div class="row g-5">

            <!-- Product Gallery -->
            <div class="col-lg-6">
                <div class="d-flex gap-3">
                    <!-- Thumbnails -->
                    <div class="product-thumbnails">
                        <?php foreach ($productImages as $i => $img): ?>
                        <div class="product-thumbnail <?= $i == 0 ? 'active' : '' ?>" data-img="<?= $img ?>">
                            <img src="<?= $img ?>" alt="View <?= $i+1 ?>" loading="lazy">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Main Image -->
                    <div class="product-main-img flex-1" style="cursor:zoom-in;">
                        <img src="<?= $productImages[0] ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="mainProductImg">
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="col-lg-6">
                <div style="padding-left:20px;">
                    <!-- Badges -->
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <?php if($product['new_arrival']): ?><span class="badge-pill badge-new">New Arrival</span><?php endif; ?>
                        <?php if($product['trending']): ?><span class="badge-pill badge-trend">🔥 Trending</span><?php endif; ?>
                        <?php if($product['sale_price']): ?><span class="badge-pill badge-sale">-<?= $discount ?>% OFF</span><?php endif; ?>
                        <?php if($product['stock'] <= 5 && $product['stock'] > 0): ?><span class="badge-pill" style="background:rgba(231,76,60,0.15);color:#e74c3c;">Only <?= $product['stock'] ?> Left!</span><?php endif; ?>
                    </div>

                    <div class="product-card-cat mb-2"><?= htmlspecialchars($product['cat_name'] ?? '') ?></div>
                    <h1 class="product-detail-name"><?= htmlspecialchars($product['name']) ?></h1>

                    <!-- Rating -->
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="product-rating">
                            <span class="stars" style="font-size:1.1rem;"><?php for($s=1;$s<=5;$s++) echo $s<=floor($product['rating'])?'★':'☆'; ?></span>
                            <span class="rating-count"><?= $product['rating'] ?> (<?= $product['reviews_count'] ?> reviews)</span>
                        </div>
                        <span style="color:var(--text-muted);font-size:13px;">|</span>
                        <span style="font-size:13px;color:<?= $product['stock'] > 0 ? 'var(--success)' : 'var(--danger)' ?>;font-weight:600;">
                            <i class="fas fa-circle me-1" style="font-size:8px;"></i>
                            <?= $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
                        </span>
                    </div>

                    <!-- Price -->
                    <div class="product-detail-price">
                        <span class="detail-price-current"><?= formatPrice($product['sale_price'] ?? $product['price']) ?></span>
                        <?php if($product['sale_price']): ?>
                        <span class="detail-price-old"><?= formatPrice($product['price']) ?></span>
                        <span style="background:rgba(231,76,60,0.12);color:#e74c3c;padding:4px 12px;border-radius:50px;font-size:13px;font-weight:600;">Save <?= formatPrice($product['price'] - $product['sale_price']) ?></span>
                        <?php endif; ?>
                    </div>

                    <hr style="border-color:var(--border);margin:24px 0;">

                    <!-- Description -->
                    <p style="color:var(--text-muted);line-height:1.9;margin-bottom:24px;font-size:14px;"><?= htmlspecialchars($product['description']) ?></p>

                    <!-- Size Selector -->
                    <div class="mb-4">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                            <label style="font-weight:600;font-size:14px;color:var(--dark);">Select Size</label>
                            <a href="#" style="font-size:12px;color:var(--primary);">Size Guide <i class="fas fa-ruler-horizontal ms-1"></i></a>
                        </div>
                        <div class="size-grid">
                            <?php foreach ($sizes as $size): ?>
                            <button class="size-btn" data-size="<?= trim($size) ?>"><?= trim($size) ?></button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="selectedSize" name="size" value="">
                    </div>

                    <!-- Color Selector -->
                    <div class="mb-4">
                        <label style="font-weight:600;font-size:14px;color:var(--dark);display:block;margin-bottom:12px;">
                            Select Color: <span id="colorLabel" style="color:var(--primary);font-style:italic;">Choose one</span>
                        </label>
                        <?php
                        $colorMap = [
                            'Black' => '#1a1a1a', 'White' => '#f5f5f5', 'Red' => '#e74c3c',
                            'Blue' => '#3498db', 'Deep Purple' => '#6C3483', 'Wine Red' => '#722f37',
                            'Midnight Blue' => '#191970', 'Forest Green' => '#228B22',
                            'Gold' => '#D4AF37', 'Pink' => '#FF69B4', 'Beige' => '#F5F5DC',
                            'Grey' => '#808080', 'Navy' => '#003087', 'Brown' => '#8B4513',
                        ];
                        ?>
                        <div class="color-grid">
                            <?php foreach ($colors as $color): $c = trim($color); ?>
                            <div class="color-swatch" style="background:<?= $colorMap[$c] ?? '#888' ?>;" title="<?= htmlspecialchars($c) ?>" data-color="<?= htmlspecialchars($c) ?>"
                                onclick="document.getElementById('colorLabel').textContent='<?= htmlspecialchars($c) ?>'"></div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="selectedColor" name="color" value="">
                    </div>

                    <!-- Quantity & Add to Cart -->
                    <div class="d-flex gap-3 align-items-center mb-4 flex-wrap">
                        <div class="qty-control">
                            <button class="qty-btn" onclick="changeQty(-1)" type="button">−</button>
                            <span class="qty-display" id="qtyDisplay">1</span>
                            <button class="qty-btn" onclick="changeQty(1)" type="button">+</button>
                        </div>
                        <button onclick="addToCart(<?= $product['id'] ?>)" class="btn-vv btn-primary-vv flex-1">
                            <i class="fas fa-shopping-bag me-2"></i> Add to Cart
                        </button>
                        <button class="product-action-btn wishlist-btn" data-id="<?= $product['id'] ?>" style="width:52px;height:52px;border-radius:12px;font-size:1.1rem;" title="Add to Wishlist">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>

                    <!-- Buy Now -->
                    <a href="checkout.php?buy_now=<?= $product['id'] ?>" class="btn-vv btn-gold w-100 justify-content-center mb-4">
                        <i class="fas fa-bolt me-2"></i> Buy Now — Instant Checkout
                    </a>

                    <!-- Trust Badges -->
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;padding:20px;background:var(--light);border-radius:12px;">
                        <?php
                        $trust = [
                            ['icon'=>'fa-shield-alt','text'=>'Secure Payment','color'=>'var(--success)'],
                            ['icon'=>'fa-undo','text'=>'30-Day Returns','color'=>'var(--primary)'],
                            ['icon'=>'fa-truck','text'=>'Fast Delivery','color'=>'var(--gold)'],
                        ];
                        foreach ($trust as $t): ?>
                        <div style="text-align:center;">
                            <i class="fas <?= $t['icon'] ?>" style="color:<?= $t['color'] ?>;font-size:1.3rem;margin-bottom:6px;display:block;"></i>
                            <div style="font-size:11px;color:var(--text-muted);font-weight:500;"><?= $t['text'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs: Description, Reviews, Shipping -->
        <div style="margin-top:80px;">
            <ul class="nav" id="productTabs" role="tablist" style="border-bottom:2px solid var(--border);margin-bottom:40px;gap:0;">
                <?php $tabs = ['description'=>'Description','reviews'=>'Reviews ('.$product['reviews_count'].')','shipping'=>'Shipping & Returns']; ?>
                <?php $first = true; foreach ($tabs as $tid => $tlabel): ?>
                <li class="nav-item">
                    <button class="nav-link <?= $first?'active':'' ?>" data-bs-toggle="tab" data-bs-target="#tab-<?= $tid ?>" style="font-family:'Poppins',sans-serif;font-weight:600;font-size:14px;color:var(--text-muted);padding:14px 28px;border:none;border-bottom:3px solid transparent;background:transparent;transition:all 0.2s;">
                        <?= $tlabel ?>
                    </button>
                </li>
                <?php $first = false; endforeach; ?>
            </ul>
            <style>.nav-link.active{color:var(--primary)!important;border-bottom-color:var(--primary)!important;}</style>

            <div class="tab-content">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="tab-description">
                    <div class="row g-5">
                        <div class="col-md-7">
                            <h4 style="font-family:'Playfair Display',serif;margin-bottom:20px;">Product Details</h4>
                            <p style="color:var(--text-muted);line-height:1.9;"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                            <ul style="margin-top:20px;color:var(--text-muted);font-size:14px;line-height:2;">
                                <li><i class="fas fa-check text-success me-2"></i> Premium quality materials</li>
                                <li><i class="fas fa-check text-success me-2"></i> True to size fit</li>
                                <li><i class="fas fa-check text-success me-2"></i> Dry clean recommended</li>
                                <li><i class="fas fa-check text-success me-2"></i> Available in multiple colors</li>
                                <li><i class="fas fa-check text-success me-2"></i> Handcrafted with care</li>
                            </ul>
                        </div>
                        <div class="col-md-5">
                            <div style="background:var(--light);border-radius:16px;padding:28px;">
                                <h5 style="font-weight:700;margin-bottom:20px;">Product Specifications</h5>
                                <?php
                                $specs = [
                                    'Material' => 'Premium Velvet / Fabric',
                                    'Available Sizes' => implode(', ', $sizes),
                                    'Colors' => implode(', ', $colors),
                                    'Category' => $product['cat_name'] ?? 'Fashion',
                                    'SKU' => 'VV-' . strtoupper(substr($product['slug'] ?? 'PROD', 0, 6)),
                                    'Availability' => $product['stock'] > 0 ? 'In Stock (' . $product['stock'] . ' units)' : 'Out of Stock',
                                ];
                                foreach ($specs as $k => $v): ?>
                                <div style="display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border);font-size:14px;">
                                    <span style="color:var(--text-muted);"><?= $k ?></span>
                                    <span style="font-weight:500;color:var(--dark);text-align:right;max-width:200px;"><?= htmlspecialchars($v) ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="tab-reviews">
                    <div class="row g-5">
                        <div class="col-md-4">
                            <div style="text-align:center;padding:40px 24px;background:var(--light);border-radius:20px;">
                                <div style="font-family:'Playfair Display',serif;font-size:5rem;font-weight:700;color:var(--primary);line-height:1;"><?= $product['rating'] ?></div>
                                <div style="font-size:1.5rem;color:var(--gold);margin:8px 0;">
                                    <?php for($s=1;$s<=5;$s++) echo $s<=floor($product['rating'])?'★':'☆'; ?>
                                </div>
                                <div style="color:var(--text-muted);font-size:14px;">Based on <?= $product['reviews_count'] ?> reviews</div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <?php if(empty($reviews)): ?>
                            <!-- Static sample reviews -->
                            <?php $sampleReviews = [
                                ['reviewer_name'=>'Aisha Khan','rating'=>5,'comment'=>'Absolutely stunning! The quality exceeded my expectations.','created_at'=>'2026-01-15'],
                                ['reviewer_name'=>'Sara Ahmed','rating'=>5,'comment'=>'Perfect fit and gorgeous fabric. Will definitely order again!','created_at'=>'2026-01-20'],
                                ['reviewer_name'=>'Fatima Ali','rating'=>4,'comment'=>'Beautiful piece, delivery was fast. Slightly different shade than the photo.','created_at'=>'2026-02-01'],
                            ];
                            foreach ($sampleReviews as $rev):
                            ?>
                            <div style="padding:24px;background:var(--light);border-radius:12px;margin-bottom:16px;border-left:4px solid var(--primary);">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;"><?= strtoupper(substr($rev['reviewer_name'],0,1)) ?></div>
                                        <div>
                                            <div style="font-weight:600;font-size:14px;"><?= htmlspecialchars($rev['reviewer_name']) ?></div>
                                            <div style="color:var(--gold);font-size:13px;"><?= str_repeat('★',$rev['rating']) ?><?= str_repeat('☆',5-$rev['rating']) ?></div>
                                        </div>
                                    </div>
                                    <span style="font-size:12px;color:var(--text-muted);"><?= date('M d, Y', strtotime($rev['created_at'])) ?></span>
                                </div>
                                <p style="color:var(--text-muted);font-size:14px;margin:0;"><?= htmlspecialchars($rev['comment']) ?></p>
                            </div>
                            <?php endforeach; ?>
                            <?php else: foreach ($reviews as $rev): ?>
                            <div style="padding:24px;background:var(--light);border-radius:12px;margin-bottom:16px;border-left:4px solid var(--primary);">
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        <div style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;color:white;font-weight:700;"><?= strtoupper(substr($rev['reviewer_name'],0,1)) ?></div>
                                        <div>
                                            <div style="font-weight:600;font-size:14px;"><?= htmlspecialchars($rev['reviewer_name']) ?></div>
                                            <div style="color:var(--gold);font-size:13px;"><?= str_repeat('★',$rev['rating']) ?><?= str_repeat('☆',5-$rev['rating']) ?></div>
                                        </div>
                                    </div>
                                    <span style="font-size:12px;color:var(--text-muted);"><?= timeAgo($rev['created_at']) ?></span>
                                </div>
                                <p style="color:var(--text-muted);font-size:14px;margin:0;"><?= htmlspecialchars($rev['comment']) ?></p>
                            </div>
                            <?php endforeach; endif; ?>

                            <!-- Write Review -->
                            <div style="background:white;border-radius:16px;padding:28px;margin-top:20px;box-shadow:var(--shadow);">
                                <h5 style="font-weight:700;margin-bottom:20px;">Write a Review</h5>
                                <?php if(!isLoggedIn()): ?>
                                <div class="alert-vv alert-info"><i class="fas fa-info-circle"></i> Please <a href="login.php" style="color:var(--primary);font-weight:600;">login</a> to write a review.</div>
                                <?php else: ?>
                                <form method="POST" action="php/review.php">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <div class="form-group-vv">
                                        <label class="form-label-vv">Your Rating</label>
                                        <div style="font-size:2rem;color:#ccc;cursor:pointer;" id="starRating">
                                            <?php for($s=1;$s<=5;$s++): ?>
                                            <span onclick="setRating(<?= $s ?>)" onmouseover="hoverRating(<?= $s ?>)" onmouseout="resetRating()" style="transition:color 0.15s;">☆</span>
                                            <?php endfor; ?>
                                        </div>
                                        <input type="hidden" name="rating" id="ratingInput" value="5">
                                    </div>
                                    <div class="form-group-vv">
                                        <label class="form-label-vv">Your Comment</label>
                                        <textarea name="comment" class="form-control-vv" rows="4" placeholder="Share your experience..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn-vv btn-primary-vv">Submit Review <i class="fas fa-paper-plane ms-2"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Tab -->
                <div class="tab-pane fade" id="tab-shipping">
                    <div class="row g-4">
                        <?php
                        $shippingInfo = [
                            ['icon'=>'🚚','title'=>'Standard Delivery','desc'=>'3-5 business days · Free on orders over Rs. 3,000 · Rs. 200 flat fee otherwise'],
                            ['icon'=>'⚡','title'=>'Express Delivery','desc'=>'1-2 business days · Rs. 500 flat fee · Available for major cities'],
                            ['icon'=>'↩️','title'=>'Returns Policy','desc'=>'30-day hassle-free returns · Item must be unworn with tags · Exchange or full refund'],
                            ['icon'=>'📦','title'=>'Packaging','desc'=>'All items are carefully packaged in premium boxes with tissue paper and Velvet Vogue dust bags'],
                        ];
                        foreach ($shippingInfo as $info): ?>
                        <div class="col-md-6">
                            <div style="padding:28px;background:var(--light);border-radius:16px;border-left:4px solid var(--primary);">
                                <div style="font-size:2rem;margin-bottom:12px;"><?= $info['icon'] ?></div>
                                <h5 style="font-weight:700;margin-bottom:8px;"><?= $info['title'] ?></h5>
                                <p style="color:var(--text-muted);font-size:14px;margin:0;"><?= $info['desc'] ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relProducts) || true): ?>
        <div style="margin-top:80px;">
            <div class="text-center mb-5">
                <span class="section-tag">You May Also Like</span>
                <h2 class="section-title">Related <span>Products</span></h2>
            </div>
            <div class="product-grid">
                <?php
                $defaultRel = [
                    ['name'=>'Silk Floral Maxi','slug'=>'silk-floral-maxi','cat_name'=>'Women','price'=>12000,'sale_price'=>null,'rating'=>4.6,'reviews_count'=>89,'trending'=>0,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400&h=530&fit=crop'],
                    ['name'=>'Power Blazer Set','slug'=>'power-blazer-set','cat_name'=>'Women','price'=>9500,'sale_price'=>7500,'rating'=>4.7,'reviews_count'=>67,'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=400&h=530&fit=crop'],
                    ['name'=>'Cashmere Turtleneck','slug'=>'cashmere-turtleneck','cat_name'=>'Women','price'=>7200,'sale_price'=>5800,'rating'=>4.8,'reviews_count'=>112,'trending'=>0,'new_arrival'=>0,'img'=>'https://images.unsplash.com/photo-1583744946564-b52ac1c389c8?w=400&h=530&fit=crop'],
                    ['name'=>'Linen Wide Leg Pants','slug'=>'linen-wide-leg-pants','cat_name'=>'Women','price'=>4800,'sale_price'=>null,'rating'=>4.3,'reviews_count'=>33,'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=400&h=530&fit=crop'],
                ];
                $displayRel = !empty($relProducts) ? $relProducts : $defaultRel;
                $relImgs = ['https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400&h=530&fit=crop','https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=400&h=530&fit=crop','https://images.unsplash.com/photo-1583744946564-b52ac1c389c8?w=400&h=530&fit=crop','https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=400&h=530&fit=crop'];
                foreach (array_slice($displayRel, 0, 4) as $i => $rp):
                    $rd = (isset($rp['sale_price']) && $rp['sale_price'] && $rp['price'] > 0) ? round((($rp['price'] - $rp['sale_price']) / $rp['price']) * 100) : 0;
                    $rImg = $rp['img'] ?? $relImgs[$i % 4];
                ?>
                <div class="product-card">
                    <div class="product-card-image">
                        <img src="<?= $rImg ?>" alt="<?= htmlspecialchars($rp['name']) ?>" loading="lazy">
                        <div class="product-card-actions">
                            <button class="product-action-btn wishlist-btn" data-id="<?= $rp['id'] ?? $i+1 ?>"><i class="far fa-heart"></i></button>
                        </div>
                        <button class="product-card-quick-add quick-add-btn" data-id="<?= $rp['id'] ?? $i+1 ?>">
                            <i class="fas fa-shopping-bag me-1"></i> Quick Add
                        </button>
                    </div>
                    <div class="product-card-body">
                        <div class="product-card-cat"><?= htmlspecialchars($rp['cat_name'] ?? '') ?></div>
                        <a href="product-detail.php?slug=<?= $rp['slug'] ?>" class="text-decoration-none">
                            <h3 class="product-card-name"><?= htmlspecialchars($rp['name']) ?></h3>
                        </a>
                        <div class="product-rating">
                            <span class="stars"><?php for($s=1;$s<=5;$s++) echo $s<=floor($rp['rating'])?'★':'☆'; ?></span>
                            <span class="rating-count">(<?= $rp['reviews_count'] ?>)</span>
                        </div>
                        <div class="product-card-price">
                            <span class="price-current"><?= formatPrice($rp['sale_price'] ?? $rp['price']) ?></span>
                            <?php if(isset($rp['sale_price']) && $rp['sale_price']): ?>
                            <span class="price-old"><?= formatPrice($rp['price']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
let currentQty = 1;
function changeQty(delta) {
    currentQty = Math.max(1, Math.min(10, currentQty + delta));
    document.getElementById('qtyDisplay').textContent = currentQty;
}

function addToCart(pid) {
    const size = document.getElementById('selectedSize')?.value;
    const color = document.getElementById('selectedColor')?.value;
    if (!size) { showToast('Please select a size', 'error'); return; }
    if (!color) { showToast('Please select a color', 'error'); return; }
    fetch(`php/cart.php?action=add&id=${pid}&qty=${currentQty}&size=${encodeURIComponent(size)}&color=${encodeURIComponent(color)}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                updateCartBadge(data.count);
                showToast('Added to cart successfully! 🛍', 'success');
            } else {
                showToast(data.message || 'Please login first', 'error');
            }
        });
}

// Star rating
let selectedRating = 5;
function setRating(r) {
    selectedRating = r;
    document.getElementById('ratingInput').value = r;
    updateStars(r);
}
function hoverRating(r) { updateStars(r); }
function resetRating() { updateStars(selectedRating); }
function updateStars(r) {
    const stars = document.querySelectorAll('#starRating span');
    stars.forEach((s, i) => {
        s.textContent = i < r ? '★' : '☆';
        s.style.color = i < r ? '#D4AF37' : '#ccc';
    });
}
updateStars(5);
</script>

<?php include 'includes/footer.php'; ?>
