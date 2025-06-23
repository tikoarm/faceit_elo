<?php

if (isset($_POST['subid']) && isset($_POST['submit'])) {
    $subid = $_POST['subid'];
    $query = $_GET;
    $query['subid'] = $subid;
    $newQuery = http_build_query($query);
    header("Location: ?" . $newQuery);
    exit;
}
if (isset($_POST['set_type'])) {
    $_SESSION['log_type'] = $_POST['set_type'];
    header("Location: ?subid=" . ($_GET['subid'] ?? ''));
    exit;
}
$subid = $_GET['subid'] ?? '';
if ($subid && !ctype_digit($subid)) {
    echo '<p style="color:red;"><strong>❌ Subserver ID must contain digits only.</strong></p>';
    $subid = ''; // reset to prevent further processing
}
?>