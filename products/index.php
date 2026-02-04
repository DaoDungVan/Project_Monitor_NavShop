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

// Lấy filter từ URL (nếu có)
$keyword    = trim($_GET['keyword'] ?? '');
$size       = trim($_GET['size'] ?? '');
$resolution = trim($_GET['resolution'] ?? '');
$panel      = trim($_GET['panel'] ?? '');
$min_price  = trim($_GET['min_price'] ?? '');
$max_price  = trim($_GET['max_price'] ?? '');


// ================= PHÂN TRANG =================
$limit = 18; // số sản phẩm mỗi trang
$page = $_GET['page'] ?? 1;
$page = max(1, (int)$page); // tránh page <= 0
$offset = ($page - 1) * $limit;

// ================= BUILD WHERE CHUNG =================
$where = " WHERE 1=1";
$params = [];

// Tìm theo tên
if ($keyword !== '') {
    $where .= " AND name LIKE ?";
    $params[] = '%' . $keyword . '%';
}

// Filter theo size
if ($size !== '') {
    $where .= " AND size = ?";
    $params[] = $size;
}

// Filter theo resolution
if ($resolution !== '') {
    $where .= " AND resolution = ?";
    $params[] = $resolution;
}

// Filter theo panel
if ($panel !== '') {
    $where .= " AND panel = ?";
    $params[] = $panel;
}

// Filter theo giá
if ($min_price !== '') {
    $where .= " AND price >= ?";
    $params[] = $min_price;
}

if ($max_price !== '') {
    $where .= " AND price <= ?";
    $params[] = $max_price;
}

// ================= QUERY ĐẾM TỔNG SẢN PHẨM (PHÂN TRANG) =================
$countSql = "SELECT COUNT(*) FROM products" . $where;
$stmtCount = $conn->prepare($countSql);
$stmtCount->execute($params);
$totalProducts = $stmtCount->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

// ================= QUERY LẤY SẢN PHẨM =================
$sql = "SELECT * FROM products" . $where . " LIMIT $limit OFFSET $offset";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ================= INCLUDE HEADER USER =================
require_once '../includes/header_user.php';
?>

<h2 class="mb-4">Monitor Shop</h2>

<!-- FORM FILTER -->
<form method="GET" class="row mb-4">

    <div class="col-md-3">
        <input
            type="text"
            name="keyword"
            class="form-control"
            placeholder="Search by name..."
            value="<?= htmlspecialchars($keyword) ?>">
    </div>

    <div class="col-md-2">
        <input
            type="number"
            name="min_price"
            class="form-control"
            placeholder="Min price"
            value="<?= htmlspecialchars($min_price) ?>">
    </div>

    <div class="col-md-2">
        <input
            type="number"
            name="max_price"
            class="form-control"
            placeholder="Max price"
            value="<?= htmlspecialchars($max_price) ?>">
    </div>

    <div class="col-md-3">
        <select name="size" class="form-control">
            <option value="">All Sizes</option>
            <option value="24" <?= $size == 24 ? 'selected' : '' ?>>24 inch</option>
            <option value="27" <?= $size == 27 ? 'selected' : '' ?>>27 inch</option>
            <option value="32" <?= $size == 32 ? 'selected' : '' ?>>32 inch</option>
        </select>
    </div>

    <div class="col-md-3">
        <select name="resolution" class="form-control" style="margin-top: 15px;">
            <option value="">All Resolution</option>
            <option value="FHD" <?= $resolution == 'FHD' ? 'selected' : '' ?>>Full HD</option>
            <option value="2K" <?= $resolution == '2K' ? 'selected' : '' ?>>2K</option>
            <option value="4K" <?= $resolution == '4K' ? 'selected' : '' ?>>4K</option>
        </select>
    </div>

    <div class="col-md-3">
        <select name="panel" class="form-control" style="margin-top: 15px;">
            <option value="">All Panel</option>
            <option value="IPS" <?= $panel == 'IPS' ? 'selected' : '' ?>>IPS</option>
            <option value="VA" <?= $panel == 'VA' ? 'selected' : '' ?>>VA</option>
            <option value="OLED" <?= $panel == 'OLED' ? 'selected' : '' ?>>OLED</option>
        </select>
    </div>

    <div class="col-md-3" style="margin-top: 15px;">
        <button style="background-color: #20b462; border: none;" class="btn btn-primary w-100">Filter</button>
    </div>

</form>

<!-- DANH SÁCH SẢN PHẨM -->
<div class="row">

    <?php if (empty($products)): ?>
        <p>No products found</p>
    <?php endif; ?>

    <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4" >
            <div class="card h-100" style="border-radius: 15px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">

                <?php if (!empty($product['image'])): ?>
                    <a href="show.php?id=<?= $product['id'] ?>">
                        <img src="../<?= htmlspecialchars($product['image']) ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>"
                            class="card-img-top"
                            style="height:200px; object-fit:contain;">
                    </a>
                <?php endif; ?>

                <div class="card-body">
                    <h5 class="card-title">
                        <h5>
                            <a style="text-decoration: none; color: inherit;" href="show.php?id=<?= $product['id'] ?>">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                        </h5>

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

                    <strong style="color:#E30019; text-align: center; display: block;">
                        <?= number_format($product['price']) ?> VND
                    </strong>
                </div>

            </div>
        </div>
    <?php endforeach; ?>

</div>

<!-- PHÂN TRANG -->
<?php if ($totalPages > 1): ?>
    <nav>
        <ul class="pagination justify-content-center">

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>" style="background-color: ;">
                    <a  class="page-link"
                        href="?page=<?= $i ?>
                        &keyword=<?= urlencode(trim($keyword)) ?>
                        &size=<?= urlencode(trim($size)) ?>
                        &resolution=<?= urlencode(trim($resolution)) ?>
                        &panel=<?= urlencode(trim($panel)) ?>
                        &min_price=<?= urlencode(trim($min_price)) ?>
                        &max_price=<?= urlencode(trim($max_price)) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

        </ul>
    </nav>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>