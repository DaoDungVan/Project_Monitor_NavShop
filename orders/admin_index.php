<?php
require_once '../middleware/admin.php';
require_once '../config/db.php';

// Lấy danh sách đơn hàng
$stmt = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once '../includes/header_admin.php'; ?>
<h2>Order Management</h2>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Total Price</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>

    <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['id'] ?></td>
            <td><?= number_format($order['total_price']) ?> VND</td>
            <td><?= $order['created_at'] ?></td>
            <td>
                <a href="show.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">
                    View
                </a>
            </td>
        </tr>
    <?php endforeach; ?>

</table>
<?php require_once '../includes/footer_admin.php'; ?>
