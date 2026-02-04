<?php
// ================= PRODUCT DETAIL =================
session_start();

// Kết nối database
require_once '../config/db.php';

// Kiểm tra có id không
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = (int)$_GET['id'];

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Không có sản phẩm thì quay về shop
if (!$product) {
    header('Location: index.php');
    exit;
}

require_once '../includes/header_user.php';
?>

<h2><?= htmlspecialchars($product['name']) ?></h2>

<div class="row">

    <!-- ẢNH -->
    <div class="col-md-5">
        <?php if ($product['image']): ?>
            <img src="../<?= htmlspecialchars($product['image']) ?>"
                 class="img-fluid border rounded">
        <?php else: ?>
            <p>No image</p>
        <?php endif; ?>
    </div>

    <!-- THÔNG TIN -->
    <div class="col-md-7">

        <h4 style="color:#E30019">
            <?= number_format($product['price']) ?> VND
        </h4>

        <ul class="list-group mb-3">
            <li class="list-group-item"><strong>Brand:</strong> <?= htmlspecialchars($product['brand']) ?></li>
            <li class="list-group-item"><strong>Size:</strong> <?= $product['size'] ?> inch</li>
            <li class="list-group-item"><strong>Resolution:</strong> <?= htmlspecialchars($product['resolution']) ?></li>
            <li class="list-group-item"><strong>Panel:</strong> <?= htmlspecialchars($product['panel']) ?></li>
            <li class="list-group-item">
                <strong>Screen:</strong>
                <?= $product['is_curved'] ? 'Curved' : 'Flat' ?>
            </li>
        </ul>

        <?php if ($product['description']): ?>
            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <?php endif; ?>

        <a href="../cart/add.php?id=<?= $product['id'] ?>"
           class="btn btn-success" style="background-color: #20b462; border: none;">
            Add to cart
        </a>

        <a href="index.php" class="btn btn-secondary" style="background-color: #E30019; border: none;">
            Back to shop
        </a>

    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
