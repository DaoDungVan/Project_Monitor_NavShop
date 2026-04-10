<?php
session_start();

if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    header('Location: ../products/admin_index.php');
    exit;
}

require_once '../includes/header_user.php';

$cart  = $_SESSION['cart'] ?? [];
$total = 0;
?>

<h1 class="page-title">Your Cart</h1>

<?php if (empty($cart)): ?>
    <div class="cart-empty">
        <p>Your cart is empty.</p>
        <p class="mt-2"><a href="/products/index.php">← Continue Shopping</a></p>
    </div>
<?php else: ?>

<div class="table-wrap">
    <table class="table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
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
                       class="btn btn-red btn-sm"
                       onclick="return confirm('Remove this item?')">
                        Remove
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>

            <tr class="td-bold">
                <td colspan="3"><strong>Grand Total</strong></td>
                <td class="cart-total-price"><?= number_format($total) ?> VND</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>

<div class="cart-actions">
    <?php if (isset($_SESSION['user'])): ?>
        <a href="checkout.php" class="btn btn-green">Checkout</a>
    <?php else: ?>
        <a href="/auth/login.php" class="btn btn-green">Login to Checkout</a>
    <?php endif; ?>
    <a href="/products/index.php" class="btn btn-gray">← Continue Shopping</a>
</div>

<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
