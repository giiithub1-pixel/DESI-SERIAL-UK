<?php
// proxy.php — Global proxy for manifest.php / widevine.php / any stream
error_reporting(0);

// ===== Helper: detect user country =====
function getUserCountry() {
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    if (!$ip) return null;

    // Cloudflare header check
    if (isset($_SERVER['HTTP_CF_IPCOUNTRY'])) return $_SERVER['HTTP_CF_IPCOUNTRY'];

    // Fallback: free GeoIP API
    $geo = @file_get_contents("http://ip-api.com/json/$ip?fields=countryCode");
    if ($geo) {
        $data = json_decode($geo, true);
        return $data['countryCode'] ?? null;
    }
    return null;
}

// ===== Handle request =====
$url = $_GET['url'] ?? '';
$proxyMode = $_GET['proxy'] ?? 'on'; // default proxy on

if (!$url) {
    http_response_code(400);
    exit("❌ Missing ?url= parameter");
}

// Check if proxy is needed
$country = getUserCountry();
$needProxy = ($proxyMode === 'off' && $country && $country !== "IN");

// ===== Direct redirect if in India or proxy off =====
if (!$needProxy) {
    header("Location: $url");
    exit;
}

// ===== Act as a proxy =====
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Forward all headers except Host
$headers = [];
foreach (getallheaders() as $key => $value) {
    if (strtolower($key) === 'host') continue;
    $headers[] = "$key: $value";
}
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Fetch content
$response = curl_exec($ch);
$info = curl_getinfo($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    http_response_code(500);
    exit("Proxy error: $err");
}

// Return response with proper content-type
if (!empty($info['content_type'])) {
    header("Content-Type: ".$info['content_type']);
}
echo $response;
exit;
