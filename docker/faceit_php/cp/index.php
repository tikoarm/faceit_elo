<?php
ob_start(); session_start();

require_once __DIR__ . '/handlers/init_session.php';
require_once __DIR__ . '/handlers/auth.php';

if(!$authenticated) {
    header("Location: /cp/login.php");
    exit;
}

require_once __DIR__ . '/functions.php';
?>

<!DOCTYPE html>
<html lang="en">

<?php
require_once __DIR__ . '/partials_main/head.php';
require_once __DIR__ . '/partials_main/body/body.php';
require_once __DIR__ . '/assets/javascript.php';
require_once __DIR__ . '/handlers/alerts.php';
?>

</html>