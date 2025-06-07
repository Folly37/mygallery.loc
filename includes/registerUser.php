<?
$stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
$stmt->execute([
    'username' => $username,
    'email' => $email,
    'password' => $hashed_password
]);