<?php
require_once __DIR__ . '/auth_functions.php'; 

function testPasswordValidation() {
    $result = registerUser('testuser', 'test@example.com', '12345', '12345');
    if (!is_array($result) || !in_array("Пароль должен быть не менее 6 символов", $result)) {
        echo "Тест 1 не пройден: не проверяется минимальная длина пароля\n";
    } else {
        echo "Тест 1 пройден\n";
    }

    $result = registerUser('testuser', 'test@example.com', 'password123', 'different123');
    if (!is_array($result) || !in_array("Пароли не совпадают", $result)) {
        echo "Тест 2 не пройден: не проверяется совпадение паролей\n";
    } else {
        echo "Тест 2 пройден\n";
    }
}

testPasswordValidation();
?>