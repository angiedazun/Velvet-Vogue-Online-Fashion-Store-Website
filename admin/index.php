<?php
$pageTitle = "Admin Dashboard";
require_once '../config/db.php';
if (!isAdmin()) { redirect('../login.php'); }

// Stats
$totalOrders    = $conn->query("SELECT COUNT(*) as c FROM orders")->fetch_assoc()['c'] ?? 0;
$totalProducts  = $conn->query("SELECT COUNT(*) as c FROM products")->fetch_assoc()['c'] ?? 0;
$totalUsers     = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='user'")->fetch_assoc()['c'] ?? 0;
$totalRevenue   = $conn->query("SELECT SUM(total_amount) as r FROM orders WHERE status != 'cancelled'")->fetch_assoc()['r'] ?? 0;
$pendingOrders  = $conn->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c'] ?? 0;
$newMessages    = $conn->query("SELECT COUNT(*) as c FROM contacts WHERE is_read=0")->fetch_assoc()['c'] ?? 0;

$recentOrders   = $conn->query("SELECT o.*, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC LIMIT 7");
$topProducts    = $conn->query("SELECT p.name, p.images, p.price, p.sale_price, p.stock, SUM(oi.quantity) as sold FROM order_items oi JOIN products p ON oi.product_id=p.id GROUP BY oi.product_id ORDER BY sold DESC LIMIT 5");
$extraCSS = '<link rel="stylesheet" href="../admin/css/dashboard.css">';
$extraJS  = '<script src="../admin/js/dashboard.js"></script>';
include 'includes/admin_header.php';
?>

<div class="admin-content">
    <!-- Page Header -->
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Dashboard</h1>
            <p class="admin-page-subtitle">Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</p>
        </div>
        <div style="font-size:13px;color:var(--text-muted);"><?= date('l, F j, Y') ?></div>
    </div>

    <!-- Stat Cards -->
    <div class="admin-stats-grid">
        <?php
        $stats = [
            ['Total Revenue','Rs. '.number_format($totalRevenue),'chart-line','#6C3483','Revenue this month','fa-arrow-up text-success','12%'],
            ['Total Orders',$totalOrders,'shopping-bag','#3498db',"$pendingOrders pending",'fa-clock text-warning',''],
            ['Products',$totalProducts,'tshirt','#27ae60','Active listings','fa-check text-success',''],
            ['Customers',$totalUsers,'users','#e74c3c','Registered users','fa-user-plus text-primary',''],
        ];
        foreach ($stats as [$label,$val,$icon,$color,$sub,$subIcon,$trend]): ?>
        <div class="admin-stat-card">
            <div class="stat-icon" style="background:<?= $color ?>20;color:<?= $color ?>;">
                <i class="fas fa-<?= $icon ?>"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $val ?></div>
                <div class="stat-label"><?= $label ?></div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">
                    <i class="fas <?= $subIcon ?> me-1"></i><?= $sub ?>
                    <?= $trend ? "<span style='color:#27ae60;font-weight:600;'>↑ $trend</span>" : '' ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row g-4">
        <!-- Recent Orders -->
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5>Recent Orders</h5>
                    <a href="orders.php" class="btn-admin-sm">View All</a>
                </div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th></tr>
                        </thead>
                        <tbody>
                            <?php while ($order = $recentOrders->fetch_assoc()):
                                $statusColors=['pending'=>'#f39c12','processing'=>'#3498db','shipped'=>'#9b59b6','delivered'=>'#27ae60','cancelled'=>'#e74c3c'];
                                $sc = $statusColors[$order['status']] ?? '#666'; ?>
                            <tr>
                                <td><strong style="color:var(--primary);"><?= htmlspecialchars($order['order_number']) ?></strong></td>
                                <td><?= htmlspecialchars($order['first_name'].' '.$order['last_name']) ?></td>
                                <td><strong><?= formatPrice($order['total_amount']) ?></strong></td>
                                <td><span style="text-transform:uppercase;font-size:11px;font-weight:700;"><?= htmlspecialchars($order['payment_method']) ?></span></td>
                                <td><span class="status-badge" style="background:<?= $sc ?>20;color:<?= $sc ?>;"><?= ucfirst($order['status']) ?></span></td>
                                <td style="color:var(--text-muted);font-size:12px;"><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5>Top Selling</h5>
                    <a href="products.php" class="btn-admin-sm">Manage</a>
                </div>
                <?php while ($p = $topProducts->fetch_assoc()):
                    $price = ($p['sale_price'] && $p['sale_price'] > 0) ? $p['sale_price'] : $p['price']; ?>
                <div style="display:flex;gap:12px;align-items:center;padding:12px 0;border-bottom:1px solid var(--border);">
                    <img src="<?= htmlspecialchars($p['images']) ?>" alt="" style="width:48px;height:56px;border-radius:10px;object-fit:cover;flex-shrink:0;">
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($p['name']) ?></div>
                        <div style="font-size:12px;color:var(--text-muted);"><?= formatPrice($price) ?></div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div style="font-weight:700;font-size:13px;color:var(--primary);"><?= $p['sold'] ?> sold</div>
                        <div style="font-size:11px;color:var(--text-muted);">Stock: <?= $p['stock'] ?></div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Quick Actions -->
            <div class="admin-card mt-4">
                <div class="admin-card-header"><h5>Quick Actions</h5></div>
                <div class="d-flex flex-column gap-2">
                    <a href="products.php?action=add" class="btn-admin-action"><i class="fas fa-plus-circle"></i> Add New Product</a>
                    <a href="orders.php?status=pending" class="btn-admin-action"><i class="fas fa-clock"></i> Pending Orders <?= $pendingOrders > 0 ? "<span style='background:#e74c3c;color:white;border-radius:20px;padding:2px 8px;font-size:11px;margin-left:auto;'>$pendingOrders</span>" : '' ?></a>
                    <a href="contacts.php?filter=unread" class="btn-admin-action"><i class="fas fa-envelope"></i> New Messages <?= $newMessages > 0 ? "<span style='background:#e74c3c;color:white;border-radius:20px;padding:2px 8px;font-size:11px;margin-left:auto;'>$newMessages</span>" : '' ?></a>
                    <a href="users.php" class="btn-admin-action"><i class="fas fa-users"></i> Manage Users</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
