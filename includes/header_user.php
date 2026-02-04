<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>NavShop</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS riêng -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #20b462;">
    <div class="container">

        <!-- LOGO -->
        <a class="navbar-brand" href="/Project_Monitor_NavShop/products/index.php">
            NavShop
        </a>

        <div class="collapse navbar-collapse">

            <!-- MENU TRÁI -->
            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="/Project_Monitor_NavShop/products/index.php">
                        Shop
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/Project_Monitor_NavShop/cart/index.php">
                        Cart
                    </a>
                </li>

                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'user'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/Project_Monitor_NavShop/users/orders.php">
                            My Orders
                        </a>
                    </li>
                <?php endif; ?>

            </ul>

            <!-- MENU PHẢI -->
            <ul class="navbar-nav ms-auto align-items-center">

                <?php if (!isset($_SESSION['user'])): ?>
                    <!-- CHƯA LOGIN -->
                    <li class="nav-item">
                        <a class="nav-link" href="/Project_Monitor_NavShop/auth/login.php">
                            Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/Project_Monitor_NavShop/auth/register.php">
                            Register
                        </a>
                    </li>

                <?php else: ?>
                    <!-- ĐÃ LOGIN -->

                    <!-- TÊN -->
                    <li class="nav-item me-2">
                        <span class="navbar-text text-light">
                            Hi,
                            <?= $_SESSION['user']['role'] === 'admin'
                                ? 'Admin'
                                : htmlspecialchars($_SESSION['user']['name']) ?>
                        </span>
                    </li>

                    <!-- AVATAR -->
                    <?php if (!empty($_SESSION['user']['avatar'])): ?>
                        <li class="nav-item me-2">
                            <img
                                src="/Project_Monitor_NavShop/<?= htmlspecialchars($_SESSION['user']['avatar']) ?>"
                                alt="Avatar"
                                class="rounded-circle"
                                style="width:32px; height:32px; object-fit:cover;">
                        </li>
                    <?php endif; ?>

                    <!-- PROFILE (CHỈ USER) -->
                    <?php if ($_SESSION['user']['role'] === 'user'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/Project_Monitor_NavShop/users/profile.php">
                                Profile
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- LOGOUT -->
                    <li class="nav-item ms-2">
                        <a href="/Project_Monitor_NavShop/auth/logout.php"
                           class="btn btn-outline-light btn-sm">
                            Logout
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

        </div>
    </div>
</nav>

<div class="container mt-4">
