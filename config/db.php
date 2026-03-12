<?php
$host = 'sql110.ezyro.com';
$dbname = 'ezyro_41372587_navshop';   // TÊN DATABASE trong phpMyAdmin
$username = 'ezyro_41372587';
$password = '0a80218f197b4a11';        // XAMPP mặc định rỗng

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password
    );

    // Báo lỗi chi tiết nếu có lỗi SQL
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>