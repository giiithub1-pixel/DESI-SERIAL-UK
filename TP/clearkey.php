<?php
require_once 'functions.php';

// This is a placeholder script for ClearKey.
// The actual key and KID must be securely retrieved for the specific channel.

if (!logged_in()) {
    http_response_code(403);
    die("Log in first.");
}

$id = $_GET['id'] ?? '';
if (empty($id) || !is_numeric($id)) {
    http_response_code(400);
    exit('Invalid channel ID');
}

$fetcherData = json_decode(getFetcherData(), true);

$channelData = null;
foreach ($fetcherData['data']['channels'] as $channel) {
    if ($channel['id'] === $id) {
        $channelData = $channel;
        break;
    }
}

if ($channelData === null) {
    http_response_code(404);
    exit('<h1>Channel data not found for the given ID.</h1>');
}

// --- ClearKey Logic Placeholder ---
// In a real-world scenario, you would fetch the KID and Key for this channel
// from a secure source, possibly from the $channelData if it's available there.

// For demonstration, we will generate a KID from the channel ID.
// IMPORTANT: The key ('k') MUST be a 16-byte value.
$kid_hex = hash('sha256', 'kid_salt' . $id);
$key_hex = hash('sha256', 'key_salt' . $id);

// Trim to required lengths
$kid_hex = substr($kid_hex, 0, 32);
$key_hex = substr($key_hex, 0, 32);


// Convert hex to base64url format for the JSON response
function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

$kid_b64 = base64url_encode(hex2bin($kid_hex));
$key_b64 = base64url_encode(hex2bin($key_hex));


$key_response = [
    'keys' => [
        [
            'kty' => 'oct',
            'k' => $key_b64,
            'kid' => $kid_b64
        ]
    ],
    'type' => 'temporary'
];

header('Content-Type: application/json');
echo json_encode($key_response);
