<?php
ob_start(); session_start();

require_once __DIR__ . '/../../config/main.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method Not Allowed');
}

$faceit_id = $_POST['faceit_id'] ?? null;
$days = isset($_POST['days']) ? (int)$_POST['days'] : 0;

if (!$faceit_id || $days <= 0) {
    http_response_code(400);
    die('Invalid input');
}

$mysqli = getDatabaseConnection();
if (!$mysqli) {
    http_response_code(500);
    die('Database connection error');
}

$stmt = $mysqli->prepare("SELECT sub_start_day, sub_end_day, faceit_username FROM users WHERE faceit_id = ?");
$stmt->bind_param("s", $faceit_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    http_response_code(404);
    die('User not found');
}

$now = date('Y-m-d H:i:s');
$start_day = $user['sub_start_day'] ?? null;
$end_day = $user['sub_end_day'] ?? null;
$faceit_username = $user['faceit_username'] ?? "unknown";

if ($end_day) {
    $new_end = date('Y-m-d H:i:s', strtotime("$end_day +$days days"));
} else {
    $new_end = date('Y-m-d H:i:s', strtotime("+$days days"));
}

$query = "UPDATE users SET status = 1, sub_end_day = ?";
$types = "s";
$params = [$new_end];

if (!$start_day) {
    $query .= ", sub_start_day = ?";
    $types .= "s";
    $params[] = $now;
}

$query .= " WHERE faceit_id = ?";
$types .= "s";
$params[] = $faceit_id;

$stmt = $mysqli->prepare($query);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
} else {
    http_response_code(500);
    echo "Database update failed.";
}

$stmt->close();
$mysqli->close();

$_SESSION['extended_successfully'] = true;
$_SESSION['extended_user'] = $faceit_username;
$_SESSION['extended_days'] = $days;
$_SESSION['extended_enddate'] = $new_end;

header("Location: /cp/index.php");
exit;
?>