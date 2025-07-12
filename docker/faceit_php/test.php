<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Чёрный фон</title>
  <style>
    body, html {
      margin: 0;
      padding: 0;
      height: 100%;
      background: #000000;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-family: sans-serif;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
  <style>
    #particles-js {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
    }
  </style>
</head>
<body>
  <div id="particles-js"></div>
  <div>
    <h1>Добро пожаловать!</h1>
    <p>Фон — просто чёрный.</p>
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