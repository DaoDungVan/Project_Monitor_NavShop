<?php
session_start();
require_once '../config/db.php';

// Lấy id sản phẩm
if (!isset($_GET['id'])) {
    header('Location: ../products/index.php');
    exit;
}

$id = $_GET['id'];

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: ../products/index.php');
    exit;
}

// Nếu giỏ hàng chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Nếu sản phẩm đã có trong giỏ
if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['qty']++;
} else {
    $_SESSION['cart'][$id] = [
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'image' => $product['image'],
        'qty' => 1
    ];
}

// Quay lại trang shop
header('Location: ../products/index.php');
exit;
