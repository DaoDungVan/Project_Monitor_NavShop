<?php
require_once '../middleware/admin.php';
require_once '../config/db.php';
require_once '../includes/upload.php';

if (!isset($_GET['id'])) {
    header('Location: admin_index.php');
    exit;
}

$id   = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: admin_index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $brand       = trim($_POST['brand'] ?? '');
    $size        = $_POST['size'] ?? '';
    $resolution  = $_POST['resolution'] ?? '';
    $panel       = $_POST['panel'] ?? '';
    $is_curved   = isset($_POST['is_curved']) ? 1 : 0;
    $price       = $_POST['price'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $imagePath   = $product['image'];

    if ($name === '' || $brand === '' || $size === '' || $resolution === '' || $panel === '' || $price === '') {
        $error = 'Please fill all required fields.';
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = 'Price must be a positive number.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM products WHERE name = ? AND id != ?");
        $stmt->execute([$name, $id]);
        if ($stmt->fetch()) {
            $error = 'Product name already exists.';
        } else {
            if (!empty($_FILES['image']['name'])) {
                $newImagePath = save_uploaded_image('image', '../uploads/products', 'uploads/products', $error);
                if ($error === '' && $newImagePath !== null) {
                    delete_uploaded_file($product['image'] ?? null);
                    $imagePath = $newImagePath;
                }
            }

            if ($error === '') {
                $stmt = $conn->prepare("
                    UPDATE products SET
                        name=?, brand=?, size=?, resolution=?, panel=?,
                        is_curved=?, price=?, image=?, description=?
                    WHERE id=?
                ");
                $stmt->execute([$name, $brand, $size, $resolution, $panel, $is_curved, $price, $imagePath, $description, $id]);
                header('Location: admin_index.php');
                exit;
            }
        }
    }
}
?>

<?php require_once '../includes/header_admin.php'; ?>

<div class="page-header-bar">
    <h1 class="page-title" style="margin-bottom:0">Edit Product</h1>
    <a href="admin_index.php" class="btn btn-gray btn-sm">← Back</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="admin-form-card">
    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label>Product Name *</label>
            <input type="text" name="name" class="form-control form-control-admin"
                   value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Brand *</label>
            <input type="text" name="brand" class="form-control form-control-admin"
                   value="<?= htmlspecialchars($product['brand']) ?>" required>
        </div>

        <div class="form-group">
            <label>Size (inch) *</label>
            <select name="size" class="form-control form-control-admin" required>
                <option value="24" <?= $product['size'] == 24 ? 'selected' : '' ?>>24 inch</option>
                <option value="27" <?= $product['size'] == 27 ? 'selected' : '' ?>>27 inch</option>
                <option value="32" <?= $product['size'] == 32 ? 'selected' : '' ?>>32 inch</option>
            </select>
        </div>

        <div class="form-group">
            <label>Resolution *</label>
            <select name="resolution" class="form-control form-control-admin" required>
                <option value="FHD"  <?= $product['resolution'] === 'FHD'  ? 'selected' : '' ?>>Full HD</option>
                <option value="2K"   <?= $product['resolution'] === '2K'   ? 'selected' : '' ?>>2K</option>
                <option value="4K"   <?= $product['resolution'] === '4K'   ? 'selected' : '' ?>>4K</option>
            </select>
        </div>

        <div class="form-group">
            <label>Panel *</label>
            <select name="panel" class="form-control form-control-admin" required>
                <option value="IPS"  <?= $product['panel'] === 'IPS'  ? 'selected' : '' ?>>IPS</option>
                <option value="VA"   <?= $product['panel'] === 'VA'   ? 'selected' : '' ?>>VA</option>
                <option value="OLED" <?= $product['panel'] === 'OLED' ? 'selected' : '' ?>>OLED</option>
            </select>
        </div>

        <div class="form-group">
            <label class="checkbox-row">
                <input type="checkbox" name="is_curved" <?= $product['is_curved'] ? 'checked' : '' ?>>
                Curved screen
            </label>
        </div>

        <div class="form-group">
            <label>Price (VND) *</label>
            <input type="number" name="price" class="form-control form-control-admin"
                   value="<?= $product['price'] ?>" min="1" required>
        </div>

        <div class="form-group">
            <label>Current Image</label><br>
            <?php if ($product['image']): ?>
                <img src="../<?= htmlspecialchars($product['image']) ?>"
                     style="width:120px;height:90px;object-fit:contain;border:1px solid var(--border);border-radius:6px;padding:4px;margin-bottom:8px;">
            <?php else: ?>
                <span class="text-muted">No image</span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Change Image</label>
            <input type="file" name="image" class="form-control form-control-admin" accept="image/*">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control form-control-admin"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-navy">Update Product</button>
            <a href="admin_index.php" class="btn btn-gray">Cancel</a>
        </div>

    </form>
</div>

<?php require_once '../includes/footer_admin.php'; ?>
