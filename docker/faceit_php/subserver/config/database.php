<?php
require_once __DIR__ . '/main.php';

// Блок выбора сабсервера и подключение к БД только если пароль корректен
$stmt = null;
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
    $mysqli = getDatabaseConnection();
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
        $mysqli = getDatabaseConnection();
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

function getDataBaseStmt(): ?mysqli_stmt {
    global $stmt;
    if ($stmt instanceof mysqli_stmt) {
        return $stmt;
    }
    return null;
}
function unlinkFaceitUser(string $faceit_id): bool {
    $mysqli = getDatabaseConnection();
    if (!$mysqli || $mysqli->connect_error) {
        echo "<p style='color:red;'><strong>Database connection error:</strong> " . htmlspecialchars($mysqli->connect_error) . "</p>";
        return false;
    }

    $query = "UPDATE users SET subserver_id = NULL WHERE faceit_id = ?";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        echo "<p>Prepare failed: " . htmlspecialchars($mysqli->error) . "</p>";
        $mysqli->close();
        return false;
    }

    $stmt->bind_param("s", $faceit_id);

    $success = $stmt->execute();
    if (!$success) {
        echo "<p>Execute failed: " . htmlspecialchars($stmt->error) . "</p>";
    }

    $stmt->close();
    $mysqli->close();

    if ($success) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Пользователь успешно отвязан',
                    showConfirmButton: false,
                    timer: 1500
                });
            });
        </script>";
    }
    return $success;
}

?>