<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Live Match Tracker for CS2 – Project Overview</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 2rem;
      background: #f9f9f9;
      color: #333;
    }
    h1 { color: #2c3e50; }
    h2 { margin-top: 2rem; color: #34495e; }
    .status { font-weight: bold; color: #27ae60; }
    .todo { font-weight: bold; color: #e67e22; }
    .contact { margin-top: 3rem; }
  </style>
</head>
<body>
  <h1>Live Match Tracker for CS2 (Commercial Project, In Progress)</h1>

  <p><strong>Note:</strong> This is a private project. Code access is available to recruiters or potential employers upon request.</p>

  <h2>🎯 Project Overview</h2>
  <ul>
    <li>Live tracking of FACEIT CS2 matches</li>
    <li>Real-time animations for ELO/level change shown on stream overlay</li>
    <li>Telegram integration for notifications</li>
  </ul>

  <h2>🧠 Architecture</h2>
  <h3>Main Server</h3>
  <p>
    <strong>Technologies:</strong> Flask, MySQL, Docker<br>
    <strong>Responsibilities:</strong> user/auth management, match storage, subscription system, web API for sub-servers
  </p>
  <p><span class="status">~90% Complete</span></p>
  <ul>
    <li>✅ REST API, DB structure, Telegram, Docker setup</li>
    <li>🔜 Admin UI, OAuth support</li>
  </ul>

  <h3>Sub-Servers</h3>
  <p>
    <strong>Technologies:</strong> Python, REST client, Docker<br>
    <strong>Responsibilities:</strong> polling FACEIT API, reporting results to the main server
  </p>
  <p><span class="status">~80% Complete</span></p>
  <ul>
    <li>✅ Periodic polling, auto-registration, Docker-ready</li>
    <li>🔜 Dynamic user reassignment, fault-tolerance logic</li>
  </ul>

  <h2>📈 Why It Matters</h2>
  <ul>
    <li>Scalable: 1 VPS per 5 users, low-cost, predictable load</li>
    <li>Streamer-friendly: visual impact + automation</li>
    <li>Monetizable: each instance is profitable with just 1–2 clients</li>
  </ul>

  <h2>📐 Detailed System Architecture</h2>

  <h3>1. Main Server</h3>
  <ul>
    <li>Written in Python using Flask, runs as a Docker container</li>
    <li>Manages central MySQL database with tables for:
      <ul>
        <li>Clients (user accounts, subscription info)</li>
        <li>Sub-servers (linked to clients, allows modular scaling)</li>
      </ul>
    </li>
    <li>Exposes internal API endpoints used by sub-servers</li>
    <li>Pushes updates to a frontend animation component (site or OBS browser source)</li>
    <li>Sends Telegram messages for match results</li>
  </ul>

  <h3>2. Sub-Servers</h3>
  <ul>
    <li>Stateless Python Docker containers that auto-register with the main server on boot</li>
    <li>Each sub-server is assigned to a group of users (typically 5 per VPS)</li>
    <li>Every 10 seconds:
      <ul>
        <li>Polls FACEIT API for each user</li>
        <li>Checks if a match has completed</li>
        <li>If yes: sends result (ELO delta, win/loss, timestamp, etc.) to the main server</li>
      </ul>
    </li>
    <li>Goal: high modularity and isolation to handle future Faceit API rate limits</li>
  </ul>

  <h3>3. Frontend Visualization (Client Side)</h3>
  <ul>
    <li>Receives JSON data from the main server after match processing</li>
    <li>Displays animated changes in:
      <ul>
        <li>ELO before and after match</li>
        <li>Level up/down transition</li>
      </ul>
    </li>
    <li>Designed for OBS / stream overlays</li>
  </ul>

  <h3>4. Monetization Strategy</h3>
  <ul>
    <li>Each sub-server can support 5 users</li>
    <li>Assuming $10/month per user:
      <ul>
        <li>Break-even point is reached with just half of one client</li>
        <li>1 VPS at ~$4/month generates profit starting from the first full user</li>
      </ul>
    </li>
    <li>Subscription handling and limitations managed via the main server</li>
  </ul>

  <h3>5. Dynamic Configuration (Settings Table)</h3>
  <ul>
    <li>Key operational settings are stored in a dedicated MySQL table <code>settings</code></li>
    <li>This allows easy adjustments without redeploying or restarting the system</li>
    <li>Current stored fields:
      <ul>
        <li><code>max_users_per_vps</code> – Maximum number of users assigned to one VPS</li>
        <li><code>month_sub_price</code> – Monthly price for a user subscription (in USD)</li>
        <li><code>trial_days</code> – Length of the free trial period (in days)</li>
      </ul>
    </li>
  </ul>

  <h3>6. Sub-Server Registry Table (<code>subservers</code>)</h3>
  <ul>
    <li>All sub-servers are tracked in the <code>subservers</code> MySQL table</li>
    <li>This table is used for assigning and monitoring backend worker containers</li>
    <li>Fields:
      <ul>
        <li><code>id</code> – Unique identifier for the sub-server (auto-increment)</li>
        <li><code>ip</code> – IP address of the sub-server</li>
        <li><code>api_key</code> – API authentication key used for secure communication with the main server</li>
        <li><code>current_user_load</code> – Number of users currently assigned to this sub-server</li>
      </ul>
    </li>
    <li>This allows the main server to efficiently balance load and control scaling logic</li>
  </ul>

  <h2 class="contact">📬 Contact</h2>
  <p>
    <strong>Developer:</strong> Tigran Kocharov<br>
    <strong>Email:</strong> tiko.nue@gmail.com<br>
    <strong>GitHub:</strong> <a href="https://github.com/tikoarm" target="_blank">tikoarm</a><br>
    <strong>Location:</strong> Nuremberg, Germany<br>
    <strong>Status:</strong> Open to full-time roles
  </p>

  <?php
  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_X_REAL_IP'] ?? $_SERVER['REMOTE_ADDR'];
  $browser = $_SERVER['HTTP_USER_AGENT'];

  echo "<p><small><strong>Your IP:</strong> {$ip}<br>";
  echo "<strong>Your Browser:</strong> {$browser}</small></p>";
  ?>

</body>
</html>