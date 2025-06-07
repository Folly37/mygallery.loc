<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . BASE_URL . "/?page=profile");
    exit;
}

$user = getCurrentUser();
if (!$user) {
    header("Location: " . BASE_URL . "/?page=login");
    exit;
}

if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/avatars/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $user['id'] . '.' . $extension;
    $targetPath = $uploadDir . $filename;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
        $pdo = new PDO('pgsql:host=localhost;dbname=gallery', 'postgres', '1234');
        $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->execute([$filename, $user['id']]);

        $_SESSION['profile_success'] = "Avatar updated successfully!";
    } else {
        $_SESSION['profile_error'] = "Failed to upload avatar.";
    }
}

header("Location: " . BASE_URL . "/?page=profile");
exit;
?>