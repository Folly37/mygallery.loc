<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/auth_functions.php';
require_once __DIR__ . '/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $result = registerUser($username, $email, $password, $confirm_password);
    
    if ($result === true) {
        $user = authenticateUser($email, $password);
        if ($user) {
            session_start(); 
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            header("Location: " . BASE_URL . "/?page=profile");
            exit;
        }
    } else {
        session_start();
        $_SESSION['register_errors'] = $result;
        header("Location: " . BASE_URL . "/?page=register");
        exit;
    }
}

header("Location: " . BASE_URL . "/?page=register");
exit;
?>