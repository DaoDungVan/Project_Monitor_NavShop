<?php
// ================= CHANGE PASSWORD =================
session_start();

// Chưa login thì đá về login
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Kết nối database
require_once '../config/db.php';

$userId = $_SESSION['user']['id'];
$error = '';
$success = '';

// Khi user submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
        $error = 'Please fill all fields';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New password and confirm password do not match';
    } else {

        // Lấy mật khẩu hiện tại trong DB
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check mật khẩu cũ
        if (!$user || !password_verify($currentPassword, $user['password'])) {
            $error = 'Current password is incorrect';
        } else {

            // Hash mật khẩu mới
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update mật khẩu
            $stmt = $conn->prepare("
                UPDATE users SET password = ?
                WHERE id = ?
            ");
            $stmt->execute([$hashedPassword, $userId]);

            $success = 'Password changed successfully';
        }
    }
}
?>

<?php require_once '../includes/header_user.php'; ?>

<h2>Change Password</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">

    <div class="mb-3">
        <label>Current Password</label>
        <input type="password" name="current_password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>New Password</label>
        <input type="password" name="new_password" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Confirm New Password</label>
        <input type="password" name="confirm_password" class="form-control" required>
    </div>

    <button class="btn btn-primary">Change Password</button>
    <a href="profile.php" class="btn btn-secondary">Back</a>

</form>

<?php require_once '../includes/footer.php'; ?>
