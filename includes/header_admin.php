<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Chặn nếu không phải admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: /Project_Monitor_NavShop/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">


    <nav class="navbar navbar-expand-lg navbar-dark " style="background-color: #024487;">
        <div class="container">

            <a class="navbar-brand" href="/Project_Monitor_NavShop/products/admin_index.php">
                Admin Panel
            </a>

            <ul class="navbar-nav me-auto">

                <li class="nav-item">
                    <a class="nav-link" href="/Project_Monitor_NavShop/products/admin_index.php">
                        Products
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/Project_Monitor_NavShop/orders/admin_index.php">
                        Orders
                    </a>
                </li>

            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item">
                    <span class="navbar-text me-3">
                        Logged: <?= htmlspecialchars($_SESSION['user']['name']) ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a href="/Project_Monitor_NavShop/auth/logout.php"
                        class="btn btn-outline-light btn-sm">
                        Logout
                    </a>
                </li>
            </ul>

        </div>
    </nav>

    <div class="container mt-4">