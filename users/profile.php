<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ../auth/login.php');
    exit;
}

require_once '../config/db.php';
require_once '../includes/upload.php';

$userId  = $_SESSION['user']['id'];
$error   = '';
$success = '';

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: ../auth/logout.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name       = trim($_POST['name'] ?? '');
    $phone      = trim($_POST['phone'] ?? '');
    $address    = trim($_POST['address'] ?? '');
    $gender     = $_POST['gender'] ?? null;
    $avatarPath = $user['avatar'];

    if ($name === '') {
        $error = 'Name is required.';
    } else {
        if (!empty($_FILES['avatar']['name'])) {
            $newAvatarPath = save_uploaded_image('avatar', '../uploads/avatars', 'uploads/avatars', $error);
            if ($error === '' && $newAvatarPath !== null) {
                delete_uploaded_file($user['avatar'] ?? null);
                $avatarPath = $newAvatarPath;
            }
        }

        if ($error === '') {
            $stmt = $conn->prepare("UPDATE users SET name=?, phone=?, address=?, gender=?, avatar=? WHERE id=?");
            $stmt->execute([$name, $phone, $address, $gender, $avatarPath, $userId]);

            $_SESSION['user']['name']   = $name;
            $_SESSION['user']['avatar'] = $avatarPath;

            $success = 'Profile updated successfully.';

            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}

require_once '../includes/header_user.php';
?>

<h1 class="page-title">My Profile</h1>

<div class="profile-card">

    <?php if ($error):   ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <label>Full Name *</label>
            <input type="text" name="name" class="form-control"
                   value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Email (read-only)</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="form-control"
                   value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" class="form-control"
                   value="<?= htmlspecialchars($user['address'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Gender</label>
            <select name="gender" class="form-control">
                <option value="">-- Select --</option>
                <option value="male"   <?= ($user['gender'] ?? '') === 'male'   ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Female</option>
                <option value="other"  <?= ($user['gender'] ?? '') === 'other'  ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label>Avatar</label><br>
            <?php if (!empty($user['avatar'])): ?>
                <img src="../<?= htmlspecialchars($user['avatar']) ?>" class="avatar-current" alt="Avatar">
            <?php endif; ?>
            <input type="file" name="avatar" class="form-control" accept="image/*">
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-green">Update Profile</button>
            <a href="change_password.php" class="btn btn-orange">Change Password</a>
        </div>

    </form>
</div>

<?php require_once '../includes/footer.php'; ?>
