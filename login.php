<?php
$pageTitle = "Login";
require_once 'config/db.php';

if (isLoggedIn()) redirect('index.php');

$error = '';
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $conn->prepare("SELECT id, first_name, last_name, password, role, is_active FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                if ($user['is_active'] && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_role'] = $user['role'];
                    $redirect = $_GET['redirect'] ?? ($user['role'] === 'admin' ? 'admin/index.php' : 'index.php');
                    redirect($redirect);
                } else {
                    $error = !$user['is_active'] ? 'Your account has been suspended.' : 'Incorrect password.';
                }
            } else {
                $error = 'No account found with that email address.';
            }
            $stmt->close();
        }
    } else {
        $error = 'Please enter your email and password.';
    }
}

$extraCSS = '<link rel="stylesheet" href="css/login.css">';
$extraJS  = '<script src="js/login.js"></script>';
include 'includes/header.php';
?>

<section class="auth-page">
    <div class="auth-bg-overlay"></div>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div class="auth-card animate-on-scroll">
                    <!-- Card Header -->
                    <div class="auth-card-header">
                        <a href="index.php" class="auth-logo">
                            <i class="fas fa-gem"></i> Velvet Vogue
                        </a>
                        <h2 class="auth-title">Welcome Back</h2>
                        <p class="auth-subtitle">Sign in to your account to continue</p>
                    </div>

                    <!-- Card Body -->
                    <div class="auth-card-body">
                        <?php if($message): ?>
                        <div class="alert-vv alert-success mb-4"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
                        <?php elseif($error): ?>
                        <div class="alert-vv alert-error mb-4"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="form-group-vv mb-3">
                                <label class="form-label-vv">Email Address</label>
                                <div class="input-group-vv">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" class="form-control-vv" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
                                </div>
                            </div>
                            <div class="form-group-vv mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label-vv mb-0">Password</label>
                                    <a href="forgot-password.php" style="font-size:12px;color:var(--primary);">Forgot password?</a>
                                </div>
                                <div class="input-group-vv" style="position:relative;">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="password" id="loginPassword" class="form-control-vv" placeholder="Enter your password" required>
                                    <span class="password-toggle" onclick="togglePassword('loginPassword', this)">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <input class="form-check-input me-2" type="checkbox" id="rememberMe" style="width:16px;height:16px;cursor:pointer;">
                                <label class="form-check-label" for="rememberMe" style="font-size:13px;color:var(--text-muted);cursor:pointer;">Remember me</label>
                            </div>
                            <button type="submit" class="btn-vv btn-primary-vv w-100 mb-4">
                                <i class="fas fa-sign-in-alt me-2"></i> Sign In
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="auth-divider"><span>or continue with</span></div>

                        <!-- Social Login Placeholder -->
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <button class="btn-social w-100" onclick="showToast('Social login coming soon!','info')">
                                    <i class="fab fa-google me-2" style="color:#ea4335;"></i> Google
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn-social w-100" onclick="showToast('Social login coming soon!','info')">
                                    <i class="fab fa-facebook-f me-2" style="color:#1877f2;"></i> Facebook
                                </button>
                            </div>
                        </div>

                        <p class="auth-switch-text">
                            Don't have an account? <a href="register.php">Create one free</a>
                        </p>
                    </div>
                </div>

                <!-- Demo Credentials -->
                <div style="background:rgba(255,255,255,0.1);border-radius:12px;padding:14px 20px;margin-top:16px;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.15);">
                    <p style="color:rgba(255,255,255,0.9);font-size:12px;margin:0;text-align:center;">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Demo Admin:</strong> admin@velvetvogue.com / admin123
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
