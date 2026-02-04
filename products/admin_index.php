<?php
// ================= PHẦN LOGIC ADMIN =================

// Chỉ admin mới được vào trang này
require_once '../middleware/admin.php';

// Kết nối database
require_once '../config/db.php';

// ================= FILTER =================
$keyword    = trim($_GET['keyword'] ?? '');
$panel      = trim($_GET['panel'] ?? '');
$min_price  = trim($_GET['min_price'] ?? '');
$max_price  = trim($_GET['max_price'] ?? '');

// ================= PHÂN TRANG =================
$limit = 8; // admin xem nhiều hơn user
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// ================= BUILD WHERE CHUNG =================
$where = " WHERE 1=1";
$params = [];

// Search theo tên
if ($keyword !== '') {
    $where .= " AND name LIKE ?";
    $params[] = "%$keyword%";
}

// Filter panel
if ($panel !== '') {
    $where .= " AND panel = ?";
    $params[] = $panel;
}

// Filter giá
if ($min_price !== '') {
    $where .= " AND price >= ?";
    $params[] = $min_price;
}
if ($max_price !== '') {
    $where .= " AND price <= ?";
    $params[] = $max_price;
}

// ================= COUNT =================
$countSql = "SELECT COUNT(*) FROM products" . $where;
$stmtCount = $conn->prepare($countSql);
$stmtCount->execute($params);
$totalProducts = $stmtCount->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

// ================= SELECT =================
$sql = "SELECT * FROM products" . $where . " ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once '../includes/header_admin.php'; ?>
<h2>Product Management</h2>

<!-- FORM SEARCH + FILTER -->
<form method="GET" class="row mb-3">

    <div class="col-md-3">
        <input type="text" name="keyword" class="form-control"
               placeholder="Search product..."
               value="<?= htmlspecialchars($keyword) ?>">
    </div>

    <div class="col-md-2">
        <input type="number" name="min_price" class="form-control"
               placeholder="Min price"
               value="<?= htmlspecialchars($min_price) ?>">
    </div>

    <div class="col-md-2">
        <input type="number" name="max_price" class="form-control"
               placeholder="Max price"
               value="<?= htmlspecialchars($max_price) ?>">
    </div>

    <div class="col-md-2">
        <select name="panel" class="form-control">
            <option value="">All panel</option>
            <option value="IPS" <?= $panel=='IPS'?'selected':'' ?>>IPS</option>
            <option value="VA" <?= $panel=='VA'?'selected':'' ?>>VA</option>
            <option value="OLED" <?= $panel=='OLED'?'selected':'' ?>>OLED</option>
        </select>
    </div>

    <div class="col-md-3">
        <button class="btn btn-primary w-100" style="background-color: #024487; border: none;">Filter</button>
    </div>

</form>

<!-- Nút thêm sản phẩm -->
<a href="create.php" class="btn btn-success mb-3">+ Add Product</a>

<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Brand</th>
            <th>Size</th>
            <th>Resolution</th>
            <th>Panel</th>
            <th>Curved</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>

        <?php if (empty($products)): ?>
            <tr>
                <td colspan="9" class="text-center">No products found</td>
            </tr>
        <?php endif; ?>

        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td><?= htmlspecialchars($product['name']) ?></td>
                <td><?= htmlspecialchars($product['brand']) ?></td>
                <td><?= $product['size'] ?></td>
                <td><?= $product['resolution'] ?></td>
                <td><?= $product['panel'] ?></td>
                <td><?= $product['is_curved'] ? 'Cong' : 'Phẳng' ?></td>
                <td><?= number_format($product['price']) ?> VND</td>
                <td>
                    <a href="edit.php?id=<?= $product['id'] ?>"
                       class="btn btn-warning btn-sm">Edit</a>

                    <a href="delete.php?id=<?= $product['id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Delete this product?')">
                       Delete
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>

    </tbody>
</table>

<!-- PHÂN TRANG ADMIN -->
<?php if ($totalPages > 1): ?>
<nav>
    <ul class="pagination justify-content-center">

        <?php for ($i=1; $i<=$totalPages; $i++): ?>
            <li class="page-item <?= $i==$page?'active':'' ?>">
                <a class="page-link"
                   href="?page=<?= $i ?>
                   &keyword=<?= urlencode($keyword) ?>
                   &panel=<?= urlencode($panel) ?>
                   &min_price=<?= urlencode($min_price) ?>
                   &max_price=<?= urlencode($max_price) ?>">
                   <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

    </ul>
</nav>
<?php endif; ?>

<?php require_once '../includes/footer_admin.php'; ?>

