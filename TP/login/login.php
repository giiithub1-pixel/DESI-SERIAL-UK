<?php
include_once '../functions.php';

function saveCreds($data) {
    $credsFile = '../app/creds';
    if (!file_exists(dirname($credsFile))) {
        mkdir(dirname($credsFile), 0755, true);
    }
    file_put_contents($credsFile, json_encode($data));
}

function doCurlRequest($url, $postData) {
    $headers = [
        'Content-Type: application/json',
        'Accept: */*',
        'Accept-Encoding: gzip, deflate, br, zstd',
        'Accept-Language: en-US,en;q=0.9,en-IN;q=0.8',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Edg/131.0.0.0',
        'device_details: {"pl":"web","os":"WINDOWS","lo":"en-us","app":"1.48.8","dn":"PC","bv":116,"bn":"OPERA","device_id":"7683d93848b0f472c508e38b1827038a","device_type":"WEB","device_platform":"PC","device_category":"open","manufacturer":"WINDOWS_OPERA_116","model":"PC","sname":""}',
        'Referer: https://watch.tataplay.com/',
        'Origin: https://watch.tataplay.com',
        'Sec-Fetch-Dest: empty',
        'Sec-Fetch-Mode: cors',
        'Sec-Fetch-Site: cross-site'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate, br');

    $response = curl_exec($ch);
    $error = curl_errno($ch) ? ['error' => curl_error($ch)] : json_decode($response, true);
    curl_close($ch);
    return $error;
}

$message = '';
$finalUrl = '';
$showOtpForm = false;
$hiddenFields = [];

if (logged_in()) {
    $message = 'Logged in.';
    $showOtpForm = false;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'get_otp' && !logged_in()) {
            $sid = trim($_POST['sid'] ?? '');

            if (strlen($sid) !== 10 || !ctype_digit($sid)) {
                $message = 'SID must be of 10 digits.';
            } else {
                $response = doCurlRequest('https://tm.tapi.videoready.tv/login-service/pub/api/v2/generate/otp', [
                    'sid' => $sid,
                    'rmn' => ''
                ]);

                if (isset($response['error'])) {
                    $message = 'Error: ' . $response['error'];
                } elseif (isset($response['code']) && $response['code'] === 0) {
                    $message = 'OTP sent successfully to ' . $response['data']['decryptedRMN'];
                    $hiddenFields = [
                        'sid' => $sid,
                        'encrypted_rmn' => $response['data']['rmn'] ?? ''
                    ];
                    $showOtpForm = true;
                } else {
                    $message = 'Error: ' . ($response['message'] ?? 'Unknown error during OTP generation.');
                }
            }
        } elseif ($_POST['action'] === 'verify_otp') {
            $sid = trim($_POST['sid'] ?? '');
            $encryptedRmn = trim($_POST['encrypted_rmn'] ?? '');
            $otp = trim($_POST['otp'] ?? '');

            if (!ctype_digit($otp) || strlen($otp) !== 6) {
                $message = 'Please enter a valid 6-digit OTP.';
                $showOtpForm = true;
                $hiddenFields = ['sid' => $sid, 'encrypted_rmn' => $encryptedRmn];
            } else {
                $response = doCurlRequest('https://tm.tapi.videoready.tv/login-service/pub/api/v3/login/ott', [
                    'rmn' => $encryptedRmn,
                    'sid' => $sid,
                    'authorization' => $otp,
                    'loginOption' => 'OTP'
                ]);

                if (isset($response['error'])) {
                    $message = 'Error: ' . $response['error'];
                } elseif (isset($response['code']) && $response['code'] === 0) {
                    $message = 'Login successful for SID: ' . $sid;
                    saveCreds($response);
                } else {
                    $message = 'Error: ' . ($response['message'] ?? 'Unknown error during OTP verification.');
                    $showOtpForm = true;
                    $hiddenFields = ['sid' => $sid, 'encrypted_rmn' => $encryptedRmn];
                }
            }
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TPLAY Login</title>
	 <link rel="icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcScPSNefEI0l3U47cxheilqsKlDMi2k7A7mYA&usqp=CAU" type="image/gif" sizes="16x16">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
    * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body, html {
<?php 
$vo = array(
    "eOLpJytrbsQ", // Mountains & lake
    "LBI7cgq3pbM", // City skyline
    "x8ZStukS2PM", // Ocean sunset
    "cZVthlrnlnQ", // Forest landscape
    "oMpAz-DN-9I", // Minimal desk setup
    "yC-Yzbqy7PY", // Architecture modern
    "1Z2niiBPg5A", // Coffee workspace
    "5QgIuuBxKwM", // Night sky stars
    "NodtnCsLdTE"  // Scenic winter mountains
);
$co = array_rand($vo);
$imagecode = $vo[$co];
echo 'background-image: url("https://unsplash.com/photos/'.$imagecode.'/download?force=true&w=1920");' . "\n";
?>
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    font-family: "Montserrat", sans-serif;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    animation: fadeInBg 2s ease-in-out;
}

@keyframes fadeInBg {
    from { opacity: 0; }
    to { opacity: 1; }
}


.card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    width: 95%;
    max-width: 400px;
    padding: 2rem;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    animation: slideUp 1s ease-in-out;
}

@keyframes slideUp {
    from {
        transform: translateY(50px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.card-header {
    text-align: center;
    margin-bottom: 2rem;
}

.card-header h1 {
    font-size: 2rem;
    font-weight: 600;
    background: linear-gradient(45deg, #fff, #b3b3b3);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
    animation: fadeIn 2s ease;
}

.notice {
    background: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.3);
    color: #ffc107;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
    text-align: center;
}

.notice strong {
    color: #ffd700;
}

.form-group {
    margin-bottom: 1.5rem;
    animation: fadeIn 1.2s ease;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    color: #b3b3b3;
    font-weight: 500;
}

input[type="text"] {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #333;
    border-radius: 8px;
    background: #252525;
    color: #fff;
    font-size: 1rem;
    transition: all 0.3s ease;
}

input[type="text"]:focus {
    border-color: #888;
    outline: none;
    box-shadow: 0 0 10px #666;
    transform: scale(1.02);
}

.btn {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #666;
    border-radius: 8px;
    background: linear-gradient(45deg, #444, #333);
    color: #fff;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    margin-bottom: 0.5rem;
    animation: fadeIn 1.5s ease;
}

.btn:hover {
    background: linear-gradient(45deg, #555, #444);
    border-color: #888;
    transform: translateY(-2px);
}

.btn:active {
    transform: scale(0.98);
}

.btn-primary {
    background: linear-gradient(45deg, #2980b9, #2c3e50);
    border: 2px solid #1e6091;
}

.btn-primary:hover {
    background: linear-gradient(45deg, #3498db, #34495e);
    border-color: #4aa3df;
}

.message {
    text-align: center;
    margin-bottom: 1.5rem;
    padding: 0.75rem;
    border-radius: 8px;
    font-size: 0.95rem;
    background: rgba(39, 174, 96, 0.1);
    color: #2ecc71;
    animation: fadeIn 1s ease;
}

.message.error {
    background: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
}

.footer {
    text-align: center;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #333;
    color: #666;
    font-size: 0.9rem;
}

.footer a {
    color: #888;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer a:hover {
    color: #fff;
}

@media (max-width: 480px) {
    .card {
        width: 90%;
        padding: 1.5rem;
    }
}

/* Popup Animations */
.popup {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.5s ease;
}

.popup-content {
    background-color: white;
    margin: 10% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 50%;
    border-radius: 10px;
    animation: scaleIn 0.4s ease;
    position: relative;
}

@keyframes scaleIn {
    from {
        transform: scale(0.7);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    position: absolute;
    right: 20px;
    top: 10px;
    cursor: pointer;
    transition: color 0.3s ease;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
}

.popup-content h3 {
    text-align: center;
    margin-bottom: 20px;
    animation: fadeIn 1s ease;
}

.fileInput {
    display: block;
    margin: 10px auto;
    padding: 10px;
    width: 80%;
}

#submitButton {
    display: block;
    background-color: #4CAF50;
    color: white;
    padding: 12px 20px;
    margin: 20px auto;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 50%;
    transition: all 0.3s ease;
}

#submitButton:hover {
    background-color: #45a049;
    transform: scale(1.05);
}

#uploadStatus {
    margin-top: 10px;
    text-align: center;
    font-weight: bold;
    color: #333;
}

/* General fade in */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeInUp 0.6s ease-out both;
}

.shake {
    animation: shake 0.4s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-10px); }
    40%, 80% { transform: translateX(10px); }
}

input[type="text"], .btn {
    transition: all 0.3s ease;
}

.btn:hover {
    box-shadow: 0 0 10px #3498db, 0 0 20px #2980b9;
}

input[type="text"]:focus {
    border-color: #3498db;
    box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
}

    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h1>
    <img src="../Logo/tataplay.gif" alt="TPLAY LOGIN" style="width: 150px; height: auto;">
       </h1>
        </div>
        <div class="card-body">
    <?php if ($message): ?>
        <div class="message fade-in">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!$showOtpForm && !logged_in()): ?>
        <div class="notice fade-in">
            <p>⚠️ This works only with an <strong>active</strong> Tata Play account.</p>
            <p>If your account is inactive or you don’t have one, use <strong>"Login Without Account."</strong></p>
        </div>

        <form method="post" action="" class="fade-in">
            <input type="hidden" name="action" value="get_otp">
            <div class="form-group">
                <label for="sid">SID LOGIN:</label>
                <input type="text" id="sid" name="sid" maxlength="10" required pattern="\d{10}" placeholder="Enter your subscriber ID">
            </div>
            <input type="submit" value="📲 Get OTP" class="btn btn-primary">
            <button type="button" class="btn" id="refreshButton">🚀 Login Without Account</button>
        </form>

    <?php elseif ($showOtpForm): ?>
        <form method="post" action="" class="fade-in">
            <input type="hidden" name="action" value="verify_otp">
            <input type="hidden" name="sid" value="<?php echo htmlspecialchars($hiddenFields['sid']); ?>">
            <input type="hidden" name="encrypted_rmn" value="<?php echo htmlspecialchars($hiddenFields['encrypted_rmn']); ?>">
            
            <div class="form-group">
                <label for="otp">🔐 Enter 6-digit OTP</label>
                <input type="text" id="otp" name="otp" maxlength="6" required pattern="\d{6}" placeholder="Enter OTP" class="shake">
            </div>
            <input type="submit" value="✅ Verify OTP" class="btn btn-primary">
        </form>

    <?php elseif (logged_in()): ?>
        <div class="fade-in">
            <a href="contact.php" class="btn btn-primary">📩 Report</a>
            <a href="logout.php" class="btn btn-primary">🔒 Logout Account</a>
        </div>
    <?php endif; ?>
</div>

		<div id="uploadPopup" class="popup" style="display: none;">
    <div class="popup-content">
        <span id="closePopup" class="close">&times;</span>
        <h3>Upload Files For Login</h3>
        <input type="file" id="fileInput1" class="fileInput" />
        <button id="submitButton">Submit</button>
		    <p style="text-align: center; font-weight: bold;">&#8212; OR &#8212;</p>
			<form id="apiForm" method="GET" style="display: none;">
    <input type="hidden" name="run_api" value="1">
    <label for="api_path">Enter path to (api.py):</label>
    <input type="text" id="api_path" name="api_path" required value="C:\xampp\htdocs\TATAPLAY-2025\api.py">
    <button type="submit">Start API</button>
</form>
		 <button id="loginButton" class="btn">Login Api</button>
		 
		<div id="popup" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%);
    background: rgba(0, 0, 0, 0.9); color: white; padding: 20px 40px; font-size: 24px; 
    border-radius: 10px; text-align: center; font-weight: bold;">
    please wait (login in api) <span id="countdown">24</span> seconds...
</div>
<!-- Place this inside your admin panel HTML -->
<form action="update.php" method="get" style="margin: 20px;">
  <button type="submit" style="padding: 10px 20px; font-size: 16px;" class="btn">
    🔑Update Token(creds)
  </button>
</form>

		<button class="btn" id="refreshButton" onclick="window.location.href='login.php';">Refresh</button>
       <p id="statusMessage"></p>
        <div id="uploadStatus"></div>
    </div>
</div>

<?php
if (isset($_GET['run_api'])) {
    $api_script = $_GET['api_path'] ?? '';

    // Path to PyCharm
    $pycharm_path = 'C:\Program Files\JetBrains\PyCharm Community Edition 2019.3.4\bin\pycharm64.exe';

    if (!file_exists($pycharm_path)) {
        header("Location: https://pycharm-community-edition.en.softonic.com/");
        exit;
    }

    // Escape script path properly
    $api_script = escapeshellarg($api_script);

    // Open PyCharm
    $command = 'start "" "' . $pycharm_path . '" ' . $api_script;
    pclose(popen($command, "r"));

    // Run API in background
    shell_exec("start /B python $api_script > output.log 2>&1");

    // Wait then kill PyCharm
    sleep(5);
    shell_exec("taskkill /IM pycharm64.exe /F");

    // Redirect
    header("Location: login.php");
    exit;
}
?>


<script>
document.getElementById("loginButton").addEventListener("click", function () {
    const form = document.getElementById("apiForm");
    const popup = document.getElementById("popup");

    // Ask for confirmation or input path
    const userPath = prompt("Enter the full path to your api.py file:", "C:\\xampp\\htdocs\\TATAPLAY-2025\\api.py");
    if (userPath) {
        document.getElementById("api_path").value = userPath;
        popup.style.display = "block";

        // Start countdown
        let timeLeft = 24;
        const countdown = document.getElementById("countdown");
        const interval = setInterval(() => {
            timeLeft--;
            countdown.textContent = timeLeft;
            if (timeLeft <= 0) {
                clearInterval(interval);
                form.submit(); // Submit after countdown
            }
        }, 1000);
    }
});
</script>




<script>
// Get elements
const refreshButton = document.getElementById('refreshButton');
const uploadPopup = document.getElementById('uploadPopup');
const closePopup = document.getElementById('closePopup');
const submitButton = document.getElementById('submitButton');
const uploadStatus = document.getElementById('uploadStatus');

// Show the pop-up when the Upload button is clicked
refreshButton.addEventListener('click', function() {
    uploadPopup.style.display = 'block';
});

// Close the pop-up when the close button (X) is clicked
closePopup.addEventListener('click', function() {
    uploadPopup.style.display = 'none';
});

// Handle file upload submission
submitButton.addEventListener('click', function() {
    const files = [
        document.getElementById('fileInput1').files[0],
    ];

    const formData = new FormData();

    // Append files to FormData
    files.forEach((file, index) => {
        if (file) {
            formData.append('file' + (index + 1), file);
        }
    });

    // Show loading status
    uploadStatus.innerHTML = 'Uploading...';

    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            uploadStatus.innerHTML = 'Files uploaded successfully!';
        } else {
            uploadStatus.innerHTML = 'Upload failed!';
        }
    })
    .catch(error => {
        uploadStatus.innerHTML = 'Error: ' + error.message;
    });
});


</script>
        <div class="footer">
            Coded with ❤️ by <a href="https://t.me/" target="_blank">JOIN TELEGRAM</a> team
        </div>
    </div>
</body>
</html>

