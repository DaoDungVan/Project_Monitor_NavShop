<?php
session_start();
require_once '../config/db.php';

$cart = $_SESSION['cart'] ?? [];

if (!empty($cart)) {

    // 1. Tính tổng tiền
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['qty'];
    }

    // 2. Tạo đơn hàng
    $userId = $_SESSION['user']['id'];

    $stmt = $conn->prepare("
    INSERT INTO orders (user_id, total_price)
    VALUES (?, ?)
");
    $stmt->execute([$userId, $total]);

    // Lấy id đơn hàng vừa tạo
    $orderId = $conn->lastInsertId();

    // 3. Lưu chi tiết sản phẩm
    $stmtItem = $conn->prepare("
        INSERT INTO order_items 
        (order_id, product_id, product_name, price, qty)
        VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($cart as $item) {
        $stmtItem->execute([
            $orderId,
            $item['id'],
            $item['name'],
            $item['price'],
            $item['qty']
        ]);
    }
}

// 4. Xoá giỏ hàng
unset($_SESSION['cart']);

// Include header user (header đã có HTML + BODY)
require_once '../includes/header_user.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">

        <div class="card text-center shadow-sm">
            <div class="card-body">

                <h2 class="text-success mb-3">✔ Checkout Successful</h2>

                <p class="mb-2">
                    Thank you for your purchase!
                </p>

                <?php if (!empty($orderId)): ?>
                    <p class="text-muted">
                        Order ID: <strong>#<?= $orderId ?></strong>
                    </p>
                <?php endif; ?>

                <div class="d-grid gap-2 mt-4">
                    <a href="../products/index.php" class="btn btn-primary">
                        Continue Shopping
                    </a>

                    <a href="../orders/history.php" class="btn btn-outline-secondary disabled">
                        View Order History (demo)
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

</div> <!-- đóng container từ header_user -->
</body>

</html>