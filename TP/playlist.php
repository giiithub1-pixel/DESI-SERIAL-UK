<?php
// Don't Sell this Script, This is 100% Free.
include 'functions.php';
if (!logged_in()) {
    die("Log in first.");
}

// Fetch Tata Play channel data
$jsonData = getFetcherData();
$data = json_decode($jsonData, true);

// Base URL setup
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$requestUri = $_SERVER['REQUEST_URI'];
$currentScript = basename($_SERVER['SCRIPT_NAME']);

// Detect User-Agent and set headers
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (stripos($userAgent, 'tivimate') !== false) { 
    $headers = '|User-Agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.69.69.69 YGX/537.36"&Origin="https://watch.tataplay.com"&Referer="https://watch.tataplay.com/"';
    $ctag = 'catchup-type="append" catchup-days="8" catchup-source="&begin={utc}&end={utcend}"';
} elseif ($userAgent === 'Mozilla/5.0 (Windows NT 10.0; rv:78.0) Gecko/20100101 Firefox/78.0') { 
    $headers = '%7CUser-Agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.69.69.69 YGX/537.36&Origin=https://watch.tataplay.com/&Referer=https://watch.tataplay.com/';
    $ctag = null;
} else { 
    $headers = '|User-Agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.69.69.69 YGX/537.36&Origin=https://watch.tataplay.com&Referer=https://watch.tataplay.com/';
    $ctag = 'catchup-type="append" catchup-days="8" catchup-source="&begin={utc}&end={utcend}"';
}

// Initialize M3U content
$m3uContent = "#EXTM3U x-tvg-url=\"https://avkb.short.gy/epg.xml.gz\"\n";

// Loop through channels
foreach ($data['data']['channels'] as $channel) {
    $id = $channel['id'];
    $name = $channel['name'];
    $logo = $channel['logo_url'];
    $genre = $channel['primaryGenre'];

    // Construct URLs
    $manifestUrl = $protocol . $host . str_replace($currentScript, 'manifest.php', $requestUri) . '?id=' . $id;
    $widevineUrl = $protocol . $host . str_replace($currentScript, 'widevine.php', $requestUri) . '?id=' . $id;

    // Wrap with proxy.php only if host is NOT localhost
    if (!in_array($host, ['127.0.0.1', '::1', 'localhost'])) {
        $manifestUrl = $protocol . $host . str_replace($currentScript, 'proxy.php', $requestUri) . "?url=" . urlencode($manifestUrl) . "&proxy=on";
        $widevineUrl = $protocol . $host . str_replace($currentScript, 'proxy.php', $requestUri) . "?url=" . urlencode($widevineUrl) . "&proxy=on";
    }

    // Write channel info into M3U
    $m3uContent .= "#KODIPROP:inputstream.adaptive.license_type=com.widevine.alpha\n";
    $m3uContent .= "#KODIPROP:inputstream.adaptive.license_key=$widevineUrl\n";
    $m3uContent .= "#KODIPROP:inputstream.adaptive.manifest_type=mpd\n";
    $m3uContent .= "#EXTINF:-1 tvg-id=\"ts$id\"";
    if ($ctag) $m3uContent .= " $ctag";
    $m3uContent .= " group-title=\"$genre\" tvg-logo=\"https://mediaready.videoready.tv/tatasky-epg/image/fetch/f_auto,fl_lossy,q_auto,h_250,w_250/$logo\",$name\n";
    $m3uContent .= $manifestUrl . $headers . "\n\n";
}

// Output playlist
header('Content-Type: audio/x-mpegurl');
header('Content-Disposition: attachment; filename="playlist.m3u"');
echo $m3uContent;
exit;
