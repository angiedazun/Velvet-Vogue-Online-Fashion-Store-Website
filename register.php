<?php
$pageTitle = "Create Account";
require_once 'config/db.php';

if (isLoggedIn()) redirect('index.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitize($_POST['first_name'] ?? '');
    $last_name  = sanitize($_POST['last_name'] ?? '');
    $email      = sanitize($_POST['email'] ?? '');
    $phone      = sanitize($_POST['phone'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    if (!$first_name || !$last_name || !$email || !$password || !$confirm) {
        $error = 'All required fields must be filled in.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if ($check) {
            $check->bind_param("s", $email);
            $check->execute();
            $check->store_result();
            if ($check->num_rows > 0) {
                $error = 'An account with this email already exists. <a href="login.php">Sign in</a>';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone, password, role, is_active) VALUES (?, ?, ?, ?, ?, 'user', 1)");
                if ($stmt) {
                    $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $hash);
                    if ($stmt->execute()) {
                        $_SESSION['message'] = "Account created successfully! Please sign in.";
                        redirect('login.php');
                    } else {
                        $error = 'Registration failed. Please try again.';
                    }
                    $stmt->close();
                }
            }
            $check->close();
        }
    }
}

$extraCSS = '<link rel="stylesheet" href="css/register.css">';
$extraJS  = '<script src="js/register.js"></script>';
include 'includes/header.php';
?>

<section class="auth-page">
    <div class="auth-bg-overlay"></div>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-6 col-lg-7 col-md-9">
                <div class="auth-card animate-on-scroll" style="padding:0;">
                    <!-- Card Header -->
                    <div class="auth-card-header">
                        <a href="index.php" class="auth-logo">
                            <i class="fas fa-gem"></i> Velvet Vogue
                        </a>
                        <h2 class="auth-title">Create Account</h2>
                        <p class="auth-subtitle">Join Velvet Vogue for exclusive fashion deals</p>
                    </div>

                    <div class="auth-card-body">
                        <?php if($error): ?>
                        <div class="alert-vv alert-error mb-4"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group-vv">
                                        <label class="form-label-vv">First Name *</label>
                                        <div class="input-group-vv">
                                            <i class="fas fa-user input-icon"></i>
                                            <input type="text" name="first_name" class="form-control-vv" placeholder="First name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-vv">
                                        <label class="form-label-vv">Last Name *</label>
                                        <div class="input-group-vv">
                                            <i class="fas fa-user input-icon"></i>
                                            <input type="text" name="last_name" class="form-control-vv" placeholder="Last name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group-vv">
                                        <label class="form-label-vv">Email Address *</label>
                                        <div class="input-group-vv">
                                            <i class="fas fa-envelope input-icon"></i>
                                            <input type="email" name="email" class="form-control-vv" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group-vv">
                                        <label class="form-label-vv">Phone Number</label>
                                        <div class="input-group-vv">
                                            <i class="fas fa-phone input-icon"></i>
                                            <input type="text" name="phone" class="form-control-vv" placeholder="+92 300 0000000" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-vv">
                                        <label class="form-label-vv">Password *</label>
                                        <div class="input-group-vv" style="position:relative;">
                                            <i class="fas fa-lock input-icon"></i>
                                            <input type="password" name="password" id="regPass" class="form-control-vv" placeholder="Min 6 characters" required>
                                            <span class="password-toggle" onclick="togglePassword('regPass',this)"><i class="fas fa-eye"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group-vv">
                                        <label class="form-label-vv">Confirm Password *</label>
                                        <div class="input-group-vv" style="position:relative;">
                                            <i class="fas fa-lock input-icon"></i>
                                            <input type="password" name="confirm_password" id="regPassConfirm" class="form-control-vv" placeholder="Re-enter password" required>
                                            <span class="password-toggle" onclick="togglePassword('regPassConfirm',this)"><i class="fas fa-eye"></i></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-start gap-2">
                                        <input class="form-check-input mt-1" type="checkbox" id="terms" required style="width:16px;height:16px;cursor:pointer;flex-shrink:0;">
                                        <label for="terms" style="font-size:12px;color:var(--text-muted);cursor:pointer;">
                                            I agree to the <a href="#" style="color:var(--primary);">Terms of Service</a> and <a href="#" style="color:var(--primary);">Privacy Policy</a>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn-vv btn-primary-vv w-100">
                                        <i class="fas fa-user-plus me-2"></i> Create Account
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Benefits -->
                        <div style="background:var(--light);border-radius:12px;padding:16px;margin-top:20px;">
                            <p style="font-size:12px;font-weight:700;color:var(--dark);margin-bottom:10px;text-transform:uppercase;letter-spacing:0.5px;">Why Join Velvet Vogue?</p>
                            <div class="row g-2">
                                <?php foreach(['Free shipping on first order','Exclusive member discounts','Early access to sales','Track your orders easily'] as $b): ?>
                                <div class="col-6" style="font-size:12px;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
                                    <i class="fas fa-check" style="color:var(--primary);font-size:10px;"></i> <?= $b ?>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <p class="auth-switch-text mt-3">
                            Already have an account? <a href="login.php">Sign in</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
