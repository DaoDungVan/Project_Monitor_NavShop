<?php
function save_uploaded_image(string $field, string $uploadDir, string $publicDir, string &$error): ?string
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        $error = 'Image upload failed.';
        return null;
    }

    if ($_FILES[$field]['size'] > 5 * 1024 * 1024) {
        $error = 'Image must be 5MB or smaller.';
        return null;
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    $mime = null;
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo !== false) {
            $mime = finfo_file($finfo, $_FILES[$field]['tmp_name']);
            finfo_close($finfo);
        }
    }

    if ($mime === null || !isset($allowedTypes[$mime])) {
        $error = 'Please upload a valid JPG, PNG, WEBP, or GIF image.';
        return null;
    }

    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        $error = 'Upload directory is not writable.';
        return null;
    }

    $imageName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $allowedTypes[$mime];
    $destination = rtrim($uploadDir, '/\\') . DIRECTORY_SEPARATOR . $imageName;

    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $destination)) {
        $error = 'Could not save uploaded image.';
        return null;
    }

    return trim($publicDir, '/\\') . '/' . $imageName;
}

function delete_uploaded_file(?string $path): void
{
    if (empty($path)) {
        return;
    }

    $normalized = str_replace('\\', '/', $path);
    if (!str_starts_with($normalized, 'uploads/')) {
        return;
    }

    $uploadsRoot = realpath(dirname(__DIR__) . '/uploads');
    $filePath = realpath(dirname(__DIR__) . '/' . $normalized);

    if ($uploadsRoot !== false && $filePath !== false && str_starts_with($filePath, $uploadsRoot) && is_file($filePath)) {
        unlink($filePath);
    }
}
?>
