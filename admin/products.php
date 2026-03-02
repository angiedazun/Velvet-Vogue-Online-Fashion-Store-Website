<?php
$pageTitle = "Products";
require_once '../config/db.php';
if (!isAdmin()) { redirect('../login.php'); }

$message = $error = '';
$action = $_GET['action'] ?? 'list';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['action'] ?? '';
    if ($act === 'add' || $act === 'edit') {
        $id          = (int)($_POST['id'] ?? 0);
        $name        = sanitize($_POST['name'] ?? '');
        $slug        = sanitize(strtolower(preg_replace('/[^a-z0-9]+/i','-', $_POST['name'] ?? '')));
        $cat_id      = (int)($_POST['category_id'] ?? 0);
        $price       = (float)($_POST['price'] ?? 0);
        $sale_price  = (float)($_POST['sale_price'] ?? 0);
        $stock       = (int)($_POST['stock'] ?? 0);
        $images      = sanitize($_POST['images'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $new_arrival = isset($_POST['new_arrival']) ? 1 : 0;
        $featured    = isset($_POST['featured']) ? 1 : 0;
        $trending    = isset($_POST['trending']) ? 1 : 0;

        if ($act === 'add') {
            $stmt = $conn->prepare("INSERT INTO products (name, slug, category_id, price, sale_price, stock, images, description, new_arrival, featured, trending) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssiddiissii", $name, $slug, $cat_id, $price, $sale_price, $stock, $images, $description, $new_arrival, $featured, $trending);
            $stmt->execute() ? $message='Product added!' : $error='Failed to add product.';
            $stmt->close();
        } else {
            $stmt = $conn->prepare("UPDATE products SET name=?, slug=?, category_id=?, price=?, sale_price=?, stock=?, images=?, description=?, new_arrival=?, featured=?, trending=? WHERE id=?");
            $stmt->bind_param("ssiddiissiii", $name,$slug,$cat_id,$price,$sale_price,$stock,$images,$description,$new_arrival,$featured,$trending,$id);
            $stmt->execute() ? $message='Product updated!' : $error='Failed to update product.';
            $stmt->close();
        }
        $action = 'list';
    } elseif ($act === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("DELETE FROM products WHERE id=$id");
        $message = 'Product deleted.';
        $action = 'list';
    } elseif ($act === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        $conn->query("UPDATE products SET featured = NOT featured WHERE id=$id");
        $message = 'Product status updated.';
        $action = 'list';
    }
}

// Fetch product for edit
$editProduct = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $eid = (int)$_GET['id'];
    $editProduct = $conn->query("SELECT * FROM products WHERE id=$eid")->fetch_assoc();
}

// Get categories
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

// Fetch product list
$search = sanitize($_GET['s'] ?? '');
$where = $search ? "WHERE p.name LIKE '%{$conn->real_escape_string($search)}%'" : '';
$products = $conn->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id $where ORDER BY p.created_at DESC");

$extraCSS = '<link rel="stylesheet" href="../admin/css/products.css">';
$extraJS  = '<script src="../admin/js/products.js"></script>';
include 'includes/admin_header.php';
?>
<div class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Products</h1>
            <p class="admin-page-subtitle">Manage your product catalog</p>
        </div>
        <a href="?action=add" class="btn-vv btn-primary-vv" style="font-size:13px;padding:10px 20px;"><i class="fas fa-plus me-1"></i> Add Product</a>
    </div>

    <?php if ($message): ?><div class="alert-vv alert-success mb-4"><i class="fas fa-check-circle"></i> <?= $message ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert-vv alert-error mb-4"><i class="fas fa-times-circle"></i> <?= $error ?></div><?php endif; ?>

    <?php if ($action === 'add' || $action === 'edit'): ?>
    <!-- Add / Edit Form -->
    <div class="admin-card mb-4">
        <div class="admin-card-header">
            <h5><?= $action==='edit' ? 'Edit Product' : 'Add New Product' ?></h5>
            <a href="products.php" class="btn-admin-sm">← Back</a>
        </div>
        <form method="POST" action="products.php">
            <input type="hidden" name="action" value="<?= $action === 'edit' ? 'edit' : 'add' ?>">
            <?php if ($editProduct): ?><input type="hidden" name="id" value="<?= $editProduct['id'] ?>"><?php endif; ?>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label-vv">Product Name *</label><input type="text" name="name" class="form-control-vv" value="<?= htmlspecialchars($editProduct['name'] ?? '') ?>" required></div>
                <div class="col-md-6"><label class="form-label-vv">Category</label><select name="category_id" class="form-control-vv"><?php while($c=$categories->fetch_assoc()): ?><option value="<?= $c['id'] ?>" <?= ($editProduct['category_id']??'0')==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option><?php endwhile; ?></select></div>
                <div class="col-md-3"><label class="form-label-vv">Price (Rs.) *</label><input type="number" name="price" class="form-control-vv" step="0.01" value="<?= $editProduct['price'] ?? '' ?>" required></div>
                <div class="col-md-3"><label class="form-label-vv">Sale Price (Rs.)</label><input type="number" name="sale_price" class="form-control-vv" step="0.01" value="<?= $editProduct['sale_price'] ?? '' ?>"></div>
                <div class="col-md-3"><label class="form-label-vv">Stock</label><input type="number" name="stock" class="form-control-vv" value="<?= $editProduct['stock'] ?? 0 ?>"></div>
                <div class="col-md-3"><label class="form-label-vv">Rating</label><input type="number" name="rating" class="form-control-vv" step="0.1" min="0" max="5" value="<?= $editProduct['rating'] ?? '5.0' ?>"></div>
                <div class="col-12"><label class="form-label-vv">Image URL</label><input type="url" name="images" class="form-control-vv" placeholder="https://..." value="<?= htmlspecialchars($editProduct['images'] ?? '') ?>"></div>
                <div class="col-12"><label class="form-label-vv">Description</label><textarea name="description" class="form-control-vv" rows="4"><?= htmlspecialchars($editProduct['description'] ?? '') ?></textarea></div>
                <div class="col-md-4"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="new_arrival" <?= ($editProduct['new_arrival']??0)?'checked':'' ?>> Mark as New Arrival</label></div>
                <div class="col-md-4"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="featured" <?= ($editProduct['featured']??0)?'checked':'' ?>> Featured Product</label></div>
                <div class="col-md-4"><label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;"><input type="checkbox" name="trending" <?= ($editProduct['trending']??0)?'checked':'' ?>> Trending</label></div>
                <div class="col-12 d-flex gap-3"><button type="submit" class="btn-vv btn-primary-vv"><i class="fas fa-save me-1"></i> Save Product</button><a href="products.php" class="btn-vv btn-outline-primary-vv">Cancel</a></div>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Product Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h5>All Products (<?= $products ? $products->num_rows : 0 ?>)</h5>
            <form method="GET" style="display:flex;gap:8px;">
                <input type="text" name="s" class="form-control-vv" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>" style="width:220px;">
                <button type="submit" class="btn-admin-sm">Search</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if ($products) while ($p = $products->fetch_assoc()):
                    $fp = ($p['sale_price']>0) ? $p['sale_price'] : $p['price']; ?>
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <img src="<?= htmlspecialchars($p['images'] ?? '') ?>" alt="" style="width:50px;height:58px;border-radius:8px;object-fit:cover;">
                            <div>
                                <div style="font-weight:600;font-size:13px;"><?= htmlspecialchars($p['name']) ?></div>
                                <div style="font-size:11px;color:var(--text-muted);">ID: <?= $p['id'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span style="font-size:12px;"><?= htmlspecialchars($p['cat_name'] ?? '-') ?></span></td>
                    <td>
                        <span style="font-weight:700;"><?= formatPrice($fp) ?></span>
                        <?php if($p['sale_price']>0): ?><br><del style="font-size:11px;color:var(--text-muted);"><?= formatPrice($p['price']) ?></del><?php endif; ?>
                    </td>
                    <td><span style="font-weight:600;color:<?= $p['stock']<5?'#e74c3c':($p['stock']<20?'#f39c12':'#27ae60') ?>;"><?= $p['stock'] ?></span></td>
                    <td><span class="status-badge" style="background:<?= $p['stock']>0?'#27ae6020':'#e74c3c20' ?>;color:<?= $p['stock']>0?'#27ae60':'#e74c3c' ?>"><?= $p['stock']>0?'In Stock':'Out of Stock' ?></span></td>
                    <td>
                        <div style="display:flex;gap:8px;">
                            <a href="?action=edit&id=<?= $p['id'] ?>" style="color:var(--primary);font-size:13px;" title="Edit"><i class="fas fa-edit"></i></a>
                            <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Delete this product?')">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" style="background:none;border:none;color:#e74c3c;font-size:13px;padding:0;cursor:pointer;" title="Delete"><i class="fas fa-trash"></i></button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" style="background:none;border:none;color:#f39c12;font-size:13px;padding:0;cursor:pointer;" title="Toggle Featured"><i class="fas fa-toggle-<?= $p['featured']?'on':'off' ?>"></i></button>
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
<?php include 'includes/admin_footer.php'; ?>
