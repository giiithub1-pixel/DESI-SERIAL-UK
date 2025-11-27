<?php
session_start();

// Configuration - MUST match get_link.php
$SECRET_SALT = 'change_this_to_random_string_xyz123'; // Same as get_link.php!

// Function to generate daily token (same as get_link.php)
function getDailyToken() {
    global $SECRET_SALT;
    $date = date('Y-m-d');
    return md5($date . $SECRET_SALT);
}

// Verify the token
$expected_token = getDailyToken();
$provided_token = isset($_GET['token']) ? $_GET['token'] : '';

// If token doesn't match, redirect to index
if ($provided_token !== $expected_token) {
    header('Location: index.php');
    exit();
}

// Token is valid - show home page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 50px auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 50px rgba(0,0,0,0.3);
        }
        h1 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        .success-msg {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .info-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .info-box p {
            margin: 10px 0;
            color: #333;
        }
        .token-display {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            word-break: break-all;
            margin: 10px 0;
        }
        .btn-back {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            margin-top: 20px;
            transition: all 0.3s;
        }
        .btn-back:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }
        .content {
            margin-top: 30px;
            line-height: 1.8;
        }
    </style>
</head>
<body>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Today's Password - ASL SPORTS</title>
  <style>
    body {
      font-family: sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      background: #f8f9fa;
      margin: 0;
    }
    .box {
      padding: 20px;
      background: white;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      text-align: center;
      position: relative;
    }
    .password {
      font-size: 40px;
      font-weight: bold;
      color: #007bff;
      cursor: pointer;
      user-select: none;
    }
    .hint {
      font-size: 14px;
      color: gray;
      margin-top: 10px;
    }
    .copied-message {
      position: absolute;
      top: -30px;
      left: 50%;
      transform: translateX(-50%);
      background-color: #28a745;
      color: white;
      padding: 5px 10px;
      border-radius: 5px;
      font-size: 14px;
      opacity: 0;
      transition: opacity 0.3s ease;
    }
    .copied-message.show {
      opacity: 1;
    }
  </style>
</head>
<body>
  <div class="box">
    <div class="copied-message" id="copiedMsg">Password copied!</div>
    <h2>Today's Password</h2>
    <p class="password" id="dailyPass">Loading...</p>
    <p class="hint">Tap the password to copy</p>
  </div>

  <script>
    function seededRandom(seed) {
      var x = Math.sin(seed) * 10000;
      return x - Math.floor(x);
    }

    function generateRandomPassword() {
      const now = new Date();
      const seed = parseInt(now.toISOString().slice(0, 10).replace(/-/g, ''));
      const rand = seededRandom(seed);
      return Math.floor(rand * 10000).toString().padStart(4, '0');
    }

    const passwordElement = document.getElementById("dailyPass");
    const copiedMsg = document.getElementById("copiedMsg");
    const password = generateRandomPassword();
    passwordElement.innerText = password;

    passwordElement.addEventListener("click", () => {
      navigator.clipboard.writeText(password).then(() => {
        copiedMsg.classList.add("show");
        setTimeout(() => {
          copiedMsg.classList.remove("show");
        }, 1000);
      }).catch(err => {
        alert("Failed to copy password: " + err);
      });
    });
  </script>

</body>
</html>


</body>
</html>
