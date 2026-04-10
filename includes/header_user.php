<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NavShop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <div class="navbar-inner">

            <a class="navbar-brand" href="/products/index.php">NavShop</a>

            <div class="navbar-nav">
                <a class="nav-link" href="/products/index.php">Shop</a>
                <a class="nav-link" href="/cart/index.php">Cart</a>

                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'user'): ?>
                    <a class="nav-link" href="/users/orders.php">My Orders</a>
                <?php endif; ?>

                <?php if (!isset($_SESSION['user'])): ?>
                    <a class="nav-link" href="/auth/login.php">Login</a>
                    <a class="nav-link" href="/auth/register.php">Register</a>
                <?php else: ?>
                    <span class="nav-text">
                        Hi, <?= $_SESSION['user']['role'] === 'admin'
                            ? 'Admin'
                            : htmlspecialchars($_SESSION['user']['name']) ?>
                    </span>

                    <?php if (!empty($_SESSION['user']['avatar'])): ?>
                        <img src="/<?= htmlspecialchars($_SESSION['user']['avatar']) ?>"
                             class="nav-avatar" alt="Avatar">
                    <?php endif; ?>

                    <?php if ($_SESSION['user']['role'] === 'user'): ?>
                        <a class="nav-link" href="/users/profile.php">Profile</a>
                    <?php endif; ?>

                    <a href="/auth/logout.php" class="btn-nav">Logout</a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</nav>

<div class="main-content">
<div class="container">
