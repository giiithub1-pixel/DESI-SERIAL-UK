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
    <div class="container">
        <h1>🎉 Welcome to Home Page!</h1>
        
        <div class="success-msg">
            <strong>✓ Success!</strong> You've successfully navigated through the link shortener.
        </div>

        <div class="info-box">
            <h3>Security Information:</h3>
            <p><strong>Current Token:</strong></p>
            <div class="token-display"><?php echo htmlspecialchars($provided_token); ?></div>
            <p><small>🔒 This token changes daily at midnight for security. Each day generates a unique access code.</small></p>
            <p><small>📅 Valid for: <?php echo date('l, F j, Y'); ?></small></p>
        </div>

        <div class="content">
            <h2>Your Protected Content</h2>
            <p>This is your home page content that can only be accessed through the shortened link with valid daily token.</p>
            <p>You can add any content, images, videos, or features you want here.</p>
        </div>

        <a href="index.php" class="btn-back">← Back to Home</a>
    </div>
</body>
</html>
