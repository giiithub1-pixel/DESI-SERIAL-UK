<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
        }

        p {
            color: #555;
            margin-bottom: 20px;
        }

        .btn {
            background: #667eea;
            color: white;
            padding: 14px 35px;
            border: none;
            border-radius: 25px;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #764ba2;
        }

        .loading {
            display: none;
            margin-top: 15px;
            color: #667eea;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <div>
        <h2>💐 Welcome To Desi Serial 💐</h2>
        <p>See te he Video How to get your Password </p>
        
       <div> <iframe
    width="100%"
    height="460"
    src="https://www.youtube.com/embed/3PPoxHiQ4lY"
    title="demo video how to get password "
    frameborder="0"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
    allowfullscreen>
</iframe></div><br>

        <button class="btn" onclick="goToHome()">Get Password</button>

        <div class="loading" id="loading">Loading...</div>
    </div>

    <script>
        function goToHome() {
            document.getElementById('loading').style.display = 'block';

            fetch('get_link.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loading').style.display = 'none';

                    if (data.success) {
                        window.location.href = data.shortened_url;
                    } else {
                        alert('Error: ' + (data.error || 'Failed to generate link'));
                    }
                })
                .catch(() => {
                    document.getElementById('loading').style.display = 'none';
                    alert('Server connection error');
                });
        }
    </script>

</body>
</html>
