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

// Lấy thông tin sản phẩm để biết ảnh
$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Nếu không tìm thấy sản phẩm
if (!$product) {
    header('Location: admin_index.php');
    exit;
}

// Nếu có ảnh thì xoá ảnh trong thư mục uploads
if (!empty($product['image']) && file_exists('../' . $product['image'])) {
    unlink('../' . $product['image']);
}

// Xoá sản phẩm trong database
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$id]);

// Quay về trang quản lý
header('Location: admin_index.php');
exit;
