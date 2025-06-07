<?php
function getCurrentUser() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        return null;
    }

   $pdo = new PDO('pgsql:host=localhost;dbname=gallery', 'postgres', '1234');
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}