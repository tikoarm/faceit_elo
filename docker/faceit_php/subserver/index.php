<?php
ob_start(); session_start();

require_once __DIR__ . '/handlers/init_session.php';
?>

<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/handlers/auth.php';
require_once __DIR__ . '/handlers/request.php';

require_once __DIR__ . '/config/database.php';

require_once __DIR__ . '/handlers/reload_users.php';
require_once __DIR__ . '/handlers/get_faceit_id.php';
require_once __DIR__ . '/handlers/match_info.php';
?>

<!DOCTYPE html>
<html lang="en">

<?php
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/body/body.php';
require_once __DIR__ . '/assets/javascript.php';
require_once __DIR__ . '/handlers/alerts.php';
?>

</html>