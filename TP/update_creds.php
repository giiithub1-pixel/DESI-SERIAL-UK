<?php
// Input from form
$newRefreshToken = $_POST['refreshToken'] ?? '';
$newExpiresIn = $_POST['expiresIn'] ?? '';
$newAccessToken = $_POST['accessToken'] ?? '';

// File path
$credsFile = 'app/creds';

// Validate
if (!$newRefreshToken || !$newExpiresIn || !$newAccessToken) {
    die("❌ All fields are required.");
}

// Ensure directory exists
if (!is_dir('app')) {
    mkdir('app', 0777, true);
}

// Full structure including entitlements
$data = [
    "code" => 0,
    "message" => "Logged in successfully.",
    "data" => [
        "refreshToken" => $newRefreshToken,
        "expiresIn" => $newExpiresIn,
        "refreshTokenThreshold" => 5,
        "accessToken" => $newAccessToken,
        "pubnubChannel" => "sub_1334085352",
        "forceChangePwd" => false,
        "isFirstTimeLoggedIn" => false,
        "isPromotionEnable" => false,
        "promotionStartDate" => null,
        "promotionEndDate" => null,
        "encryptedPassword" => "",
        "userDetails" => [
            "sid" => "1334085352",
            "sName" => "Bharathi KM",
            "rmn" => "9535656902",
            "isPremium" => false,
            "isPVR" => false,
            "acStatus" => "ACTIVE",
            "entitlements" => array_map(fn($pkgId) => [
                "type" => "",
                "pkgId" => $pkgId,
                "status" => null
            ], [
                "1000000035", "1000000045", "1000000080", "1000000164", "1000000216",
                "1000000222", "1000000246", "1000000247", "1000000257", "1000000261",
                "1000000263", "1000000265", "1000000270", "1000000278", "1000000283",
                "1000000342", "1000000363", "1000000364", "1000000432", "1000000433",
                "1000000436", "1000000474", "1000000477", "1000000501", "1000000508",
                "1000000520", "1000000538", "1000000616", "1000000660", "1000000661",
                "1000000672", "1000000695", "1000000945", "1000001023", "1000001274",
                "1000001368", "1000001544", "1000001685", "1000001562", "1000000968"
            ]),
            "devices" => [],
            "primaryDeviceSubStatus" => "",
            "dndStatus" => null,
            "lastRechargeStatus" => "Done",
            "lastRechargeTimeStamp" => "2024-12-22T04:38:24.000+00:00"
        ],
        "userProfile" => [
            "id" => "b30bc3f4-48d6-4904-8941-7264573b6470",
            "subscriberId" => "1334085352",
            "profileName" => "Bharathi KM",
            "ageGroup" => "35-45",
            "gender" => "Female",
            "profilePic" => null,
            "isDefaultProfile" => true,
            "isKidsProfile" => false,
            "isDeleted" => false,
            "languages" => [[
                "id" => 7,
                "name" => "English",
                "localizedName" => null
            ]],
            "categories" => [
                ["id" => 9, "title" => "Entertainment", "localizedTitle" => null],
                ["id" => 7, "title" => "Kids", "localizedTitle" => null],
                ["id" => 15, "title" => "Others", "localizedTitle" => null],
                ["id" => 8, "title" => "Regional", "localizedTitle" => null],
                ["id" => 26, "title" => "Shopping", "localizedTitle" => null]
            ],
            "appProfileLanguage" => ""
        ],
        "deviceRegError" => null,
        "deviceDetails" => [
            "deviceName" => "Bharathi KM_WINDOWS_OPERA_116_PC"
        ],
        "isHD" => true
    ],
    "localizedMessage" => "Logged in successfully."
];

// Write to file with pretty JSON format
file_put_contents($credsFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo '
<!-- Bootstrap CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body {
    background-color: #f0f8ff;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .fade-in {
    animation: fadeIn 0.8s ease-out;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>

<div class="card text-center p-4 fade-in shadow-lg" style="background-color: #e6f7ff; border-radius: 12px;">
  <p class="text-success fs-4 fw-bold mb-3">✅ Login successfully.</p>
  <a href="index.php" class="btn btn-primary px-4 py-2">Back to home</a>
</div>
';
?>

