<?php
require_once __DIR__ . '/../config/main.php';

if (isset($_POST['update_subserver'])) {
    unset($_SESSION['active_subserver']);
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

function getAvailableSubserver(): ?array {
    $conn = getDatabaseConnection();
    if (!$conn) {
        http_response_code(500);
        echo json_encode(["error" => "Database connection failed"]);
        return null;
    }

    $query = "SELECT id, ip, port, api_key, location FROM subservers";
    $result = $conn->query($query);

    if (!$result) {
        http_response_code(500);
        echo json_encode(["error" => "Database query failed"]);
        return null;
    }

    $servers = [];

    while ($row = $result->fetch_assoc()) {
        $url = "http://{$row['ip']}:{$row['port']}/check_access?api_key={$row['api_key']}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Быстрый переход к следующему
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $pingMs = round(curl_getinfo($ch, CURLINFO_TOTAL_TIME) * 1000);
        curl_close($ch);

        if ($http_code === 200 && $response !== false) {
            $data = json_decode($response, true);
            if (isset($data['access']) && $data['access'] === 'allowed') {
                $servers[] = [
                    'id' => $row['id'],
                    'ip' => $row['ip'],
                    'port' => $row['port'],
                    'api_key' => $row['api_key'],
                    'pingMs' => $pingMs,
                    'location' => $row['location'] ?? 'unknown',
                ];
            }
        }
    }

    if (!empty($servers)) {
        return $servers;
    } else {
        return [];
    }
}

function isSubserverStillAlive(array $server): bool {
    if (!isset($server['ip'], $server['port'], $server['api_key'])) {
        return false;
    }
    $url = "http://{$server['ip']}:{$server['port']}/check_access?api_key={$server['api_key']}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200 && $response !== false) {
        $data = json_decode($response, true);
        return isset($data['access']) && $data['access'] === 'allowed';
    }
    return false;
}

if(!isset($_SESSION['active_subserver'])) {
    $servers = getAvailableSubserver();
    $_SESSION['active_subserver'] = $servers[0]; // Сохраняем один, а не массив
}