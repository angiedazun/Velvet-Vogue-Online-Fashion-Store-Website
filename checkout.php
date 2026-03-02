<?php
$pageTitle = "Checkout";
require_once 'config/db.php';

if (!isLoggedIn()) { redirect('login.php?redirect=checkout.php'); }

$uid = (int)$_SESSION['user_id'];
$cartItems = [];
$subtotal = 0;

$res = $conn->query("SELECT c.*, p.name, p.price, p.sale_price, p.images, p.stock, p.slug
    FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id=$uid");
while ($row = $res->fetch_assoc()) {
    $price = ($row['sale_price'] && $row['sale_price'] > 0) ? $row['sale_price'] : $row['price'];
    $row['final_price'] = $price;
    $row['item_total']  = $price * $row['quantity'];
    $subtotal += $row['item_total'];
    $cartItems[] = $row;
}

if (empty($cartItems) && !isset($_GET['buy_now'])) { redirect('cart.php'); }

$shipping = $subtotal >= 3000 ? 0 : 200;
$total = $subtotal + $shipping;
$orderPlaced = false;
$orderNumber = '';

// Get user details
$userRes = $conn->query("SELECT * FROM users WHERE id=$uid");
$user = $userRes->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname    = sanitize($_POST['first_name'] ?? '');
    $lname    = sanitize($_POST['last_name'] ?? '');
    $email    = sanitize($_POST['email'] ?? '');
    $phone    = sanitize($_POST['phone'] ?? '');
    $address  = sanitize($_POST['address'] ?? '');
    $city     = sanitize($_POST['city'] ?? '');
    $province = sanitize($_POST['province'] ?? '');
    $postal   = sanitize($_POST['postal'] ?? '');
    $payment  = sanitize($_POST['payment_method'] ?? 'cod');
    $notes    = sanitize($_POST['notes'] ?? '');

    if ($fname && $email && $address && $city) {
        $orderNumber = generateOrderNumber();
        $fullAddress = "$address, $city, $province $postal";

        $orderStmt = $conn->prepare("INSERT INTO orders (order_number, user_id, total_amount, shipping_fee, payment_method, shipping_name, shipping_email, shipping_phone, shipping_address, shipping_city, notes, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,'pending')");
        $fullName = "$fname $lname";
        $orderStmt->bind_param("siddssssssss", $orderNumber, $uid, $total, $shipping, $payment, $fullName, $email, $phone, $fullAddress, $city, $notes);
        if ($orderStmt->execute()) {
            $orderId = $conn->insert_id;
            foreach ($cartItems as $item) {
                $price = $item['final_price'];
                $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size, color) VALUES (?,?,?,?,?,?)");
                $itemStmt->bind_param("iiidss", $orderId, $item['product_id'], $item['quantity'], $price, $item['size'], $item['color']);
                $itemStmt->execute();
                $itemStmt->close();
                // Reduce stock
                $conn->query("UPDATE products SET stock = stock - {$item['quantity']} WHERE id={$item['product_id']} AND stock >= {$item['quantity']}");
            }
            // Clear cart
            $conn->query("DELETE FROM cart WHERE user_id=$uid");
            $orderPlaced = true;

            // ── Email Confirmation ──────────────────────────
            $emailSubject = "Order Confirmed – " . $orderNumber . " | Velvet Vogue";
            $itemList = '';
            foreach ($cartItems as $ci) {
                $itemList .= "  - {$ci['name']} (x{$ci['quantity']}) — Rs. " . number_format($ci['item_total'], 2) . "\n";
            }
            $emailBody = "Hi $fname,\n\nThank you for your order with Velvet Vogue!\n\n";
            $emailBody .= "Order Number: $orderNumber\n";
            $emailBody .= "Payment: " . strtoupper($payment) . "\n\n";
            $emailBody .= "Items:\n$itemList\n";
            $emailBody .= "Subtotal: Rs. " . number_format($subtotal, 2) . "\n";
            $emailBody .= "Shipping: " . ($shipping > 0 ? 'Rs. ' . number_format($shipping, 2) : 'Free') . "\n";
            $emailBody .= "Total: Rs. " . number_format($total, 2) . "\n\n";
            $emailBody .= "Delivery to: $fullAddress\n\n";
            $emailBody .= "Track your order on: " . SITE_URL . "/orders.php?order=$orderId\n\n";
            $emailBody .= "Thank you for shopping with Velvet Vogue!\n– The VV Team";
            $emailHeaders = "From: " . SITE_EMAIL . "\r\nMIME-Version: 1.0\r\nContent-type: text/plain; charset=UTF-8";
            @mail($email, $emailSubject, $emailBody, $emailHeaders);
        }
        $orderStmt->close();
    }
}

$extraCSS = '<link rel="stylesheet" href="css/checkout.css">';
$extraJS  = '<script src="js/checkout.js"></script>';
include 'includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-vv mb-4">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <a href="cart.php">Cart</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-current">Checkout</span>
        </div>
        <h1 class="page-hero-title">Secure <span>Checkout</span></h1>
    </div>
</section>

<section style="padding:80px 0;background:var(--light);">
    <div class="container">
        <?php if ($orderPlaced): ?>
        <!-- Order Confirmation -->
        <div class="text-center py-5 animate-on-scroll">
            <div style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--gold));display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:2.5rem;color:white;">
                <i class="fas fa-check"></i>
            </div>
            <h2 style="font-family:'Playfair Display',serif;font-weight:700;color:var(--dark);margin-bottom:12px;">Order Placed!</h2>
            <p style="color:var(--text-muted);margin-bottom:6px;font-size:1.1rem;">Thank you for shopping with Velvet Vogue!</p>
            <p style="color:var(--text-muted);">Your order number: <strong style="color:var(--primary);"><?= htmlspecialchars($orderNumber) ?></strong></p>
            <p style="color:var(--text-muted);font-size:14px;margin-bottom:32px;">We'll send a confirmation to your email. Expected delivery: 3-5 business days.</p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="products.php" class="btn-vv btn-primary-vv"><i class="fas fa-shopping-bag me-2"></i>Continue Shopping</a>
                <a href="index.php" class="btn-vv btn-outline-primary-vv"><i class="fas fa-home me-2"></i>Go Home</a>
            </div>
        </div>
        <?php else: ?>
        <form method="POST" action="" id="checkoutForm">
            <div class="row g-4">
                <!-- Shipping Form -->
                <div class="col-lg-7">
                    <!-- Steps -->
                    <div class="checkout-steps">
                        <div class="checkout-step active"><span>1</span> Shipping</div>
                        <div class="checkout-step-line"></div>
                        <div class="checkout-step"><span>2</span> Payment</div>
                        <div class="checkout-step-line"></div>
                        <div class="checkout-step"><span>3</span> Confirm</div>
                    </div>

                    <!-- Shipping Section -->
                    <div class="form-vv mb-4">
                        <h5 style="font-family:'Playfair Display',serif;font-weight:700;margin-bottom:20px;"><i class="fas fa-map-marker-alt me-2" style="color:var(--primary);"></i>Shipping Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">First Name *</label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="first_name" class="form-control-vv" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Last Name *</label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="last_name" class="form-control-vv" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Email Address *</label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" name="email" class="form-control-vv" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Phone Number *</label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-phone input-icon"></i>
                                        <input type="text" name="phone" class="form-control-vv" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Street Address *</label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-home input-icon"></i>
                                        <input type="text" name="address" class="form-control-vv" placeholder="House #, Street, Area" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">City *</label>
                                    <input type="text" name="city" class="form-control-vv" placeholder="Karachi" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Province</label>
                                    <select name="province" class="form-control-vv">
                                        <option>Sindh</option><option>Punjab</option><option>KPK</option><option>Balochistan</option><option>Gilgit-Baltistan</option><option>AJK</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Postal Code</label>
                                    <input type="text" name="postal" class="form-control-vv" placeholder="75500">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Order Notes (Optional)</label>
                                    <textarea name="notes" class="form-control-vv" rows="3" placeholder="Special instructions for delivery..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Section -->
                    <div class="form-vv">
                        <h5 style="font-family:'Playfair Display',serif;font-weight:700;margin-bottom:20px;"><i class="fas fa-credit-card me-2" style="color:var(--primary);"></i>Payment Method</h5>
                        <div class="payment-options">
                            <?php
                            $methods = [
                                ['cod','cash-register','Cash on Delivery','Pay when your order arrives','#f39c12'],
                                ['jazzcash','mobile-alt','JazzCash','Mobile wallet payment','#e74c3c'],
                                ['easypaisa','wallet','Easypaisa','Mobile wallet payment','#27ae60'],
                                ['card','credit-card','Credit / Debit Card','Visa, Mastercard, etc.','#3498db'],
                            ];
                            foreach ($methods as $i => [$val,$icon,$lbl,$desc,$color]): ?>
                            <label class="payment-option <?= $i==0?'active':'' ?>" onclick="selectPayment(this)">
                                <input type="radio" name="payment_method" value="<?= $val ?>" <?= $i==0?'checked':'' ?> style="display:none;">
                                <div class="payment-opt-icon" style="background:<?= $color ?>20;color:<?= $color ?>;"><i class="fas fa-<?= $icon ?>"></i></div>
                                <div>
                                    <div style="font-weight:600;font-size:14px;"><?= $lbl ?></div>
                                    <div style="font-size:12px;color:var(--text-muted);"><?= $desc ?></div>
                                </div>
                                <i class="fas fa-check-circle ms-auto payment-check"></i>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-5">
                    <div class="cart-summary" style="position:sticky;top:100px;">
                        <h5 style="font-family:'Playfair Display',serif;font-weight:700;margin-bottom:20px;">Order Summary</h5>
                        <!-- Items -->
                        <div style="max-height:300px;overflow-y:auto;margin-bottom:20px;" class="pe-1">
                            <?php foreach ($cartItems as $item): ?>
                            <div style="display:flex;gap:12px;margin-bottom:14px;align-items:center;">
                                <div style="position:relative;flex-shrink:0;">
                                    <img src="<?= htmlspecialchars($item['images']) ?>" alt="" style="width:60px;height:70px;border-radius:10px;object-fit:cover;">
                                    <span style="position:absolute;top:-6px;right:-6px;width:20px;height:20px;border-radius:50%;background:var(--primary);color:white;font-size:10px;display:flex;align-items:center;justify-content:center;font-weight:700;"><?= $item['quantity'] ?></span>
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-weight:600;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($item['name']) ?></div>
                                    <?php if ($item['size']): ?><div style="font-size:11px;color:var(--text-muted);">Size: <?= htmlspecialchars($item['size']) ?></div><?php endif; ?>
                                </div>
                                <div style="font-weight:700;font-size:13px;white-space:nowrap;"><?= formatPrice($item['item_total']) ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <hr style="border-color:var(--border);">
                        <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
                        <div class="summary-row"><span>Shipping</span><span><?= $shipping==0 ? '<span style="color:#27ae60;font-weight:600;">FREE</span>' : formatPrice($shipping) ?></span></div>
                        <hr style="border-color:var(--border);">
                        <div class="summary-row total-row"><span>Total</span><span><?= formatPrice($total) ?></span></div>
                        <button type="submit" class="btn-vv btn-primary-vv w-100 mt-4" style="font-size:1rem;">
                            <i class="fas fa-lock me-2"></i> Place Order — <?= formatPrice($total) ?>
                        </button>
                        <div style="text-align:center;margin-top:12px;font-size:12px;color:var(--text-muted);">
                            <i class="fas fa-shield-alt me-1"></i> 100% Secure & Encrypted Checkout
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php endif; ?>
    </div>
</section>

<style>
.checkout-steps{display:flex;align-items:center;margin-bottom:24px;}
.checkout-step{display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--text-muted);}
.checkout-step span{width:28px;height:28px;border-radius:50%;background:var(--border);display:flex;align-items:center;justify-content:center;font-size:12px;}
.checkout-step.active{color:var(--primary);}
.checkout-step.active span{background:var(--primary);color:white;}
.checkout-step-line{flex:1;height:2px;background:var(--border);margin:0 8px;}
.payment-options{display:flex;flex-direction:column;gap:10px;}
.payment-option{display:flex;align-items:center;gap:14px;padding:14px 16px;border:2px solid var(--border);border-radius:14px;cursor:pointer;transition:all 0.2s;}
.payment-option.active{border-color:var(--primary);background:var(--primary-light);}
.payment-opt-icon{width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;}
.payment-check{color:var(--primary);opacity:0;transition:opacity 0.2s;}
.payment-option.active .payment-check{opacity:1;}
</style>
<script>
function selectPayment(el){
    document.querySelectorAll('.payment-option').forEach(e=>e.classList.remove('active'));
    el.classList.add('active');
    el.querySelector('input[type=radio]').checked=true;
}
</script>

<?php include 'includes/footer.php'; ?>
