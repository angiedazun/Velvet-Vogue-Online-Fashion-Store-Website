<?php
$pageTitle = "Forgot Password";
require_once 'config/db.php';

if (isLoggedIn()) redirect('account.php');

// Ensure password_resets table exists
$conn->query("CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$step    = 'request'; // request | sent | reset | done
$error   = '';
$success = '';
$token   = sanitize($_GET['token'] ?? '');

/* ─── If token in URL → show reset form ──────── */
if ($token) {
    $now = date('Y-m-d H:i:s');
    $row = $conn->query("SELECT * FROM password_resets WHERE token='$token' AND expires_at > '$now' AND used=0 LIMIT 1")->fetch_assoc();
    if ($row) {
        $step = 'reset';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'])) {
            $new     = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';
            if (strlen($new) < 6) { $error = 'Password must be at least 6 characters.'; }
            elseif ($new !== $confirm) { $error = 'Passwords do not match.'; }
            else {
                $hash  = password_hash($new, PASSWORD_DEFAULT);
                $email = $row['email'];
                $conn->query("UPDATE users SET password='$hash' WHERE email='$email'");
                $conn->query("UPDATE password_resets SET used=1 WHERE token='$token'");
                $step = 'done';
            }
        }
    } else {
        $error = 'This reset link is invalid or has expired. Please request a new one.';
    }
}

/* ─── Email form submitted ────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$token) {
    $email = sanitize($_POST['email'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $user = $conn->query("SELECT id, first_name FROM users WHERE email='$email' AND is_active=1 LIMIT 1")->fetch_assoc();
        if ($user) {
            // Delete old tokens for this email
            $conn->query("DELETE FROM password_resets WHERE email='$email'");
            // Create token
            $newToken  = bin2hex(random_bytes(32));
            $expires   = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $stmt      = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?,?,?)");
            $stmt->bind_param("sss", $email, $newToken, $expires);
            $stmt->execute();
            $stmt->close();

            $resetLink = SITE_URL . "/forgot-password.php?token=$newToken";

            // Try to send email (may not work without mail config — link shown on page as fallback)
            $subject = "Reset Your Velvet Vogue Password";
            $body    = "Hi {$user['first_name']},\n\nClick the link below to reset your password (valid for 1 hour):\n\n$resetLink\n\nIf you did not request this, ignore this email.\n\n– Velvet Vogue";
            $headers = "From: noreply@velvetvogue.com\r\nMIME-Version: 1.0\r\nContent-type: text/plain";
            @mail($email, $subject, $body, $headers);

            $_SESSION['_reset_link'] = $resetLink; // Store for local dev display
            $step = 'sent';
        } else {
            // Don't reveal if email exists
            $step = 'sent';
        }
    }
}

$extraCSS = '<link rel="stylesheet" href="css/login.css">';
include 'includes/header.php';
?>

<section class="auth-page">
    <div class="auth-bg-overlay"></div>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-5 col-lg-6 col-md-8">
                <div class="auth-card animate-on-scroll">

                    <?php if ($step === 'request'): ?>
                    <!-- STEP 1: Enter email -->
                    <div class="auth-card-header">
                        <a href="index.php" class="auth-logo"><i class="fas fa-gem"></i> Velvet Vogue</a>
                        <h2 class="auth-title">Forgot Password?</h2>
                        <p class="auth-subtitle">Enter your email and we'll send a reset link</p>
                    </div>
                    <div class="auth-card-body">
                        <?php if ($error): ?>
                        <div class="alert-vv alert-error mb-4"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="form-group-vv mb-4">
                                <label class="form-label-vv">Email Address</label>
                                <div class="input-group-vv">
                                    <i class="fas fa-envelope input-icon"></i>
                                    <input type="email" name="email" class="form-control-vv" placeholder="you@example.com" required autofocus>
                                </div>
                            </div>
                            <button type="submit" class="btn-vv btn-primary-vv w-100 mb-3">
                                <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                            </button>
                        </form>
                        <p class="auth-switch-text">Remembered your password? <a href="login.php">Sign in</a></p>
                    </div>

                    <?php elseif ($step === 'sent'): ?>
                    <!-- STEP 2: Email sent confirmation -->
                    <div class="auth-card-header" style="background:linear-gradient(135deg,#1a6e3a 0%,#27ae60 100%);">
                        <a href="index.php" class="auth-logo"><i class="fas fa-gem"></i> Velvet Vogue</a>
                        <div style="font-size:3rem;margin:12px 0;"><i class="fas fa-envelope-open-text" style="color:#D4AF37;"></i></div>
                        <h2 class="auth-title">Check Your Email</h2>
                        <p class="auth-subtitle">If an account exists, a reset link has been sent.</p>
                    </div>
                    <div class="auth-card-body" style="text-align:center;">
                        <p style="color:var(--text-muted);font-size:14px;margin-bottom:20px;">
                            The link expires in <strong>1 hour</strong>. Check your spam folder if you don't see it.
                        </p>
                        <?php if (isset($_SESSION['_reset_link'])): ?>
                        <div style="background:#1a0a2e0d;border:1px dashed var(--primary);border-radius:10px;padding:14px;margin-bottom:20px;text-align:left;">
                            <p style="font-size:11px;color:var(--text-muted);margin-bottom:6px;font-weight:700;text-transform:uppercase;letter-spacing:1px;">
                                <i class="fas fa-laptop-code me-1"></i> Dev Mode – Reset Link:
                            </p>
                            <a href="<?= htmlspecialchars($_SESSION['_reset_link']) ?>" style="font-size:12px;word-break:break-all;color:var(--primary);">
                                <?= htmlspecialchars($_SESSION['_reset_link']) ?>
                            </a>
                        </div>
                        <?php unset($_SESSION['_reset_link']); ?>
                        <?php endif; ?>
                        <a href="login.php" class="btn-vv btn-primary-vv" style="padding:12px 28px;">
                            <i class="fas fa-arrow-left me-2"></i>Back to Login
                        </a>
                    </div>

                    <?php elseif ($step === 'reset'): ?>
                    <!-- STEP 3: Enter new password -->
                    <div class="auth-card-header">
                        <a href="index.php" class="auth-logo"><i class="fas fa-gem"></i> Velvet Vogue</a>
                        <h2 class="auth-title">Set New Password</h2>
                        <p class="auth-subtitle">Choose a strong password for your account</p>
                    </div>
                    <div class="auth-card-body">
                        <?php if ($error): ?>
                        <div class="alert-vv alert-error mb-4"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="form-group-vv mb-3">
                                <label class="form-label-vv">New Password</label>
                                <div class="input-group-vv" style="position:relative;">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="new_password" id="newPwd" class="form-control-vv" minlength="6" required>
                                    <span class="password-toggle" onclick="togglePassword('newPwd',this)"><i class="fas fa-eye"></i></span>
                                </div>
                            </div>
                            <div class="form-group-vv mb-4">
                                <label class="form-label-vv">Confirm Password</label>
                                <div class="input-group-vv" style="position:relative;">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" name="confirm_password" id="confPwd" class="form-control-vv" required>
                                    <span class="password-toggle" onclick="togglePassword('confPwd',this)"><i class="fas fa-eye"></i></span>
                                </div>
                            </div>
                            <button type="submit" class="btn-vv btn-primary-vv w-100">
                                <i class="fas fa-shield-alt me-2"></i>Reset Password
                            </button>
                        </form>
                    </div>

                    <?php elseif ($step === 'done'): ?>
                    <!-- STEP 4: Done -->
                    <div class="auth-card-header" style="background:linear-gradient(135deg,#1a6e3a 0%,#27ae60 100%);">
                        <a href="index.php" class="auth-logo"><i class="fas fa-gem"></i> Velvet Vogue</a>
                        <div style="font-size:3rem;margin:12px 0;"><i class="fas fa-check-circle" style="color:#D4AF37;"></i></div>
                        <h2 class="auth-title">Password Reset!</h2>
                        <p class="auth-subtitle">Your password has been changed successfully.</p>
                    </div>
                    <div class="auth-card-body" style="text-align:center;">
                        <p style="color:var(--text-muted);margin-bottom:24px;">You can now sign in with your new password.</p>
                        <a href="login.php" class="btn-vv btn-primary-vv" style="padding:12px 32px;">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In Now
                        </a>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
