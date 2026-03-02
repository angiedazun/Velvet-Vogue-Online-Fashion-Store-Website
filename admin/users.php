<?php
$pageTitle = "Customers";
require_once '../config/db.php';
if (!isAdmin()) { redirect('../login.php'); }

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = (int)($_POST['user_id'] ?? 0);
    $act = $_POST['act'] ?? '';
    if ($act === 'toggle_status') {
        $conn->query("UPDATE users SET is_active = IF(is_active=1,0,1) WHERE id=$uid AND role='user'");
        $message = 'User status updated.';
    } elseif ($act === 'delete') {
        $conn->query("DELETE FROM users WHERE id=$uid AND role='user'");
        $message = 'User deleted.';
    }
}

$search = sanitize($_GET['s'] ?? '');
$where  = $search ? "WHERE role='user' AND (first_name LIKE '%{$conn->real_escape_string($search)}%' OR last_name LIKE '%{$conn->real_escape_string($search)}%' OR email LIKE '%{$conn->real_escape_string($search)}%')" : "WHERE role='user'";
$users  = $conn->query("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id=u.id) as order_count FROM users u $where ORDER BY u.created_at DESC");

$extraCSS = '<link rel="stylesheet" href="../admin/css/users.css">';
$extraJS  = '<script src="../admin/js/users.js"></script>';
include 'includes/admin_header.php';
?>
<div class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Customers</h1>
            <p class="admin-page-subtitle">View and manage registered customers</p>
        </div>
    </div>

    <?php if ($message): ?><div class="alert-vv alert-success mb-4"><i class="fas fa-check-circle"></i> <?= $message ?></div><?php endif; ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h5>All Customers (<?= $users ? $users->num_rows : 0 ?>)</h5>
            <form method="GET" style="display:flex;gap:8px;">
                <input type="text" name="s" class="form-control-vv" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>" style="width:250px;">
                <button type="submit" class="btn-admin-sm">Search</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead><tr><th>#</th><th>Customer</th><th>Phone</th><th>Orders</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if($users) while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td style="color:var(--text-muted);font-size:12px;"><?= $u['id'] ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:38px;height:38px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:white;flex-shrink:0;"><?= strtoupper(substr($u['first_name'],0,1)) ?></div>
                            <div>
                                <div style="font-weight:600;font-size:13px;"><?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?></div>
                                <div style="font-size:11px;color:var(--text-muted);"><?= htmlspecialchars($u['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;"><?= htmlspecialchars($u['phone'] ?: '—') ?></td>
                    <td>
                        <a href="orders.php?customer=<?= $u['id'] ?>" style="font-weight:700;color:var(--primary);font-size:13px;text-decoration:none;">
                            <?= $u['order_count'] ?> order<?= $u['order_count']!=1?'s':'' ?>
                        </a>
                    </td>
                    <td style="font-size:12px;color:var(--text-muted);"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                    <td><span class="status-badge" style="background:<?= $u['is_active']?'#27ae6020':'#e74c3c20' ?>;color:<?= $u['is_active']?'#27ae60':'#e74c3c' ?>"><?= $u['is_active'] ? 'Active' : 'Suspended' ?></span></td>
                    <td>
                        <div style="display:flex;gap:8px;">
                            <button type="button" class="btn-view-user" data-user-id="<?= $u['id'] ?>" style="background:none;border:none;padding:0;cursor:pointer;color:var(--primary);font-size:13px;" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>"><input type="hidden" name="act" value="toggle_status">
                                <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;color:var(--primary);font-size:13px;" title="Toggle Status">
                                    <i class="fas fa-<?= $u['is_active']?'ban':'check-circle' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Delete this customer?')">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>"><input type="hidden" name="act" value="delete">
                                <button type="submit" style="background:none;border:none;padding:0;cursor:pointer;color:#e74c3c;font-size:13px;" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- ============ USER DETAIL DRAWER ============ -->
<div id="userDrawerOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:1050;backdrop-filter:blur(2px);"></div>
<div id="userDrawer" style="position:fixed;right:-400px;top:0;width:380px;max-width:95vw;height:100vh;background:white;z-index:1051;box-shadow:-4px 0 30px rgba(0,0,0,0.15);transition:right 0.3s cubic-bezier(0.34,1.56,0.64,1);overflow-y:auto;display:flex;flex-direction:column;">
    <div style="background:linear-gradient(135deg,var(--dark) 0%,var(--primary) 100%);padding:24px 20px;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <div style="width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,0.2);color:white;font-size:1.2rem;font-weight:700;display:flex;align-items:center;justify-content:center;margin-bottom:10px;" id="drawerAvatar">U</div>
            <div style="color:white;font-weight:700;font-size:1rem;" id="drawerUserName">—</div>
            <div style="color:rgba(255,255,255,0.6);font-size:12px;" id="drawerUserEmail">—</div>
        </div>
        <button id="drawerCloseBtn" style="background:rgba(255,255,255,0.15);border:none;width:32px;height:32px;border-radius:50%;color:white;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;align-self:flex-start;">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div style="padding:20px;flex:1;">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
            <div style="background:#f8f9fa;border-radius:12px;padding:14px;text-align:center;">
                <div style="font-size:1.4rem;font-weight:700;color:var(--primary);" id="drawerTotalOrders">—</div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:500;">Total Orders</div>
            </div>
            <div style="background:#f8f9fa;border-radius:12px;padding:14px;text-align:center;">
                <div style="font-size:1.1rem;font-weight:700;color:#27ae60;" id="drawerTotalSpent">—</div>
                <div style="font-size:11px;color:var(--text-muted);font-weight:500;">Total Spent</div>
            </div>
        </div>
        <div style="border:1px solid var(--border);border-radius:12px;overflow:hidden;">
            <div style="padding:10px 14px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;font-size:13px;">
                <span style="color:var(--text-muted);">Phone</span>
                <span style="font-weight:600;" id="drawerUserPhone">—</span>
            </div>
            <div style="padding:10px 14px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;font-size:13px;">
                <span style="color:var(--text-muted);">Joined</span>
                <span style="font-weight:600;" id="drawerUserJoined">—</span>
            </div>
            <div style="padding:10px 14px;display:flex;justify-content:space-between;font-size:13px;">
                <span style="color:var(--text-muted);">Delivered</span>
                <span style="font-weight:600;color:#27ae60;" id="drawerDelivered">—</span>
            </div>
        </div>
        <div id="drawerLastOrder" style="display:none;margin-top:16px;">
            <p style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);margin-bottom:8px;">Last Order</p>
            <div style="background:var(--light);border-radius:12px;padding:14px;font-size:13px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="color:var(--primary);font-weight:700;" id="drawerLastOrderNum">—</span>
                    <span id="drawerLastOrderStatus" style="border-radius:20px;padding:2px 10px;font-size:11px;font-weight:700;"></span>
                </div>
                <div style="display:flex;justify-content:space-between;color:var(--text-muted);">
                    <span id="drawerLastOrderDate">—</span>
                    <strong id="drawerLastOrderAmount">—</strong>
                </div>
            </div>
        </div>
        <div style="margin-top:20px;display:flex;gap:10px;">
            <a id="drawerViewOrdersLink" href="orders.php" class="btn-admin-action" style="flex:1;justify-content:center;font-size:12px;">
                <i class="fas fa-box me-2"></i>View Orders
            </a>
        </div>
    </div>
</div>
<style>
#userDrawer.open { right: 0; }
#userDrawerOverlay.show { display: block !important; }
</style>

<?php include 'includes/admin_footer.php'; ?>
