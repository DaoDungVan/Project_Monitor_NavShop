<?php
require_once '../middleware/admin.php';
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    header('Location: admin_index.php');
    exit;
}

$orderId = (int)$_GET['id'];

// Lấy thông tin đơn hàng + tên khách
$stmtOrder = $conn->prepare("
    SELECT o.*, u.name AS customer_name, u.email AS customer_email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmtOrder->execute([$orderId]);
$order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: admin_index.php');
    exit;
}

// Lấy các sản phẩm trong đơn
$stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once '../includes/header_admin.php'; ?>

<div class="page-header-bar">
    <h1 class="page-title" style="margin-bottom:0">Order #<?= $orderId ?></h1>
    <a href="admin_index.php" class="btn btn-gray btn-sm">← Back to Orders</a>
</div>

<!-- Order summary -->
<div style="background:var(--white);border-radius:10px;padding:16px 20px;box-shadow:var(--shadow);margin-bottom:20px;display:flex;gap:32px;flex-wrap:wrap;">
    <div><span class="text-muted">Customer:</span> <strong><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></strong></div>
    <div><span class="text-muted">Email:</span> <?= htmlspecialchars($order['customer_email'] ?? '–') ?></div>
    <div><span class="text-muted">Date:</span> <?= $order['created_at'] ?></div>
    <div><span class="text-muted">Total:</span> <strong class="cart-total-price"><?= number_format($order['total_price']) ?> VND</strong></div>
</div>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Unit Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr><td colspan="4" class="td-center text-muted">No items found.</td></tr>
            <?php endif; ?>

            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= number_format($item['price']) ?> VND</td>
                    <td><?= $item['qty'] ?></td>
                    <td><strong><?= number_format($item['price'] * $item['qty']) ?> VND</strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../includes/footer_admin.php'; ?>
