<?php
require_once __DIR__ . '/database.php'; 

function registerUser($username, $email, $password, $confirm_password) {
    $errors = [];
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "Все поля обязательны для заполнения";
    }
    
    if (strlen($username) < 3 || strlen($username) > 30) {
        $errors[] = "Имя пользователя должно быть от 3 до 30 символов";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некорректный email";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Пароль должен быть не менее 6 символов";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Пароли не совпадают";
    }
    
    if (empty($errors)) {
        try {
            $db = Database::getInstance();
            
            $stmt = $db->prepare("SELECT id FROM users WHERE username = :username OR email = :email LIMIT 1");
            $stmt->execute([
                'username' => $username,
                'email' => $email
            ]);
            
            if ($stmt->fetch()) {
                $errors[] = "Пользователь с таким именем или email уже существует";
                return $errors;
            }
            
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password
            ]);
            
            return true;
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $errors[] = "Ошибка регистрации. Пожалуйста, попробуйте позже.";
            return $errors;
        }
    }
    
    return $errors;
}

function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching user: " . $e->getMessage());
        return null;
    }
}
?>