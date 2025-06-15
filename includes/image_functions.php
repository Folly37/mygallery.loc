<?php
function handleImageUpload($user_id) {
    $errors = [];
    $file = $_FILES['image'];
    
    // Валидация файла
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['status' => 'error', 'message' => "Ошибка загрузки файла (код: {$file['error']})"];
    }
    
    $maxSize = 5 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        return ['status' => 'error', 'message' => "Файл слишком большой (максимум 5MB)"];
    }
    
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($file['tmp_name']);
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($mimeType, $allowedMimeTypes) || !in_array($extension, $allowedExtensions)) {
        return ['status' => 'error', 'message' => "Допустимы только JPG, PNG и GIF изображения"];
    }
    
    try {
        $filename = uniqid() . '.' . $extension;
        $uploadPath = __DIR__ . '/../uploads/images/' . $filename;
        
        if (!file_exists(dirname($uploadPath))) {
            mkdir(dirname($uploadPath), 0777, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO images (user_id, filename, title, description) 
                                VALUES (:user_id, :filename, :title, :description)");
            $stmt->execute([
                'user_id' => $user_id,
                'filename' => $filename,
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? ''
            ]);
            
            return [
                'status' => 'success',
                'message' => 'Изображение успешно загружено',
                'filename' => $filename,
                'image_id' => $db->lastInsertId()
            ];
        }
    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => "Ошибка базы данных: " . $e->getMessage()];
    }
    
    return ['status' => 'error', 'message' => "Ошибка при сохранении файла"];
}

function handleImageUpdate($user_id) {
    if (empty($_POST['image_id']) || empty($_POST['title'])) {
        return ['status' => 'error', 'message' => 'Не заполнены обязательные поля'];
    }
    
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE images SET title = :title, description = :description 
                            WHERE id = :image_id AND user_id = :user_id");
        $success = $stmt->execute([
            'title' => $_POST['title'],
            'description' => $_POST['description'] ?? '',
            'image_id' => $_POST['image_id'],
            'user_id' => $user_id
        ]);
        
        if ($success) {
            return ['status' => 'success', 'message' => 'Данные обновлены'];
        }
        return ['status' => 'error', 'message' => 'Изображение не найдено или нет прав'];
    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => "Ошибка базы данных: " . $e->getMessage()];
    }
}

function handleImageDelete($user_id) {
    if (empty($_POST['image_id'])) {
        return ['status' => 'error', 'message' => 'Не указано изображение для удаления'];
    }
    
    try {
        $db = Database::getInstance();
        
        // Получаем информацию об изображении
        $stmt = $db->prepare("SELECT filename FROM images WHERE id = :image_id AND user_id = :user_id");
        $stmt->execute(['image_id' => $_POST['image_id'], 'user_id' => $user_id]);
        $image = $stmt->fetch();
        
        if ($image) {
            // Удаляем из БД
            $stmt = $db->prepare("DELETE FROM images WHERE id = :image_id AND user_id = :user_id");
            $stmt->execute(['image_id' => $_POST['image_id'], 'user_id' => $user_id]);
            
            // Удаляем файл
            $filePath = __DIR__ . '/../uploads/images/' . $image['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            return ['status' => 'success', 'message' => 'Изображение удалено'];
        }
        
        return ['status' => 'error', 'message' => 'Изображение не найдено или нет прав'];
    } catch (PDOException $e) {
        return ['status' => 'error', 'message' => "Ошибка базы данных: " . $e->getMessage()];
    }
}

function getUserImages($user_id, $format = null) {
    $db = Database::getInstance();
    $sql = "SELECT * FROM images WHERE user_id = :user_id";
    $params = ['user_id' => $user_id];
    
    if ($format) {
        $sql .= " AND filename LIKE :format";
        $params['format'] = '%.' . $format;
    }
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}