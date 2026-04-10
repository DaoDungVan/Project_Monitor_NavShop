<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';

$userId = $_SESSION['user']['id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header_user.php';
?>

<h1 class="page-title">My Orders</h1>

<?php if (empty($orders)): ?>
    <div class="cart-empty">
        <p>You have no orders yet.</p>
        <p class="mt-2"><a href="/products/index.php">← Start Shopping</a></p>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table class="table">
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
                        <td><strong>#<?= $order['id'] ?></strong></td>
                        <td class="cart-total-price"><?= number_format($order['total_price']) ?> VND</td>
                        <td class="text-muted"><?= $order['created_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
