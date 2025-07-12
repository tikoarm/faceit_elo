<?php

require_once __DIR__ . '/../config/main.php';

$conn = getDatabaseConnection();
$loginError = false;
$authenticated = false;

$password = $_POST['password'] ?? '';
$user = $_POST['user'] ?? '';

if ($conn && $password !== '' && $user !== '') {
    $stmt = $conn->prepare("SELECT password FROM cp_users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    
    if ($stmt->fetch() && password_verify($password, $hashedPassword)) {
        $_SESSION['authenticated'] = true;
    } else {
        $loginError = true;
    }

    $stmt->close();
    $conn->close();
}

$authenticated = $_SESSION['authenticated'] ?? false;
?>