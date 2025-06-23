<?php

// Database Table (Vertical, per new instructions)
echo "<div>";
echo "<h3 style='margin-bottom:4px;'>DataBase</h3>";
echo "<table style='border-collapse: collapse; font-size: 12px; margin-bottom: 0;'>";
echo "
    <tr>
        <th style='background: #f0f0f0;' colspan='2'>API Key</th>
    </tr>
    <tr>
        <td colspan='2' style='padding: 4px 8px;'>" . htmlspecialchars(obfuscate_text($api_key)) . "</td>
    </tr>";
echo "
    <tr>
        <th style='background: #f0f0f0;'>IP</th>
        <th style='background: #f0f0f0;'>Port</th>
    </tr>
    <tr>
        <td style='padding: 4px 8px;'>" . htmlspecialchars($ip) . "</td>
        <td style='padding: 4px 8px;'>" . htmlspecialchars($port) . "</td>
    </tr>";
echo "</table>";
echo "</div>";
?>