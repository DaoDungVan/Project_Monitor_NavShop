<?php
// ================= PHẦN LOGIC (LUÔN Ở TRÊN CÙNG) =================
session_start();

// Nếu admin truy cập shop user → đá sang admin panel
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    header('Location: admin_index.php');
    exit;
}

// Kết nối database
require_once '../config/db.php';

// ================= FILTER =================
$keyword    = trim($_GET['keyword'] ?? '');
$size       = trim($_GET['size'] ?? '');
$resolution = trim($_GET['resolution'] ?? '');
$panel      = trim($_GET['panel'] ?? '');
$min_price  = trim($_GET['min_price'] ?? '');
$max_price  = trim($_GET['max_price'] ?? '');
$sort_price = trim($_GET['sort_price'] ?? '');

// ================= PHÂN TRANG =================
$limit = 18;
$page = $_GET['page'] ?? 1;
$page = max(1, (int)$page);
$offset = ($page - 1) * $limit;

// ================= BUILD WHERE =================
$where = " WHERE 1=1";
$params = [];

// Search name
if ($keyword !== '') {
    $where .= " AND name LIKE ?";
    $params[] = '%' . $keyword . '%';
}

// Filter size
if ($size !== '') {
    $where .= " AND size = ?";
    $params[] = $size;
}

// Filter resolution
if ($resolution !== '') {
    $where .= " AND resolution = ?";
    $params[] = $resolution;
}

// Filter panel
if ($panel !== '') {
    $where .= " AND panel = ?";
    $params[] = $panel;
}

// Filter price
if ($min_price !== '') {
    $where .= " AND price >= ?";
    $params[] = $min_price;
}
if ($max_price !== '') {
    $where .= " AND price <= ?";
    $params[] = $max_price;
}

// ================= SORT =================
$orderBy = " ORDER BY id DESC"; // mặc định: sản phẩm mới nhất

if ($sort_price === 'asc') {
    $orderBy = " ORDER BY price ASC";
} elseif ($sort_price === 'desc') {
    $orderBy = " ORDER BY price DESC";
}

// ================= COUNT TOTAL =================
$countSql = "SELECT COUNT(*) FROM products" . $where;
$stmtCount = $conn->prepare($countSql);
$stmtCount->execute($params);
$totalProducts = $stmtCount->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

// ================= GET PRODUCTS =================
$sql = "SELECT * FROM products" . $where . $orderBy . " LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================= HEADER =================
require_once '../includes/header_user.php';
?>

<h2 class="mb-4">Monitor Shop</h2>

<!-- FILTER FORM -->
<form method="GET" class="row mb-4">

    <div class="col-md-3">
        <input type="text" name="keyword" class="form-control"
            placeholder="Search by name..."
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

    <div class="col-md-3">
        <select name="sort_price" class="form-control">
            <option value="">Sort by price</option>
            <option value="asc" <?= $sort_price === 'asc' ? 'selected' : '' ?>>
                Price: Low → High
            </option>
            <option value="desc" <?= $sort_price === 'desc' ? 'selected' : '' ?>>
                Price: High → Low
            </option>
        </select>
    </div>

    <div class="col-md-3 mt-3">
        <select name="size" class="form-control">
            <option value="">All Sizes</option>
            <option value="24" <?= $size == 24 ? 'selected' : '' ?>>24 inch</option>
            <option value="27" <?= $size == 27 ? 'selected' : '' ?>>27 inch</option>
            <option value="32" <?= $size == 32 ? 'selected' : '' ?>>32 inch</option>
        </select>
    </div>

    <div class="col-md-3 mt-3">
        <select name="resolution" class="form-control">
            <option value="">All Resolution</option>
            <option value="FHD" <?= $resolution == 'FHD' ? 'selected' : '' ?>>Full HD</option>
            <option value="2K" <?= $resolution == '2K' ? 'selected' : '' ?>>2K</option>
            <option value="4K" <?= $resolution == '4K' ? 'selected' : '' ?>>4K</option>
        </select>
    </div>

    <div class="col-md-3 mt-3">
        <select name="panel" class="form-control">
            <option value="">All Panel</option>
            <option value="IPS" <?= $panel == 'IPS' ? 'selected' : '' ?>>IPS</option>
            <option value="VA" <?= $panel == 'VA' ? 'selected' : '' ?>>VA</option>
            <option value="OLED" <?= $panel == 'OLED' ? 'selected' : '' ?>>OLED</option>
        </select>
    </div>

    <div class="col-md-3 mt-3">
        <button class="btn btn-primary w-100" style="background:#20b462;border:none">
            Filter
        </button>
    </div>

</form>

<!-- PRODUCT LIST -->
<div class="row">
    <?php if (empty($products)): ?>
        <p>No products found</p>
    <?php endif; ?>

    <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100" style="border-radius:15px">

                <?php if ($product['image']): ?>
                    <a href="show.php?id=<?= $product['id'] ?>">
                        <img src="../<?= htmlspecialchars($product['image']) ?>"
                            class="card-img-top"
                            style="height:200px;object-fit:contain">
                    </a>
                <?php endif; ?>

                <div class="card-body">
                    <h5>
                        <a href="show.php?id=<?= $product['id'] ?>"
                            style="text-decoration:none;color:inherit">
                            <?= htmlspecialchars($product['name']) ?>
                        </a>
                    </h5>

                    <div style="background-color: #e9e9e9; padding: 10px; border-radius: 15px; margin-bottom: 10px; 
                    display: flex; justify-content: center; align-items: flex-start; gap: 70px;">
                        <p class="card-text">
                            Brand: <?= htmlspecialchars($product['brand']) ?><br>
                            Size: <?= $product['size'] ?> inch<br>
                            Resolution: <?= htmlspecialchars($product['resolution']) ?><br>
                        </p>
                        <p class="card-text">
                            Panel: <?= htmlspecialchars($product['panel']) ?><br>
                            Screen: <?= $product['is_curved'] ? 'Curved' : 'Flat' ?>
                        </p>
                    </div>

                    <strong style="color:#E30019; text-align:center; display:block; font-size:18px;">
                        <?= number_format($product['price']) ?> VND
                    </strong>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- PAGINATION -->
<?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link"
                        href="?page=<?= $i ?>
                   &keyword=<?= urlencode($keyword) ?>
                   &size=<?= urlencode($size) ?>
                   &resolution=<?= urlencode($resolution) ?>
                   &panel=<?= urlencode($panel) ?>
                   &min_price=<?= urlencode($min_price) ?>
                   &max_price=<?= urlencode($max_price) ?>
                   &sort_price=<?= urlencode($sort_price) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>