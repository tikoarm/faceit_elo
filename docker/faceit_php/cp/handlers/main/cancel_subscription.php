<?php
session_start();
require_once __DIR__ . '/../../config/main.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

$faceit_id = $_POST['faceit_id'] ?? null;
if (!$faceit_id) {
    http_response_code(400);
    die('Invalid input');
}

$mysqli = getDatabaseConnection();
if (!$mysqli) {
    http_response_code(500);
    die('Database connection error');
}

$sel = $mysqli->prepare("SELECT faceit_username FROM users WHERE faceit_id = ?");
$sel->bind_param("s", $faceit_id);
$sel->execute();
$sel->bind_result($faceit_username);
$sel->fetch();
$sel->close();
$stmt = $mysqli->prepare("UPDATE users SET status = 0 WHERE faceit_id = ?");
$stmt->bind_param("s", $faceit_id);
if (!$stmt->execute()) {
    http_response_code(500);
    die('Database update failed');
}
$stmt->close();
$mysqli->close();

$_SESSION['unsubscribed_successfully'] = true;

$username = "$faceit_username (ID: $faceit_id)";
$_SESSION['unsubscribed_user'] = $username;

header("Location: /cp/index.php");
exit;
?>