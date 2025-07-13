<?php

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
        echo "
            <tr>
                <th style='background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);'>Nickname</th>
                <th style='background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);'>ELO</th>
                <th style='background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);'>Game ID</th>
                <th style='background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);'>Last Game ID</th>
                <th style='background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);'>Delay</th>
                <th style='background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);'>Action</th>
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
                    <td>" . htmlspecialchars($user['nickname']) . "</td>
                    <td>" . htmlspecialchars((string)$user['elo']) . "</td>";
            echo "<td>$gameid_display</td>";
            echo "<td>$last_gameid_display</td>";
            echo "<td>" . htmlspecialchars((string)$user['delay']) . "</td>";
            echo "<td>
                    <form method='POST' action='?subid={$subid}' style='margin:0;'>
                        <input type='hidden' name='unlink_faceit_id' value='" . htmlspecialchars($user['faceit_id'], ENT_QUOTES) . "'>
                        <button type='submit' style='padding:2px 8px; font-size:12px;'>Unlink</button>
                    </form>
                  </td>
                </tr>";
        }
        echo "</table>";
        // Insert the Find/Update buttons side-by-side, same size, aligned
        echo "<div style='display: flex; gap: 8px; margin-top: 6px; align-items: center;'>";
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
?>