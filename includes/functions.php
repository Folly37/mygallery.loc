<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/?page=login");
        exit;
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function displayError($message) {
    echo '<div class="alert alert-danger">' . $message . '</div>';
}

function displaySuccess($message) {
    echo '<div class="alert alert-success">' . $message . '</div>';
}

function authenticateUser($email, $password) {
    try {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("SELECT id, username, email, password FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            return [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ];
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/?page=login");
        exit;
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT id, username, email FROM users WHERE id = :id");
        $stmt->execute(['id' => $_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error getting user: " . $e->getMessage());
        return null;
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function displayMessages() {
    if (!empty($_SESSION['errors'])) {
        foreach ($_SESSION['errors'] as $error) {
            echo '<div class="alert alert-danger">' . sanitizeInput($error) . '</div>';
        }
        unset($_SESSION['errors']);
    }
    
    if (!empty($_SESSION['success'])) {
        echo '<div class="alert alert-success">' . sanitizeInput($_SESSION['success']) . '</div>';
        unset($_SESSION['success']);
    }
}
}
?>