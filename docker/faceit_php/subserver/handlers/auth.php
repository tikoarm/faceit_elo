<?php

$correctPassword = '123';
$password = $_POST['password'] ?? '';
if ($password === $correctPassword) {
    $_SESSION['authenticated'] = true;
} elseif ($password !== '') {
    $loginError = true;
}
$authenticated = $_SESSION['authenticated'] ?? false;
?>