<?php
// ================= PROFILE USER =================
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

// Lấy thông tin user hiện tại
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Nếu không có user (phòng trường hợp lỗi)
if (!$user) {
    header('Location: ../auth/logout.php');
    exit;
}

// Khi user submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Lấy dữ liệu từ form
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender  = $_POST['gender'] ?? null;

    // Giữ avatar cũ mặc định
    $avatarPath = $user['avatar'];

    // Validate
    if ($name === '') {
        $error = 'Name is required';
    } else {

        // ================= UPLOAD AVATAR =================
        if (!empty($_FILES['avatar']['name'])) {

            // Đổi tên ảnh để tránh trùng
            $avatarName = time() . '_' . $_FILES['avatar']['name'];
            $uploadDir = '../uploads/avatars/';
            $targetPath = $uploadDir . $avatarName;

            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {

                // Xoá avatar cũ nếu có
                if (!empty($user['avatar']) && file_exists('../' . $user['avatar'])) {
                    unlink('../' . $user['avatar']);
                }

                $avatarPath = 'uploads/avatars/' . $avatarName;
            }
        }

        // ================= UPDATE USER =================
        $stmt = $conn->prepare("
            UPDATE users SET
                name = ?,
                phone = ?,
                address = ?,
                gender = ?,
                avatar = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $name,
            $phone,
            $address,
            $gender,
            $avatarPath,
            $userId
        ]);

        // Cập nhật lại session name
        $_SESSION['user']['name'] = $name;

        $success = 'Profile updated successfully';

        // Reload lại user
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<?php require_once '../includes/header_user.php'; ?>

<h2>User Profile</h2>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">

    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control"
            value="<?= htmlspecialchars($user['name']) ?>" required>
    </div>

    <div class="mb-3">
        <label>Email (readonly)</label>
        <input type="email" class="form-control"
            value="<?= htmlspecialchars($user['email']) ?>" readonly>
    </div>

    <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control"
            value="<?= htmlspecialchars($user['phone']) ?>">
    </div>

    <div class="mb-3">
        <label>Address</label>
        <input type="text" name="address" class="form-control"
            value="<?= htmlspecialchars($user['address']) ?>">
    </div>

    <div class="mb-3">
        <label>Gender</label>
        <select name="gender" class="form-control">
            <option value="">-- Select --</option>
            <option value="male" <?= $user['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
            <option value="female" <?= $user['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
            <option value="other" <?= $user['gender'] == 'other' ? 'selected' : '' ?>>Other</option>
        </select>
    </div>

    <div class="mb-3">
        <label>Avatar</label><br>

        <?php if ($user['avatar']): ?>
            <img src="../<?= $user['avatar'] ?>" width="100" class="mb-2">
        <?php endif; ?>

        <input type="file" name="avatar" class="form-control">
    </div>

    <button class="btn btn-primary">Update Profile</button>
</form>
<a href="change_password.php" class="btn btn-warning mt-3">
    Change Password
</a>

<?php require_once '../includes/footer.php'; ?>