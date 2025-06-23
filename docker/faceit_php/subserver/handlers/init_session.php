<?php

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /subserver/index.php");
    exit;
}
if (isset($_POST['subid']) && isset($_POST['submit'])) {
    $subid = $_POST['subid'];
    $query = $_GET;
    $query['subid'] = $subid;
    $newQuery = http_build_query($query);
    header("Location: ?" . $newQuery);
    exit;
}
?>