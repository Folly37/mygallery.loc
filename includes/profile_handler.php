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

$avatarPath = $user['avatar']; 
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/avatars/';
        if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true); 
    } 
    $fileExt = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
    $fileName = 'avatar_' . $user['id'] . '_' . time() . '.' . $fileExt;
    $uploadPath = $uploadDir . $fileName;

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['avatar']['type'], $allowedTypes)) {
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
            if ($user['avatar'] && file_exists(__DIR__ . $user['avatar']) && !str_contains($user['avatar'], 'default_avatar')) {
                unlink(__DIR__ . $user['avatar']);
            }
            $avatarPath = '/uploads/avatars/' . $fileName; 
        } else {
            $_SESSION['profile_error'] = "Ошибка загрузки аватара!";
        }
    } else {
        $_SESSION['profile_error'] = "Допустимые форматы: JPEG, PNG, GIF!";
    }
}

$pdo = new PDO('pgsql:host=localhost;dbname=gallery', 'postgres', '1234');
$stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, avatar = ? WHERE id = ?");
$stmt->execute([
    trim($_POST['username']),
    trim($_POST['email']),
    $avatarPath,
    $user['id']
]);


header("Location: " . BASE_URL . "/?page=profile");
exit;
?>