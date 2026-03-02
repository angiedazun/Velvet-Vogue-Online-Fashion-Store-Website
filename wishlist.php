<?php
$pageTitle = "My Wishlist";
require_once 'config/db.php';
if (!isLoggedIn()) { redirect('login.php?redirect=wishlist.php'); }

$uid = (int)$_SESSION['user_id'];

/* ─── Remove from wishlist ────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_id'])) {
    $pid = (int)$_POST['remove_id'];
    $conn->query("DELETE FROM wishlist WHERE user_id=$uid AND product_id=$pid");
    header('Location: wishlist.php');
    exit;
}

/* ─── Add to cart from wishlist ───────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart_id'])) {
    $pid = (int)$_POST['add_to_cart_id'];
    $sid = session_id();
    $chk = $conn->query("SELECT id, quantity FROM cart WHERE user_id=$uid AND product_id=$pid LIMIT 1");
    if ($chk && $chk->num_rows > 0) {
        $row = $chk->fetch_assoc();
        $conn->query("UPDATE cart SET quantity=quantity+1 WHERE id={$row['id']}");
    } else {
        $conn->query("INSERT INTO cart (user_id, session_id, product_id, quantity) VALUES ($uid,'$sid',$pid,1)");
    }
    header('Location: cart.php');
    exit;
}

/* ─── Fetch wishlist ──────────────────────────── */
$wishItems = [];
$res = $conn->query("SELECT w.*, p.name, p.price, p.sale_price, p.images, p.slug, p.stock,
    IFNULL(cat.name,'') as category_name
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    LEFT JOIN categories cat ON p.category_id = cat.id
    WHERE w.user_id = $uid
    ORDER BY w.created_at DESC");
while ($row = $res->fetch_assoc()) { $wishItems[] = $row; }

$extraCSS = '<link rel="stylesheet" href="css/wishlist.css">';
$extraJS  = '<script src="js/wishlist.js"></script>';
include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-vv mb-4">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span>My Wishlist</span>
        </div>
        <h1 class="page-hero-title">My Wishlist</h1>
        <p class="page-hero-sub"><?= count($wishItems) ?> saved item<?= count($wishItems) != 1 ? 's' : '' ?></p>
    </div>
</section>

<section style="padding:60px 0 80px;">
<div class="container">

<?php if (empty($wishItems)): ?>
<!-- Empty State -->
<div class="wishlist-empty">
    <div class="wishlist-empty-icon"><i class="far fa-heart"></i></div>
    <h4>Your wishlist is empty</h4>
    <p>Save your favourite items by clicking the heart icon on any product.</p>
    <a href="products.php" class="btn-vv btn-primary-vv" style="padding:14px 32px;">
        <i class="fas fa-shopping-bag me-2"></i>Explore Products
    </a>
</div>

<?php else: ?>

<!-- Top Actions Bar -->
<div class="wishlist-bar">
    <span><?= count($wishItems) ?> items in your wishlist</span>
    <div style="display:flex;gap:10px;">
        <form method="POST" id="clearWishlistForm">
            <input type="hidden" name="clear_all" value="1">
            <button type="button" onclick="clearWishlist()" class="btn-vv" style="padding:10px 18px;font-size:12px;background:var(--light);color:#e74c3c;border:1px solid #e74c3c30;">
                <i class="fas fa-trash-alt me-2"></i>Clear All
            </button>
        </form>
    </div>
</div>

<!-- Wishlist Grid -->
<div class="wishlist-grid">
    <?php foreach ($wishItems as $item):
        $finalPrice = ($item['sale_price'] && $item['sale_price'] > 0) ? $item['sale_price'] : $item['price'];
        $hasDiscount = $item['sale_price'] && $item['sale_price'] > 0;
        $discountPct = $hasDiscount ? round((($item['price'] - $item['sale_price']) / $item['price']) * 100) : 0;
        $img = $item['images']
            ? "https://images.unsplash.com/photo-{$item['images']}?w=400&h=500&fit=crop"
            : "https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=400&h=500&fit=crop";
        $inStock = $item['stock'] > 0;
    ?>
    <div class="wishlist-card" id="wishCard<?= $item['product_id'] ?>">
        <!-- Image -->
        <div class="wishlist-card-img">
            <a href="product-detail.php?slug=<?= htmlspecialchars($item['slug']) ?>">
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($item['name']) ?>"
                     onerror="this.src='https://images.unsplash.com/photo-1434389677669?w=400&h=500&fit=crop'">
            </a>
            <?php if ($hasDiscount): ?>
            <span class="wishlist-badge">-<?= $discountPct ?>%</span>
            <?php endif; ?>
            <?php if (!$inStock): ?>
            <div class="wishlist-out-of-stock">Out of Stock</div>
            <?php endif; ?>
            <!-- Remove Button -->
            <form method="POST" class="wishlist-remove-form">
                <input type="hidden" name="remove_id" value="<?= $item['product_id'] ?>">
                <button type="submit" class="wishlist-remove-btn" title="Remove from wishlist">
                    <i class="fas fa-times"></i>
                </button>
            </form>
        </div>

        <!-- Info -->
        <div class="wishlist-card-body">
            <div class="wishlist-category"><?= htmlspecialchars($item['category_name']) ?></div>
            <a href="product-detail.php?slug=<?= htmlspecialchars($item['slug']) ?>" class="wishlist-name">
                <?= htmlspecialchars($item['name']) ?>
            </a>
            <div class="wishlist-price">
                <span class="price-current">Rs. <?= number_format($finalPrice, 0) ?></span>
                <?php if ($hasDiscount): ?>
                <span class="price-original">Rs. <?= number_format($item['price'], 0) ?></span>
                <?php endif; ?>
            </div>

            <!-- Add to Cart -->
            <?php if ($inStock): ?>
            <form method="POST" class="wishlist-cart-form">
                <input type="hidden" name="add_to_cart_id" value="<?= $item['product_id'] ?>">
                <button type="submit" class="btn-vv btn-primary-vv w-100" style="padding:11px;font-size:13px;">
                    <i class="fas fa-shopping-bag me-2"></i>Add to Cart
                </button>
            </form>
            <?php else: ?>
            <button class="btn-vv w-100" style="padding:11px;font-size:13px;background:var(--light);color:var(--text-muted);cursor:not-allowed;" disabled>
                <i class="fas fa-ban me-2"></i>Out of Stock
            </button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Continue Shopping -->
<div style="text-align:center;margin-top:40px;">
    <a href="products.php" class="btn-vv" style="padding:13px 30px;font-size:14px;background:var(--light);color:var(--dark);">
        <i class="fas fa-chevron-left me-2"></i>Continue Shopping
    </a>
</div>

<?php endif; ?>

</div>
</section>

<?php include 'includes/footer.php'; ?>
