<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; font-family: 'Inter', sans-serif; }
    body {
      margin: 0;
      height: 100vh;
      background: linear-gradient(135deg, #1e3cff, #2f80ed);
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-card {
      background: #fff;
      width: 380px;
      border-radius: 14px;
      padding: 28px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    h2 {
      text-align: center;
      letter-spacing: 3px;
      margin: 10px 0 20px;
    }
    .form-group { margin-bottom: 14px; }
    .form-group input {
      width: 100%;
      padding: 12px 14px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 14px;
    }
    .btn {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      border: none;
      font-weight: 600;
      cursor: pointer;
      background: #2d9cdb;
      color: #fff;
    }
    .footer {
      text-align: center;
      font-size: 12px;
      margin-top: 14px;
      color: #777;
    }
  </style>
</head>
<body>

<div class="login-card">
  <img src="img/logo-SMA-Tadika-Pertiwi.png" height="42" style="display:block;margin:auto">
  <h2>LOGIN</h2>

  <form action="proses_login.php" method="POST">
    <div class="form-group">
      <input type="text" name="login_id" placeholder="Username Guru / NIS Siswa" required>
    </div>

    <div class="form-group">
      <input type="password" name="password" placeholder="Password" required>
    </div>

    <button type="submit" class="btn">LOGIN</button>
  </form>

  <div class="footer">© SMA TADIKA PERTIWI</div>
</div>

</body>
</html>
