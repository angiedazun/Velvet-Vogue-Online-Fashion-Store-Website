<?php
$pageTitle = "Orders";
require_once '../config/db.php';
if (!isAdmin()) { redirect('../login.php'); }

$message = '';
// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $oid    = (int)$_POST['order_id'];
    $status = sanitize($_POST['status'] ?? '');
    $allowed = ['pending','processing','shipped','delivered','cancelled'];
    if (in_array($status, $allowed)) {
        $conn->query("UPDATE orders SET status='$status' WHERE id=$oid");
        $message = 'Order status updated.';
    }
}

$filter = sanitize($_GET['status'] ?? '');
$where  = $filter ? "WHERE o.status='".  $conn->real_escape_string($filter) ."'" : '';
$orders = $conn->query("SELECT o.*, u.first_name, u.last_name, u.email FROM orders o LEFT JOIN users u ON o.user_id=u.id $where ORDER BY o.created_at DESC");

$extraCSS = '<link rel="stylesheet" href="../admin/css/orders.css">';
$extraJS  = '<script src="../admin/js/orders.js"></script>';
include 'includes/admin_header.php';
?>
<div class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Orders</h1>
            <p class="admin-page-subtitle">Manage customer orders</p>
        </div>
    </div>

    <?php if ($message): ?><div class="alert-vv alert-success mb-4"><i class="fas fa-check-circle"></i> <?= $message ?></div><?php endif; ?>

    <!-- Filter Tabs -->
    <div style="display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap;">
        <?php foreach ([''=>'All','pending'=>'Pending','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $val=>$lbl):
            $active = $filter === $val;
            $cnt = $conn->query("SELECT COUNT(*) as c FROM orders".($val?" WHERE status='$val'":""))->fetch_assoc()['c'] ?? 0; ?>
        <a href="orders.php<?= $val ? "?status=$val" : '' ?>" style="padding:8px 16px;border-radius:20px;font-size:12px;font-weight:600;text-decoration:none;background:<?= $active?'var(--primary)':'white' ?>;color:<?= $active?'white':'var(--text-muted)' ?>;border:1px solid <?= $active?'var(--primary)':'var(--border)' ?>;">
            <?= $lbl ?> (<?= $cnt ?>)
        </a>
        <?php endforeach; ?>
    </div>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead><tr><th>Order #</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php
                $statusColors=['pending'=>'#f39c12','processing'=>'#3498db','shipped'=>'#9b59b6','delivered'=>'#27ae60','cancelled'=>'#e74c3c'];
                while ($o = $orders->fetch_assoc()):
                    $sc = $statusColors[$o['status']] ?? '#666';
                    $itemCount = $conn->query("SELECT SUM(quantity) as n FROM order_items WHERE order_id={$o['id']}")->fetch_assoc()['n'] ?? 0; ?>
                <tr>
                    <td><strong style="color:var(--primary);"><?= htmlspecialchars($o['order_number']) ?></strong></td>
                    <td>
                        <div style="font-weight:600;font-size:13px;"><?= htmlspecialchars($o['first_name'].' '.$o['last_name']) ?></div>
                        <div style="font-size:11px;color:var(--text-muted);"><?= htmlspecialchars($o['email']) ?></div>
                    </td>
                    <td style="font-size:13px;"><?= $itemCount ?> item<?= $itemCount!=1?'s':'' ?></td>
                    <td><strong><?= formatPrice($o['total_amount']) ?></strong></td>
                    <td><span style="text-transform:uppercase;font-size:11px;font-weight:700;background:var(--light);padding:3px 8px;border-radius:6px;"><?= htmlspecialchars($o['payment_method']) ?></span></td>
                    <td style="font-size:12px;color:var(--text-muted);"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                    <td><span class="status-badge" style="background:<?= $sc ?>20;color:<?= $sc ?>;"><?= ucfirst($o['status']) ?></span></td>
                    <td>
                        <form method="POST" style="display:flex;gap:6px;align-items:center;">
                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                            <select name="status" class="form-control-vv" style="font-size:11px;padding:4px 8px;width:120px;">
                                <?php foreach (['pending','processing','shipped','delivered','cancelled'] as $s): ?>
                                <option value="<?= $s ?>" <?= $o['status']==$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" style="background:var(--primary);color:white;border:none;border-radius:6px;padding:4px 10px;font-size:11px;cursor:pointer;">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
