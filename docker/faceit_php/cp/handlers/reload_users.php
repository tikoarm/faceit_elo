<?php

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
?>