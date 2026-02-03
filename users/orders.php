<?php
// ================= USER ORDER HISTORY =================
session_start();

// Chưa login thì đá về login
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';

$userId = $_SESSION['user']['id'];

// Lấy danh sách đơn hàng của user
$stmt = $conn->prepare("
    SELECT * FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once '../includes/header_user.php'; ?>

<h2>My Orders</h2>

<?php if (empty($orders)): ?>
    <p>You have no orders.</p>
<?php else: ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Total Price</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>

    <?php foreach ($orders as $order): ?>
        <tr>
            <td>#<?= $order['id'] ?></td>
            <td><?= number_format($order['total_price']) ?> VND</td>
            <td><?= $order['created_at'] ?></td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>
<?php require_once '../includes/footer.php'; ?>
<?php endif; ?>


