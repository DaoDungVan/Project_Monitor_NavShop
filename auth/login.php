<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['user'])) {
    header('Location: ' . ($_SESSION['user']['role'] === 'admin'
        ? '../products/admin_index.php'
        : '../products/index.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter email and password.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id'     => $user['id'],
                'name'   => $user['name'],
                'role'   => $user['role'],
                'avatar' => $user['avatar'] ?? null,
            ];
            header('Location: ' . ($user['role'] === 'admin'
                ? '../products/admin_index.php'
                : '../products/index.php'));
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – NavShop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">

        <div class="auth-logo"><span>NavShop</span></div>
        <p class="auth-subtitle">Sign in to your account</p>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-warning"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="your@email.com" required autofocus>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-green btn-block">Login</button>
        </form>

        <div class="auth-footer">
            Don't have an account?
            <a href="register.php" class="auth-link">Register</a>
        </div>

    </div>
</div>

</body>
</html>
