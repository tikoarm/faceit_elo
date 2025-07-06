<?php
$userid = isset($_GET['userid']) ? intval($_GET['userid']) : 0;
$password = isset($_GET['password']) ? $_GET['password'] : '';

if ($userid === 0 || $password === '') {
    http_response_code(400);
    die("Missing userid or password");
}

$host = getenv('MYSQL_HOST') ?: 'localhost';
$port = getenv('MYSQL_PORT') ?: 3306;
$dbuser = getenv('MYSQL_USER') ?: 'root';
$dbpass = getenv('MYSQL_PASSWORD') ?: '';
$dbname = getenv('MYSQL_DATABASE') ?: '';

$mysqli = new mysqli($host, $dbuser, $dbpass, $dbname, $port);

if ($mysqli->connect_error) {
    http_response_code(500);
    die("Database connection error: " . $mysqli->connect_error);
}
$userid = $_GET['userid'] ?? ''; // пример получения userid, поменяй под свой способ
$password = $_GET['password'] ?? ''; // пароль для проверки

$stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("s", $userid);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$stmt->bind_result($hash);
if (!$stmt->fetch()) {
    // Нет результата
    http_response_code(401);
    die("Unauthorized: Userid not found");
}
$stmt->close();

if (!password_verify($password, $hash)) {
    http_response_code(401);
    die("Unauthorized: Invalid password");
}

$mysqli->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Animation</title>
    <link rel="stylesheet" type="text/css" href="styles/main.css">
	<link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
</head>
<body>
    <div id="widget">
	  <div class="widget-overlay"></div>
	  <div class="widget-container">
	    <div id="level"></div>
	    <div id="totalelo"></div>
	    <div id="amount"></div>
	  </div>
	</div>

    <script src="js/jquery.min.js"></script>
    <script>
    const userid = <?= $userid ?>;
    const time = 3500;
    const evtSource = new EventSource("sse.php?userid=" + userid);

	hideWidget();
    evtSource.onmessage = function(event) {
      if (!event.data || event.data.trim().length < 2) return;

      try {
        const data = JSON.parse(event.data);
        const elo = parseInt(data.elo);
        const eloDiff = parseInt(data.elo_diff || 0);
        const isShown = data.shown === true || data.shown === "true";

        if (!isShown && !isNaN(eloDiff) && eloDiff !== 0) {
          $('#widget').css('--start', (time - 300) / 1000 + 's');
          startAnimation(elo, eloDiff);
        } else {
          hideWidget();
        }
      } catch (err) {
        console.error("Invalid JSON:", event.data, err);
      }
    };

    function startAnimation(totalelo, diff) {
      const newElo = Math.max(0, totalelo + diff);
      const oldLevel = getLevel(totalelo);
      const newLevel = getLevel(newElo);

      const oldColor = getColor(oldLevel);
      const newColor = getColor(newLevel);

      $('#level').html(oldLevel).css({
        '--color': oldColor,
        '--p': 75 / 100 * oldLevel * 10,
        '--a': 0
      }).removeClass('active').addClass('anim');

      $('#totalelo').html(`<span>${totalelo}</span> ELO`);
      $('#amount').html((diff > 0 ? '+' : '') + diff + ' ELO').removeClass('green red').addClass(diff > 0 ? 'green' : 'red');
      const overlay = $('.widget-overlay');
      overlay.removeClass('win loss');
      if (diff > 0) {
        overlay.addClass('win');
      } else {
        overlay.addClass('loss');
      }
      $('#widget').fadeIn(150);

      setTimeout(() => {
        animateELO(totalelo, diff);
      }, time);

	  setTimeout(() => {
	    animateLevelProgress(oldLevel, newLevel, newColor);
	  }, time + 1600);

      setTimeout(() => {
        $('#widget').fadeOut(300);
        mark_as_shown(userid);
      }, time + 3600);
    }

    function animateELO(start, diff) {
      const end = Math.max(0, start + diff);
      $("#totalelo span").prop("number", start).animate({
        number: end
      }, {
        duration: 1000,
        step: function(now) {
          $(this).text(Math.floor(now));
        }
      });

      setTimeout(() => {
        $('#level').addClass('active');
      }, 1600);
    }

	function animateLevelProgress(fromLevel, toLevel, color) {
	  const levelElement = document.getElementById('level');
	  $('#level').html(toLevel).css('--color', color);

	  const start = 75 / 100 * fromLevel * 10;
	  const end = 75 / 100 * toLevel * 10;
	  const duration = 1500;
	  const startTime = performance.now();

	  function animate(time) {
	    const elapsed = time - startTime;
	    const progress = Math.min(elapsed / duration, 1);
	    const easedProgress = easeInOutQuad(progress);
	    const current = start + (end - start) * easedProgress;

	    levelElement.style.setProperty('--p', current);

	    if (progress < 1) {
	      requestAnimationFrame(animate);
	    } else {
	      // После анимации добавить эффект активного состояния
	      $('#level').addClass('active');
	    }
	  }

	  // Запускаем анимацию
	  $('#level').removeClass('active').addClass('anim');
	  requestAnimationFrame(animate);
	}


	function easeInOutQuad(t) {
	  return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
	}



    function hideWidget() {
      document.getElementById("widget").style.display = "none";
      document.body.style.background = "transparent";
    }
  </script>
    <script src="js/faceit.js"></script>

</body>
</html>
