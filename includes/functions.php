<?php
require_once __DIR__ . '/config.php';

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
}
?>