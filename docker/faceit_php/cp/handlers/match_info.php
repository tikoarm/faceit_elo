<?php
require_once dirname(__DIR__, 1) . '/functions.php';

// AJAX matchinfo proxy
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_matchinfo'], $_POST['matchinfo_for'])) 
{
    $gameid = $_POST['matchinfo_for'];
    $userid = $_POST['userinfo_for'];

    // Проверка переменных
    if ($stmt->fetch())
    {
        $url = "http://$ip:$port/matchinfo?api_key=$api_key&match_id=" . urlencode($gameid) . "&user_id=" . urlencode($userid);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Новый вывод CURL-ответа (см. инструкцию)
        if ($response !== false) {
            $response_data = json_decode($response, true);

            if (isset($response_data['status'])) 
            {
                $status = $response_data['status'];

                if ($status === 'error') 
                {
                    echo "[CURL] ❌ Ошибка: " . ($response_data['message'] ?? 'Нет сообщения об ошибке') . "\n";
                } 
                elseif ($status === 'ONGOING') 
                {   
                    $output = "Внимание! 🔄 Матч в процессе! 🔄\n\n";
                    $output .= "👤 Player: <strong><a href='https://www.faceit.com/en/players/" . ($response_data['nickname'] ?? '-') . "' target='_blank' rel='noopener noreferrer'>" . ($response_data['nickname'] ?? '-') . "</a></strong>\n";
                    if($response_data['match_id']) {
                        $output .= "🆔 Match ID: <strong><a href='https://www.faceit.com/ru/cs2/room/" . ($response_data['match_id'] ?? '-') . "' target='_blank' rel='noopener noreferrer'>" .    ($response_data['match_id'] ?? '-') . "</a></strong>\n";
                    } else $output .= "🆔 Match ID: <strong>-</strong>\n";

                    $output .= "🌍 Location: <strong>" . ($response_data['location'] ?? '-') . "</strong>\n";
                    $output .= "🏆 Queue: <strong>" . ($response_data['competition_name'] ?? '-') . "</strong>\n";
                    $output .= "📍 Map: <strong>" . ($response_data['map'] ?? '-') . "</strong>\n";
                    $output .= "🔢 Score: <strong>" . ($response_data['ongoing_score'] ?? '-') . "</strong>\n";
                    echo $output;
                } 
                elseif ($status === 'FINISHED') 
                {
                    $output = "👤 Player: <strong><a href='https://www.faceit.com/en/players/" . ($response_data['nickname'] ?? '-') . "' target='_blank' rel='noopener noreferrer'>" . ($response_data['nickname'] ?? '-') . "</a></strong>\n";
                    if($response_data['match_id']) {
                        $output .= "🆔 Match ID: <strong><a href='https://www.faceit.com/ru/cs2/room/" . ($response_data['match_id'] ?? '-') . "' target='_blank' rel='noopener noreferrer'>" .    ($response_data['match_id'] ?? '-') . "</a></strong>\n";
                    } else $output .= "🆔 Match ID: <strong>-</strong>\n";
                    


                    $output .= "🌍 Location: <strong>" . ($response_data['location'] ?? '-') . "</strong>\n";
                    $output .= "🏆 Queue: <strong>" . ($response_data['competition_name'] ?? '-') . "</strong>\n";
                    $output .= "📍 Map: <strong>" . ($response_data['map'] ?? '-') . "</strong>\n";
                    $output .= "🏁 Started: <strong>" . (convert_unix_timestamp($response_data['started_at']) ?? '-') . "</strong>\n";
                    $output .= "🚩 Finished: <strong>" . (convert_unix_timestamp($response_data['finished_at']) ?? '-') . "</strong>\n";
                    $output .= "🔢 Overtime Score: <strong>" . ($response_data['overtime_score'] ?? '-') . "</strong>\n";
                    $output .= "🔢 Score: <strong>" . ($response_data['total_score'] ?? '-') . "</strong>\n";
                    $output .= "📣 Status: <strong>" . $status . "</strong>\n";
                    $output .= "🔗 URL: <strong><a href='" . ($response_data['faceit_url'] ?? '-') . "' target='_blank' rel='noopener noreferrer'>link</a></strong>\n\n";

                    if (!empty($response_data['player_stats']) && is_array($response_data['player_stats'])) {
                        $ps = $response_data['player_stats'];
                        $stats_table = "
<div style='background-color: rgba(20, 20, 20, 0.95); color: #f0f0f0; border-radius: 10px; padding: 20px;'>
<table style='width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 6px; background-color: rgba(20, 20, 20, 0.8); color: #f0f0f0; border-radius: 6px; overflow: hidden;'>
<tr><th style='background-color: rgba(40, 40, 40, 0.9); padding: 6px; text-align: left;'>Stat</th><th style='background-color: rgba(40, 40, 40, 0.9); padding: 6px; text-align: left;'>Value</th></tr>";

                        $stat_map = [
                            'kills' => 'Kills',
                            'assists' => 'Assists',
                            'deaths' => 'Deaths',
                            'kd_ratio' => 'K/D',
                            'kr_ratio' => 'K/R',
                            'hs_percentage' => 'HS%',
                            'mvps' => 'MVPs',
                            'damage' => 'Damage',
                            'utility_damage' => 'Utility Damage',
                            'first_kills' => 'First Kills',
                            'match_1v1_win_rate' => 'Match 1v1 Wins (rate)',
                            'match_entry_rate' => 'Match Entry Rate',
                            'enemies_flashed_per_round' => 'Enemies Flashed / Round',
                            '1v1wins' => '1v1 Wins',
                        ];

                        foreach ($stat_map as $key => $label) {
                            $value = isset($ps[$key]) ? $ps[$key] : '-';
                            $stats_table .= "<tr><td style='border-top: 1px solid #333; padding: 4px 8px;'>$label</td><td style='border-top: 1px solid #333; padding: 4px 8px;'>$value</td></tr>";
                        }

                        $stats_table .= "</table></div>";
                        $output .= "<strong>📊 Player Stats:</strong>" . $stats_table;
                    }

                    echo $output;
                } 
                else 
                {
                    echo "[CURL] ⚠️ Неизвестный статус: " . htmlspecialchars($status) . "\n";
                }
            } 
            else 
            {
                echo "[CURL] ❓ Ответ без статуса: " . htmlspecialchars($response) . "\n";
            }
        } 
        else 
        {
            echo "[CURL] ❌ Ошибка CURL:";
            if (!empty($response)) {
                echo " Ответ: " . htmlspecialchars($response) . "\n";
            }
            echo " (HTTP $http_code)\n";
        }
    }
    exit;
}
?>