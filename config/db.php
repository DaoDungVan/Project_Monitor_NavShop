<?php
$host = 'localhost';
$dbname = 'navshop';   // TÊN DATABASE trong phpMyAdmin
$username = 'root';
$password = '';        // XAMPP mặc định rỗng

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