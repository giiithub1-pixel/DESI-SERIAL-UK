<?php
// --- Step 1: Authentication Check ---
$auth_file = 'app/creds';

if (!file_exists($auth_file)) {
    http_response_code(403); // Forbidden
    die('<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Access Denied</title><style>body { font-family: sans-serif; background-color: #0d1117; color: #e0e0e0; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; } .container { text-align: center; padding: 2rem; background-color: #161b22; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); border: 1px solid #30363d; } h1 { color: #f44336; } a { color: #58a6ff; text-decoration: none; } a:hover { text-decoration: underline; }</style></head><body><div class="container"><h1>Access Denied</h1><p>Please <a href="login/login.php">login first</a> to access this content.</p></div></body></html>');
}

// --- Step 2: Initialize Variables and Validate Channel ID ---
$player_can_load = false;
$error_message = '';
$channelId = null;
$channelName = "Live Stream"; // Default name

if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
    $player_can_load = true;
    $channelId = intval($_GET['id']);

    // --- Build the dynamic URLs ---
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    
    $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

    $manifestUrl = $protocol . $host . $basePath . '/manifest.php?id=' . $channelId;
    $widevineUrl = $protocol . $host . $basePath . '/widevine.php?id=' . $channelId;

} else {
    $error_message = 'Error: No valid Channel ID was specified. Please provide a numeric ID in the URL, like "?id=123".';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tataplay - Secure Stream</title>
    
    <script src="https://content.jwplatform.com/libraries/SAHhwvZq.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        /* --- IMPROVED UI & ANIMATIONS --- */
        :root {
            --dark-bg: #0d1117;
            --primary-bg: #161b22;
            --primary-text: #e6edf3;
            --accent-color: #58a6ff;
            --accent-hover: #79c0ff;
            --border-color: #30363d;
            --shadow-color: rgba(88, 166, 255, 0.2);
            --error-bg: #2b0f0f;
            --error-text: #ff9a9a;
            --error-border: #8d2a2a;
        }

        body {
            font-family: "Poppins", sans-serif;
            background-color: var(--dark-bg);
            background-image: radial-gradient(circle at 20% 20%, rgba(88, 166, 255, 0.1), transparent 30%);
            margin: 0;
            padding: 20px;
            color: var(--primary-text);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .main-container {
            width: 100%;
            max-width: 960px;
            background-color: var(--primary-bg);
            border-radius: 16px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            border: 1px solid var(--border-color);
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stream-header {
            padding: 20px 25px;
            background-color: rgba(0,0,0,0.2);
            border-bottom: 1px solid var(--border-color);
        }

        .stream-header h1 {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        #jwplayerDiv {
            width: 100%;
            background-color: #000;
        }

        .controls {
            padding: 20px 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0,0,0,0.2);
            border-top: 1px solid var(--border-color);
        }

        .btn {
            background-color: var(--accent-color);
            border: 1px solid var(--accent-color);
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            font-family: "Poppins", sans-serif;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background-color: var(--accent-hover);
            border-color: var(--accent-hover);
            transform: scale(1.03) translateY(-2px);
            box-shadow: 0 0 15px var(--shadow-color);
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn i {
            font-size: 0.9em;
        }

        .error-message {
            text-align: center;
            color: var(--error-text);
            background-color: var(--error-bg);
            border: 1px solid var(--error-border);
            padding: 20px;
            border-radius: 8px;
            max-width: 960px;
            margin: 40px auto;
            font-weight: 600;
            animation: fadeIn 0.5s ease-out;
        }

        .toast {
            position: fixed;
            bottom: -100px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #2a2d31;
            color: #fff;
            padding: 12px 25px;
            border-radius: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            font-size: 0.95rem;
            z-index: 1000;
            transition: bottom 0.5s ease;
            border: 1px solid var(--border-color);
        }
        .toast.show {
            bottom: 30px;
        }
    </style>
</head>
<body>

<?php if ($player_can_load): ?>
    <div class="main-container">
        <header class="stream-header">
            <h1><i class="fas fa-satellite-dish"></i> <?php echo htmlspecialchars($channelName); ?></h1>
        </header>

        <div id="jwplayerDiv"></div>
        
        <div class="controls">
            <button id="recordBtn" class="btn" onclick="showComingSoon()">
                <i class="fas fa-circle"></i>
                <span>Record</span>
            </button>
        </div>
    </div>
<?php else: ?>
    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<div id="toastNotification" class="toast"></div>

<script>
    <?php if ($player_can_load): ?>
    
    jwplayer("jwplayerDiv").setup({
        file: "<?php echo $manifestUrl; ?>",
        type: "dash",
        drm: {
            "widevine": {
                "url": "<?php echo $widevineUrl; ?>"
            }
        },
        width: "100%",
        aspectratio: "16:9",
        autostart: true
    });

    
    const toast = document.getElementById('toastNotification');
    let toastTimeout;

    function showToast(message) {
        clearTimeout(toastTimeout);
        toast.textContent = message;
        toast.classList.add('show');
        toastTimeout = setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    
    function showComingSoon() {
        showToast('📢 Recorder feature is coming soon!');
    }
    <?php endif; ?>
</script>

</body>
</html>