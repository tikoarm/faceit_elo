<?php

$log_types = [
    'info' => 'Info',
    'warning' => 'Warning',
    'error' => 'Error',
    'systemlog' => 'System Log',
    'matchlogs' => 'Match Logs'
];

if (isset($_GET['type'])) $_SESSION['log_type'] = $_GET['type'];
$log_type = $_SESSION['log_type'] ?? 'info';
if (isset($_GET['amount'])) $_SESSION['log_amount'] = $_GET['amount'];
$amount = $_SESSION['log_amount'] ?? '25';
if (isset($_GET['order'])) $_SESSION['log_order'] = $_GET['order'];
$order = $_SESSION['log_order'] ?? 'desc';

$url = "http://$ip:$port/logs/view?api_key=$api_key&log_type=$log_type&amount=$amount";

// Initialize cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // max 2 sec to connect
curl_setopt($ch, CURLOPT_TIMEOUT, 4);        // max 4 sec for entire request

// Execute request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error_msg = curl_error($ch);
curl_close($ch);

// Filter buttons
echo "<div style='margin-bottom: 10px;'>";
foreach ($log_types as $key => $label) {
    echo "
        <form method='POST' action='?subid={$subid}' style='display:inline;'>
            <input type='hidden' name='set_type' value='{$key}'>
            <button type='submit'>{$label}</button>
        </form>
    ";
}
echo "</div>";


echo "
    <div style='margin-bottom: 10px;'>
        <form method='POST' action='?subid={$subid}' style='display:inline;'>
            <input type='hidden' name='set_amount' value='25'>
            <button type='submit'>Last 25 lines</button>
        </form>
        <form method='POST' action='?subid={$subid}' style='display:inline;'>
            <input type='hidden' name='set_amount' value='50'>
                <button type='submit'>Last 50 lines</button>
        </form>
        <form method='POST' action='?subid={$subid}' style='display:inline;'>
            <input type='hidden' name='set_amount' value='100'>
            <button type='submit'>Last 100 lines</button>
        </form>
        <form method='POST' action='?subid={$subid}' style='display:inline;'>
            <input type='hidden' name='set_amount' value='-1'>
            <button type='submit'>Show all</button>
        </form>
    </div>";

echo "
    <div style='margin-bottom: 10px;'>
        <form method='POST' action='?subid={$subid}' style='display:inline;'>
            <input type='hidden' name='set_order' value='asc'>
            <button type='submit'>Newest first</button>
        </form>
        <form method='POST' action='?subid={$subid}' style='display:inline;'>
            <input type='hidden' name='set_order' value='desc'>
                <button type='submit'>Oldest first</button>
        </form>
    </div>";

// Handle connection error
if ($response === false) {
    echo "<p>cURL error: " . htmlspecialchars($error_msg) . "</p>";
    return;
}

if ($http_code >= 400) {
    echo "<p>API returned HTTP status $http_code</p>";
    return;
}

$data = json_decode($response, true);
if (!$data) {
    echo "<p>Failed to decode response.</p>";
   return;
}

if (isset($data['error'])) {
    echo "<p>Error: " . htmlspecialchars($data['error']) . "</p>";
    return;
}

if (!isset($data['lines'])) {
    echo "<p>Failed to load logs.</p>";
    return;
}

// Logs output
echo "<h2>Logs (" . ($log_types[$log_type] ?? htmlspecialchars($log_type)) . ")</h2><pre>";
$lines = $data['lines'];
if ($order === 'asc') {
    $lines = array_reverse($lines);
}
foreach ($lines as $line) {
    echo htmlspecialchars($line) . "\n";
}
echo "</pre>";
?>