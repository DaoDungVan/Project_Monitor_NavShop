<?php
require_once '../middleware/admin.php';
require_once '../config/db.php';

$limit  = 10;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$stmtCount  = $conn->query("SELECT COUNT(*) FROM orders");
$totalOrders = $stmtCount->fetchColumn();
$totalPages  = ceil($totalOrders / $limit);

$stmt = $conn->prepare("
    SELECT o.id, o.total_price, o.created_at,
           u.name AS customer_name, u.email AS customer_email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.id DESC
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once '../includes/header_admin.php'; ?>

<h1 class="page-title">Order Management</h1>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Total Price</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr><td colspan="6" class="td-center text-muted">No orders found.</td></tr>
            <?php endif; ?>

            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><strong>#<?= $order['id'] ?></strong></td>
                    <td><?= htmlspecialchars($order['customer_name'] ?? 'Guest') ?></td>
                    <td class="text-muted"><?= htmlspecialchars($order['customer_email'] ?? '–') ?></td>
                    <td class="cart-total-price"><?= number_format($order['total_price']) ?> VND</td>
                    <td class="text-muted"><?= $order['created_at'] ?></td>
                    <td>
                        <a href="show.php?id=<?= $order['id'] ?>" class="btn btn-sky btn-sm">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
<nav class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a class="page-link <?= $i == $page ? 'active' : '' ?>"
           href="?page=<?= $i ?>"><?= $i ?></a>
    <?php endfor; ?>
</nav>
<?php endif; ?>

<?php require_once '../includes/footer_admin.php'; ?>
