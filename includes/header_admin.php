<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel – NavShop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav class="navbar navbar-admin">
    <div class="container">
        <div class="navbar-inner">

            <a class="navbar-brand" href="/products/admin_index.php">Admin Panel</a>

            <div class="navbar-nav">
                <a class="nav-link" href="/products/admin_index.php">Products</a>
                <a class="nav-link" href="/orders/admin_index.php">Orders</a>
                <span class="nav-text">
                    <?= htmlspecialchars($_SESSION['user']['name']) ?>
                </span>
                <a href="/auth/logout.php" class="btn-nav">Logout</a>
            </div>

        </div>
    </div>
</nav>

<div class="main-content">
<div class="container">
