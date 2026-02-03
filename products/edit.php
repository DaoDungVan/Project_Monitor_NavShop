<?php
// Chỉ admin mới được vào
require_once '../middleware/admin.php';

// Kết nối database
require_once '../config/db.php';

// Kiểm tra có id không
if (!isset($_GET['id'])) {
    header('Location: admin_index.php');
    exit;
}

$id = $_GET['id'];

// Lấy thông tin sản phẩm hiện tại
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Nếu không tìm thấy sản phẩm
if (!$product) {
    header('Location: admin_index.php');
    exit;
}

$error = '';

// Khi admin submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lấy dữ liệu từ form
    $name        = trim($_POST['name'] ?? '');
    $brand       = trim($_POST['brand'] ?? '');
    $size        = $_POST['size'] ?? '';
    $resolution  = $_POST['resolution'] ?? '';
    $panel       = $_POST['panel'] ?? '';
    $is_curved   = isset($_POST['is_curved']) ? 1 : 0;
    $price       = $_POST['price'] ?? '';
    $description = trim($_POST['description'] ?? '');

    // Giữ ảnh cũ mặc định
    $imagePath = $product['image'];

    // Validate dữ liệu
    if (
        $name === '' || $brand === '' || $size === '' ||
        $resolution === '' || $panel === '' || $price === ''
    ) {
        $error = 'Please fill all required fields';
    } else {

        // ================= CHECK TRÙNG TÊN SẢN PHẨM (TRỪ CHÍNH NÓ) =================
        $stmt = $conn->prepare("
            SELECT id FROM products 
            WHERE name = ? AND id != ?
        ");
        $stmt->execute([$name, $id]);

        if ($stmt->fetch()) {
            $error = 'Product name already exists';
        } else {

            // Nếu admin upload ảnh mới
            if (!empty($_FILES['image']['name'])) {

                // Đổi tên ảnh mới
                $imageName = time() . '_' . $_FILES['image']['name'];
                $uploadDir = '../uploads/products/';
                $targetPath = $uploadDir . $imageName;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {

                    // Xoá ảnh cũ nếu có
                    if (!empty($product['image']) && file_exists('../' . $product['image'])) {
                        unlink('../' . $product['image']);
                    }

                    // Cập nhật đường dẫn ảnh mới
                    $imagePath = 'uploads/products/' . $imageName;
                }
            }

            // ================= UPDATE PRODUCT =================
            $stmt = $conn->prepare("
                UPDATE products SET
                    name = ?,
                    brand = ?,
                    size = ?,
                    resolution = ?,
                    panel = ?,
                    is_curved = ?,
                    price = ?,
                    image = ?,
                    description = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $name,
                $brand,
                $size,
                $resolution,
                $panel,
                $is_curved,
                $price,
                $imagePath,
                $description,
                $id
            ]);

            // Xong thì quay về trang admin
            header('Location: admin_index.php');
            exit;
        }
    }
}
?>

<?php require_once '../includes/header_admin.php'; ?>
<h2>Edit Product</h2>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <?= $error ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <div class="mb-3">
        <label>Product Name</label>
        <input type="text" name="name" class="form-control"
               value="<?= htmlspecialchars($product['name']) ?>" required>
    </div>

    <div class="mb-3">
        <label>Brand</label>
        <input type="text" name="brand" class="form-control"
               value="<?= htmlspecialchars($product['brand']) ?>" required>
    </div>

    <div class="mb-3">
        <label>Size (inch)</label>
        <select name="size" class="form-control" required>
            <option value="24" <?= $product['size'] == 24 ? 'selected' : '' ?>>24 inch</option>
            <option value="27" <?= $product['size'] == 27 ? 'selected' : '' ?>>27 inch</option>
            <option value="32" <?= $product['size'] == 32 ? 'selected' : '' ?>>32 inch</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Resolution</label>
        <select name="resolution" class="form-control" required>
            <option value="FHD" <?= $product['resolution'] == 'FHD' ? 'selected' : '' ?>>Full HD</option>
            <option value="2K" <?= $product['resolution'] == '2K' ? 'selected' : '' ?>>2K</option>
            <option value="4K" <?= $product['resolution'] == '4K' ? 'selected' : '' ?>>4K</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Panel</label>
        <select name="panel" class="form-control" required>
            <option value="IPS" <?= $product['panel'] == 'IPS' ? 'selected' : '' ?>>IPS</option>
            <option value="VA" <?= $product['panel'] == 'VA' ? 'selected' : '' ?>>VA</option>
            <option value="OLED" <?= $product['panel'] == 'OLED' ? 'selected' : '' ?>>OLED</option>
        </select>
    </div>

    <div class="mb-3">
        <label>
            <input type="checkbox" name="is_curved" <?= $product['is_curved'] ? 'checked' : '' ?>>
            Curved screen
        </label>
    </div>

    <div class="mb-3">
        <label>Price (VND)</label>
        <input type="number" name="price" class="form-control"
               value="<?= $product['price'] ?>" required>
    </div>

    <div class="mb-3">
        <label>Current Image</label><br>
        <?php if ($product['image']): ?>
            <img src="../<?= $product['image'] ?>" width="120">
        <?php else: ?>
            <span>No image</span>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label>Change Image</label>
        <input type="file" name="image" class="form-control">
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="admin_index.php" class="btn btn-secondary">Back</a>

</form>
<?php require_once '../includes/footer_admin.php'; ?>

