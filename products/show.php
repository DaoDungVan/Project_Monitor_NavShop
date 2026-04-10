<?php
session_start();
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id   = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: index.php');
    exit;
}

require_once '../includes/header_user.php';
?>

<h1 class="page-title"><?= htmlspecialchars($product['name']) ?></h1>

<div class="detail-grid">

    <!-- IMAGE -->
    <div class="detail-img-wrap">
        <?php if ($product['image']): ?>
            <img src="../<?= htmlspecialchars($product['image']) ?>"
                 alt="<?= htmlspecialchars($product['name']) ?>">
        <?php else: ?>
            <p class="text-muted" style="padding:40px;text-align:center;">No image available</p>
        <?php endif; ?>
    </div>

    <!-- INFO -->
    <div>
        <div class="detail-price"><?= number_format($product['price']) ?> VND</div>

        <table class="specs-table">
            <tr><td>Brand</td>      <td><?= htmlspecialchars($product['brand']) ?></td></tr>
            <tr><td>Size</td>       <td><?= $product['size'] ?> inch</td></tr>
            <tr><td>Resolution</td> <td><?= htmlspecialchars($product['resolution']) ?></td></tr>
            <tr><td>Panel</td>      <td><?= htmlspecialchars($product['panel']) ?></td></tr>
            <tr><td>Screen</td>     <td><?= $product['is_curved'] ? 'Curved' : 'Flat' ?></td></tr>
        </table>

        <?php if ($product['description']): ?>
            <p class="detail-desc"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
        <?php endif; ?>

        <div class="detail-actions">
            <a href="../cart/add.php?id=<?= $product['id'] ?>" class="btn btn-green">
                Add to Cart
            </a>
            <a href="index.php" class="btn btn-gray">
                ← Back to Shop
            </a>
        </div>
    </div>

</div>

<?php require_once '../includes/footer.php'; ?>
