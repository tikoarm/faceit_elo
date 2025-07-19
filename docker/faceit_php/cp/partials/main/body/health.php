<?php
$url = 'http://faceit_app:5050/health';  // Internal Docker network call

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 3);

$response = curl_exec($ch);

if ($response === false) {
    echo "<div class='health-container' style='display: inline-block; vertical-align: top;'>Error contacting faceit_app: " . curl_error($ch) . "</div>";
} else {
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "<div class='health-container' style='display: inline-block; vertical-align: top;'>Invalid JSON response</div>";
    } else {
        echo "<div class='health-container' style='display: inline-block; vertical-align: top; margin-top: 0; margin-bottom: 0;'>";
        echo "<table class='health-table'>";
        foreach ($data as $key => $value) {
            echo "<tr><th>" . htmlspecialchars($key) . "</th><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        echo "</table>";
        echo "</div>";
    }
}

curl_close($ch);
?>