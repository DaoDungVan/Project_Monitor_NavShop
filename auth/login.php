<?php
// ================= LOGIC (LUÔN Ở TRÊN CÙNG) =================
session_start();
require_once '../config/db.php';

// Nếu đã login rồi thì không cho vào trang login nữa
if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] === 'admin') {
        header('Location: ../products/admin_index.php');
    } else {
        header('Location: ../products/index.php');
    }
    exit;
}

$error = '';

// Nếu submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter email and password';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user'] = [
                'id'   => $user['id'],
                'name' => $user['name'],
                'role' => $user['role']
            ];

            if ($user['role'] === 'admin') {
                header('Location: ../products/admin_index.php');
            } else {
                header('Location: ../products/index.php');
            }
            exit;

        } else {
            $error = 'Invalid email or password';
        }
    }
}

// ================= INCLUDE HEADER USER =================
require_once '../includes/header_user.php';
?>

<h2>Login</h2>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="POST" class="mt-3">
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <button class="btn btn-primary">Login</button>
    <a href="register.php" class="btn btn-link">Register</a>
</form>

<?php require_once '../includes/footer.php'; ?>
