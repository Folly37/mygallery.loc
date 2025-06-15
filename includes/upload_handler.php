<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/auth_functions.php';
require_once __DIR__ . '/image_functions.php';

header('Content-Type: application/json');

$user = getCurrentUser();
if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Необходима авторизация']);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $response = [];
        
        if (isset($_FILES['image'])) {
            $response = handleImageUpload($user['id']);
        } 
        elseif (isset($_POST['action']) && $_POST['action'] === 'update_image') {
            $response = handleImageUpdate($user['id']);
        }
        elseif (isset($_POST['action']) && $_POST['action'] === 'delete_image') {
            $response = handleImageDelete($user['id']);
        }
        
        echo json_encode($response);
        exit;
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Ошибка сервера: ' . $e->getMessage()
    ]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Неверный запрос']);