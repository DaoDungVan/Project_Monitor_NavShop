<?php
// Chỉ admin mới được vào
require_once '../middleware/admin.php';

// Kết nối database
require_once '../config/db.php';

// ================= PHÂN TRANG =================
$limit = 5; // số đơn hàng mỗi trang
$page = $_GET['page'] ?? 1;
$page = max(1, (int)$page);
$offset = ($page - 1) * $limit;

// ================= ĐẾM TỔNG ĐƠN =================
$stmt = $conn->query("SELECT COUNT(*) FROM orders");
$totalOrders = $stmt->fetchColumn();
$totalPages = ceil($totalOrders / $limit);

// ================= LẤY DANH SÁCH ĐƠN =================
$stmt = $conn->prepare("
    SELECT * FROM orders
    ORDER BY id
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
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

    <?php if (empty($orders)): ?>
        <tr>
            <td colspan="4" class="text-center">No orders found</td>
        </tr>
    <?php endif; ?>

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

<!-- ================= PAGINATION ================= -->
<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<?php require_once '../includes/footer_admin.php'; ?>
