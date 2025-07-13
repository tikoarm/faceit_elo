<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_account_db'], $_POST['faceit_username'])) 
{
    $fusername = $_POST['faceit_username'];
    $userid = getFaceitId($fusername);
    if($userid === 'unknown') {
        echo "❌ Failed to retrieve Faceit ID for the provided username.";
        exit;
    }

    require_once __DIR__ . '/../../config/main.php';
    $mysqli = getDatabaseConnection();
    if (!$mysqli) {
        http_response_code(500);
        exit('Database connection failed.');
    }

    $password_plain = bin2hex(random_bytes(10)); // 20 символов

    $stmt = $mysqli->prepare("INSERT INTO users (faceit_id, faceit_username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $userid, $fusername, $password_plain);
    try {
        $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        if (str_contains($e->getMessage(), 'Duplicate entry')) {
            echo "❌ A user with this Faceit ID already exists.";
        } else {
            echo "❌ Database error: " . $e->getMessage();
        }
        exit;
    }

    $result = "User '$fusername' with FaceitID '$userid' created successfully.";
    $result = $result . "\nPassword: $password_plain";
    echo $result;
    exit;
}
