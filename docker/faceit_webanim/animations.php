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
        var time = 3500;

        const userid = <?= $userid ?>;
        const evtSource = new EventSource("sse.php?userid=" + userid);
    
        evtSource.onmessage = function(event) {
            const raw = event.data;

            if (!raw || raw.trim().length < 2) {
                console.warn("Пустой или повреждённый event.data:", raw);
                return;
            }

            let data;
            try {
                data = JSON.parse(raw);
            } catch (err) {
                console.error("Ошибка при парсинге JSON:", raw, err);
                return;
            }

            const elo = parseInt(data.elo);
            const eloDiff = parseInt(data.elo_diff || 0);
            const isShown = data.shown === true || data.shown === "true";

            $('#widget').css('--start', (time - 300) / 1000 + 's')

            if (!isShown && !isNaN(eloDiff) && eloDiff !== 0) {
                updateStats(elo, eloDiff);
                setTimeout(() => updateELO(elo, eloDiff), time);
            } else {
                document.getElementById("widget").style.display = "none";
                document.body.style.background = "transparent";
            }
        };

        //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        function updateELO(totalelo, amount) {
		  // Calculate result and update totalelo
		  let result = totalelo + amount;
		  if (result < 0) {
		    result = 0;
		  }

		  // Animate totalelo update from current value to result
		  $("#totalelo span").animate(
		    {
		      num: result - totalelo,
		    },
		    {
		      duration: 1000,
		      step: function (num) {
		        this.innerHTML = Math.floor(num + totalelo);
		      },
		    }
		  );

		  // Hide amount after 2 seconds
		  setTimeout(function () {
		    $("#level").addClass('active');
		  }, 1000);
		}
        function updateStats(totalelo, amount) {
			var newElo = totalelo + amount;

			if (newElo < 0) {
			    newElo = 0;
			}

			var oldLevel = getLevel(totalelo);
			var color = getColor(oldLevel);

			$('#widget').show();
			$('#level').html(oldLevel).css({
				'--color': color,
				'--p': 75/100 * oldLevel * 10,
			}).addClass('anim');;
			$('#totalelo').html(`<span>${totalelo}</span>` + ' ELO');
			$('#amount').html((amount > 0 ? "+" : "") + amount + ' ELO').addClass(amount > 0 ? "green" : "red");

			setTimeout(function() {
				var newLevel = getLevel(newElo);
				var color = getColor(newLevel);

				$('#level').html(newLevel).css({
					'--color': color,
					'--a': 75/100 * newLevel * 10
				}).addClass('active');;
			}, time + 1000);

			setTimeout(function() {
				$('#widget').fadeOut();
                mark_as_shown(userid);
			}, time + 3000);
		}
        // Функція для визначення рівня за значенням ELO
		
    </script>
    <script src="js/faceit.js"></script>

</body>
</html>
