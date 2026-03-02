<?php
$pageTitle = "Shopping Cart";
require_once 'config/db.php';

$cartItems = [];
$subtotal = 0;

if (isLoggedIn()) {
    $uid = $_SESSION['user_id'];
    $res = $conn->query("SELECT c.*, p.name, p.price, p.sale_price, p.images, p.stock, p.slug
        FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $uid");
    while ($row = $res->fetch_assoc()) {
        $price = ($row['sale_price'] && $row['sale_price'] > 0) ? $row['sale_price'] : $row['price'];
        $row['final_price'] = $price;
        $row['item_total'] = $price * $row['quantity'];
        $subtotal += $row['item_total'];
        $cartItems[] = $row;
    }
} else {
    // Session cart fallback
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $pid => $item) {
            $res = $conn->query("SELECT * FROM products WHERE id = $pid LIMIT 1");
            if ($row = $res->fetch_assoc()) {
                $price = ($row['sale_price'] && $row['sale_price'] > 0) ? $row['sale_price'] : $row['price'];
                $row['quantity'] = $item['quantity'];
                $row['size'] = $item['size'] ?? '';
                $row['color'] = $item['color'] ?? '';
                $row['final_price'] = $price;
                $row['item_total'] = $price * $item['quantity'];
                $subtotal += $row['item_total'];
                $cartItems[] = $row;
            }
        }
    }
}

$shipping = ($subtotal >= 3000 || $subtotal == 0) ? 0 : 200;
$total = $subtotal + $shipping;

$extraCSS = '<link rel="stylesheet" href="css/cart.css">';
$extraJS  = '<script src="js/cart.js"></script>';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-vv mb-4">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-current">Shopping Cart</span>
        </div>
        <h1 class="page-hero-title">Your <span>Cart</span></h1>
        <p style="color:rgba(255,255,255,0.6);margin-top:10px;"><?= count($cartItems) ?> item<?= count($cartItems) != 1 ? 's' : '' ?> in your cart</p>
    </div>
</section>

<section style="padding:80px 0;background:var(--light);">
    <div class="container">
        <?php if (empty($cartItems)): ?>
        <!-- Empty Cart -->
        <div class="text-center py-5">
            <div style="font-size:5rem;color:var(--border);margin-bottom:24px;"><i class="fas fa-shopping-bag"></i></div>
            <h3 style="font-family:'Playfair Display',serif;font-weight:700;color:var(--dark);margin-bottom:12px;">Your Cart is Empty</h3>
            <p style="color:var(--text-muted);margin-bottom:28px;">Looks like you haven't added anything yet. Start shopping!</p>
            <a href="products.php" class="btn-vv btn-primary-vv">
                <i class="fas fa-shopping-bag me-2"></i> Browse Products
            </a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <!-- Cart Items Column -->
            <div class="col-lg-8">
                <!-- Header -->
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                    <h4 style="font-family:'Playfair Display',serif;font-weight:700;">Cart Items</h4>
                    <button onclick="clearCart()" style="background:none;border:none;color:var(--text-muted);font-size:13px;cursor:pointer;">
                        <i class="fas fa-trash me-1"></i> Clear Cart
                    </button>
                </div>

                <!-- Items -->
                <?php foreach ($cartItems as $item): ?>
                <div class="cart-item" id="cart-item-<?= $item['id'] ?>">
                    <div class="cart-item-img">
                        <a href="product-detail.php?id=<?= $item['product_id'] ?? $item['id'] ?>">
                            <img src="<?= htmlspecialchars($item['images']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" loading="lazy">
                        </a>
                    </div>
                    <div class="cart-item-info">
                        <div class="cart-item-brand">Velvet Vogue</div>
                        <h5 class="cart-item-name">
                            <a href="product-detail.php?id=<?= $item['product_id'] ?? $item['id'] ?>" style="color:inherit;"><?= htmlspecialchars($item['name']) ?></a>
                        </h5>
                        <div class="cart-item-meta">
                            <?php if (!empty($item['size'])): ?>
                            <span>Size: <strong><?= htmlspecialchars($item['size']) ?></strong></span>
                            <?php endif; ?>
                            <?php if (!empty($item['color'])): ?>
                            <span>Color: <strong><?= htmlspecialchars($item['color']) ?></strong></span>
                            <?php endif; ?>
                        </div>
                        <div class="cart-item-price"><?= formatPrice($item['final_price']) ?></div>
                    </div>
                    <div class="cart-item-controls">
                        <div class="qty-control">
                            <button class="qty-btn" onclick="changeQty(<?= $item['id'] ?>, -1)">−</button>
                            <span class="qty-value" id="qty-<?= $item['id'] ?>"><?= $item['quantity'] ?></span>
                            <button class="qty-btn" onclick="changeQty(<?= $item['id'] ?>, 1)">+</button>
                        </div>
                        <div class="cart-item-subtotal" id="subtotal-<?= $item['id'] ?>"><?= formatPrice($item['item_total']) ?></div>
                        <button class="cart-item-remove" onclick="removeCartItem(<?= $item['id'] ?>)" title="Remove">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Continue Shopping -->
                <div style="margin-top:24px;">
                    <a href="products.php" style="color:var(--primary);font-size:14px;font-weight:500;text-decoration:none;">
                        <i class="fas fa-arrow-left me-1"></i> Continue Shopping
                    </a>
                </div>
            </div>

            <!-- Summary Column -->
            <div class="col-lg-4">
                <div class="cart-summary">
                    <h4 style="font-family:'Playfair Display',serif;font-weight:700;margin-bottom:24px;">Order Summary</h4>

                    <!-- Coupon Code -->
                    <div class="coupon-box mb-4">
                        <label style="font-size:13px;font-weight:600;margin-bottom:8px;display:block;">Coupon Code</label>
                        <div style="display:flex;gap:8px;">
                            <input type="text" id="couponInput" class="form-control-vv" placeholder="Enter code..." style="flex:1;">
                            <button class="btn-vv btn-dark-vv" onclick="applyCoupon()" style="white-space:nowrap;padding:10px 16px;font-size:13px;">Apply</button>
                        </div>
                        <div id="couponMsg" style="font-size:12px;margin-top:6px;"></div>
                    </div>

                    <!-- Totals -->
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="cart-subtotal"><?= formatPrice($subtotal) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span id="cart-shipping" style="color:<?= $shipping == 0 ? '#27ae60' : 'inherit' ?>;">
                            <?= $shipping == 0 ? '<span style="color:#27ae60;font-weight:600;">FREE</span>' : formatPrice($shipping) ?>
                        </span>
                    </div>
                    <?php if ($subtotal < 3000 && $subtotal > 0): ?>
                    <div style="font-size:11px;color:var(--text-muted);margin-top:-14px;margin-bottom:10px;">
                        Add <?= formatPrice(3000 - $subtotal) ?> more for free shipping
                    </div>
                    <?php endif; ?>
                    <div class="summary-row" id="discount-row" style="display:none;color:#27ae60;">
                        <span>Discount</span>
                        <span id="discount-amt">-Rs. 0</span>
                    </div>
                    <hr style="border-color:var(--border);">
                    <div class="summary-row total-row">
                        <span>Total</span>
                        <span id="cart-total"><?= formatPrice($total) ?></span>
                    </div>

                    <!-- Checkout Button -->
                    <a href="checkout.php" class="btn-vv btn-primary-vv w-100 mt-4" style="text-align:center;display:block;">
                        <i class="fas fa-credit-card me-2"></i> Proceed to Checkout
                    </a>

                    <!-- Security Badges -->
                    <div style="margin-top:20px;padding:16px;background:var(--light);border-radius:12px;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-shield-alt" style="color:var(--primary);"></i>
                            <span style="font-size:12px;font-weight:600;">Secure Checkout</span>
                        </div>
                        <p style="font-size:11px;color:var(--text-muted);margin:0;">Your payment info is encrypted and secure.</p>
                        <div class="payment-methods mt-3" style="display:flex;gap:6px;flex-wrap:wrap;">
                            <?php foreach(['VISA','MC','JazzCash','EasyPaisa','COD'] as $pm): ?>
                            <span style="background:white;border:1px solid var(--border);border-radius:6px;padding:4px 8px;font-size:10px;font-weight:700;"><?= $pm ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- You May Also Like (static) -->
        <div style="margin-top:80px;">
            <div class="text-center mb-4">
                <h3 style="font-family:'Playfair Display',serif;font-weight:700;">You May Also <span style="color:var(--primary);">Like</span></h3>
                <div class="divider-gold center"></div>
            </div>
            <div class="row g-3">
                <?php
                $suggestions = $conn->query("SELECT * FROM products ORDER BY RAND() LIMIT 4");
                $hasSugg = $suggestions && $suggestions->num_rows > 0;
                if ($hasSugg):
                    while ($p = $suggestions->fetch_assoc()):
                        $sp = ($p['sale_price'] && $p['sale_price'] > 0) ? $p['sale_price'] : $p['price'];
                ?>
                <div class="col-lg-3 col-md-6">
                    <div class="product-card" style="height:100%;">
                        <div class="product-img-wrap">
                            <a href="product-detail.php?id=<?= $p['id'] ?>">
                                <img src="<?= htmlspecialchars($p['images']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                            </a>
                        </div>
                        <div class="product-card-body">
                            <h5 class="product-name"><a href="product-detail.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a></h5>
                            <div class="product-price"><?= formatPrice($sp) ?></div>
                            <button class="btn-vv btn-outline-primary-vv w-100 mt-2" onclick="addToCart(<?= $p['id'] ?>)" style="font-size:12px;padding:8px;">
                                <i class="fas fa-cart-plus me-1"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function changeQty(itemId, change) {
    const qtyEl = document.getElementById('qty-' + itemId);
    let qty = parseInt(qtyEl.textContent) + change;
    if (qty < 1) qty = 1;
    fetch('php/cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=update&item_id=${itemId}&quantity=${qty}`
    }).then(r => r.json()).then(d => {
        if (d.success) {
            qtyEl.textContent = qty;
            const subtotalEl = document.getElementById('subtotal-' + itemId);
            if (subtotalEl && d.item_total) subtotalEl.textContent = d.item_total;
            if (d.subtotal) { document.getElementById('cart-subtotal').textContent = d.subtotal; document.getElementById('cart-total').textContent = d.total; }
            updateCartBadge(d.cart_count || 0);
        }
    }).catch(() => { qtyEl.textContent = parseInt(qtyEl.textContent) + change > 0 ? parseInt(qtyEl.textContent) + change : 1; });
}

function removeCartItem(itemId) {
    if (!confirm('Remove this item from cart?')) return;
    fetch('php/cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=remove&item_id=${itemId}`
    }).then(r => r.json()).then(d => {
        if (d.success) {
            const el = document.getElementById('cart-item-' + itemId);
            if (el) { el.style.opacity='0'; setTimeout(()=>{ el.remove(); if(d.cart_count===0) location.reload(); }, 300); }
            updateCartBadge(d.cart_count || 0);
            if (d.subtotal) { document.getElementById('cart-subtotal').textContent = d.subtotal; document.getElementById('cart-total').textContent = d.total; }
        }
    }).catch(() => showToast('Failed to remove item. Please try again.', 'error'));
}

function clearCart() {
    if (!confirm('Remove all items from cart?')) return;
    fetch('php/cart.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=clear'
    }).then(r => r.json()).then(d => {
        if (d.success) { updateCartBadge(0); location.reload(); }
    }).catch(() => location.reload());
}

function applyCoupon() {
    const code = document.getElementById('couponInput').value.trim().toUpperCase();
    const msg = document.getElementById('couponMsg');
    const validCoupons = { 'VELVET10': 10, 'VOGUE20': 20, 'FIRST15': 15 };
    if (validCoupons[code]) {
        const pct = validCoupons[code];
        const subtotalEl = document.getElementById('cart-subtotal');
        const totalEl = document.getElementById('cart-total');
        msg.innerHTML = `<span style="color:#27ae60;"><i class="fas fa-check-circle me-1"></i>${pct}% discount applied!</span>`;
        document.getElementById('discount-row').style.display = 'flex';
        document.getElementById('discount-amt').textContent = 'Coupon: -' + pct + '%';
        showToast(`Coupon applied! You saved ${pct}%`, 'success');
    } else {
        msg.innerHTML = '<span style="color:#e74c3c;"><i class="fas fa-times-circle me-1"></i>Invalid coupon code.</span>';
    }
}
</script>

<?php include 'includes/footer.php'; ?>
