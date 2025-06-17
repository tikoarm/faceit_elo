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

        p, form {
            margin-bottom: 4px;
        }

        button {
            font-size: 11px;
        }
    </style>
</head>
<body>
    <?php
    $correctPassword = 'V3ry$trongP@ssw0rd!';
    // Get values from URL if they exist
    $password = isset($_GET['password']) ? htmlspecialchars($_GET['password']) : '';
    $subid = isset($_GET['subid']) ? $_GET['subid'] : '';

    if ($subid && !ctype_digit($subid)) {
        echo '<p style="color:red;"><strong>❌ Subserver ID must contain digits only.</strong></p>';
        $subid = ''; // reset to prevent further processing
    }

    // Подключение к БД и извлечение субсерверов для выпадающего списка
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
    $subserverOptions = '';
    if (!$mysqli->connect_error) {
        $result = $mysqli->query("SELECT id, ip, location FROM subservers");
        while ($row = $result->fetch_assoc()) {
            $selected = ($row['id'] == $subid) ? 'selected' : '';
            $label = "ID {$row['id']} | {$row['ip']} | {$row['location']}";
            $subserverOptions .= "<option value=\"{$row['id']}\" $selected>" . htmlspecialchars($label) . "</option>";
        }
        $mysqli->close();
    }
    ?>

    <h2>Subserver Logs Viewer</h2>
    <form method="GET">
        <label for="password">Password: </label>
        <input type="<?= ($password === $correctPassword) ? 'password' : 'text' ?>" id="password" name="password" value="<?= $password ?>">

        <label for="subid">Subserver ID: </label>
        <select id="subid" name="subid">
            <?= $subserverOptions ?>
        </select>

        <button type="submit">Open</button>
    </form>

    <?php
    if ($password && $subid) {
        if ($password !== $correctPassword) {
            echo '<p style="color:red;"><strong>❌ Incorrect password.</strong></p>';
        } else {
            $mysqli = new mysqli($host, $username, $password_db, $dbname, (int)$port);

            if ($mysqli->connect_error) {
                echo "<p style='color:red;'><strong>Database connection error:</strong> " . htmlspecialchars($mysqli->connect_error) . "</p>";
            } else {
                $stmt = $mysqli->prepare("SELECT api_key, ip, port FROM subservers WHERE id = ?");
                $stmt->bind_param("i", $subid);
                $stmt->execute();
                $stmt->bind_result($api_key, $ip, $port);

                if ($stmt->fetch()) 
                {
                    echo "<p>";
                    echo "<strong>API Key:</strong> " . htmlspecialchars($api_key) . "<br>";
                    echo "<strong>IP:</strong> " . htmlspecialchars($ip) . "<br>";
                    echo "<strong>Port:</strong> " . htmlspecialchars($port) . "<br>";
                    echo "</p>";
                    
                    $log_type = $_GET['type'] ?? 'info';
                    $amount = $_GET['amount'] ?? '25';
                    $order = $_GET['order'] ?? 'desc';

                    $url = "http://$ip:$port/logs/view?admin_key=$api_key&log_type=$log_type&amount=$amount";

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
                            <a href='?password=$password&subid=$subid&type=info&amount=$amount'><button>Info</button></a>
                            <a href='?password=$password&subid=$subid&type=warning&amount=$amount'><button>Warning</button></a>
                            <a href='?password=$password&subid=$subid&type=error&amount=$amount'><button>Error</button></a>
                            <a href='?password=$password&subid=$subid&type=systemlog&amount=$amount'><button>System Log</button></a>
                          </div>";

                    echo "<div style='margin-bottom: 10px;'>
                            <a href='?password=$password&subid=$subid&type=$log_type&amount=25'><button>Last 25 lines</button></a>
                            <a href='?password=$password&subid=$subid&type=$log_type&amount=50'><button>Last 50 lines</button></a>
                            <a href='?password=$password&subid=$subid&type=$log_type&amount=100'><button>Last 100 lines</button></a>
                            <a href='?password=$password&subid=$subid&type=$log_type&amount=-1'><button>Show all</button></a>
                          </div>";

                    echo "<div style='margin-bottom: 10px;'>
                            <a href='?password=$password&subid=$subid&type=$log_type&amount=$amount&order=asc'><button>Newest first</button></a>
                            <a href='?password=$password&subid=$subid&type=$log_type&amount=$amount&order=desc'><button>Oldest first</button></a>
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
    }
    ?>
</body>
</html>