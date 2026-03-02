<?php
require_once dirname(__DIR__) . '/config/db.php';
$cartCount = getCartCount();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Velvet Vogue - Premium Online Fashion Store. Discover the latest trends in clothing, accessories & more.">
    <meta name="keywords" content="fashion, clothing, velvet vogue, online store, Pakistan">
    <title><?= isset($pageTitle) ? $pageTitle . ' | Velvet Vogue' : 'Velvet Vogue – Premium Fashion Store' ?></title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="shortcut icon" href="favicon.svg">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= (strpos($currentPage, 'admin') !== false) ? '../' : '' ?>css/style.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>👗</text></svg>">

    <?php if (isset($extraCSS)) echo $extraCSS; ?>
</head>
<body>

<!-- Preloader -->
<div id="preloader">
    <div class="preloader-logo">VV</div>
    <div class="preloader-bar"></div>
</div>

<!-- Search Overlay -->
<div id="searchOverlay" style="position:fixed;inset:0;background:rgba(26,10,46,0.97);z-index:9999;display:flex;align-items:center;justify-content:center;opacity:0;visibility:hidden;transition:all 0.3s ease;" class="search-overlay">
    <button id="searchClose" style="position:absolute;top:30px;right:40px;background:none;border:none;color:white;font-size:2rem;cursor:pointer;"><i class="fas fa-times"></i></button>
    <div style="width:100%;max-width:600px;padding:0 20px;">
        <p style="color:rgba(255,255,255,0.5);font-size:12px;letter-spacing:3px;text-transform:uppercase;margin-bottom:16px;">Search Products</p>
        <input id="searchInput" type="text" placeholder="What are you looking for?" style="width:100%;background:transparent;border:none;border-bottom:2px solid rgba(212,175,55,0.5);color:white;font-size:2rem;font-family:'Playfair Display',serif;padding:12px 0;outline:none;" oninput="liveSearch(this.value)">
        <div id="searchResults" style="margin-top:24px;"></div>
    </div>
</div>

<style>
.search-overlay.active { opacity: 1 !important; visibility: visible !important; }
</style>

<!-- Navbar -->
<nav class="navbar-main">
    <!-- Top Bar -->
    <div class="topbar d-none d-md-block">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex gap-4">
                    <span><i class="fas fa-phone me-1"></i> +92 300 1234567</span>
                    <span><i class="fas fa-envelope me-1"></i> info@velvetvogue.com</span>
                </div>
                <div class="d-flex gap-4">
                    <a href="#"><i class="fab fa-instagram me-1"></i> Instagram</a>
                    <a href="#"><i class="fab fa-facebook-f me-1"></i> Facebook</a>
                    <span><i class="fas fa-truck me-1"></i> Free shipping over Rs. 3,000</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navbar -->
    <div class="container">
        <div class="navbar-inner">
            <a href="<?= (strpos($currentPage, 'admin') !== false) ? '../' : '' ?>index.php" class="navbar-logo">
                Velvet <span>Vogue</span>
            </a>

            <div class="nav-links">
                <a href="<?= (strpos($currentPage, 'admin') !== false) ? '../' : '' ?>index.php" class="<?= $currentPage == 'index.php' ? 'active' : '' ?>">Home</a>
                <a href="<?= (strpos($currentPage, 'admin') !== false) ? '../' : '' ?>products.php" class="<?= $currentPage == 'products.php' ? 'active' : '' ?>">Shop</a>
                <a href="<?= (strpos($currentPage, 'admin') !== false) ? '../' : '' ?>products.php?category=women" class="<?= (isset($_GET['category']) && $_GET['category']=='women') ? 'active' : '' ?>">Women</a>
                <a href="<?= (strpos($currentPage, 'admin') !== false) ? '../' : '' ?>products.php?category=men" class="<?= (isset($_GET['category']) && $_GET['category']=='men') ? 'active' : '' ?>">Men</a>
                <a href="<?= (strpos($currentPage, 'admin') !== false) ? '../' : '' ?>products.php?category=accessories" class="<?= (isset($_GET['category']) && $_GET['category']=='accessories') ? 'active' : '' ?>">Accessories</a>
                <a href="<?= (strpos($currentPage, 'admin') !== false) ? '../' : '' ?>about.php" class="<?= $currentPage == 'about.php' ? 'active' : '' ?>">About</a>
                <a href="<?= (strpos($currentPage, 'admin') !== false) ? '../' : '' ?>contact.php" class="<?= $currentPage == 'contact.php' ? 'active' : '' ?>">Contact</a>
            </div>

            <div class="nav-actions">
                <button class="nav-icon-btn" id="searchToggle" title="Search">
                    <i class="fas fa-search"></i>
                </button>

                <?php if (isLoggedIn()): ?>
                <div class="dropdown">
                    <button class="nav-icon-btn dropdown-toggle" data-bs-toggle="dropdown" title="Account" style="border:none;">
                        <i class="fas fa-user"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" style="border-radius:12px;border:1px solid rgba(108,52,131,0.2);box-shadow:0 10px 40px rgba(0,0,0,0.15);min-width:180px;overflow:hidden;">
                        <li><span class="dropdown-item-text" style="font-size:13px;color:#888;padding:10px 16px;">Hi, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>!</span></li>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li><a class="dropdown-item" href="account.php"><i class="fas fa-user-circle me-2 text-primary"></i>My Account</a></li>
                        <li><a class="dropdown-item" href="orders.php"><i class="fas fa-box me-2 text-primary"></i>My Orders</a></li>
                        <li><a class="dropdown-item" href="wishlist.php"><i class="fas fa-heart me-2 text-danger"></i>Wishlist</a></li>
                        <?php if(isAdmin()): ?>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li><a class="dropdown-item" href="admin/index.php"><i class="fas fa-shield-alt me-2 text-warning"></i>Admin Panel</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider m-0"></li>
                        <li><a class="dropdown-item text-danger" href="php/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
                <a href="wishlist.php" class="nav-icon-btn" title="Wishlist">
                    <i class="far fa-heart"></i>
                </a>
                <?php else: ?>
                <a href="login.php" class="nav-icon-btn" title="Login">
                    <i class="fas fa-user"></i>
                </a>
                <?php endif; ?>

                <a href="cart.php" class="nav-icon-btn" title="Cart">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-badge" id="cartBadge" <?= $cartCount == 0 ? 'style="display:none"' : '' ?>>
                        <?= $cartCount ?>
                    </span>
                </a>

                <button class="nav-hamburger" id="hamburger">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <a href="index.php"><i class="fas fa-home me-2"></i> Home</a>
            <a href="products.php"><i class="fas fa-store me-2"></i> Shop All</a>
            <a href="products.php?category=women"><i class="fas fa-female me-2"></i> Women</a>
            <a href="products.php?category=men"><i class="fas fa-male me-2"></i> Men</a>
            <a href="products.php?category=accessories"><i class="fas fa-gem me-2"></i> Accessories</a>
            <a href="about.php"><i class="fas fa-info-circle me-2"></i> About</a>
            <a href="contact.php"><i class="fas fa-envelope me-2"></i> Contact</a>
            <?php if (!isLoggedIn()): ?>
            <a href="login.php"><i class="fas fa-sign-in-alt me-2"></i> Login</a>
            <a href="register.php"><i class="fas fa-user-plus me-2"></i> Register</a>
            <?php else: ?>
            <a href="php/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
// Mobile menu toggle
document.getElementById('hamburger')?.addEventListener('click', function() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('open');
    const icon = this.querySelector('i');
    icon.classList.toggle('fa-bars');
    icon.classList.toggle('fa-times');
});

// Search overlay
document.getElementById('searchToggle')?.addEventListener('click', function() {
    const overlay = document.getElementById('searchOverlay');
    overlay.classList.add('active');
    document.getElementById('searchInput')?.focus();
});
document.getElementById('searchClose')?.addEventListener('click', function() {
    document.getElementById('searchOverlay').classList.remove('active');
});

// Live search
function liveSearch(query) {
    const resultsDiv = document.getElementById('searchResults');
    if (query.length < 2) { resultsDiv.innerHTML = ''; return; }
    fetch(`php/search.php?q=${encodeURIComponent(query)}`)
        .then(r => r.json())
        .then(data => {
            if (data.length === 0) {
                resultsDiv.innerHTML = '<p style="color:rgba(255,255,255,0.4);font-size:14px;">No products found</p>';
                return;
            }
            resultsDiv.innerHTML = data.map(p => `
                <a href="product-detail.php?slug=${p.slug}" style="display:flex;align-items:center;gap:16px;padding:12px;border-radius:8px;margin-bottom:8px;background:rgba(255,255,255,0.05);text-decoration:none;transition:background 0.2s;" onmouseover="this.style.background='rgba(108,52,131,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                    <div style="width:50px;height:60px;border-radius:8px;background:rgba(108,52,131,0.3);flex-shrink:0;overflow:hidden;">
                        <img src="images/products/placeholder.jpg" style="width:100%;height:100%;object-fit:cover;" onerror="this.style.display='none'">
                    </div>
                    <div>
                        <div style="color:white;font-size:14px;font-weight:500;">${p.name}</div>
                        <div style="color:#D4AF37;font-size:13px;">Rs. ${parseFloat(p.price).toLocaleString()}</div>
                    </div>
                </a>
            `).join('');
        });
}
</script>
