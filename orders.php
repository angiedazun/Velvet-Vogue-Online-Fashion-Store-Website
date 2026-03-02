<?php
$pageTitle = "My Orders";
require_once 'config/db.php';
if (!isLoggedIn()) { redirect('login.php?redirect=orders.php'); }

$uid = (int)$_SESSION['user_id'];

/* ─── Single order detail view ────────────────── */
$viewOrderId = isset($_GET['order']) ? (int)$_GET['order'] : 0;
$orderDetail = null;
$orderItems  = [];

if ($viewOrderId) {
    $orderDetail = $conn->query("SELECT * FROM orders WHERE id=$viewOrderId AND user_id=$uid")->fetch_assoc();
    if ($orderDetail) {
        $res = $conn->query("SELECT oi.*, p.name, p.images, p.slug FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = $viewOrderId");
        while ($row = $res->fetch_assoc()) { $orderItems[] = $row; }
    }
}

/* ─── Orders list ─────────────────────────────── */
$filter = sanitize($_GET['status'] ?? '');
$where  = $filter ? "AND o.status='" . $conn->real_escape_string($filter) . "'" : '';
$orders = $conn->query("SELECT o.*, (SELECT SUM(quantity) FROM order_items WHERE order_id=o.id) as item_count
    FROM orders o WHERE o.user_id=$uid $where ORDER BY o.created_at DESC");

$extraCSS = '<link rel="stylesheet" href="css/orders.css">';
include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-vv mb-4">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <a href="account.php">My Account</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span><?= $orderDetail ? 'Order Detail' : 'My Orders' ?></span>
        </div>
        <h1 class="page-hero-title"><?= $orderDetail ? 'Order #' . htmlspecialchars($orderDetail['order_number']) : 'My Orders' ?></h1>
    </div>
</section>

<section style="padding:60px 0 80px;">
<div class="container">

<?php if ($orderDetail): ?>
<!-- ============ ORDER DETAIL VIEW ============ -->
<div class="mb-4">
    <a href="orders.php" class="btn-vv" style="background:var(--light);color:var(--dark);padding:10px 20px;font-size:13px;border-radius:10px;text-decoration:none;font-weight:600;">
        <i class="fas fa-arrow-left me-2"></i>Back to Orders
    </a>
</div>

<div class="row g-4">
    <!-- Order Items -->
    <div class="col-lg-8">
        <div class="order-detail-card">
            <div class="order-detail-header">
                <div>
                    <h5><?= htmlspecialchars($orderDetail['order_number']) ?></h5>
                    <p>Placed on <?= date('d F Y, h:i A', strtotime($orderDetail['created_at'])) ?></p>
                </div>
                <?php
                $scMap = ['pending'=>'#f39c12','processing'=>'#3498db','shipped'=>'#9b59b6','delivered'=>'#27ae60','cancelled'=>'#e74c3c'];
                $sc = $scMap[$orderDetail['status']] ?? '#666'; ?>
                <span class="order-status-badge large" style="background:<?= $sc ?>20;color:<?= $sc ?>;"><?= ucfirst($orderDetail['status']) ?></span>
            </div>

            <!-- Order Progress -->
            <?php if ($orderDetail['status'] !== 'cancelled'): ?>
            <?php
            $steps = ['pending','processing','shipped','delivered'];
            $curIdx = array_search($orderDetail['status'], $steps);
            ?>
            <div class="order-progress">
                <?php foreach ($steps as $i => $step): ?>
                <div class="order-step <?= $i <= $curIdx ? 'done' : '' ?> <?= $i == $curIdx ? 'current' : '' ?>">
                    <div class="step-dot">
                        <?php if ($i <= $curIdx): ?><i class="fas fa-check"></i><?php else: ?><span><?= $i+1 ?></span><?php endif; ?>
                    </div>
                    <span class="step-label"><?= ucfirst($step) ?></span>
                </div>
                <?php if ($i < count($steps)-1): ?>
                <div class="step-line <?= $i < $curIdx ? 'done' : '' ?>"></div>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div style="background:#e74c3c10;border:1px solid #e74c3c30;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
                <i class="fas fa-times-circle me-2" style="color:#e74c3c;"></i>
                <span style="color:#e74c3c;font-weight:600;font-size:13px;">This order has been cancelled.</span>
            </div>
            <?php endif; ?>

            <!-- Items -->
            <h6 style="font-weight:700;margin-bottom:16px;">Order Items</h6>
            <?php foreach ($orderItems as $item):
                $img = $item['images'] ? "https://images.unsplash.com/photo-{$item['images']}?w=120&h=150&fit=crop" : "https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=120&h=150&fit=crop";
            ?>
            <div class="order-item-row">
                <div class="order-item-img">
                    <img src="<?= $img ?>" alt="<?= htmlspecialchars($item['name']) ?>" onerror="this.src='https://images.unsplash.com/photo-1434389677669?w=120&h=150&fit=crop'">
                </div>
                <div class="order-item-info">
                    <div class="order-item-name">
                        <a href="product-detail.php?slug=<?= htmlspecialchars($item['slug'] ?? '') ?>"><?= htmlspecialchars($item['name'] ?? 'Product') ?></a>
                    </div>
                    <?php if ($item['size'] || $item['color']): ?>
                    <div class="order-item-variants">
                        <?php if ($item['size']): ?><span>Size: <?= htmlspecialchars($item['size']) ?></span><?php endif; ?>
                        <?php if ($item['color']): ?><span>Colour: <?= htmlspecialchars($item['color']) ?></span><?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div class="order-item-price">
                        <span><strong>Rs. <?= number_format($item['price'], 2) ?></strong> × <?= $item['quantity'] ?></span>
                        <strong style="color:var(--primary);">Rs. <?= number_format($item['price'] * $item['quantity'], 2) ?></strong>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Summary & Shipping -->
    <div class="col-lg-4">
        <!-- Order Summary -->
        <div class="order-summary-card">
            <h6>Order Summary</h6>
            <?php $subtotal = $orderDetail['total_amount'] - $orderDetail['shipping_fee']; ?>
            <div class="summary-line"><span>Subtotal</span><span>Rs. <?= number_format($subtotal, 2) ?></span></div>
            <div class="summary-line"><span>Shipping</span><span><?= $orderDetail['shipping_fee'] > 0 ? 'Rs. ' . number_format($orderDetail['shipping_fee'], 2) : '<span style="color:#27ae60;">Free</span>' ?></span></div>
            <?php if (($orderDetail['discount'] ?? 0) > 0): ?>
            <div class="summary-line"><span>Discount</span><span style="color:#27ae60;">-Rs. <?= number_format($orderDetail['discount'], 2) ?></span></div>
            <?php endif; ?>
            <div class="summary-line total-line"><span>Total</span><span>Rs. <?= number_format($orderDetail['total_amount'], 2) ?></span></div>
            <div class="payment-badge">
                <i class="fas fa-<?= $orderDetail['payment_method'] === 'cod' ? 'money-bill' : 'credit-card' ?> me-2"></i>
                Payment: <?= strtoupper($orderDetail['payment_method']) ?>
            </div>
        </div>

        <!-- Shipping Address -->
        <div class="order-summary-card mt-3">
            <h6>Shipping Details</h6>
            <div class="shipping-info">
                <div><i class="fas fa-user me-2 text-muted"></i><?= htmlspecialchars($orderDetail['shipping_name']) ?></div>
                <div><i class="fas fa-phone me-2 text-muted"></i><?= htmlspecialchars($orderDetail['shipping_phone'] ?: '—') ?></div>
                <div><i class="fas fa-envelope me-2 text-muted"></i><?= htmlspecialchars($orderDetail['shipping_email']) ?></div>
                <div><i class="fas fa-map-marker-alt me-2 text-muted"></i><?= htmlspecialchars($orderDetail['shipping_address']) ?></div>
                <div><i class="fas fa-city me-2 text-muted"></i><?= htmlspecialchars($orderDetail['shipping_city']) ?></div>
            </div>
        </div>

        <?php if ($orderDetail['notes']): ?>
        <div class="order-summary-card mt-3">
            <h6>Order Note</h6>
            <p style="font-size:13px;color:var(--text-muted);margin:0;"><?= nl2br(htmlspecialchars($orderDetail['notes'])) ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php else: ?>
<!-- ============ ORDERS LIST ============ -->

<!-- Filter Tabs -->
<div class="orders-filter-tabs">
    <?php foreach ([''=>'All Orders','pending'=>'Pending','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $val => $lbl):
        $cnt = $conn->query("SELECT COUNT(*) as c FROM orders WHERE user_id=$uid" . ($val ? " AND status='$val'" : ""))->fetch_assoc()['c'] ?? 0;
        $active = ($filter === $val);
    ?>
    <a href="orders.php<?= $val ? "?status=$val" : '' ?>" class="orders-filter-btn <?= $active ? 'active' : '' ?>">
        <?= $lbl ?> <span class="filter-count"><?= $cnt ?></span>
    </a>
    <?php endforeach; ?>
</div>

<?php if ($orders && $orders->num_rows > 0): ?>
<div class="orders-list">
    <?php
    $scMap = ['pending'=>'#f39c12','processing'=>'#3498db','shipped'=>'#9b59b6','delivered'=>'#27ae60','cancelled'=>'#e74c3c'];
    while ($ord = $orders->fetch_assoc()):
        $sc = $scMap[$ord['status']] ?? '#666';
    ?>
    <div class="order-card">
        <div class="order-card-header">
            <div class="order-num-block">
                <div class="order-number"><?= htmlspecialchars($ord['order_number']) ?></div>
                <div class="order-date"><?= date('d M Y', strtotime($ord['created_at'])) ?></div>
            </div>
            <span class="order-status-badge" style="background:<?= $sc ?>20;color:<?= $sc ?>;"><?= ucfirst($ord['status']) ?></span>
        </div>
        <div class="order-card-body">
            <div class="order-meta-item">
                <span class="meta-label">Items</span>
                <span class="meta-value"><?= (int)$ord['item_count'] ?> item<?= $ord['item_count']!=1?'s':'' ?></span>
            </div>
            <div class="order-meta-item">
                <span class="meta-label">Total</span>
                <span class="meta-value"><strong><?= formatPrice($ord['total_amount']) ?></strong></span>
            </div>
            <div class="order-meta-item">
                <span class="meta-label">Payment</span>
                <span class="meta-value" style="text-transform:uppercase;font-size:11px;background:var(--light);padding:3px 8px;border-radius:6px;"><?= htmlspecialchars($ord['payment_method']) ?></span>
            </div>
            <div class="order-meta-item">
                <span class="meta-label">City</span>
                <span class="meta-value"><?= htmlspecialchars($ord['shipping_city'] ?: '—') ?></span>
            </div>
        </div>
        <div class="order-card-footer">
            <a href="orders.php?order=<?= $ord['id'] ?>" class="btn-vv btn-primary-vv" style="padding:10px 20px;font-size:13px;">
                <i class="fas fa-eye me-2"></i>View Order
            </a>
            <?php if ($ord['status'] === 'delivered'): ?>
            <a href="product-detail.php" class="btn-vv" style="padding:10px 20px;font-size:13px;background:var(--light);color:var(--dark);">
                <i class="fas fa-redo me-2"></i>Reorder
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php else: ?>
<div class="orders-empty-state">
    <div class="empty-icon"><i class="fas fa-box-open"></i></div>
    <h4>No <?= $filter ? ucfirst($filter) : '' ?> Orders</h4>
    <p>You haven't placed any <?= $filter ? strtolower($filter) : '' ?> orders yet.</p>
    <a href="products.php" class="btn-vv btn-primary-vv" style="padding:12px 28px;">
        <i class="fas fa-shopping-bag me-2"></i>Start Shopping
    </a>
</div>
<?php endif; ?>

<?php endif; ?>

</div>
</section>

<?php include 'includes/footer.php'; ?>
