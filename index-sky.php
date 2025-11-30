<?php
// Generate a daily random ID (changes automatically every day)
$daily_key = md5(date("Y-m-d"));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Index</title>

    <style>
        .cart {
            background: linear-gradient(90deg, #ff8c00, #ff6a00);
            padding: 15px 35px;
            border-radius: 20px;
            color: white;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            cursor: pointer;
            border: none;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.3);
        }
    </style>

</head>
<body>

<a href="home-sky.php?key=<?php echo $daily_key; ?>">
    <button class="cart">Sky Sp Cricket</button>
</a>

</body>
</html>