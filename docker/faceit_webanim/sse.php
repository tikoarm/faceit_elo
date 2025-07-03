<?php
session_write_close();
header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");

$userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;
if ($userid === 0) {
    exit();
}

$dataDir = __DIR__ . "/data";
$file = $dataDir . "/sse_data_$userid.json";
$lastModTime = 0;

while (true) {
    clearstatcache();
    if (file_exists($file)) {
        $modTime = filemtime($file);
        if ($modTime > $lastModTime) {
            $lastModTime = $modTime;
            
            $data = file_get_contents($file);
            if (trim($data) !== '') {
                $json = json_decode($data, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    echo "data: " . json_encode($json) . "\n\n";
                } else {
                    // Повреждённый JSON — отправляем пустой объект
                    echo "data: {}\n\n";
                }
            } else {
                echo "data: {}\n\n";
            }
            ob_flush();
            flush();
        }
    }
    sleep(1);
}
