<?php
// Bắt buộc phải có session để kiểm tra đăng nhập
session_start();

// Nếu chưa đăng nhập thì không cho vào
if (!isset($_SESSION['user'])) {
    // Chưa login → quay về trang login
    header('Location: ../auth/login.php');
    exit;
}
?>