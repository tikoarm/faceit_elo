<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['optimize_subservers'])) 
{
    require_once __DIR__ . '/../../config/main.php';
    $mysqli = getDatabaseConnection();
    if (!$mysqli) {
        http_response_code(500);
        exit('Database connection failed.');
    }

    // Fetch all users with status = 1
    $stmt = $mysqli->prepare("SELECT id, status FROM users WHERE status = 1");
    $stmt->execute();
    $res = $stmt->get_result();

    $users = [];
    while ($row = $res->fetch_assoc()) {
        $users[] = $row;
    }

    // Fetch subservers and filter by alive status
    $query = "SELECT id, ip, port, api_key, location, current_user_load FROM subservers";
    $result = $mysqli->query($query);

    $alive = [];
    while ($row = $result->fetch_assoc()) {
        if (isSubserverStillAlive($row)) {
            $alive[] = [
                'id' => $row['id'],
                'current_user_load' => $row['current_user_load'],
            ];
        }
    }

    ////////////

    $max_users_pro_subserver = 5;
    $numUsers = count($users);
    $numServers = count($alive);
    $assignedPerServer = $numServers > 0 ? (int) ceil($numUsers / $numServers) : 0;
    $overloaded = $assignedPerServer > $max_users_pro_subserver;

    // Distribute users among subservers with recommended max
    // Prepare distribution of specific user IDs to each server
    $userIds = array_column($users, 'id');
    $chunks = array_chunk($userIds, $assignedPerServer);
    // If there are more servers than chunks, pad with empty arrays
    $serverCount = count($alive);
    if (count($chunks) < $serverCount) {
        for ($i = count($chunks); $i < $serverCount; $i++) {
            $chunks[] = [];
        }
    }

    // Update user records: assign each user to its subserver
    foreach ($alive as $index => $srv) {
        $serverId = $srv['id'];
        foreach ($chunks[$index] as $userId) {
            $stmt = $mysqli->prepare("UPDATE users SET subserver_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $serverId, $userId);
            $stmt->execute();
        }
    }

    $distribution = [];
    foreach ($alive as $index => $srv) {
        $entry = [
            'id' => $srv['id'],
            'current_user_load' => $srv['current_user_load'],
            'assigned_users' => count($chunks[$index]),
            'users' => $chunks[$index],
        ];
        if ($overloaded) {
            $entry['warning'] = 'Server overloaded';
        }
        $distribution[] = $entry;
    }

    echo '<h2>Subserver Assignment Results</h2><ul>';
    foreach ($distribution as $entry) {
        echo '<li>';
        echo 'Subserver ' . htmlspecialchars($entry['id']) . ': old load ' . htmlspecialchars($entry['current_user_load'])
             . ', assigned users ' . htmlspecialchars($entry['assigned_users']);
        if (!empty($entry['users'])) {
            echo ' (User IDs: ' . implode(', ', array_map('htmlspecialchars', $entry['users'])) . ')';
        }
        if (!empty($entry['warning'])) {
            echo ' <strong>[' . htmlspecialchars($entry['warning']) . ']</strong>';
        }
        echo '</li>';
    }
    echo '</ul>';
    exit;
}
