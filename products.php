<?php
$pageTitle = "Shop All Products";
require_once 'config/db.php';

// Filters
$category = isset($_GET['category']) ? sanitize($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitize($_GET['sort']) : 'newest';
$minPrice = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
$maxPrice = isset($_GET['max_price']) ? intval($_GET['max_price']) : 999999;
$isNew = isset($_GET['new']) ? 1 : 0;

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Build Query
$where = "WHERE p.price BETWEEN $minPrice AND $maxPrice";
if ($category === 'sale') {
    $where .= " AND (c.slug = 'sale' OR p.sale_price IS NOT NULL)";
} elseif ($category) {
    $where .= " AND c.slug = '$category'";
}
if ($search) $where .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
if ($isNew) $where .= " AND p.new_arrival = 1";

$orderBy = match($sort) {
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'popular' => 'p.reviews_count DESC',
    'rating' => 'p.rating DESC',
    default => 'p.id DESC',
};

$countRes = $conn->query("SELECT COUNT(*) as total FROM products p LEFT JOIN categories c ON p.category_id = c.id $where");
$totalProducts = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalProducts / $perPage);

$productsRes = $conn->query("SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id $where ORDER BY $orderBy LIMIT $perPage OFFSET $offset");
$products = $productsRes ? $productsRes->fetch_all(MYSQLI_ASSOC) : [];

$catsRes = $conn->query("SELECT * FROM categories ORDER BY name");
$cats = $catsRes ? $catsRes->fetch_all(MYSQLI_ASSOC) : [];

$extraCSS = '<link rel="stylesheet" href="css/products.css">';
$extraJS  = '<script src="js/products.js"></script>';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-vv mb-4">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-current">Shop<?= $category ? ' / ' . ucfirst($category) : '' ?></span>
        </div>
        <h1 class="page-hero-title">
            <?= $search ? 'Search: "<span>' . htmlspecialchars($search) . '</span>"' :
               ($category ? ucfirst($category) . ' <span>Collection</span>' : 'All <span>Products</span>') ?>
        </h1>
        <p style="color:rgba(255,255,255,0.6);margin-top:12px;"><?= $totalProducts ?> products found</p>
    </div>
</section>

<section style="padding:60px 0 100px;background:var(--light);">
    <div class="container">
        <div class="row g-5">

            <!-- SIDEBAR -->
            <div class="col-lg-3">
                <div style="position:sticky;top:110px;">

                    <!-- Search -->
                    <div style="background:white;border-radius:16px;padding:24px;box-shadow:var(--shadow);margin-bottom:20px;">
                        <h6 style="font-weight:700;margin-bottom:16px;font-size:14px;letter-spacing:1px;text-transform:uppercase;">Search</h6>
                        <form method="GET">
                            <div class="input-group-vv">
                                <i class="fas fa-search input-icon" style="color:var(--primary);"></i>
                                <input type="text" name="search" class="form-control-vv" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                            </div>
                            <?php if($category): ?><input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>"><?php endif; ?>
                        </form>
                    </div>

                    <!-- Categories -->
                    <div style="background:white;border-radius:16px;padding:24px;box-shadow:var(--shadow);margin-bottom:20px;">
                        <h6 style="font-weight:700;margin-bottom:16px;font-size:14px;letter-spacing:1px;text-transform:uppercase;">Categories</h6>
                        <a href="products.php" style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:8px;font-size:14px;color:var(--text);text-decoration:none;<?= !$category ? 'background:var(--accent);color:var(--primary);font-weight:600;' : '' ?>transition:all 0.2s;" onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background=<?= !$category ? "'var(--accent)'" : "'transparent'" ?>">
                            All Products <span style="background:var(--primary);color:white;border-radius:50px;padding:2px 10px;font-size:11px;"><?= $totalProducts ?></span>
                        </a>
                        <?php foreach ($cats as $cat): ?>
                        <?php $catCount = $conn->query("SELECT COUNT(*) as c FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE c.slug = '{$cat['slug']}'")->fetch_assoc()['c']; ?>
                        <a href="products.php?category=<?= $cat['slug'] ?>" style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;border-radius:8px;font-size:14px;color:var(--text);text-decoration:none;<?= $category == $cat['slug'] ? 'background:var(--accent);color:var(--primary);font-weight:600;' : '' ?>transition:all 0.2s;" onmouseover="this.style.background='var(--accent)'" onmouseout="this.style.background=<?= $category == $cat['slug'] ? "'var(--accent)'" : "'transparent'" ?>">
                            <?= htmlspecialchars($cat['name']) ?> <span style="background:#f0f0f0;color:#888;border-radius:50px;padding:2px 10px;font-size:11px;"><?= $catCount ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Price Filter -->
                    <div style="background:white;border-radius:16px;padding:24px;box-shadow:var(--shadow);margin-bottom:20px;">
                        <h6 style="font-weight:700;margin-bottom:16px;font-size:14px;letter-spacing:1px;text-transform:uppercase;">Price Range</h6>
                        <form method="GET" id="priceForm">
                            <?php if($category): ?><input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>"><?php endif; ?>
                            <?php if($search): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
                            <input type="range" id="priceRange" min="0" max="50000" value="<?= $maxPrice == 999999 ? 50000 : $maxPrice ?>" style="width:100%;accent-color:var(--primary);" oninput="document.getElementById('priceVal').textContent='Rs. '+parseInt(this.value).toLocaleString()">
                            <div style="display:flex;justify-content:space-between;font-size:13px;color:var(--text-muted);margin-top:8px;">
                                <span>Rs. 0</span>
                                <span id="priceVal">Rs. <?= $maxPrice == 999999 ? '50,000' : number_format($maxPrice) ?></span>
                            </div>
                            <input type="hidden" name="max_price" id="maxPriceInput" value="<?= $maxPrice == 999999 ? 50000 : $maxPrice ?>">
                            <button type="submit" onclick="document.getElementById('maxPriceInput').value=document.getElementById('priceRange').value" class="btn-vv btn-primary-vv w-100 mt-3" style="padding:10px;font-size:13px;">Apply Filter</button>
                        </form>
                    </div>

                    <!-- Quick Filters -->
                    <div style="background:white;border-radius:16px;padding:24px;box-shadow:var(--shadow);">
                        <h6 style="font-weight:700;margin-bottom:16px;font-size:14px;letter-spacing:1px;text-transform:uppercase;">Quick Filters</h6>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <a href="products.php?new=1" style="display:flex;align-items:center;gap:10px;font-size:14px;color:var(--text);padding:8px 0;text-decoration:none;">
                                <span style="width:16px;height:16px;background:var(--primary);border-radius:4px;flex-shrink:0;<?= $isNew ? '' : 'background:white;border:2px solid #ddd;' ?>"></span>
                                New Arrivals
                            </a>
                            <a href="products.php?sale=1" style="display:flex;align-items:center;gap:10px;font-size:14px;color:var(--text);padding:8px 0;text-decoration:none;">
                                <span style="width:16px;height:16px;background:white;border:2px solid #ddd;border-radius:4px;flex-shrink:0;"></span>
                                On Sale
                            </a>
                            <a href="products.php?trending=1" style="display:flex;align-items:center;gap:10px;font-size:14px;color:var(--text);padding:8px 0;text-decoration:none;">
                                <span style="width:16px;height:16px;background:white;border:2px solid #ddd;border-radius:4px;flex-shrink:0;"></span>
                                Trending
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PRODUCTS -->
            <div class="col-lg-9">

                <!-- Sort Bar -->
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:16px;">
                    <p style="font-size:14px;color:var(--text-muted);margin:0;">Showing <strong><?= count($products) ?></strong> of <strong><?= $totalProducts ?></strong> products</p>
                    <form method="GET" style="display:flex;align-items:center;gap:12px;">
                        <?php if($category): ?><input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>"><?php endif; ?>
                        <?php if($search): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
                        <label style="font-size:13px;color:var(--text-muted);">Sort by:</label>
                        <select name="sort" onchange="this.form.submit()" style="padding:8px 16px;border:2px solid var(--border);border-radius:8px;font-family:'Poppins',sans-serif;font-size:13px;outline:none;cursor:pointer;background:white;color:var(--text);">
                            <option value="newest" <?= $sort=='newest'?'selected':'' ?>>Newest</option>
                            <option value="popular" <?= $sort=='popular'?'selected':'' ?>>Most Popular</option>
                            <option value="rating" <?= $sort=='rating'?'selected':'' ?>>Highest Rated</option>
                            <option value="price_asc" <?= $sort=='price_asc'?'selected':'' ?>>Price: Low to High</option>
                            <option value="price_desc" <?= $sort=='price_desc'?'selected':'' ?>>Price: High to Low</option>
                        </select>
                    </form>
                </div>

                <?php if (empty($products)): ?>
                <!-- Static showcase when DB is empty -->
                <div class="product-grid">
                    <?php
                    $staticProds = [
                        ['id'=>1, 'name'=>'Elegant Velvet Dress',   'slug'=>'elegant-velvet-dress',   'cat'=>'women', 'price'=>8500,  'sale_price'=>6800,  'rating'=>4.8,'reviews_count'=>124,'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&h=530&fit=crop'],
                        ['id'=>2, 'name'=>'Silk Floral Maxi',        'slug'=>'silk-floral-maxi',        'cat'=>'women', 'price'=>12000, 'sale_price'=>null,   'rating'=>4.6,'reviews_count'=>89, 'trending'=>0,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400&h=530&fit=crop'],
                        ['id'=>3, 'name'=>'Power Blazer Set',         'slug'=>'power-blazer-set',         'cat'=>'women', 'price'=>9500,  'sale_price'=>7500,  'rating'=>4.7,'reviews_count'=>67, 'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=400&h=530&fit=crop'],
                        ['id'=>4, 'name'=>'Boho Wrap Skirt',          'slug'=>'boho-wrap-skirt',          'cat'=>'women', 'price'=>3200,  'sale_price'=>null,   'rating'=>4.4,'reviews_count'=>45, 'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1509551388413-e18d0ac5d495?w=400&h=530&fit=crop'],
                        ['id'=>5, 'name'=>'Classic Oxford Shirt',     'slug'=>'classic-oxford-shirt',     'cat'=>'men',   'price'=>4500,  'sale_price'=>3500,  'rating'=>4.5,'reviews_count'=>78, 'trending'=>0,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1603252109303-2751441dd157?w=400&h=530&fit=crop'],
                        ['id'=>6, 'name'=>'Slim Fit Chinos',          'slug'=>'slim-fit-chinos',          'cat'=>'men',   'price'=>5500,  'sale_price'=>null,   'rating'=>4.6,'reviews_count'=>56, 'trending'=>1,'new_arrival'=>0,'img'=>'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=400&h=530&fit=crop'],
                        ['id'=>7, 'name'=>'Leather Biker Jacket',     'slug'=>'leather-biker-jacket',     'cat'=>'men',   'price'=>18000, 'sale_price'=>14000, 'rating'=>4.9,'reviews_count'=>203,'trending'=>1,'new_arrival'=>0,'img'=>'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&h=530&fit=crop'],
                        ['id'=>8, 'name'=>'Gold Chain Necklace',      'slug'=>'gold-chain-necklace',      'cat'=>'accessories','price'=>2800,'sale_price'=>null,'rating'=>4.7,'reviews_count'=>91, 'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=400&h=530&fit=crop'],
                        ['id'=>9, 'name'=>'Designer Sunglasses',      'slug'=>'designer-sunglasses',      'cat'=>'accessories','price'=>3500,'sale_price'=>2800,'rating'=>4.5,'reviews_count'=>44, 'trending'=>0,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400&h=530&fit=crop'],
                        ['id'=>10,'name'=>'Cashmere Turtleneck',      'slug'=>'cashmere-turtleneck',      'cat'=>'women', 'price'=>7200,  'sale_price'=>5800,  'rating'=>4.8,'reviews_count'=>112,'trending'=>0,'new_arrival'=>0,'img'=>'https://images.unsplash.com/photo-1583744946564-b52ac1c389c8?w=400&h=530&fit=crop'],
                        ['id'=>11,'name'=>'Formal Suit',              'slug'=>'formal-suit',              'cat'=>'men',   'price'=>22000, 'sale_price'=>18000, 'rating'=>4.9,'reviews_count'=>167,'trending'=>1,'new_arrival'=>0,'img'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=530&fit=crop'],
                        ['id'=>12,'name'=>'Linen Wide Leg Pants',     'slug'=>'linen-wide-leg-pants',     'cat'=>'women', 'price'=>4800,  'sale_price'=>null,   'rating'=>4.3,'reviews_count'=>33, 'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=400&h=530&fit=crop'],
                        ['id'=>13,'name'=>'Rainbow Tutu Dress',       'slug'=>'rainbow-tutu-dress',       'cat'=>'kids',  'price'=>2500,  'sale_price'=>1800,  'rating'=>4.7,'reviews_count'=>56, 'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1503944168849-8bf86875bbd8?w=400&h=530&fit=crop'],
                        ['id'=>14,'name'=>'Denim Dungarees',          'slug'=>'kids-denim-dungarees',     'cat'=>'kids',  'price'=>1800,  'sale_price'=>null,   'rating'=>4.5,'reviews_count'=>34, 'trending'=>0,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1554568218-0f1715e72254?w=400&h=530&fit=crop'],
                        ['id'=>15,'name'=>'Boys Graphic Tee Set',     'slug'=>'boys-graphic-tee-set',     'cat'=>'kids',  'price'=>1200,  'sale_price'=>900,   'rating'=>4.6,'reviews_count'=>78, 'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1535572290543-960a8046f5af?w=400&h=530&fit=crop'],
                        ['id'=>17,'name'=>'Floral Summer Dress',      'slug'=>'floral-summer-dress',      'cat'=>'sale',  'price'=>4500,  'sale_price'=>2700,  'rating'=>4.6,'reviews_count'=>89, 'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400&h=530&fit=crop'],
                        ['id'=>18,'name'=>'Oversized Denim Jacket',   'slug'=>'oversized-denim-jacket',   'cat'=>'sale',  'price'=>7500,  'sale_price'=>4500,  'rating'=>4.7,'reviews_count'=>134,'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1491336477066-31156b5e4f35?w=400&h=530&fit=crop'],
                        ['id'=>20,'name'=>'Satin Slip Dress',         'slug'=>'satin-slip-dress',         'cat'=>'sale',  'price'=>6500,  'sale_price'=>3800,  'rating'=>4.8,'reviews_count'=>198,'trending'=>1,'new_arrival'=>1,'img'=>'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?w=400&h=530&fit=crop'],
                    ];
                    // Filter by current category (empty = all; 'sale' = any with sale_price)
                    $filteredStatic = array_filter($staticProds, function($p) use ($category) {
                        if (!$category) return true;
                        if ($category === 'sale') return $p['sale_price'] !== null || $p['cat'] === 'sale';
                        return $p['cat'] === $category;
                    });
                    foreach ($filteredStatic as $p):
                        $discount = $p['sale_price'] ? round((($p['price'] - $p['sale_price']) / $p['price']) * 100) : 0;
                    ?>
                    <div class="product-card animate-on-scroll">
                        <div class="product-card-image">
                            <img src="<?= $p['img'] ?>" alt="<?= $p['name'] ?>" loading="lazy">
                            <div class="product-badges">
                                <?php if($p['new_arrival']): ?><span class="badge-pill badge-new">New</span><?php endif; ?>
                                <?php if($p['sale_price']): ?><span class="badge-pill badge-sale">-<?= $discount ?>%</span><?php endif; ?>
                                <?php if($p['trending']): ?><span class="badge-pill badge-trend">🔥</span><?php endif; ?>
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
                            <div class="product-card-cat"><?= $p['cat'] ?></div>
                            <a href="product-detail.php?slug=<?= $p['slug'] ?>" class="text-decoration-none">
                                <h3 class="product-card-name"><?= $p['name'] ?></h3>
                            </a>
                            <div class="product-rating">
                                <span class="stars"><?php for($s=1;$s<=5;$s++) echo $s<=floor($p['rating'])?'★':'☆'; ?></span>
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
                    <?php endforeach; ?>
                </div>

                <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($products as $i => $p):
                        $fallbackImgs = ['https://images.unsplash.com/photo-1595777457583-95e059d581b8?w=400&h=530&fit=crop','https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=400&h=530&fit=crop','https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=400&h=530&fit=crop','https://images.unsplash.com/photo-1603252109303-2751441dd157?w=400&h=530&fit=crop','https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400&h=530&fit=crop','https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=400&h=530&fit=crop','https://images.unsplash.com/photo-1509551388413-e18d0ac5d495?w=400&h=530&fit=crop','https://images.unsplash.com/photo-1583744946564-b52ac1c389c8?w=400&h=530&fit=crop'];
                        $imgSrc = !empty($p['images']) ? $p['images'] : $fallbackImgs[$i % count($fallbackImgs)];
                        $discount = ($p['sale_price'] && $p['price'] > 0) ? round((($p['price'] - $p['sale_price']) / $p['price']) * 100) : 0;
                    ?>
                    <div class="product-card animate-on-scroll delay-<?= ($i % 4) * 100 ?>">
                        <div class="product-card-image">
                            <img src="<?= htmlspecialchars($imgSrc) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                            <div class="product-badges">
                                <?php if($p['new_arrival']): ?><span class="badge-pill badge-new">New</span><?php endif; ?>
                                <?php if($p['sale_price']): ?><span class="badge-pill badge-sale">-<?= $discount ?>%</span><?php endif; ?>
                                <?php if($p['trending']): ?><span class="badge-pill badge-trend">🔥</span><?php endif; ?>
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
                                <span class="stars"><?php for($s=1;$s<=5;$s++) echo $s<=floor($p['rating'])?'★':'☆'; ?></span>
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
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="pagination-vv">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?= $page-1 ?><?= $category?"&category=$category":'' ?><?= $search?"&search=$search":'' ?>" class="page-item-vv"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <?php for ($i = max(1, $page-2); $i <= min($totalPages, $page+2); $i++): ?>
                    <a href="?page=<?= $i ?><?= $category?"&category=$category":'' ?><?= $search?"&search=$search":'' ?>" class="page-item-vv <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page+1 ?><?= $category?"&category=$category":'' ?><?= $search?"&search=$search":'' ?>" class="page-item-vv"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
