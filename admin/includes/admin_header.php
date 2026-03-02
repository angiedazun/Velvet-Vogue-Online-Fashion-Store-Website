<?php
$currentAdminPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> | Velvet Vogue Admin</title>
    <link rel="icon" type="image/svg+xml" href="../favicon.svg">
    <link rel="shortcut icon" href="../favicon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
    <style>
        :root{--sidebar-w:260px;}
        body{font-family:'Poppins',sans-serif;background:#f0f2f5;overflow-x:hidden;}
        /* Sidebar */
        .admin-sidebar{width:var(--sidebar-w);height:100vh;position:fixed;left:0;top:0;background:var(--dark);z-index:1000;display:flex;flex-direction:column;transition:transform 0.3s;}
        .admin-sidebar-brand{padding:24px 20px;border-bottom:1px solid rgba(255,255,255,0.08);}
        .admin-sidebar-brand a{color:white;text-decoration:none;font-size:1.2rem;font-weight:700;display:flex;align-items:center;gap:10px;}
        .admin-sidebar-brand a i{color:var(--gold);}
        .admin-sidebar-brand small{display:block;font-size:10px;color:rgba(255,255,255,0.4);margin-top:2px;font-weight:400;}
        .admin-nav{flex:1;overflow-y:auto;padding:16px 0;}
        .admin-nav-section{padding:8px 20px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,0.3);margin-top:8px;}
        .admin-nav a{display:flex;align-items:center;gap:12px;padding:12px 20px;color:rgba(255,255,255,0.65);text-decoration:none;font-size:13px;font-weight:500;transition:all 0.2s;position:relative;}
        .admin-nav a:hover,.admin-nav a.active{color:white;background:rgba(255,255,255,0.07);}
        .admin-nav a.active::before{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:var(--gold);border-radius:0 3px 3px 0;}
        .admin-nav a i{width:18px;text-align:center;}
        .admin-nav .badge-count{margin-left:auto;background:#e74c3c;color:white;border-radius:20px;padding:2px 8px;font-size:10px;}
        .admin-sidebar-footer{padding:16px 20px;border-top:1px solid rgba(255,255,255,0.08);}
        .admin-sidebar-user{display:flex;align-items:center;gap:10px;margin-bottom:12px;}
        .admin-sidebar-user-avatar{width:36px;height:36px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:white;flex-shrink:0;}
        .admin-sidebar-user-name{font-size:12px;font-weight:600;color:white;}
        .admin-sidebar-user-role{font-size:10px;color:rgba(255,255,255,0.4);}
        /* Main */
        .admin-main{margin-left:var(--sidebar-w);min-height:100vh;display:flex;flex-direction:column;}
        .admin-topbar{background:white;padding:0 24px;height:64px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 0 #e8e8e8;position:sticky;top:0;z-index:100;}
        .admin-topbar-left{display:flex;align-items:center;gap:16px;}
        .sidebar-toggle{background:none;border:none;font-size:1.1rem;color:var(--dark);cursor:pointer;padding:6px;}
        .admin-topbar-right{display:flex;align-items:center;gap:16px;}
        .topbar-btn{background:none;border:none;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--text-muted);cursor:pointer;font-size:14px;position:relative;}
        .topbar-btn:hover{background:var(--light);}
        .topbar-notif{position:absolute;top:4px;right:4px;width:8px;height:8px;background:#e74c3c;border-radius:50%;}
        .admin-content{padding:28px;flex:1;}
        .admin-page-header{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;}
        .admin-page-title{font-size:1.6rem;font-weight:700;color:var(--dark);margin:0;}
        .admin-page-subtitle{font-size:13px;color:var(--text-muted);margin:4px 0 0;}
        /* Stat cards */
        .admin-stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;margin-bottom:28px;}
        .admin-stat-card{background:white;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.07);display:flex;gap:16px;align-items:center;}
        .stat-icon{width:52px;height:52px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0;}
        .stat-value{font-size:1.5rem;font-weight:700;color:var(--dark);}
        .stat-label{font-size:12px;color:var(--text-muted);font-weight:500;}
        /* Admin cards */
        .admin-card{background:white;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.07);}
        .admin-card-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;}
        .admin-card-header h5{font-weight:700;font-size:1rem;margin:0;color:var(--dark);}
        .btn-admin-sm{font-size:12px;color:var(--primary);text-decoration:none;font-weight:600;padding:6px 12px;border-radius:8px;border:1px solid var(--primary);transition:all 0.2s;}
        .btn-admin-sm:hover{background:var(--primary);color:white;}
        /* Table */
        .admin-table{width:100%;border-collapse:separate;border-spacing:0;font-size:13px;}
        .admin-table thead th{padding:10px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:var(--text-muted);background:var(--light);border-bottom:1px solid var(--border);}
        .admin-table thead th:first-child{border-radius:8px 0 0 0;}
        .admin-table thead th:last-child{border-radius:0 8px 0 0;}
        .admin-table tbody td{padding:14px 16px;border-bottom:1px solid var(--border);vertical-align:middle;}
        .admin-table tbody tr:hover{background:var(--light);}
        .status-badge{padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700;}
        .btn-admin-action{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:var(--light);color:var(--dark);text-decoration:none;font-size:13px;font-weight:500;transition:all 0.2s;}
        .btn-admin-action:hover{background:var(--primary);color:white;}
        /* Responsive */
        @media(max-width:991px){.admin-sidebar{transform:translateX(-100%);}.admin-sidebar.open{transform:translateX(0);}.admin-main{margin-left:0;}.admin-stats-grid{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:576px){.admin-stats-grid{grid-template-columns:1fr;}}
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-brand">
        <a href="index.php"><i class="fas fa-gem"></i><div><span>Velvet Vogue</span><small>Admin Panel</small></div></a>
    </div>
    <nav class="admin-nav">
        <div class="admin-nav-section">Main</div>
        <a href="index.php" <?= $currentAdminPage=='index'?'class="active"':'' ?>><i class="fas fa-th-large"></i> Dashboard</a>

        <div class="admin-nav-section">Catalog</div>
        <a href="products.php" <?= $currentAdminPage=='products'?'class="active"':'' ?>><i class="fas fa-tshirt"></i> Products</a>
        <a href="categories.php" <?= $currentAdminPage=='categories'?'class="active"':'' ?>><i class="fas fa-tags"></i> Categories</a>

        <div class="admin-nav-section">Commerce</div>
        <a href="orders.php" <?= $currentAdminPage=='orders'?'class="active"':'' ?>>
            <i class="fas fa-shopping-bag"></i> Orders
            <?php $pc=$conn->query("SELECT COUNT(*) as c FROM orders WHERE status='pending'")->fetch_assoc()['c']??0; if($pc>0) echo "<span class='badge-count'>$pc</span>"; ?>
        </a>
        <a href="users.php" <?= $currentAdminPage=='users'?'class="active"':'' ?>><i class="fas fa-users"></i> Customers</a>

        <div class="admin-nav-section">Support</div>
        <a href="contacts.php" <?= $currentAdminPage=='contacts'?'class="active"':'' ?>>
            <i class="fas fa-envelope"></i> Messages
            <?php $mc=$conn->query("SELECT COUNT(*) as c FROM contacts WHERE is_read=0")->fetch_assoc()['c']??0; if($mc>0) echo "<span class='badge-count'>$mc</span>"; ?>
        </a>

        <div class="admin-nav-section">Account</div>
        <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Website</a>
        <a href="../php/logout.php"><i class="fas fa-sign-out-alt"></i> Sign Out</a>
    </nav>
    <div class="admin-sidebar-footer">
        <div class="admin-sidebar-user">
            <div class="admin-sidebar-user-avatar"><?= strtoupper(substr($_SESSION['user_name']??'A',0,1)) ?></div>
            <div><div class="admin-sidebar-user-name"><?= htmlspecialchars($_SESSION['user_name']??'Admin') ?></div><div class="admin-sidebar-user-role">Administrator</div></div>
        </div>
    </div>
</aside>

<!-- Main Wrapper -->
<div class="admin-main">
    <!-- Topbar -->
    <header class="admin-topbar">
        <div class="admin-topbar-left">
            <button class="sidebar-toggle" onclick="document.getElementById('adminSidebar').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <nav style="font-size:13px;color:var(--text-muted);">
                <a href="index.php" style="color:var(--text-muted);text-decoration:none;">Admin</a>
                <span class="mx-2">/</span>
                <span style="color:var(--dark);font-weight:600;"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></span>
            </nav>
        </div>
        <div class="admin-topbar-right">
            <button class="topbar-btn" onclick="document.getElementById('adminSearchBar').classList.toggle('d-none')"><i class="fas fa-search"></i></button>
            <a href="orders.php?status=pending" class="topbar-btn" style="position:relative;">
                <i class="fas fa-bell"></i>
                <?php if ($pc > 0): ?><span class="topbar-notif"></span><?php endif; ?>
            </a>
            <a href="../php/logout.php" class="topbar-btn" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>
