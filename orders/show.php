<?php
require_once '../middleware/admin.php';
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    header('Location: admin_index.php');
    exit;
}

$orderId = $_GET['id'];

// Lấy chi tiết sản phẩm trong đơn
$stmt = $conn->prepare("
    SELECT * FROM order_items WHERE order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<?php require_once '../includes/header_admin.php'; ?>
<h2>Order #<?= $orderId ?></h2>

<table class="table table-bordered">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Total</th>
    </tr>

    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= number_format($item['price']) ?> VND</td>
            <td><?= $item['qty'] ?></td>
            <td><?= number_format($item['price'] * $item['qty']) ?> VND</td>
        </tr>
    <?php endforeach; ?>

</table>

<a href="admin_index.php" class="btn btn-secondary">Back</a>
<?php require_once '../includes/footer_admin.php'; ?>
