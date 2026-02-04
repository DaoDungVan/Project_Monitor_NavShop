<!-- <?php
session_start();

// Nếu giỏ hàng trống → quay về cart
if (empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

require_once '../includes/header_user.php';
?>

<h2>Guest Checkout</h2>

<p>Please enter your information to place order.</p>

<form method="POST" action="guest_checkout_process.php">

    <label>Name</label>
    <input type="text" name="name" required>

    <label>Email</label>
    <input type="email" name="email" required>

    <label>Phone</label>
    <input type="text" name="phone" required>

    <label>Address</label>
    <input type="text" name="address" required>

    <button class="btn btn-success">Place Order</button>
</form>

<?php require_once '../includes/footer.php'; ?> -->
