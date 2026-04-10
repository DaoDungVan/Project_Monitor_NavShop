<?php
session_start();

if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    header('Location: admin_index.php');
    exit;
}

require_once '../config/db.php';

// ===== FILTER =====
$keyword    = trim($_GET['keyword'] ?? '');
$size       = trim($_GET['size'] ?? '');
$resolution = trim($_GET['resolution'] ?? '');
$panel      = trim($_GET['panel'] ?? '');
$min_price  = trim($_GET['min_price'] ?? '');
$max_price  = trim($_GET['max_price'] ?? '');
$sort_price = trim($_GET['sort_price'] ?? '');

// ===== PAGINATION =====
$limit  = 18;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// ===== BUILD WHERE =====
$where  = " WHERE 1=1";
$params = [];

if ($keyword !== '') { $where .= " AND name LIKE ?"; $params[] = '%' . $keyword . '%'; }
if ($size    !== '') { $where .= " AND size = ?";    $params[] = $size; }
if ($resolution !== '') { $where .= " AND resolution = ?"; $params[] = $resolution; }
if ($panel   !== '') { $where .= " AND panel = ?";   $params[] = $panel; }
if ($min_price !== '') { $where .= " AND price >= ?"; $params[] = $min_price; }
if ($max_price !== '') { $where .= " AND price <= ?"; $params[] = $max_price; }

$orderBy = " ORDER BY id DESC";
if ($sort_price === 'asc')  $orderBy = " ORDER BY price ASC";
if ($sort_price === 'desc') $orderBy = " ORDER BY price DESC";

// ===== COUNT =====
$stmtCount = $conn->prepare("SELECT COUNT(*) FROM products" . $where);
$stmtCount->execute($params);
$totalProducts = $stmtCount->fetchColumn();
$totalPages    = ceil($totalProducts / $limit);

// ===== GET PRODUCTS =====
$stmt = $conn->prepare("SELECT * FROM products" . $where . $orderBy . " LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header_user.php';
?>

<h1 class="page-title">Monitor Shop</h1>

<!-- FILTER -->
<div class="filter-bar">
    <form method="GET">
        <div class="filter-row">
            <input type="text" name="keyword" class="form-control"
                   placeholder="Search by name..."
                   value="<?= htmlspecialchars($keyword) ?>">

            <input type="number" name="min_price" class="form-control"
                   placeholder="Min price"
                   value="<?= htmlspecialchars($min_price) ?>">

            <input type="number" name="max_price" class="form-control"
                   placeholder="Max price"
                   value="<?= htmlspecialchars($max_price) ?>">

            <select name="sort_price" class="form-control">
                <option value="">Sort by price</option>
                <option value="asc"  <?= $sort_price === 'asc'  ? 'selected' : '' ?>>Price: Low → High</option>
                <option value="desc" <?= $sort_price === 'desc' ? 'selected' : '' ?>>Price: High → Low</option>
            </select>
        </div>

        <div class="filter-row2">
            <select name="size" class="form-control">
                <option value="">All Sizes</option>
                <option value="24" <?= $size == 24 ? 'selected' : '' ?>>24 inch</option>
                <option value="27" <?= $size == 27 ? 'selected' : '' ?>>27 inch</option>
                <option value="32" <?= $size == 32 ? 'selected' : '' ?>>32 inch</option>
            </select>

            <select name="resolution" class="form-control">
                <option value="">All Resolution</option>
                <option value="FHD"  <?= $resolution === 'FHD'  ? 'selected' : '' ?>>Full HD</option>
                <option value="2K"   <?= $resolution === '2K'   ? 'selected' : '' ?>>2K</option>
                <option value="4K"   <?= $resolution === '4K'   ? 'selected' : '' ?>>4K</option>
            </select>

            <select name="panel" class="form-control">
                <option value="">All Panel</option>
                <option value="IPS"  <?= $panel === 'IPS'  ? 'selected' : '' ?>>IPS</option>
                <option value="VA"   <?= $panel === 'VA'   ? 'selected' : '' ?>>VA</option>
                <option value="OLED" <?= $panel === 'OLED' ? 'selected' : '' ?>>OLED</option>
            </select>

            <button type="submit" class="btn btn-green">Filter</button>
        </div>
    </form>
</div>

<!-- PRODUCT GRID -->
<?php if (empty($products)): ?>
    <p class="no-products">No products found.</p>
<?php else: ?>
<div class="product-grid">
    <?php foreach ($products as $p): ?>
        <div class="card">
            <?php if ($p['image']): ?>
                <a href="show.php?id=<?= $p['id'] ?>">
                    <img src="../<?= htmlspecialchars($p['image']) ?>"
                         class="card-img" alt="<?= htmlspecialchars($p['name']) ?>">
                </a>
            <?php endif; ?>

            <div class="card-body">
                <div class="card-name">
                    <a href="show.php?id=<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['name']) ?>
                    </a>
                </div>

                <div class="card-specs">
                    <div>
                        Brand: <?= htmlspecialchars($p['brand']) ?><br>
                        Size: <?= $p['size'] ?> inch<br>
                        Resolution: <?= htmlspecialchars($p['resolution']) ?>
                    </div>
                    <div>
                        Panel: <?= htmlspecialchars($p['panel']) ?><br>
                        Screen: <?= $p['is_curved'] ? 'Curved' : 'Flat' ?>
                    </div>
                </div>

                <div class="card-price">
                    <?= number_format($p['price']) ?> VND
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- PAGINATION -->
<?php if ($totalPages > 1): ?>
<nav class="pagination">
    <?php
    $queryBase = http_build_query(array_filter([
        'keyword'    => $keyword,
        'size'       => $size,
        'resolution' => $resolution,
        'panel'      => $panel,
        'min_price'  => $min_price,
        'max_price'  => $max_price,
        'sort_price' => $sort_price,
    ]));
    for ($i = 1; $i <= $totalPages; $i++):
    ?>
        <a class="page-link <?= $i == $page ? 'active' : '' ?>"
           href="?page=<?= $i ?><?= $queryBase ? '&' . $queryBase : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</nav>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
