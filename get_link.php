<?php
session_start();

// Configuration
$BINDAAS_API_KEY = '33b600ef6ea47053499418e24d32d10abe2682bc'; // Your API key
$SECRET_SALT = 'change_this_to_random_string_xyz123'; // Change this!

// Function to generate daily token
function getDailyToken() {
    global $SECRET_SALT;
    $date = date('Y-m-d'); // Changes every day
    return md5($date . $SECRET_SALT);
}

// Function to shorten URL with BindaasLinks
function shortenWithBindaasLinks($url, $api_key) {
    $api_url = 'https://bindaaslinks.com/api?api=' . $api_key . '&url=' . urlencode($url);
    
    // Use cURL for better error handling
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        throw new Exception('cURL Error: ' . $error);
    }
    
    if ($http_code !== 200) {
        throw new Exception('HTTP Error: ' . $http_code);
    }
    
    // Parse JSON response
    $data = json_decode($response, true);
    
    if (isset($data['shortenedUrl'])) {
        return $data['shortenedUrl'];
    } elseif (isset($data['error'])) {
        throw new Exception('API Error: ' . $data['error']);
    } else {
        // If response is just the URL string
        return trim($response);
    }
}

// Generate the actual destination URL with daily token
$token = getDailyToken();
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$actual_url = $protocol . '://' . $host . '/home.php?token=' . $token;

// Shorten the URL
try {
    $shortened_url = shortenWithBindaasLinks($actual_url, $BINDAAS_API_KEY);
    
    echo json_encode([
        'success' => true,
        'shortened_url' => $shortened_url,
        'token' => $token // For debugging (remove in production)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
