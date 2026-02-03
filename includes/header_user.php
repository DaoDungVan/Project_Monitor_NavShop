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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">


    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">

            <!-- LOGO -->
            <a class="navbar-brand" href="/Project_Monitor_NavShop/products/index.php">
                NavShop
            </a>

            <div class="collapse navbar-collapse">

                <!-- MENU BÊN TRÁI -->
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
                    <li class="nav-item">
                        <a class="nav-link" href="/Project_Monitor_NavShop/users/orders.php">
                            My Orders
                        </a>
                    </li>

                </ul>

                <!-- MENU BÊN PHẢI -->
                <ul class="navbar-nav ms-auto">

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
                        <li class="nav-item">
                            <span class="navbar-text me-3">
                                Hi,
                                <?= $_SESSION['user']['role'] === 'admin'
                                    ? 'Admin'
                                    : htmlspecialchars($_SESSION['user']['name']) ?>
                            </span>
                        </li>

                        <?php if ($_SESSION['user']['role'] === 'user'): ?>
                            <!-- PROFILE CHỈ DÀNH CHO USER -->
                            <li class="nav-item">
                                <a class="nav-link" href="/Project_Monitor_NavShop/users/profile.php">
                                    Profile
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
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