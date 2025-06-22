<?php session_start();
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

// AJAX matchinfo proxy
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_matchinfo'], $_POST['matchinfo_for'])) {
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
                    echo "[CURL] ✅ Матч завершён\n";
                    print_r ($response_data);
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
    <title>Subserver Logs Viewer</title>
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <h2>Subserver Logs Viewer</h2>
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
            if ($stmt->fetch()) {
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

                // Health Info Table (Horizontal)
                echo "<div>";
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
                        echo "<tr>";
                        foreach ($health_keys as $key) {
                            echo "<th style='border: 1px solid #ccc; padding: 4px 8px; background: #f0f0f0;'>" . htmlspecialchars($key) . "</th>";
                        }
                        echo "</tr><tr>";
                        foreach ($health_keys as $key) {
                            $val = isset($health_data[$key]) ? (is_array($health_data[$key]) ? json_encode($health_data[$key]) : $health_data[$key]) : '';
                            if ($key === 'timestamp') {
                                $val = convert_timestamp((string)$val);
                            }
                            echo "<td style='border: 1px solid #ccc; padding: 4px 8px;'>" . htmlspecialchars((string)$val) . "</td>";
                        }
                        echo "</tr></table>";
                    } else {
                        echo "<p>Invalid health check response.</p>";
                    }
                }
                echo "</div>";

                // Database Table (Horizontal)
                echo "<div>";
                echo "<h3 style='margin-bottom:4px;'>DataBase</h3>";
                echo "<table style='border-collapse: collapse; font-size: 12px; margin-bottom: 0;'>";
                echo "<tr>";
                echo "<th style='border: 1px solid #ccc; padding: 4px 8px; background: #f0f0f0;'>API Key</th>";
                echo "<th style='border: 1px solid #ccc; padding: 4px 8px; background: #f0f0f0;'>IP</th>";
                echo "<th style='border: 1px solid #ccc; padding: 4px 8px; background: #f0f0f0;'>Port</th>";
                echo "</tr><tr>";
                echo "<td style='border: 1px solid #ccc; padding: 4px 8px;'>" . htmlspecialchars(obfuscate_text($api_key)) . "</td>";
                echo "<td style='border: 1px solid #ccc; padding: 4px 8px;'>" . htmlspecialchars($ip) . "</td>";
                echo "<td style='border: 1px solid #ccc; padding: 4px 8px;'>" . htmlspecialchars($port) . "</td>";
                echo "</tr></table>";
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
                                //$gameid_display = "<a href='#' class='gameid-link' data-gameid='" . htmlspecialchars($gameid_text, ENT_QUOTES) . "'>" . htmlspecialchars($gameid_text) . "</a>";
                                $gameid_display = "<a href='#' class='gameid-link' data-gameid='" . htmlspecialchars($gameid_text, ENT_QUOTES) . "' data-userid='" . htmlspecialchars($user['faceit_id'], ENT_QUOTES) . "'>" . htmlspecialchars($gameid_text) . "</a>";
                            } else {
                                $gameid_display = "—";
                            }
                            // $gameid_php_link is now unused/removed
                            $last_gameid_text = $user['last_gameid'] ?? null;
                            $last_gameid_display = $last_gameid_text ? "<a href='#' onclick=\"showGameID('Last Game', '$last_gameid_text'); return false;\">" . htmlspecialchars($last_gameid_text) . "</a>" : "—";
                            echo "<tr>
                                        <td style='border: 1px solid #ccc; padding: 3px 5px;'>" . htmlspecialchars($user['nickname']) . "</td>
                                        <td style='border: 1px solid #ccc; padding: 3px 5px;'>" . htmlspecialchars((string)$user['elo']) . "</td>";
                            echo "<td style='border: 1px solid #ccc; padding: 3px 5px;'>$gameid_display</td>";
                            echo "<td style='border: 1px solid #ccc; padding: 3px 5px;'>$last_gameid_display</td>";
                            echo "<td style='border: 1px solid #ccc; padding: 3px 5px;'>" . htmlspecialchars((string)$user['delay']) . "</td>
                                      </tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>Invalid response from users API.</p>";
                    }
                }
                echo "</div>";
                // --- End flexbox ---
                echo "</div>";

                $log_type = $_GET['type'] ?? 'info';
                $amount = $_GET['amount'] ?? '25';
                $order = $_GET['order'] ?? 'desc';

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
                            <a href='?subid=$subid&type=info&amount=$amount'><button>Info</button></a>
                            <a href='?subid=$subid&type=warning&amount=$amount'><button>Warning</button></a>
                            <a href='?subid=$subid&type=error&amount=$amount'><button>Error</button></a>
                            <a href='?subid=$subid&type=systemlog&amount=$amount'><button>System Log</button></a>
                          </div>";

                echo "<div style='margin-bottom: 10px;'>
                            <a href='?subid=$subid&type=$log_type&amount=25'><button>Last 25 lines</button></a>
                            <a href='?subid=$subid&type=$log_type&amount=50'><button>Last 50 lines</button></a>
                            <a href='?subid=$subid&type=$log_type&amount=100'><button>Last 100 lines</button></a>
                            <a href='?subid=$subid&type=$log_type&amount=-1'><button>Show all</button></a>
                          </div>";

                echo "<div style='margin-bottom: 10px;'>
                            <a href='?subid=$subid&type=$log_type&amount=$amount&order=asc'><button>Newest first</button></a>
                            <a href='?subid=$subid&type=$log_type&amount=$amount&order=desc'><button>Oldest first</button></a>
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
            } else {
                echo "<p style='color:red;'><strong>❌ No subserver found with this ID.</strong></p>";
            }

            $stmt->close();
            $mysqli->close();
        }
    }
    ?>
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
</script>

</html>