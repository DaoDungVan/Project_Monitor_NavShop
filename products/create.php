<?php
require_once '../middleware/admin.php';
require_once '../config/db.php';
require_once '../includes/upload.php';

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

    if ($name === '' || $brand === '' || $size === '' || $resolution === '' || $panel === '' || $price === '') {
        $error = 'Please fill all required fields.';
    } elseif (!is_numeric($price) || $price <= 0) {
        $error = 'Price must be a positive number.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM products WHERE name = ?");
        $stmt->execute([$name]);
        if ($stmt->fetch()) {
            $error = 'Product name already exists.';
        } else {
            $imagePath = null;
            if (!empty($_FILES['image']['name'])) {
                $imagePath = save_uploaded_image('image', '../uploads/products', 'uploads/products', $error);
            }

            if ($error === '') {
                $stmt = $conn->prepare("
                    INSERT INTO products (name, brand, size, resolution, panel, is_curved, price, image, description)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$name, $brand, $size, $resolution, $panel, $is_curved, $price, $imagePath, $description]);
                header('Location: admin_index.php');
                exit;
            }
        }
    }
}
?>

<?php require_once '../includes/header_admin.php'; ?>

<div class="page-header-bar">
    <h1 class="page-title" style="margin-bottom:0">Add Product</h1>
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
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Brand *</label>
            <input type="text" name="brand" class="form-control form-control-admin"
                   value="<?= htmlspecialchars($_POST['brand'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label>Size (inch) *</label>
            <select name="size" class="form-control form-control-admin" required>
                <option value="">-- Select size --</option>
                <option value="24" <?= ($_POST['size'] ?? '') == 24 ? 'selected' : '' ?>>24 inch</option>
                <option value="27" <?= ($_POST['size'] ?? '') == 27 ? 'selected' : '' ?>>27 inch</option>
                <option value="32" <?= ($_POST['size'] ?? '') == 32 ? 'selected' : '' ?>>32 inch</option>
            </select>
        </div>

        <div class="form-group">
            <label>Resolution *</label>
            <select name="resolution" class="form-control form-control-admin" required>
                <option value="">-- Select resolution --</option>
                <option value="FHD"  <?= ($_POST['resolution'] ?? '') === 'FHD'  ? 'selected' : '' ?>>Full HD</option>
                <option value="2K"   <?= ($_POST['resolution'] ?? '') === '2K'   ? 'selected' : '' ?>>2K</option>
                <option value="4K"   <?= ($_POST['resolution'] ?? '') === '4K'   ? 'selected' : '' ?>>4K</option>
            </select>
        </div>

        <div class="form-group">
            <label>Panel *</label>
            <select name="panel" class="form-control form-control-admin" required>
                <option value="">-- Select panel --</option>
                <option value="IPS"  <?= ($_POST['panel'] ?? '') === 'IPS'  ? 'selected' : '' ?>>IPS</option>
                <option value="VA"   <?= ($_POST['panel'] ?? '') === 'VA'   ? 'selected' : '' ?>>VA</option>
                <option value="OLED" <?= ($_POST['panel'] ?? '') === 'OLED' ? 'selected' : '' ?>>OLED</option>
            </select>
        </div>

        <div class="form-group">
            <label class="checkbox-row">
                <input type="checkbox" name="is_curved" <?= isset($_POST['is_curved']) ? 'checked' : '' ?>>
                Curved screen
            </label>
        </div>

        <div class="form-group">
            <label>Price (VND) *</label>
            <input type="number" name="price" class="form-control form-control-admin"
                   value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" min="1" required>
        </div>

        <div class="form-group">
            <label>Product Image</label>
            <input type="file" name="image" class="form-control form-control-admin" accept="image/*">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-control form-control-admin"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-green">Save Product</button>
            <a href="admin_index.php" class="btn btn-gray">Cancel</a>
        </div>

    </form>
</div>

<?php require_once '../includes/footer_admin.php'; ?>
