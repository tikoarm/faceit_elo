<?php
ob_start(); session_start();
require_once __DIR__ . '/handlers/init_session.php';
require_once __DIR__ . '/handlers/auth.php';

if ($authenticated) {
    header("Location: /cp/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Faceit Widget | Control Panel</title>
  <link rel="stylesheet" href="assets/login.css">

  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
</head>
<body>
  <div id="particles-js"></div>
  <div class="wrapper">
    <form method="POST" action="" id="mainForm">
      <h2>Login</h2>
        <div class="input-field">
        <input type="text" name="user" required value="<?php echo htmlspecialchars($_POST['user'] ?? ''); ?>">
        <label>Enter your login</label>
      </div>
      <div class="input-field">
        <input type="password" name="password" required>
        <label>Enter your password</label>
      </div>
      <?php if (!empty($loginError)): ?>
            <p style="color:red;">❌ Incorrect password.</p>
        <?php endif; ?>
      <div class="forget">
        <label for="remember">
          <input type="checkbox" id="remember">
          <p>Remember me</p>
        </label>
        <a href="#">Forgot password?</a>
      </div>
      <button type="submit" name="submit">Log In</button>
      <div class="register">
        <p>Don't have an account? <a href="#">Register</a></p>
      </div>
    </form>
  </div>
  <script>
    particlesJS("particles-js", {
      "particles": {
        "number": { "value": 80 },
        "color": { "value": "#ffffff" },
        "shape": { "type": "circle" },
        "opacity": { "value": 0.5 },
        "size": { "value": 3 },
        "line_linked": {
          "enable": true,
          "distance": 150,
          "color": "#ffffff",
          "opacity": 0.4,
          "width": 1
        },
        "move": {
          "enable": true,
          "speed": 2
        }
      },
      "interactivity": {
        "detect_on": "canvas",
        "events": {
          "onhover": { "enable": true, "mode": "grab" }
        }
      },
      "retina_detect": true
    });
  </script>
</body>
</html>