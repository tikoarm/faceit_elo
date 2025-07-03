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
    return "#CDCDCD";
  } else if (level >= 2 && level <= 3) {
    return "#1CE400";
  } else if (level >= 4 && level <= 7) {
    return "#FFC800";
  } else if (level >= 8 && level <= 9) {
    return "#FF6309";
  } else if (level == 10) {
    return "#FE1F00";
  }
}

function mark_as_shown(userid) {
  fetch("mark_shown.php?userid=" + userid)
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        console.log("Файл помечен как shown");
      }
    })
    .catch((error) => console.error("Ошибка при обновлении shown:", error));
}
