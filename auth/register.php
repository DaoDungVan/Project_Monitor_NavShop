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
    $name            = trim($_POST['name'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '' || $confirmPassword === '') {
        $error = 'Please fill all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Password confirmation does not match.';
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
    <title>Register - NavShop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-shell">
        <div class="auth-visual">
            <a href="../products/index.php" class="auth-brand">NavShop</a>
            <div class="auth-visual-copy">
                <p class="eyebrow">New account</p>
                <h1>Build your monitor shortlist.</h1>
                <p>Save your profile, checkout faster, and keep your orders in one place.</p>
            </div>
            <div class="auth-spec-row">
                <span>OLED</span>
                <span>2K</span>
                <span>240Hz</span>
            </div>
        </div>

        <div class="auth-panel">
            <div class="auth-panel-top">
                <a href="../products/index.php" class="auth-home-link">Back to shop</a>
            </div>

            <div class="auth-card">
                <div class="auth-logo"><span>Create account</span></div>
                <p class="auth-subtitle">Join NavShop for faster checkout.</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($success) ?>
                        <br><a href="login.php" class="auth-link">Go to login</a>
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
                        <div class="password-field">
                            <input type="password" name="password" id="register-password" class="form-control"
                                   placeholder="Min. 6 characters" required>
                            <button type="button" class="password-toggle" data-toggle-password="register-password">Show</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div class="password-field">
                            <input type="password" name="confirm_password" id="register-confirm-password" class="form-control"
                                   placeholder="Repeat password" required>
                            <button type="button" class="password-toggle" data-toggle-password="register-confirm-password">Show</button>
                        </div>
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
    </div>
</div>

<script src="../assets/js/main.js"></script>
</body>
</html>
