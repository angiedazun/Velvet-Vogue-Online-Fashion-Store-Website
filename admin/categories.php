<?php
$pageTitle = "Categories";
require_once '../config/db.php';
if (!isAdmin()) { redirect('../login.php'); }

$message = '';
$error   = '';

/* ─── Handle POST ─────────────────────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = $_POST['act'] ?? '';

    if ($act === 'add') {
        $name  = sanitize($_POST['name'] ?? '');
        $slug  = sanitize(strtolower(str_replace([' ', "'"], ['-', ''], $_POST['slug'] ?? '')));
        $image = sanitize($_POST['image_url'] ?? '');
        $desc  = sanitize($_POST['description'] ?? '');
        if ($name && $slug) {
            $chk = $conn->query("SELECT id FROM categories WHERE slug='$slug' LIMIT 1");
            if ($chk->num_rows > 0) { $error = "A category with slug '$slug' already exists."; }
            else {
                $stmt = $conn->prepare("INSERT INTO categories (name, slug, image, description) VALUES (?,?,?,?)");
                $stmt->bind_param("ssss", $name, $slug, $image, $desc);
                $stmt->execute() ? $message = "Category '$name' added successfully!" : $error = 'Failed to add category.';
                $stmt->close();
            }
        } else { $error = 'Name and slug are required.'; }

    } elseif ($act === 'edit') {
        $id    = (int)$_POST['cat_id'];
        $name  = sanitize($_POST['name'] ?? '');
        $slug  = sanitize(strtolower(str_replace([' ', "'"], ['-', ''], $_POST['slug'] ?? '')));
        $image = sanitize($_POST['image_url'] ?? '');
        $desc  = sanitize($_POST['description'] ?? '');
        if ($name && $slug && $id) {
            $stmt = $conn->prepare("UPDATE categories SET name=?, slug=?, image=?, description=? WHERE id=?");
            $stmt->bind_param("ssssi", $name, $slug, $image, $desc, $id);
            $stmt->execute() ? $message = "Category updated successfully!" : $error = 'Failed to update.';
            $stmt->close();
        }

    } elseif ($act === 'delete') {
        $id = (int)$_POST['cat_id'];
        $cnt = $conn->query("SELECT COUNT(*) as c FROM products WHERE category_id=$id")->fetch_assoc()['c'] ?? 0;
        if ($cnt > 0) { $error = "Cannot delete — $cnt product(s) are in this category. Reassign them first."; }
        else {
            $conn->query("DELETE FROM categories WHERE id=$id");
            $message = 'Category deleted.';
        }
    }
}

/* ─── Fetch categories ────────────────────────── */
$categories = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id=c.id) as product_count FROM categories c ORDER BY c.id ASC");

/* ─── Fetch single cat for edit modal ────────── */
$editCat = null;
if (isset($_GET['edit'])) {
    $editId  = (int)$_GET['edit'];
    $editCat = $conn->query("SELECT * FROM categories WHERE id=$editId LIMIT 1")->fetch_assoc();
}

$extraCSS = '<link rel="stylesheet" href="../admin/css/categories.css">';
$extraJS  = '<script src="../admin/js/categories.js"></script>';
include 'includes/admin_header.php';
?>

<div class="admin-content">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Categories</h1>
            <p class="admin-page-subtitle">Manage product categories</p>
        </div>
        <button class="btn-vv btn-primary-vv" style="padding:10px 20px;font-size:13px;" onclick="openAddModal()">
            <i class="fas fa-plus me-2"></i>Add Category
        </button>
    </div>

    <?php if ($message): ?><div class="alert-vv alert-success mb-4"><i class="fas fa-check-circle me-2"></i><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error):   ?><div class="alert-vv alert-error mb-4"><i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <!-- Categories Grid -->
    <div class="categories-grid">
        <?php if ($categories) while ($cat = $categories->fetch_assoc()):
            $bgImg = $cat['image'] ? "url('{$cat['image']}')" : "url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400&h=200&fit=crop')";
        ?>
        <div class="category-card">
            <div class="category-card-img" style="background-image:<?= $bgImg ?>;">
                <div class="category-card-overlay">
                    <div class="category-actions">
                        <button class="cat-action-btn" onclick="openEditModal(<?= $cat['id'] ?>, <?= htmlspecialchars(json_encode(['id'=>$cat['id'],'name'=>$cat['name'],'slug'=>$cat['slug'],'image_url'=>$cat['image'],'description'=>$cat['description']])) ?>)" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Delete this category? This cannot be undone.')">
                            <input type="hidden" name="act" value="delete">
                            <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                            <button type="submit" class="cat-action-btn danger" title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="category-card-body">
                <div class="category-card-name"><?= htmlspecialchars($cat['name']) ?></div>
                <div class="category-card-meta">
                    <span class="cat-slug">/ <?= htmlspecialchars($cat['slug']) ?></span>
                    <a href="../products.php?category=<?= htmlspecialchars($cat['slug']) ?>" target="_blank" class="cat-product-count">
                        <?= $cat['product_count'] ?> product<?= $cat['product_count']!=1?'s':'' ?>
                    </a>
                </div>
                <?php if ($cat['description']): ?>
                <p class="cat-desc"><?= htmlspecialchars(substr($cat['description'], 0, 80)) ?><?= strlen($cat['description']) > 80 ? '...' : '' ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- ============ ADD MODAL ============ -->
<div class="cat-modal-backdrop" id="addModalBackdrop" style="display:none;">
    <div class="cat-modal">
        <div class="cat-modal-header">
            <h5><i class="fas fa-plus me-2"></i>Add New Category</h5>
            <button onclick="closeModals()" class="cat-modal-close"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" class="cat-modal-body">
            <input type="hidden" name="act" value="add">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-vv">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control-vv" placeholder="e.g. Kids" id="addNameInput" oninput="autoSlug(this,'addSlugInput')" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-vv">Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" id="addSlugInput" class="form-control-vv" placeholder="e.g. kids" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label-vv">Image URL</label>
                    <input type="url" name="image_url" class="form-control-vv" placeholder="https://images.unsplash.com/...">
                </div>
                <div class="col-md-12">
                    <label class="form-label-vv">Description</label>
                    <textarea name="description" class="form-control-vv" rows="2" placeholder="Short category description..."></textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-3">
                <button type="submit" class="btn-vv btn-primary-vv"><i class="fas fa-save me-2"></i>Save Category</button>
                <button type="button" onclick="closeModals()" class="btn-vv" style="background:var(--light);color:var(--dark);">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- ============ EDIT MODAL ============ -->
<div class="cat-modal-backdrop" id="editModalBackdrop" style="display:none;">
    <div class="cat-modal">
        <div class="cat-modal-header">
            <h5><i class="fas fa-edit me-2"></i>Edit Category</h5>
            <button onclick="closeModals()" class="cat-modal-close"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" class="cat-modal-body">
            <input type="hidden" name="act" value="edit">
            <input type="hidden" name="cat_id" id="editCatId">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label-vv">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="editName" class="form-control-vv" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label-vv">Slug <span class="text-danger">*</span></label>
                    <input type="text" name="slug" id="editSlug" class="form-control-vv" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label-vv">Image URL</label>
                    <input type="url" name="image_url" id="editImageUrl" class="form-control-vv">
                    <div class="cat-img-preview-wrap">
                        <img id="editImgPreview" src="" alt="" style="display:none;max-height:100px;border-radius:8px;margin-top:8px;">
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="form-label-vv">Description</label>
                    <textarea name="description" id="editDesc" class="form-control-vv" rows="2"></textarea>
                </div>
            </div>
            <div class="mt-4 d-flex gap-3">
                <button type="submit" class="btn-vv btn-primary-vv"><i class="fas fa-save me-2"></i>Update Category</button>
                <button type="button" onclick="closeModals()" class="btn-vv" style="background:var(--light);color:var(--dark);">Cancel</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
