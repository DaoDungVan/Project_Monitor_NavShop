<?php
// Chỉ admin mới được vào trang này
require_once '../middleware/admin.php';

// Kết nối database
require_once '../config/db.php';

// Biến lưu lỗi
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

    // Kiểm tra các trường bắt buộc
    if (
        $name === '' || $brand === '' || $size === '' ||
        $resolution === '' || $panel === '' || $price === ''
    ) {
        $error = 'Please fill all required fields';
    }

    // ================= VALIDATE PRICE =================
    // Giá phải là số và lớn hơn 0
    else if (!is_numeric($price) || $price <= 0) {
        $error = 'Price must be greater than 0';
    }

    else {

        // ================= CHECK TRÙNG TÊN =================
        $stmt = $conn->prepare("SELECT id FROM products WHERE name = ?");
        $stmt->execute([$name]);

        if ($stmt->fetch()) {
            $error = 'Product name already exists';
        } else {

            // ================= UPLOAD IMAGE =================

            // Mặc định chưa có ảnh
            $imagePath = null;

            // Nếu admin có chọn ảnh
            if (!empty($_FILES['image']['name'])) {

                // Đổi tên ảnh để tránh trùng
                $imageName = time() . '_' . $_FILES['image']['name'];

                // Thư mục lưu ảnh
                $uploadDir = '../uploads/products/';
                $targetPath = $uploadDir . $imageName;

                // Di chuyển ảnh từ thư mục tạm vào uploads
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                    // Lưu đường dẫn ảnh để insert vào DB
                    $imagePath = 'uploads/products/' . $imageName;
                }
            }

            // ================= INSERT PRODUCT =================

            // Insert sản phẩm vào database
            $stmt = $conn->prepare("
                INSERT INTO products 
                (name, brand, size, resolution, panel, is_curved, price, image, description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
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
                $description
            ]);

            // Thêm xong quay về trang quản lý
            header('Location: admin_index.php');
            exit;
        }
    }
}
?>

<?php require_once '../includes/header_admin.php'; ?>
<h2>Add Product</h2>

<!-- Hiển thị lỗi nếu có -->
<?php if ($error): ?>
    <div class="alert alert-danger">
        <?= $error ?>
    </div>
<?php endif; ?>

<!-- Form thêm sản phẩm -->
<form method="POST" enctype="multipart/form-data" style="margin-bottom: 20px;">

    <div class="mb-3">
        <label>Product Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Brand</label>
        <input type="text" name="brand" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Size (inch)</label>
        <select name="size" class="form-control" required>
            <option value="">-- Select size --</option>
            <option value="24">24 inch</option>
            <option value="27">27 inch</option>
            <option value="32">32 inch</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Resolution</label>
        <select name="resolution" class="form-control" required>
            <option value="">-- Select resolution --</option>
            <option value="FHD">Full HD</option>
            <option value="2K">2K</option>
            <option value="4K">4K</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Panel</label>
        <select name="panel" class="form-control" required>
            <option value="">-- Select panel --</option>
            <option value="IPS">IPS</option>
            <option value="VA">VA</option>
            <option value="OLED">OLED</option>
        </select>
    </div>

    <div class="mb-3">
        <label>
            <input type="checkbox" name="is_curved"> Curved screen
        </label>
    </div>

    <div class="mb-3">
        <label>Price (VND)</label>
        <input type="number" name="price" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Product Image</label>
        <input type="file" name="image" class="form-control">
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control"></textarea>
    </div>

    <button class="btn btn-success">Save</button>
    <a href="admin_index.php" class="btn btn-secondary">Back</a>

</form>

<?php require_once '../includes/footer_admin.php'; ?>
