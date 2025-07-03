<?php
$userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;

// Безопасная проверка
if ($userid <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Неверный ID"]);
    exit;
}

// Формируем путь к нужному JSON-файлу
$filename = __DIR__ . "/data/sse_data_{$userid}.json";

// Проверка существования
if (!file_exists($filename)) {
    http_response_code(404);
    echo json_encode(["error" => "Файл не найден"]);
    exit;
}

// Читаем JSON
$json = file_get_contents($filename);
$data = json_decode($json, true);

// Меняем "shown" на true
$data['shown'] = true;

// Сохраняем обратно
file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

// Ответ клиенту
echo json_encode(["success" => true]);
?>
