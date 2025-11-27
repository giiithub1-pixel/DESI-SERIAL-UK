<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Secure Access Live Card</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }
    
    .card5 {
      width: 95%;
      max-width: 400px;
      margin: 10px;
      border-radius: 10px;
      overflow: hidden;
      background-color: black;
      position: relative;
      transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
      box-shadow: 0 14px 15px rgba(0, 0, 0, 0.2), 0 6px 20px rgba(0, 0, 0, 1.9);
      cursor: pointer;
    }
    .card5:hover { transform: translateY(-10px); }
    .card5 img {
      width: 100%;
      height: auto;
      transition: opacity 0.3s ease;
    }
    .card5:hover img { opacity: 0.8; }

    .card5-container {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .live-indicator, .countdown-timer, .custom-message {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      padding: 8px 24px;
      font-size: 22px;
      font-weight: bold;
      border-radius: 5px;
      color: white;
      text-align: center;
      width: 80%;
      box-sizing: border-box;
    }

    .live-indicator {
      background-color: red;
      border: none;
      animation: blink 1s infinite;
      display: none;
      bottom: 0px;
    }

    .countdown-timer {
      background-color: rgba(0, 0, 0, 0.6);
      display: none;
      bottom: 0px;
    }

    .custom-message {
      background-color: rgba(0, 0, 0, 0.6);
      display: none;
      bottom: 33px;
      font-size: 30px;
    }

    @keyframes blink {
      0%, 100% { opacity: 1; }
      50% { opacity: 0; }
    }

    @media (max-width: 768px), (max-width: 480px) {
      .card5 { width: 98%; }
      .live-indicator, .countdown-timer, .custom-message {
        font-size: 15px;
        padding: 6px 18px;
        width: 70%;
      }
      .custom-message { font-size: 20px; color: white; }
    }
  </style>
</head>
<body>
  <div class="card5-container">
    <div class="card5" onclick="generateLink()">
      <img src="https://images.wondershare.com/filmora/article-images/css-loading-text-animation-1.gif" alt="Card Image" id="cardImage">
      <div class="custom-message"></div>
      <div class="live-indicator">Live Now</div>
      <div class="countdown-timer"></div>
    </div>
  </div>

  <script>
    // Secure Access System
    function generateLink() {
      fetch('generate.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            window.location.href = 'redirect.php?id=' + data.token;
          } else {
            alert('Error generating link. Please try again.');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error generating link. Please try again.');
        });
    }

    // Match / Countdown System
    const timeRanges = [
      {
        match: "Fancode",
        dates: {
          "2025-10-29": [
            { startHour: 12, startMinute: 30, endHour: 17, endMinute: 30, message: "1st T20i", image: "https://i.ibb.co/vCrC3x5w/file-29357.jpg" }
          ],
          "2025-10-24": [
            { startHour: 8, startMinute: 30, endHour: 16, endMinute: 0, message: "3rd ODI", image: "https://i.ibb.co/7tsS11LH/INDvAUS.jpg" }
          ]
        }
      }
    ];

    function padZero(n) { return n < 10 ? '0' + n : n; }
    function getFormattedDate(date) {
      return `${date.getFullYear()}-${padZero(date.getMonth() + 1)}-${padZero(date.getDate())}`;
    }

    function getCurrentSchedule(schedules, now) {
      if (!schedules || schedules.length === 0) return null;
      for (let schedule of schedules) {
        const start = new Date(now);
        start.setHours(schedule.startHour, schedule.startMinute, 0, 0);
        const end = new Date(now);
        end.setHours(schedule.endHour, schedule.endMinute, 0, 0);
        if (now >= start && now < end) return schedule;
      }
      return null;
    }

    function getNextSchedule(scheduleDates) {
      const now = new Date();
      const dateKeys = Object.keys(scheduleDates).sort();
      const todayStr = getFormattedDate(now);
      const todaySchedules = scheduleDates[todayStr];
      if (todaySchedules) {
        for (let schedule of todaySchedules) {
          const startTime = new Date(now);
          startTime.setHours(schedule.startHour, schedule.startMinute, 0, 0);
          if (startTime > now) return { startTime, ...schedule };
        }
      }
      for (const dateStr of dateKeys) {
        if (dateStr <= todayStr) continue;
        const [year, month, day] = dateStr.split('-').map(Number);
        const schedules = scheduleDates[dateStr];
        if (schedules && schedules.length > 0) {
          const firstSchedule = schedules[0];
          const startTime = new Date(year, month - 1, day, firstSchedule.startHour, firstSchedule.startMinute, 0, 0);
          return { startTime, ...firstSchedule };
        }
      }
      return null;
    }

    function checkLiveStatus() {
      const now = new Date();
      const todayStr = getFormattedDate(now);
      document.querySelectorAll('.card5').forEach((card, index) => {
        const liveIndicator = card.querySelector('.live-indicator');
        const countdownTimer = card.querySelector('.countdown-timer');
        const customMessage = card.querySelector('.custom-message');
        const cardImage = card.querySelector('img');
        const schedule = timeRanges[index];
        const schedulesToday = schedule?.dates[todayStr];
        const currentSchedule = getCurrentSchedule(schedulesToday, now);
        if (currentSchedule) {
          liveIndicator.style.display = 'inline-block';
          countdownTimer.style.display = 'none';
          customMessage.textContent = currentSchedule.message;
          customMessage.style.display = 'inline-block';
          cardImage.src = currentSchedule.image;
        } else {
          liveIndicator.style.display = 'none';
          const nextSchedule = getNextSchedule(schedule.dates);
          if (nextSchedule) {
            const diffSec = Math.floor((nextSchedule.startTime - now) / 1000);
            const days = Math.floor(diffSec / (3600 * 24));
            const hours = Math.floor((diffSec % (3600 * 24)) / 3600);
            const minutes = Math.floor((diffSec % 3600) / 60);
            const seconds = diffSec % 60;
            countdownTimer.textContent = `Live in: ${days}d ${padZero(hours)}h ${padZero(minutes)}m ${padZero(seconds)}s`;
            countdownTimer.style.display = 'inline-block';
            customMessage.textContent = nextSchedule.message;
            customMessage.style.display = 'inline-block';
            cardImage.src = nextSchedule.image;
          } else {
            countdownTimer.style.display = 'none';
            customMessage.style.display = 'none';
            cardImage.src = "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT3_L1HJ4x1FhjtgVTNWszj_ZxmkGy2vvjPbA&s";
          }
        }
      });
    }

    setInterval(checkLiveStatus, 1000);
    window.onload = checkLiveStatus;
  </script>
</body>
</html>
