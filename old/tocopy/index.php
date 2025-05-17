<?
include_once 'config.php';
if(!isset($_GET['token']))
	die('');
$token = $_GET['token'];
if($token != USER_TOKEN)
	die('');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Result</title>

	<link rel="stylesheet" type="text/css" href="../engine/styles/main.css?wef">
	<link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
</head>
<body>
	<div id="widget" style="display: none;">
	  <div class="widget-overlay"></div>
	  <div class="widget-container">
	    <div id="level"></div>
	    <div id="totalelo"></div>
	    <div id="amount"></div>
	  </div>
	</div>
	
	<script src="../engine/js/libraries/jquery.min.js"></script>
	<script>
		var check_timestamp;
		var check_animation = false;
		var time = 2300;

		function makeid(length) {
		    let result = '';
		    const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		    const charactersLength = characters.length;
		    let counter = 0;
		    while (counter < length) {
		      result += characters.charAt(Math.floor(Math.random() * charactersLength));
		      counter += 1;
		    }
		    return result;
		}

		$(document).ready(function(){
			$('#widget').css('--start', (time - 300) / 1000 + 's')

			setInterval(checkPushAnimation, 1000);
		});

		function checkPushAnimation(){
			console.log('check_animation', check_animation);
			if(check_animation)
				return false;
			check_animation = true;
			$.ajax({
			    url: '<?=DIR?>/push.json?' + makeid(5),
			    method: 'get',
			    dataType: 'json',
			    success: function(data){
			    	if(typeof data.time == 'undefined'){
						check_animation = false;

			    		return false;
			    	}

			    	if(!check_timestamp){
			    		check_timestamp = data.time;
						check_animation = false;
			    	}

			    	else if(check_timestamp != data.time){
			    		check_timestamp = data.time;
			    		updateStats(data.totalelo, data.amount);

						setTimeout(function() {
							updateELO(data.totalelo, data.amount)
						}, time);
			    	
			    	} else
						check_animation = false;
			    }
			});
		}

		function updateStats(totalelo, amount) {
			var newElo = totalelo + amount;

			if (newElo < 0) {
			    newElo = 0;
			}

			var newLevel = getLevel(totalelo);
			var color = getColor(newLevel);

			$('#widget').show();
			$('#level').html(newLevel).css({
				'--color': color,
				'--p': 75/100 * newLevel * 10,
			}).addClass('anim');;
			$('#totalelo').html(`<span>${totalelo}</span>` + ' ELO');
			$('#amount').html((amount > 0 ? "+" : "") + amount + ' ELO').addClass(amount > 0 ? "green" : "red");

			setTimeout(function() {
				var newLevel = getLevel(newElo);
				var color = getColor(newLevel);

				$('#level').html(newLevel).css({
					'--color': color,
					'--a': 75/100 * newLevel * 10
				});
			}, time + 1000);

			setTimeout(function() {
				$('#widget').fadeOut(400, function(){
					$('.widget-container').html('').append(
						$('<div>', {id: 'level'}),
						$('<div>', {id: 'totalelo'}),
						$('<div>', {id: 'amount'}),
					);
					check_animation = false;
				});
			}, time + 3000);
		}

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


		// Функція для визначення рівня за значенням ELO
		function getLevel(elo) {
		  if (elo >= 0 && elo <= 800) {
		    return 1;
		  } else if (elo >= 801 && elo <= 950) {
		    return 2;
		  } else if (elo >= 951 && elo <= 1100) {
		    return 3;
		  } else if (elo >= 1101 && elo <= 1250) {
		    return 4;
		  } else if (elo >= 1251 && elo <= 1400) {
		    return 5;
		  } else if (elo >= 1401 && elo <= 1550) {
		    return 6;
		  } else if (elo >= 1551 && elo <= 1700) {
		    return 7;
		  } else if (elo >= 1701 && elo <= 1850) {
		    return 8;
		  } else if (elo >= 1851 && elo <= 2000) {
		    return 9;
		  } else {
		    return 10;
		  }
		}

		function getColor(level) {
		  if (level == 1) {
		    return '#CDCDCD';
		  } else if (level >= 2 && level <= 3) {
		    return '#1CE400';
		  } else if (level >= 4 && level <= 7) {
		    return '#FFC800';
		  } else if (level >= 8 && level <= 9) {
		    return '#FF6309';
		  } else if (level == 10) {
		    return '#FE1F00';
		  }
		}
	</script>

</body>
</html>