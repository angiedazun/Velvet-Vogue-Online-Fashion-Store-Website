<?php
$pageTitle = "My Account";
require_once 'config/db.php';
if (!isLoggedIn()) { redirect('login.php?redirect=account.php'); }

$uid     = (int)$_SESSION['user_id'];
$success = '';
$error   = '';

/* ─── Handle POST actions ─────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['act'] ?? '';

    if ($act === 'profile') {
        $fname = sanitize($_POST['first_name'] ?? '');
        $lname = sanitize($_POST['last_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        if ($fname && $lname) {
            $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, phone=? WHERE id=?");
            $stmt->bind_param("sssi", $fname, $lname, $phone, $uid);
            if ($stmt->execute()) {
                $_SESSION['user_name'] = "$fname $lname";
                $success = 'Profile updated successfully!';
            }
            $stmt->close();
        } else { $error = 'First and last name are required.'; }

    } elseif ($act === 'email') {
        $email = sanitize($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } else {
            $chk = $conn->prepare("SELECT id FROM users WHERE email=? AND id!=?");
            $chk->bind_param("si", $email, $uid);
            $chk->execute(); $chk->store_result();
            if ($chk->num_rows > 0) { $error = 'This email address is already in use.'; }
            else {
                $conn->query("UPDATE users SET email='$email' WHERE id=$uid");
                $success = 'Email address updated successfully!';
            }
            $chk->close();
        }

    } elseif ($act === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if (strlen($new) < 6) {
            $error = 'New password must be at least 6 characters.';
        } elseif ($new !== $confirm) {
            $error = 'New passwords do not match.';
        } else {
            $row = $conn->query("SELECT password FROM users WHERE id=$uid")->fetch_assoc();
            if (password_verify($current, $row['password'])) {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $conn->query("UPDATE users SET password='$hash' WHERE id=$uid");
                $success = 'Password changed successfully!';
            } else {
                $error = 'Current password is incorrect.';
            }
        }
    }
}

/* ─── Fetch fresh user data ───────────────────── */
$user      = $conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();
$orderRow  = $conn->query("SELECT COUNT(*) as total_orders, IFNULL(SUM(total_amount),0) as total_spent, SUM(IF(status='delivered',1,0)) as delivered FROM orders WHERE user_id=$uid")->fetch_assoc();
$wishCount = $conn->query("SELECT COUNT(*) as c FROM wishlist WHERE user_id=$uid")->fetch_assoc()['c'] ?? 0;
$recentOrders = $conn->query("SELECT * FROM orders WHERE user_id=$uid ORDER BY created_at DESC LIMIT 5");

$extraCSS = '<link rel="stylesheet" href="css/account.css">';
$extraJS  = '<script src="js/account.js"></script>';
include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-vv mb-4">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span>My Account</span>
        </div>
        <h1 class="page-hero-title">My Account</h1>
        <p class="page-hero-sub">Welcome back, <?= htmlspecialchars($user['first_name']) ?>!</p>
    </div>
</section>

<section class="account-section">
    <div class="container">

        <?php if ($success): ?>
        <div class="alert-vv alert-success mb-4"><i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($success) ?></div>
        <?php elseif ($error): ?>
        <div class="alert-vv alert-error mb-4"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Stats Row -->
        <div class="account-stats-row">
            <div class="acc-stat-card">
                <div class="acc-stat-icon"><i class="fas fa-box"></i></div>
                <div>
                    <div class="acc-stat-value"><?= (int)$orderRow['total_orders'] ?></div>
                    <div class="acc-stat-label">Total Orders</div>
                </div>
            </div>
            <div class="acc-stat-card">
                <div class="acc-stat-icon" style="background:rgba(212,175,55,0.12);color:#D4AF37;"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="acc-stat-value"><?= (int)$orderRow['delivered'] ?></div>
                    <div class="acc-stat-label">Delivered</div>
                </div>
            </div>
            <div class="acc-stat-card">
                <div class="acc-stat-icon" style="background:rgba(231,76,60,0.1);color:#e74c3c;"><i class="fas fa-heart"></i></div>
                <div>
                    <div class="acc-stat-value"><?= $wishCount ?></div>
                    <div class="acc-stat-label">Wishlist Items</div>
                </div>
            </div>
            <div class="acc-stat-card">
                <div class="acc-stat-icon" style="background:rgba(39,174,96,0.1);color:#27ae60;"><i class="fas fa-rupee-sign"></i></div>
                <div>
                    <div class="acc-stat-value"><?= 'Rs. ' . number_format($orderRow['total_spent'], 0) ?></div>
                    <div class="acc-stat-label">Total Spent</div>
                </div>
            </div>
        </div>

        <div class="account-grid">

            <!-- LEFT: Sidebar Tabs -->
            <div class="account-sidebar">
                <div class="account-avatar-box">
                    <div class="account-avatar-circle">
                        <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                    </div>
                    <div class="account-avatar-name"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></div>
                    <div class="account-avatar-email"><?= htmlspecialchars($user['email']) ?></div>
                    <span class="account-member-since">Member since <?= date('M Y', strtotime($user['created_at'])) ?></span>
                </div>
                <nav class="account-nav" id="accountNav">
                    <button class="acc-nav-btn active" data-tab="profile"><i class="fas fa-user me-2"></i>Profile Info</button>
                    <button class="acc-nav-btn" data-tab="email"><i class="fas fa-envelope me-2"></i>Change Email</button>
                    <button class="acc-nav-btn" data-tab="password"><i class="fas fa-lock me-2"></i>Change Password</button>
                    <button class="acc-nav-btn" data-tab="orders"><i class="fas fa-box me-2"></i>Recent Orders</button>
                    <a href="orders.php" class="acc-nav-btn"><i class="fas fa-list me-2"></i>All Orders</a>
                    <a href="wishlist.php" class="acc-nav-btn"><i class="fas fa-heart me-2"></i>My Wishlist</a>
                    <hr style="border-color:var(--border);margin:8px 0;">
                    <a href="php/logout.php" class="acc-nav-btn text-danger"><i class="fas fa-sign-out-alt me-2"></i>Sign Out</a>
                </nav>
            </div>

            <!-- RIGHT: Tab Content -->
            <div class="account-content">

                <!-- PROFILE TAB -->
                <div class="account-tab active" id="tab-profile">
                    <div class="account-card">
                        <div class="account-card-header">
                            <h5><i class="fas fa-user me-2"></i>Profile Information</h5>
                            <p>Update your personal details</p>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="act" value="profile">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label-vv">First Name <span class="text-danger">*</span></label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="first_name" class="form-control-vv" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-vv">Last Name <span class="text-danger">*</span></label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="last_name" class="form-control-vv" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-vv">Phone Number</label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-phone input-icon"></i>
                                        <input type="tel" name="phone" class="form-control-vv" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="+92 300 0000000">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-vv">Current Email</label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" class="form-control-vv" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                    </div>
                                    <small class="acc-help-text">To change email, use the "Change Email" tab</small>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn-vv btn-primary-vv">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- EMAIL TAB -->
                <div class="account-tab" id="tab-email">
                    <div class="account-card">
                        <div class="account-card-header">
                            <h5><i class="fas fa-envelope me-2"></i>Change Email Address</h5>
                            <p>Update your login email address</p>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="act" value="email">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label-vv">Current Email</label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" class="form-control-vv" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label-vv">New Email Address <span class="text-danger">*</span></label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-at input-icon"></i>
                                        <input type="email" name="email" class="form-control-vv" placeholder="Enter new email address" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn-vv btn-primary-vv">
                                    <i class="fas fa-save me-2"></i>Update Email
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- PASSWORD TAB -->
                <div class="account-tab" id="tab-password">
                    <div class="account-card">
                        <div class="account-card-header">
                            <h5><i class="fas fa-lock me-2"></i>Change Password</h5>
                            <p>Keep your account secure with a strong password</p>
                        </div>
                        <form method="POST" id="changePasswordForm">
                            <input type="hidden" name="act" value="password">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label-vv">Current Password <span class="text-danger">*</span></label>
                                    <div class="input-group-vv" style="position:relative;">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" name="current_password" id="currentPwd" class="form-control-vv" required>
                                        <span class="password-toggle" onclick="togglePassword('currentPwd',this)"><i class="fas fa-eye"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-vv">New Password <span class="text-danger">*</span></label>
                                    <div class="input-group-vv" style="position:relative;">
                                        <i class="fas fa-key input-icon"></i>
                                        <input type="password" name="new_password" id="newPwd" class="form-control-vv" minlength="6" required>
                                        <span class="password-toggle" onclick="togglePassword('newPwd',this)"><i class="fas fa-eye"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-vv">Confirm New Password <span class="text-danger">*</span></label>
                                    <div class="input-group-vv" style="position:relative;">
                                        <i class="fas fa-key input-icon"></i>
                                        <input type="password" name="confirm_password" id="confirmPwd" class="form-control-vv" required>
                                        <span class="password-toggle" onclick="togglePassword('confirmPwd',this)"><i class="fas fa-eye"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="password-strength-bar mt-3" id="pwdStrengthBar" style="display:none;">
                                <div class="strength-track"><div class="strength-fill" id="pwdStrengthFill"></div></div>
                                <span class="strength-label" id="pwdStrengthLabel"></span>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn-vv btn-primary-vv">
                                    <i class="fas fa-shield-alt me-2"></i>Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ORDERS TAB -->
                <div class="account-tab" id="tab-orders">
                    <div class="account-card">
                        <div class="account-card-header">
                            <h5><i class="fas fa-box me-2"></i>Recent Orders</h5>
                            <a href="orders.php" style="font-size:13px;color:var(--primary);font-weight:600;text-decoration:none;">View All →</a>
                        </div>
                        <?php if ($recentOrders && $recentOrders->num_rows > 0): ?>
                        <div class="acc-orders-list">
                            <?php
                            $scMap = ['pending'=>'#f39c12','processing'=>'#3498db','shipped'=>'#9b59b6','delivered'=>'#27ae60','cancelled'=>'#e74c3c'];
                            while ($ord = $recentOrders->fetch_assoc()):
                                $sc = $scMap[$ord['status']] ?? '#666';
                                $itemCount = $conn->query("SELECT SUM(quantity) as n FROM order_items WHERE order_id={$ord['id']}")->fetch_assoc()['n'] ?? 0;
                            ?>
                            <div class="acc-order-row">
                                <div class="acc-order-num">
                                    <strong><?= htmlspecialchars($ord['order_number']) ?></strong>
                                    <span><?= date('d M Y', strtotime($ord['created_at'])) ?></span>
                                </div>
                                <div class="acc-order-meta">
                                    <span><?= $itemCount ?> item<?= $itemCount!=1?'s':'' ?></span>
                                    <strong><?= formatPrice($ord['total_amount']) ?></strong>
                                </div>
                                <div>
                                    <span class="order-status-badge" style="background:<?= $sc ?>20;color:<?= $sc ?>;"><?= ucfirst($ord['status']) ?></span>
                                </div>
                                <a href="orders.php?order=<?= $ord['id'] ?>" class="btn-vv" style="padding:7px 14px;font-size:12px;">View</a>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <?php else: ?>
                        <div class="acc-empty-state">
                            <i class="fas fa-box-open"></i>
                            <h6>No orders yet</h6>
                            <p>You haven't placed any orders. Start shopping!</p>
                            <a href="products.php" class="btn-vv btn-primary-vv" style="padding:10px 24px;font-size:13px;">Shop Now</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div><!-- /.account-content -->
        </div><!-- /.account-grid -->
    </div>
</section>

<?php include 'includes/footer.php'; ?>
