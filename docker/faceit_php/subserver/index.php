<?php
ob_start(); session_start();

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: /subserver/index.php");
    exit;
}
if (isset($_POST['subid']) && isset($_POST['submit'])) {
    $subid = $_POST['subid'];
    $query = $_GET;
    $query['subid'] = $subid;
    $newQuery = http_build_query($query);
    header("Location: ?" . $newQuery);
    exit;
}
function obfuscate_text(string $text): string
{
    $len = strlen($text);

    if ($len > 6) {
        return substr($text, 0, 3) . '...' . substr($text, -3);
    } elseif ($len > 2) {
        return substr($text, 0, 2) . str_repeat('*', $len - 2);
    } else {
        return $text;
    }
}
function convert_timestamp(string $timestamp): string
{
    try {
        $dt = new DateTime($timestamp);
        return $dt->format('d.m.Y H:i:s');
    } catch (Exception $e) {
        return $timestamp;
    }
}
?>

<?php
$correctPassword = '123';
$password = $_POST['password'] ?? '';
if ($password === $correctPassword) {
    $_SESSION['authenticated'] = true;
} elseif ($password !== '') {
    $loginError = true;
}
$authenticated = $_SESSION['authenticated'] ?? false;

if (isset($_POST['subid']) && isset($_POST['submit'])) {
    $subid = $_POST['subid'];
    $query = $_GET;
    $query['subid'] = $subid;
    $newQuery = http_build_query($query);
    header("Location: ?" . $newQuery);
    exit;
}
if (isset($_POST['set_type'])) {
    $_SESSION['log_type'] = $_POST['set_type'];
    header("Location: ?subid=" . ($_GET['subid'] ?? ''));
    exit;
}
$subid = $_GET['subid'] ?? '';
if ($subid && !ctype_digit($subid)) {
    echo '<p style="color:red;"><strong>❌ Subserver ID must contain digits only.</strong></p>';
    $subid = ''; // reset to prevent further processing
}

// Блок выбора сабсервера и подключение к БД только если пароль корректен
$subserverOptions = '';
if ($authenticated) {
    // Load .env manually (копия логики ниже, но только для подключения к БД)
    $envPath = __DIR__ . '/../.env';
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            list($name, $value) = explode('=', $line, 2);
            $_ENV[$name] = $value;
        }
    }
    $host = $_ENV['MYSQL_HOST'] ?? 'localhost';
    $port = $_ENV['MYSQL_PORT'] ?? '3306';
    $dbname = $_ENV['MYSQL_DATABASE'] ?? '';
    $username = $_ENV['MYSQL_USER'] ?? 'root';
    $password_db = $_ENV['MYSQL_PASSWORD'] ?? '';
    $mysqli = new mysqli($host, $username, $password_db, $dbname, (int)$port);
    if (!$mysqli->connect_error) {
        $result = $mysqli->query("SELECT id, ip, location FROM subservers");
        while ($row = $result->fetch_assoc()) {
            $selected = ($row['id'] == $subid) ? 'selected' : '';
            $label = "ID {$row['id']} | {$row['ip']} | {$row['location']}";
            $subserverOptions .= "<option value=\"{$row['id']}\" $selected>" . htmlspecialchars($label) . "</option>";
        }
        $mysqli->close();
    }
}

if($authenticated) 
{
    if($subid) 
    {
        $mysqli = new mysqli($host, $username, $password_db, $dbname, (int)$port);
        if ($mysqli->connect_error) 
        {
            echo "<p style='color:red;'><strong>Database connection error:</strong> " . htmlspecialchars($mysqli->connect_error) . "</p>";
        } 
        else 
        {
            $stmt = $mysqli->prepare("SELECT api_key, ip, port FROM subservers WHERE id = ?");
            $stmt->bind_param("i", $subid);
            $stmt->execute();
            $stmt->bind_result($api_key, $ip, $port);
        }
    }
}

// Handle Update Users form submission
if (isset($_POST['reload_users'])) 
{
    if ($stmt->fetch())
    {
        $reload_url = "http://{$ip}:{$port}/users/reload?api_key=" . urlencode($api_key);
        $ch_reload = curl_init($reload_url);
        curl_setopt($ch_reload, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_reload, CURLOPT_HEADER, false);
        curl_setopt($ch_reload, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch_reload, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch_reload, CURLOPT_POST, true);

        $reload_response = curl_exec($ch_reload);
        $reload_http_code = curl_getinfo($ch_reload, CURLINFO_HTTP_CODE);
        $reload_error_msg = curl_error($ch_reload);
        curl_close($ch_reload);

        // Redirect after reload
        $_SESSION['reloaded'] = 1;
        header("Location: ?subid={$subid}");
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nickname_lookup'], $_POST['nickname'])) 
{
    if ($stmt->fetch())
    {
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
            echo "Ошибка API: HTTP $http_code – " . htmlspecialchars($msg);
        } else {
            $json = json_decode($response, true);
            if (isset($json['player_id'])) {
                echo "✅ Faceit ID пользователя $nickname - '" . htmlspecialchars($json['player_id']) . "'";
            } else {
                echo "Ответ API: " . htmlspecialchars($response);
            }
        }
    }
    exit;
}

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
                    echo "[CURL] 🔄 Матч в процессе\n";
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
                    $output .= "🏁 Started: <strong>" . ($response_data['started_at'] ?? '-') . "</strong>\n";
                    $output .= "🚩 Finished: <strong>" . ($response_data['finished_at'] ?? '-') . "</strong>\n";
                    $output .= "🔢 Overtime Score: <strong>" . ($response_data['overtime_score'] ?? '-') . "</strong>\n";
                    $output .= "🔢 Score: <strong>" . ($response_data['total_score'] ?? '-') . "</strong>\n";
                    $output .= "📣 Status: <strong>" . $status . "</strong>\n";
                    $output .= "🔗 URL: <strong><a href='" . ($response_data['faceit_url'] ?? '-') . "' target='_blank' rel='noopener noreferrer'>link</a></strong>\n\n";

                    if (!empty($response_data['player_stats']) && is_array($response_data['player_stats'])) {
                        $ps = $response_data['player_stats'];
                        $stats_table = "
<table style='border-collapse: collapse; font-size: 12px; margin-top: 6px;'>
<tr><th style='border: 1px solid #ccc; padding: 4px 8px;'>Stat</th><th style='border: 1px solid #ccc; padding: 4px 8px;'>Value</th></tr>";

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
                            $stats_table .= "<tr><td style='border: 1px solid #ccc; padding: 4px 8px;'>$label</td><td style='border: 1px solid #ccc; padding: 4px 8px;'>$value</td></tr>";
                        }

                        $stats_table .= "</table>";
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Subserver Viewer</title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            line-height: 1.4;
        }

        pre {
            font-family: monospace;
            font-size: 12px;
            white-space: pre-wrap;
            line-height: 0.8;
        }

        h2 {
            font-size: 16px;
            margin-top: 10px;
            margin-bottom: 6px;
        }

        p,
        form {
            margin-bottom: 4px;
        }

        button {
            font-size: 11px;
        }

        th, td {
            text-align: center;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <h2>Subserver Viewer</h2>
    <?php if (!empty($loginError)): ?>
        <p style="color:red;"><strong>❌ Incorrect password.</strong></p>
    <?php endif; ?>
    <form method="POST" action="" id="mainForm">
        <?php if (!$authenticated): ?>
            <label for="password">Password: </label>
            <input type="password" id="password" name="password" value="">
        <?php endif; ?>

        <?php if ($authenticated): ?>
            <label for="subid">Subserver ID: </label>
            <select id="subid" name="subid">
                <?= $subserverOptions ?>
            </select>
        <?php endif; ?>

        <button type="submit" name="submit">Open</button>
        <?php if ($authenticated): ?>
            <a href="?logout=1"><button type="button">Logout</button></a>
        <?php endif; ?>
    </form>


    <?php
    if ($authenticated) {
        if ($subid) {
            if ($stmt->fetch()) 
            {
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
                                    echo "<th style='border: 1px solid #ccc; padding: 4px 6px; background: #f0f0f0;'>" . htmlspecialchars($key) . "</th>";
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
                                    echo "<td style='border: 1px solid #ccc; padding: 4px 6px;'>" . htmlspecialchars((string)$val) . "</td>";
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

                // Database Table (Vertical, per new instructions)
                echo "<div>";
                echo "<h3 style='margin-bottom:4px;'>DataBase</h3>";
                echo "<table style='border-collapse: collapse; font-size: 12px; margin-bottom: 0;'>";
                echo "<tr>
                        <th style='border: 1px solid #ccc; padding: 4px 8px; background: #f0f0f0;' colspan='2'>API Key</th>
                      </tr>
                      <tr>
                        <td colspan='2' style='border: 1px solid #ccc; padding: 4px 8px;'>" . htmlspecialchars(obfuscate_text($api_key)) . "</td>
                      </tr>";
                echo "<tr>
                        <th style='border: 1px solid #ccc; padding: 4px 8px; background: #f0f0f0;'>IP</th>
                        <th style='border: 1px solid #ccc; padding: 4px 8px; background: #f0f0f0;'>Port</th>
                      </tr>
                      <tr>
                        <td style='border: 1px solid #ccc; padding: 4px 8px;'>" . htmlspecialchars($ip) . "</td>
                        <td style='border: 1px solid #ccc; padding: 4px 8px;'>" . htmlspecialchars($port) . "</td>
                      </tr>";
                echo "</table>";
                echo "</div>";

                // Tracked Users Table (keep as rows, but reduce padding)
                echo "<div style='min-width: 320px;'>";
                echo "<h3 style='margin-bottom:4px;'>Tracked Users</h3>";
                $users_url = "http://$ip:$port/users/trackinfo?api_key=$api_key";
                $users_ch = curl_init($users_url);
                curl_setopt($users_ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($users_ch, CURLOPT_HEADER, false);
                curl_setopt($users_ch, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($users_ch, CURLOPT_TIMEOUT, 4);
                $users_response = curl_exec($users_ch);
                $users_http_code = curl_getinfo($users_ch, CURLINFO_HTTP_CODE);
                $users_error_msg = curl_error($users_ch);
                curl_close($users_ch);

                if ($users_response === false) {
                    echo "<p>Users API request failed: " . htmlspecialchars($users_error_msg) . "</p>";
                } elseif ($users_http_code >= 400) {
                    echo "<p>Users API returned HTTP $users_http_code</p>";
                } else {
                    $users_data = json_decode($users_response, true);
                if (is_array($users_data)) {
                    echo "<table style='border-collapse: collapse; font-size: 12px; margin-bottom: 0;'>";
                    echo "<tr>
                                <th style='border: 1px solid #ccc; padding: 3px 5px; background: #f0f0f0;'>Nickname</th>
                                <th style='border: 1px solid #ccc; padding: 3px 5px; background: #f0f0f0;'>ELO</th>
                                <th style='border: 1px solid #ccc; padding: 3px 5px; background: #f0f0f0;'>Game ID</th>
                                <th style='border: 1px solid #ccc; padding: 3px 5px; background: #f0f0f0;'>Last Game ID</th>
                                <th style='border: 1px solid #ccc; padding: 3px 5px; background: #f0f0f0;'>Delay</th>
                              </tr>";
                    foreach ($users_data['tracked_users'] ?? [] as $user) {
                        $gameid_text = $user['gameid'] ?? null;
                        if ($gameid_text) {
                            $gameid_display = "<a href='#' class='gameid-link' data-gameid='" . htmlspecialchars($gameid_text, ENT_QUOTES) . "' data-userid='" . htmlspecialchars($user['faceit_id'], ENT_QUOTES) . "'>" . htmlspecialchars($gameid_text) . "</a>";
                        } else {
                            $gameid_display = "—";
                        }

                        // $gameid_php_link is now unused/removed
                        $last_gameid_text = $user['last_gameid'] ?? null;
                        $last_gameid_display = "<a href='#' class='gameid-link' data-gameid='" . htmlspecialchars($last_gameid_text, ENT_QUOTES) . "' data-userid='" . htmlspecialchars($user['faceit_id'], ENT_QUOTES) . "'>" . htmlspecialchars($last_gameid_text) . "</a>";

                        echo "<tr>
                                    <td style='border: 1px solid #ccc; padding: 3px 5px;'>" . htmlspecialchars($user['nickname']) . "</td>
                                    <td style='border: 1px solid #ccc; padding: 3px 5px;'>" . htmlspecialchars((string)$user['elo']) . "</td>";
                        echo "<td style='border: 1px solid #ccc; padding: 3px 5px;'>$gameid_display</td>";
                        echo "<td style='border: 1px solid #ccc; padding: 3px 5px;'>$last_gameid_display</td>";
                        echo "<td style='border: 1px solid #ccc; padding: 3px 5px;'>" . htmlspecialchars((string)$user['delay']) . "</td>
                                  </tr>";
                    }
                    echo "</table>";
                    // Insert the Find/Update buttons side-by-side, same size, aligned
                    echo "<div style='display: flex; gap: 8px; margin-top: 6px; align-items: center;'>";
                    echo "<div style='flex: 0 0 auto;'>
                            <button id=\"findUserBtn\" type=\"button\" style=\"padding: 6px 14px; width: 130px;\">Find User</button>
                          </div>";
                    echo "<div style='flex: 0 0 auto;'>
                            <form method='POST' action='?subid={$subid}' style='margin: 0;'>
                              <input type='hidden' name='reloaded' value='1'>
                              <button type='submit' name='reload_users' style='padding: 6px 14px; width: 130px;'>Update Users</button>
                            </form>
                          </div>";
                    echo "</div>";
                } else {
                    echo "<p>Invalid response from users API.</p>";
                }
                }
                echo "</div>";
                // --- End flexbox ---
                echo "</div>";

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

                // Handle connection error
                if ($response === false) {
                    echo "<p>cURL error: " . htmlspecialchars($error_msg) . "</p>";
                    exit;
                }

                if ($http_code >= 400) {
                    echo "<p>API returned HTTP status $http_code</p>";
                }

                $data = json_decode($response, true);
                if (!$data) {
                    echo "<p>Failed to decode response.</p>";
                    exit;
                }

                if (isset($data['error'])) {
                    echo "<p>Error: " . htmlspecialchars($data['error']) . "</p>";
                    exit;
                }

                if (!isset($data['lines'])) {
                    echo "<p>Failed to load logs.</p>";
                    exit;
                }

                // Filter buttons
                echo "<div style='margin-bottom: 10px;'>
                        <form method='POST' action='?subid={$subid}' style='display:inline;'>
                            <input type='hidden' name='set_type' value='info'>
                            <button type='submit'>Info</button>
                        </form>
                        <form method='POST' action='?subid={$subid}' style='display:inline;'>
                            <input type='hidden' name='set_type' value='warning'>
                            <button type='submit'>Warning</button>
                        </form>
                        <form method='POST' action='?subid={$subid}' style='display:inline;'>
                            <input type='hidden' name='set_type' value='error'>
                            <button type='submit'>Error</button>
                        </form>
                        <form method='POST' action='?subid={$subid}' style='display:inline;'>
                            <input type='hidden' name='set_type' value='systemlog'>
                            <button type='submit'>System Log</button>
                        </form>
                    </div>";

                echo "<div style='margin-bottom: 10px;'>
                            <a href='?subid=$subid&set_amount=25'><button>Last 25 lines</button></a>
                            <a href='?subid=$subid&set_amount=50'><button>Last 50 lines</button></a>
                            <a href='?subid=$subid&set_amount=100'><button>Last 100 lines</button></a>
                            <a href='?subid=$subid&set_amount=-1'><button>Show all</button></a>
                          </div>";

                echo "<div style='margin-bottom: 10px;'>
                            <a href='?subid=$subid&set_order=asc'><button>Newest first</button></a>
                            <a href='?subid=$subid&set_order=desc'><button>Oldest first</button></a>
                          </div>";

                // Logs output
                echo "<h2>Logs (" . htmlspecialchars($log_type) . ")</h2><pre>";
                $lines = $data['lines'];
                if ($order === 'asc') {
                    $lines = array_reverse($lines);
                }
                foreach ($lines as $line) {
                    echo htmlspecialchars($line) . "\n";
                }
                echo "</pre>";
            } 
            else 
            {
                echo "<p style='color:red;'><strong>❌ No subserver found with this ID.</strong></p>";
            }

            $stmt->close();
            $mysqli->close();
        }
    }
    ?>
</div>
</body>
<script>
    document.querySelectorAll('.gameid-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var gameid = this.dataset.gameid;
            var userid = this.dataset.userid;
            var form = new FormData();
            form.append('ajax_matchinfo', '1');
            form.append('matchinfo_for', gameid);
            form.append('userinfo_for', userid || '0');
            fetch(window.location.pathname + '?subid=<?php echo $subid; ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: form
                })
                .then(function(res) {
                    return res.text();
                })
                .then(function(text) {
                    Swal.fire({
                        title: 'Match Info',
                        html: '<pre style="text-align:left; font-family:monospace; font-size:12px; white-space:pre-wrap; line-height: 1.4;">' + text + '</pre>',
                        width: 700
                    });
                })
                .catch(function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err
                    });
                });
        });
    });

    document.getElementById('findUserBtn')?.addEventListener('click', function () {
        Swal.fire({
            title: 'Enter nickname',
            input: 'text',
            inputLabel: 'FACEIT nickname',
            showCancelButton: true,
            confirmButtonText: 'Lookup',
            preConfirm: (value) => {
                if (!value) {
                    Swal.showValidationMessage('Please enter a nickname');
                }
                return value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = new FormData();
                form.append('nickname_lookup', '1');
                form.append('nickname', result.value);

                fetch(window.location.pathname + '?subid=<?php echo $subid; ?>', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: form
                })
                .then(res => res.text())
                .then(text => {
                    Swal.fire({
                        title: 'Faceit ID',
                        text: text
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ошибка запроса',
                        text: err
                    });
                });
            }
        });
    });
</script>

<?php
if (isset($_SESSION['reloaded']) && $_SESSION['reloaded'] == 1) {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Users successfully reloaded.'
        });
    </script>";
    if($_SESSION['reloaded'] == 1) $_SESSION['reloaded']++;
    else unset($_SESSION['reloaded']);
}
?>

</html>