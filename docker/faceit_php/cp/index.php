<?php
ob_start(); session_start();

require_once __DIR__ . '/handlers/init_session.php';
require_once __DIR__ . '/handlers/auth.php';

if(!$authenticated) {
    header("Location: /cp/login.php");
    exit;
}

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/handlers/active_subserver.php';

require_once __DIR__ . '/handlers/main/get_faceit_id.php';
require_once __DIR__ . '/handlers/main/create_account.php';
?>

<!DOCTYPE html>
<html lang="en">

<?php
require_once __DIR__ . '/partials/main/head.php';
require_once __DIR__ . '/partials/main/body/body.php';
require_once __DIR__ . '/partials/main/body/active_subserver.php';
require_once __DIR__ . '/partials/main/body/right_navi.php';
require_once __DIR__ . '/partials/main/body/user_list.php';
require_once __DIR__ . '/partials/main/body/health.php';
require_once __DIR__ . '/assets/javascript/main_js.php';
require_once __DIR__ . '/handlers/alerts.php';
?>

</html>