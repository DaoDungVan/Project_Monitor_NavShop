<?php
session_start();
require_once '../config/db.php';

if (isset($_SESSION['user'])) {
    header('Location: ' . ($_SESSION['user']['role'] === 'admin'
        ? '../products/admin_index.php'
        : '../products/index.php'));
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Please fill all required fields.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'Email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$name, $email, $hashed]);
            $success = 'Registration successful. You can login now.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – NavShop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">

        <div class="auth-logo"><span>NavShop</span></div>
        <p class="auth-subtitle">Create a new account</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
                <br><a href="login.php" class="auth-link">Go to Login →</a>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                       placeholder="Your name" required autofocus>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="your@email.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control"
                       placeholder="Min. 6 characters" required>
            </div>

            <button type="submit" class="btn btn-green btn-block">Register</button>
        </form>
        <?php endif; ?>

        <div class="auth-footer">
            Already have an account?
            <a href="login.php" class="auth-link">Login</a>
        </div>

    </div>
</div>

</body>
</html>
