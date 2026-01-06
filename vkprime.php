
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(#132700 0%, #000000 100%);
            padding: 20px;
            color: white;
        }

        .container {
            max-width: 900px;
            width: 85%;
            text-align: center;
        }

        h2 {
            color: white;
            margin-bottom: 15px;
            font-size: 42px;
            font-weight: 700;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
        }

        p {
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 40px;
            font-size: 20px;
            line-height: 1.6;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .video-wrapper {
            position: relative;
            padding-bottom: 156.25%;
            height: 0;
            overflow: hidden;
            border-radius: 15px;
            margin-bottom: 40px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.4);
            background: rgba(0, 0, 0, 0.2);
        }

        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 15px;
        }

        .btn {
            background: white;
            color: #667eea;
            padding: 18px 50px;
            border: none;
            border-radius: 35px;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
            background: #f8f9ff;
        }

        .btn:active {
            transform: translateY(-1px);
        }

        .loading {
            display: none;
            margin-top: 25px;
            color: white;
            font-size: 18px;
            font-weight: 600;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .loading::after {
            content: '...';
            animation: dots 1.5s steps(4, end) infinite;
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60%, 100% { content: '...'; }
        }

        @media (max-width: 768px) {
            h2 {
                font-size: 32px;
            }

            p {
                font-size: 18px;
                margin-bottom: 30px;
            }

            .btn {
                padding: 16px 40px;
                font-size: 18px;
            }

            .video-wrapper {
                margin-bottom: 30px;
            }
        }

        @media (max-width: 480px) {
            h2 {
                font-size: 26px;
            }

            p {
                font-size: 16px;
            }

            .btn {
                padding: 14px 35px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>💐 Desi Serial 💐</h2>
        <p>°See video get your Password!</p>
        
        <div class="video-wrapper">
            <iframe
                src="https://www.youtube.com/embed/1TiSRD7gNcs"
                title="demo video how to get password"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen>
            </iframe>
        </div>

        <button class="btn" onclick="goToHome()">Get Password</button>

        <div class="loading" id="loading">Loading</div>
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
