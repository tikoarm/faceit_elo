<?php
//curl -X POST -d "userid=2&elo=1999&elo_diff=+2&api_key=admin_sk-f32ae769a8b54c0c92e0e08c7d0bd17a" http://localhost:8895/send_update.php
/*
    C:\Users\kocha>curl -X POST -d "faceit_id=53a8d759-076b-4b4a-8101-7b12fa40032d&elo=1999&elo_diff=+2&api_key=admin_sk-f32ae769a8b54c0c92e0e08c7d0bd17a" http://localhost:8895/send_update.php
    Your request was sent: <br>UserID: 2<br>faceit_id: 53a8d759-076b-4b4a-8101-7b12fa40032d<br>ELO: 1999<br>ELO Difference: 2<br>Timestamp: 1751563351<br>
    C:\Users\kocha>
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Only POST allowed";
    exit();
}

$api_key = $_POST['api_key'] ?? null;
if (!$api_key) {
    http_response_code(400);
    echo "API Key is required";
    exit();
}


$api_admin_key = getenv('API_ADMIN_KEY') ?: '';
if ($api_admin_key !== $api_key) {
    http_response_code(400);
    echo "Invalid API Key";
    exit();
}

$faceit_id = isset($_POST['faceit_id']) ? $_POST['faceit_id'] : '';
$elo = isset($_POST['elo']) ? intval($_POST['elo']) : 0;
$elo_diff = isset($_POST['elo_diff']) ? intval($_POST['elo_diff']) : 0;

if ($faceit_id === 0 || $elo === 0) {
    http_response_code(400);
    echo "Missing parameters";
    exit();
}

try {
    $host = getenv('MYSQL_HOST') ?: 'localhost';
    $port = getenv('MYSQL_PORT') ?: 3306;
    $dbuser = getenv('MYSQL_USER') ?: 'root';
    $dbpass = getenv('MYSQL_PASSWORD') ?: '';
    $dbname = getenv('MYSQL_DATABASE') ?: '';

    $mysqli = new mysqli($host, $dbuser, $dbpass, $dbname, $port);
    if ($mysqli->connect_error) {
        http_response_code(500);
        die("Database connection error: " . $mysqli->connect_error);
    }

    $stmt = $mysqli->prepare("SELECT id FROM users WHERE faceit_id = ? LIMIT 1");
    if (!$stmt) {
        http_response_code(500);
        die("Prepare failed: " . $mysqli->error);
    }

    $stmt->bind_param("s", $faceit_id);
    $stmt->execute();
    $stmt->bind_result($userid);
    $fetch_result = $stmt->fetch();
    $stmt->close();

    if (!$fetch_result) {
        http_response_code(403);
        echo "Forbidden: faceit_id not found";
        exit();
    }
    $mysqli->close();
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database error: " . $e->getMessage();
    exit();
}

// Создаем папку, если не существует
$dataDir = __DIR__ . "/data";
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

$filePath = $dataDir . "/sse_data_$userid.json";
$timestamp = time();

$data = [
    "faceit_id" => $faceit_id,
    "userid" => $userid,
    "elo" => $elo,
    "elo_diff" => $elo_diff,
    "timestamp" => $timestamp,
    "shown" => false
];

file_put_contents($filePath, json_encode($data));


echo "Your request was sent: <br>";
echo "UserID: " . $userid . "<br>";
echo "faceit_id: " . $faceit_id . "<br>";
echo "ELO: " . $elo . "<br>";
echo "ELO Difference: " . $elo_diff . "<br>";
echo "Timestamp: " . $timestamp . "<br>";