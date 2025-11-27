<?php
// cleanup.php - Optional: Run this periodically to clean old tokens
$dbFile = 'tokens.json';

if (file_exists($dbFile)) {
    $tokens = json_decode(file_get_contents($dbFile), true) ?: [];
    
    $currentTime = time();
    $tokens = array_filter($tokens, function($token) use ($currentTime) {
        return ($currentTime - $token['created']) < 3600; // Keep tokens less than 1 hour old
    });
    
    file_put_contents($dbFile, json_encode($tokens));
    echo "Cleanup completed. Remaining tokens: " . count($tokens);
} else {
    echo "No token file found.";
}
?>