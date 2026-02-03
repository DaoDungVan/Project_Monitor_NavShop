<?php
// Dùng lại middleware auth để chắc chắn là đã login
require_once 'auth.php';

// Sau khi login rồi, kiểm tra quyền
if ($_SESSION['user']['role'] !== 'admin') {
    // Không phải admin → không có quyền
    echo "You do not have permission to access this page";
    exit;
}
?>