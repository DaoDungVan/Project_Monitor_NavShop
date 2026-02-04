<?php
// Bắt đầu session (sau này có thể dùng để auto login)
session_start();

// Kết nối database
require_once '../config/db.php';

// Nếu đã đăng nhập rồi thì không cho vào trang register nữa
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: ../products/admin_index.php');
    } else {
        header('Location: ../products/index.php');
    }
    exit;
}

// Biến lưu lỗi / thông báo
$error = '';
$success = '';

// Khi người dùng submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lấy dữ liệu từ form
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Kiểm tra nhập đủ chưa
    if ($name === '' || $email === '' || $password === '') {
        $error = 'Please fill all required fields';
    } else {

        // Kiểm tra email đã tồn tại chưa
        // Dùng prepare để tránh SQL Injection
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            // Email đã tồn tại
            $error = 'Email already exists';
        } else {

            // Hash password trước khi lưu vào database
            // Không bao giờ lưu password dạng plain text
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert user mới (role mặc định là user)
            $stmt = $conn->prepare("
                INSERT INTO users (name, email, password, role)
                VALUES (?, ?, ?, 'user')
            ");
            $stmt->execute([$name, $email, $hashedPassword]);

            $success = 'Register successful. You can login now.';
        }
    }
}

// Include header user (header đã có sẵn HTML + BODY)
require_once '../includes/header_user.php';
?>

<h2>Register</h2>

<!-- Hiển thị lỗi -->
<?php if ($error): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- Hiển thị thành công -->
<?php if ($success): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>

<!-- Form đăng ký -->
<form method="POST">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <button class="btn btn-primary" style="background-color: #20b462; border: none;">Register</button>
    <a href="login.php" class="btn btn-link" style="text-decoration: none;">Already have an account? Login</a>
</form>

<?php require_once '../includes/footer.php'; ?>