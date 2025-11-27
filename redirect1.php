<?php
// redirect.php - Validates token and displays content in iframe
$dbFile = 'tokens.json';
$targetUrl = 'https://crichd-iframe-plr.blogspot.com/p/star-sports-1hd.html';

// Get token from URL
$token = $_GET['id'] ?? '';

if (empty($token)) {
    die('Invalid access. Please use the button to generate a new link.');
}

// Read tokens
$tokens = [];
if (file_exists($dbFile)) {
    $tokens = json_decode(file_get_contents($dbFile), true) ?: [];
}

// Check if token exists and is not used
if (!isset($tokens[$token])) {
    die('Invalid or expired link. Please generate a new one.');
}

if ($tokens[$token]['used']) {
    die('This link has already been used. Please generate a new one.');
}

// Mark token as used
$tokens[$token]['used'] = true;
file_put_contents($dbFile, json_encode($tokens));

// Display content in iframe - URL stays hidden
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Viewer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body, html {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
            display: block;
        }
    </style>
</head>
<body>
    <iframe src="<?php echo htmlspecialchars($targetUrl); ?>" 
            allowfullscreen 
            allow="autoplay; encrypted-media; picture-in-picture"
            sandbox="allow-same-origin allow-scripts allow-popups allow-forms allow-pointer-lock">
    </iframe>
    
    <script>
        // Prevent users from inspecting or accessing the iframe source
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
        
        // Disable F12 and common inspect shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.keyCode == 123 || // F12
                (e.ctrlKey && e.shiftKey && e.keyCode == 73) || // Ctrl+Shift+I
                (e.ctrlKey && e.shiftKey && e.keyCode == 67) || // Ctrl+Shift+C
                (e.ctrlKey && e.shiftKey && e.keyCode == 74) || // Ctrl+Shift+J
                (e.ctrlKey && e.keyCode == 85)) { // Ctrl+U
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
