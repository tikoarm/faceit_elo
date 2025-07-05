<?php

$correctPassword = getenv('SUBSERVERS_PASSWORD') ?: 'mD9!sL#8f@VqZ2^gR3w*Bt7$NyX&6UcK';
$password = $_POST['password'] ?? '';
if ($password === $correctPassword) {
    $_SESSION['authenticated'] = true;
} elseif ($password !== '') {
    $loginError = true;
}
$authenticated = $_SESSION['authenticated'] ?? false;
?>