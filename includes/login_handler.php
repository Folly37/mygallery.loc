<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';  
require_once __DIR__ . '/auth_functions.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = sanitizeInput($_POST['password'] ?? '');
    
    $user = authenticateUser($email, $password);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        
    header("Location: " . BASE_URL . "/?page=profile");
        exit;
    } else {
        $_SESSION['login_error'] = "Неверный email или пароль";
        header("Location: " . BASE_URL . "/?page=login");
        exit;
    }
}
?>