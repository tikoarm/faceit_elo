<?php
ob_start(); session_start();

require_once __DIR__ . '/handlers/init_session.php';
?>

<?php
require_once __DIR__ . '/handlers/auth.php';
if(!$authenticated) {
    header("Location: /cp/login.php");
    exit;
}

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/handlers/request.php';

require_once __DIR__ . '/config/database.php';

require_once __DIR__ . '/handlers/reload_users.php';
require_once __DIR__ . '/handlers/match_info.php';
?>

<!DOCTYPE html>
<html lang="en">

<?php
require_once __DIR__ . '/partials/subserver/head.php';
require_once __DIR__ . '/partials/subserver/body/body.php';
require_once __DIR__ . '/assets/javascript/subserv_js.php';
require_once __DIR__ . '/handlers/alerts.php';
?>

</html>