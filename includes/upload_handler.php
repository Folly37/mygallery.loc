<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/auth_functions.php';


$user = getCurrentUser();
if (!$user) {
    header("Location: " . BASE_URL . "/?page=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $errors = [];
    $file = $_FILES['image'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Ошибка загрузки файла (код: {$file['error']})";
    }
    
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        $errors[] = "Файл слишком большой (максимум 5MB)";
    }
    
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($file['tmp_name']);
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($mimeType, $allowedMimeTypes) || !in_array($extension, $allowedExtensions)) {
        $errors[] = "Допустимы только JPG, PNG и GIF изображения";
    }
    
    if (empty($errors)) {
        try {
            $filename = uniqid() . '.' . $extension;
            $uploadPath = __DIR__ . '/../uploads/images/' . $filename;
            
            if (!file_exists(dirname($uploadPath))) {
                mkdir(dirname($uploadPath), 0777, true);
            }
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $db = Database::getInstance();
                $stmt = $db->prepare("INSERT INTO images (user_id, filename, title, description) VALUES (:user_id, :filename, :title, :description)");
                $stmt->execute([
                    'user_id' => $_SESSION['user_id'],
                    'filename' => $filename,
                    'title' => $_POST['title'] ?? '',
                    'description' => $_POST['description'] ?? ''
                ]);
                
                $_SESSION['upload_success'] = "Изображение успешно загружено!";
                header("Location: " . BASE_URL . "/?page=gallery");
                exit;
            } else {
                $errors[] = "Ошибка при сохранении файла";
            }
        } catch (PDOException $e) {
            $errors[] = "Ошибка базы данных: " . $e->getMessage();
        }
    }
    
    $_SESSION['upload_errors'] = $errors;
    header("Location: " . BASE_URL . "/?page=upload");
    exit;
}