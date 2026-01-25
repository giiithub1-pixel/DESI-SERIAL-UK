<?php
session_start();

// Destroy the session completely
session_unset();
session_destroy();

// --- MODIFIED: Safely remove both credentials and playlist cache ---

// Remove the credentials file
$creds_file = '../app/creds';
if (file_exists($creds_file)) {
    unlink($creds_file);
}

// Remove the cached playlist JSON file
$playlist_file = '../app/js/playlist.json';
if (file_exists($playlist_file)) {
    unlink($playlist_file);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Logging Out...</title>
  <meta http-equiv="refresh" content="3;url=login.php">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  
  <style>
    /* --- NEW & IMPROVED STYLES --- */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      height: 100vh;
      background: linear-gradient(135deg, #1d2b64, #f8cdda);
      font-family: 'Poppins', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 15px;
    }

    .card {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-radius: 20px;
      padding: 40px 35px;
      width: 100%;
      max-width: 420px;
      text-align: center;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
      animation: fadeIn 0.8s ease-out forwards;
      border: 1px solid rgba(255, 255, 255, 0.25);
      position: relative;
      overflow: hidden; /* Important for the progress bar */
    }

    .logout-icon {
      font-size: 60px;
      color: #39ff14; /* Neon green */
      margin-bottom: 20px;
      animation: popIn 0.6s 0.3s ease-out backwards;
      text-shadow: 0 0 15px #39ff1488;
    }

    h2 {
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 10px;
      color: #ffffff;
    }

    p {
      font-size: 1rem;
      color: #e0e0e0;
      line-height: 1.6;
    }

    .small-note {
      margin-top: 25px;
      font-size: 0.85rem;
      color: #c0c0c0;
    }

    .small-note a {
      color: #f8cdda;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
    }

    .small-note a:hover {
      color: #fff;
      text-decoration: underline;
    }

    .redirect-bar {
      position: absolute;
      bottom: 0;
      left: 0;
      height: 5px;
      width: 100%;
      background: linear-gradient(90deg, #39ff14, #21fcff);
      animation: shrink 3s linear forwards;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px) scale(0.98); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    @keyframes popIn {
      from { opacity: 0; transform: scale(0.5); }
      to { opacity: 1; transform: scale(1); }
    }

    @keyframes shrink {
      from { width: 100%; }
      to { width: 0%; }
    }

    @media (max-width: 480px) {
      .card { padding: 35px 25px; }
      h2 { font-size: 1.8rem; }
      p { font-size: 0.95rem; }
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="logout-icon">
      <i class="fas fa-check-circle"></i>
    </div>
    <h2>Logout Successful</h2>
    <p>Your session has been cleared. You will be redirected shortly.</p>
    <div class="small-note">(If not redirected, <a href="login.php">click here</a>)</div>
    
    <div class="redirect-bar"></div>
  </div>
</body>
</html>