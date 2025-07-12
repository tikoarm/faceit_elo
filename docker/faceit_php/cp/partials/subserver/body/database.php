<?php

// Database Table (Vertical, per new instructions)
echo "<div>";
echo "<h3 style='margin-bottom:4px;'>DataBase</h3>";
echo "<table style='border-collapse: collapse; font-size: 12px; margin-bottom: 0;'>";
echo "
    <tr>
        <th style='background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);' colspan='2'>API Key</th>
    </tr>
    <tr>
        <td colspan='2' style='padding: 4px 8px;'>" . htmlspecialchars(obfuscate_text($api_key)) . "</td>
    </tr>";
echo "
    <tr>
        <th style='background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);'>IP</th>
        <th style='background: rgba(0, 0, 0, 0.3); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);'>Port</th>
    </tr>
    <tr>
        <td style='padding: 4px 8px;'>" . htmlspecialchars($ip) . "</td>
        <td style='padding: 4px 8px;'>" . htmlspecialchars($port) . "</td>
    </tr>";
echo "</table>";
echo "</div>";
?>