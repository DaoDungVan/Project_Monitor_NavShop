<?php
require_once '../middleware/admin.php';
require_once '../config/db.php';

$keyword   = trim($_GET['keyword'] ?? '');
$panel     = trim($_GET['panel'] ?? '');
$min_price = trim($_GET['min_price'] ?? '');
$max_price = trim($_GET['max_price'] ?? '');

$limit  = 10;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

$where  = " WHERE 1=1";
$params = [];

if ($keyword   !== '') { $where .= " AND name LIKE ?";   $params[] = "%$keyword%"; }
if ($panel     !== '') { $where .= " AND panel = ?";     $params[] = $panel; }
if ($min_price !== '') { $where .= " AND price >= ?";    $params[] = $min_price; }
if ($max_price !== '') { $where .= " AND price <= ?";    $params[] = $max_price; }

$stmtCount = $conn->prepare("SELECT COUNT(*) FROM products" . $where);
$stmtCount->execute($params);
$totalProducts = $stmtCount->fetchColumn();
$totalPages    = ceil($totalProducts / $limit);

$stmt = $conn->prepare("SELECT * FROM products" . $where . " ORDER BY id DESC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once '../includes/header_admin.php'; ?>

<div class="page-header-bar">
    <h1 class="page-title" style="margin-bottom:0">Product Management</h1>
    <a href="create.php" class="btn btn-green">+ Add Product</a>
</div>

<!-- FILTER -->
<div class="filter-bar" style="margin-bottom:20px">
    <form method="GET">
        <div class="filter-row">
            <input type="text" name="keyword" class="form-control form-control-admin"
                   placeholder="Search product..."
                   value="<?= htmlspecialchars($keyword) ?>">

            <input type="number" name="min_price" class="form-control form-control-admin"
                   placeholder="Min price"
                   value="<?= htmlspecialchars($min_price) ?>">

            <input type="number" name="max_price" class="form-control form-control-admin"
                   placeholder="Max price"
                   value="<?= htmlspecialchars($max_price) ?>">

            <select name="panel" class="form-control form-control-admin">
                <option value="">All panel</option>
                <option value="IPS"  <?= $panel === 'IPS'  ? 'selected' : '' ?>>IPS</option>
                <option value="VA"   <?= $panel === 'VA'   ? 'selected' : '' ?>>VA</option>
                <option value="OLED" <?= $panel === 'OLED' ? 'selected' : '' ?>>OLED</option>
            </select>
        </div>

        <div style="margin-top:10px">
            <button type="submit" class="btn btn-navy">Filter</button>
            <a href="admin_index.php" class="btn btn-gray btn-sm" style="margin-left:6px">Reset</a>
        </div>
    </form>
</div>

<!-- TABLE -->
<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Brand</th>
                <th>Size</th>
                <th>Resolution</th>
                <th>Panel</th>
                <th>Screen</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr><td colspan="9" class="td-center text-muted">No products found.</td></tr>
            <?php endif; ?>

            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['brand']) ?></td>
                    <td><?= $p['size'] ?> inch</td>
                    <td><?= $p['resolution'] ?></td>
                    <td><?= $p['panel'] ?></td>
                    <td><?= $p['is_curved'] ? 'Curved' : 'Flat' ?></td>
                    <td><?= number_format($p['price']) ?> VND</td>
                    <td>
                        <div class="td-actions">
                            <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-orange btn-sm">Edit</a>
                            <a href="delete.php?id=<?= $p['id'] ?>"
                               class="btn btn-red btn-sm"
                               onclick="return confirm('Delete this product?')">Delete</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- PAGINATION -->
<?php if ($totalPages > 1): ?>
<nav class="pagination">
    <?php
    $q = http_build_query(array_filter(['keyword' => $keyword, 'panel' => $panel, 'min_price' => $min_price, 'max_price' => $max_price]));
    for ($i = 1; $i <= $totalPages; $i++):
    ?>
        <a class="page-link <?= $i == $page ? 'active' : '' ?>"
           href="?page=<?= $i ?><?= $q ? '&' . $q : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</nav>
<?php endif; ?>

<?php require_once '../includes/footer_admin.php'; ?>
