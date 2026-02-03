<?php
session_start();

// Nếu admin cố vào cart → đá về admin panel
if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    header('Location: ../products/admin_index.php');
    exit;
}

// Include header USER
require_once '../includes/header_user.php';

$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<h2>Your Cart</h2>

<?php if (empty($cart)): ?>
    <p>Your cart is empty.</p>
<?php else: ?>

<table class="table table-bordered">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Qty</th>
        <th>Total</th>
        <th>Action</th>
    </tr>

<?php foreach ($cart as $item):
    $lineTotal = $item['price'] * $item['qty'];
    $total += $lineTotal;
?>
<tr>
    <td><?= htmlspecialchars($item['name']) ?></td>
    <td><?= number_format($item['price']) ?> VND</td>
    <td><?= $item['qty'] ?></td>
    <td><?= number_format($lineTotal) ?> VND</td>
    <td>
        <a href="remove.php?id=<?= $item['id'] ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Remove this item?')">
           Remove
        </a>
    </td>
</tr>
<?php endforeach; ?>

<tr>
    <th colspan="3">Grand Total</th>
    <th><?= number_format($total) ?> VND</th>
    <th></th>
</tr>
</table>

<a href="checkout.php" class="btn btn-primary">Checkout</a>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
