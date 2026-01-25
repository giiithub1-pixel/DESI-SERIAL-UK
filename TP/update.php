<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Creds Tokens</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

  <style>
    :root {
      --primary-glow: #00c6ff;
    }

    body {
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      height: 100vh;
      margin: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
      color: #fff;
    }

    .form-container {
      background: rgba(30, 30, 50, 0.4);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      border-radius: 16px;
      padding: 40px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 10px 35px rgba(0, 0, 0, 0.5);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .form-header h3 {
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .form-header p {
      color: #ccc;
      font-size: 0.9rem;
    }

    .input-group-text {
      background-color: rgba(255, 255, 255, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-right: none;
      color: var(--primary-glow);
      border-radius: .375rem 0 0 .375rem;
    }
    
    .form-control {
      background: rgba(255, 255, 255, 0.1);
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-left: none;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      background: rgba(255, 255, 255, 0.2);
      box-shadow: 0 0 10px var(--primary-glow);
      color: white;
      border-color: rgba(0, 198, 255, 0.5);
    }
    
    .form-control::placeholder {
      color: #bbb;
    }

    .btn {
        transition: all 0.3s ease-in-out;
        font-weight: 500;
        padding: 10px 0;
    }
    
    .btn-custom {
      background: linear-gradient(45deg, #0072ff, #00c6ff);
      border: none;
    }

    .btn-custom:hover {
      background: linear-gradient(45deg, #00c6ff, #0072ff);
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0, 198, 255, 0.4);
    }

    .btn-whatsapp {
      background-color: #25d366;
      border: none;
    }

    .btn-whatsapp:hover {
      background-color: #1ebe5d;
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(37, 211, 102, 0.4);
    }
    
    .btn-outline-light:hover {
        background-color: var(--primary-glow);
        border-color: var(--primary-glow);
        color: #1e3c72;
        transform: translateY(-2px);
    }

    .modal-content {
        background: #2a3a60;
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }
    .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }
    .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

  </style>
</head>
<body>
  
  
  <div class="form-container animate__animated animate__fadeInDown">
    <div class="form-header text-center mb-4">
      <h3>🔐 Secure Token Update</h3>
      <p>Paste the provided tokens below to refresh your credentials.</p>
    </div>
    
    <form action="update_creds.php" method="POST">
      
      <label class="form-label">Refresh Token</label>
      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-arrow-repeat"></i></span>
        <input type="text" class="form-control" name="refreshToken" required placeholder="Enter refresh token">
      </div>

      <label class="form-label">Expires In</label>
      <div class="input-group mb-3">
        <span class="input-group-text"><i class="bi bi-clock-history"></i></span>
        <input type="text" class="form-control" name="expiresIn" required placeholder="Enter expiration time (e.g., 3600)">
      </div>

      <label class="form-label">Access Token</label>
      <div class="input-group mb-4">
        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
        <input type="text" class="form-control" name="accessToken" required placeholder="Enter access token">
      </div>

      <div class="d-grid gap-2 mb-3">
        <button type="submit" class="btn btn-custom btn-lg"><i class="bi bi-check-circle-fill"></i> Update Token</button>
      </div>
      
      <div class="d-flex justify-content-between">
         <button type="button" class="btn btn-outline-light w-50 me-2" data-bs-toggle="modal" data-bs-target="#helpModal">
            <i class="bi bi-question-circle"></i> Help
         </button>
         <a href="https://wa.me/916362341104?text=Hi, please provide the Tata Play token" target="_blank" class="btn btn-whatsapp w-50 ms-2">
           <i class="bi bi-whatsapp"></i> Get Token
         </a>
      </div>
      
    </form>
  </div>

  <div class="modal fade" id="helpModal" tabindex="-1" aria-labelledby="helpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="helpModalLabel"><i class="bi bi-info-circle-fill"></i> How to Update Tokens</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>You will receive three pieces of information:</p>
          <ul>
            <li><strong>Refresh Token:</strong> A long string used to get a new access token.</li>
            <li><strong>Expires In:</strong> A number representing how many seconds the token is valid for.</li>
            <li><strong>Access Token:</strong> The main token used to access the service.</li>
          </ul>
          <p class="mb-0">Simply <strong>copy</strong> each value and <strong>paste</strong> it into the corresponding field on the form. Then, click the "Update Token" button.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Got It!</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>