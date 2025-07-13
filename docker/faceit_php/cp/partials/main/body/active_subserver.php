<?php

$server = $_SESSION['active_subserver'];
if (!empty($server)) {
    echo "<div style='position: absolute; top: 10px; right: 10px; z-index: 9999;'>";
    echo "<div style='color: #0f0; text-align: right;'>
                  &#128994; ID: {$server['id']} &nbsp; Ping: {$server['pingMs']}ms &nbsp;
                  Location: {$server['location']} &nbsp; IP: {$server['ip']}
                  &nbsp; <form method='post' style='display:inline;'>
                      <button type='submit' name='update_subserver' style='background:none;border:none;color:#0ff;cursor:pointer;'>Update</button>
                  </form>
              </div>";
    echo "</div>";
} else {
    echo "<div style='position: absolute; top: 10px; right: 10px; z-index: 9999;'>
              <div style='color: #f00; border: 1px solid #f00; padding: 5px; border-radius: 4px; display: inline-block; text-align: right;'>
                  &#128308; No active subserver connections available
              </div>
              &nbsp; <form method='post' style='display:inline;'>
                      <button type='submit' name='update_subserver' style='background:none;border:none;color:#0ff;cursor:pointer;'>Update</button>
                  </form>
          </div>";
}
