<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user'])) {
    $_SESSION['error'] = 'Please login to checkout your order.';
    header('Location: ../auth/login.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    header('Location: index.php');
    exit;
}

// 1. Tính tổng tiền
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['qty'];
}

// 2. Tạo đơn hàng
$userId = $_SESSION['user']['id'];
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
$stmt->execute([$userId, $total]);
$orderId = $conn->lastInsertId();

// 3. Lưu chi tiết sản phẩm
$stmtItem = $conn->prepare("
    INSERT INTO order_items (order_id, product_id, product_name, price, qty)
    VALUES (?, ?, ?, ?, ?)
");
foreach ($cart as $item) {
    $stmtItem->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['qty']]);
}

// 4. Xoá giỏ hàng
unset($_SESSION['cart']);

require_once '../includes/header_user.php';
?>

<div class="checkout-card">
    <div class="checkout-icon">✅</div>
    <h2 class="checkout-title">Checkout Successful!</h2>
    <p class="checkout-sub">Thank you for your purchase!</p>
    <p class="checkout-id">Order ID: <strong>#<?= $orderId ?></strong></p>
    <p class="checkout-id">Total: <strong><?= number_format($total) ?> VND</strong></p>

    <div class="checkout-actions">
        <a href="/products/index.php" class="btn btn-green">Continue Shopping</a>
        <a href="/users/orders.php" class="btn btn-gray">View My Orders</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
