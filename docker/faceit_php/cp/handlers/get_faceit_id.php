<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nickname_lookup'], $_POST['nickname'])) {
    if(!isSubserverStillAlive($_SESSION['active_subserver'])) {
        echo "❌ No active subserver connections available";
        exit;
    }

    $ip = '148.251.162.18';
    $port = '5055';
    $api_key = '9e5eeeb557a12510616e4168d39c208c';
    $nickname = $_POST['nickname'];
    $flask_url = "http://$ip:$port/get_faceit_id?nickname=" . urlencode($nickname) . "&api_key=$api_key";

    $ch = curl_init($flask_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 4);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($response === false || $http_code >= 400) {
        $json = json_decode($response, true);
        $msg = $json['error'] ?? ($json ? json_encode($json) : ($error ?: 'Unknown error'));
        echo "API Error: HTTP $http_code – " . htmlspecialchars($msg);
    } else {
        $json = json_decode($response, true);
        if (isset($json['player_id'])) {
            echo "✅ $nickname's Faceit ID is '" . htmlspecialchars($json['player_id']) . "'";
        } else {
            echo "API Response: " . htmlspecialchars($response);
        }
    }
    exit;
}
