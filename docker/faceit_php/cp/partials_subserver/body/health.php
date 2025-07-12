<?php
require_once dirname(__DIR__, 2) . '/functions.php';

$health_ch = curl_init("http://$ip:$port/health");
curl_setopt($health_ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($health_ch, CURLOPT_HEADER, false);
curl_setopt($health_ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($health_ch, CURLOPT_TIMEOUT, 4);
$health_response = curl_exec($health_ch);
$health_http_code = curl_getinfo($health_ch, CURLINFO_HTTP_CODE);
$health_error_msg = curl_error($health_ch);
curl_close($health_ch);

// --- Inline tables in a flexbox container ---
echo "<div style='display: flex; flex-wrap: wrap; gap: 10px; align-items: flex-start; margin-bottom: 14px;'>";

// Health Info Table (3 columns per row, compact)
echo "<div style='max-width: 480px;'>";
echo "<h3 style='margin-bottom:4px;'>Health Info</h3>";
if ($health_response === false) {
    echo "<p>Health check failed: " . htmlspecialchars($health_error_msg) . "</p>";
} elseif ($health_http_code >= 400) {
    echo "<p>Health check returned HTTP $health_http_code</p>";
} else {
    $health_data = json_decode($health_response, true);
    if ($health_data) {
        $health_keys = ['api_key', 'status', 'timestamp', 'timezone', 'uptime', 'version'];
        echo "<table style='border-collapse: collapse; font-size: 12px; margin-bottom: 0;'>";
        for ($i = 0; $i < count($health_keys); $i += 3) {
            echo "<tr>";
            for ($j = 0; $j < 3; $j++) {
                $key_index = $i + $j;
                if ($key_index < count($health_keys)) {
                    $key = $health_keys[$key_index];
                    echo "<th style='background: #f0f0f0;'>" . htmlspecialchars($key) . "</th>";
                } else {
                    echo "<th></th>";
                }
            }
            echo "</tr><tr>";
            for ($j = 0; $j < 3; $j++) {
                $key_index = $i + $j;
                if ($key_index < count($health_keys)) {
                    $key = $health_keys[$key_index];
                    $val = isset($health_data[$key]) ? (is_array($health_data[$key]) ? json_encode($health_data[$key]) : $health_data[$key]) : '';
                    if ($key === 'timestamp') {
                        $val = convert_timestamp((string)$val);
                    }
                    echo "<td style='padding: 4px 6px;'>" . htmlspecialchars((string)$val) . "</td>";
                } else {
                    echo "<td></td>";
                }
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Invalid health check response.</p>";
    }
}
echo "</div>";
?>