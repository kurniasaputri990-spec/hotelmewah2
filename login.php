<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Cek jika admin login manual
    if ($email === 'admin@123' && $password === 'admin123') {
        $_SESSION['username'] = 'admin';
        header("Location: admin_orders.php");
        exit;
    }

    // Login user biasa dari database
    $query = mysqli_query($conn, "SELECT * FROM users2 WHERE email = '$email'");
    $user = mysqli_fetch_assoc($query);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Email atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Hotel Sederhana</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap');

    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(to bottom right, #0a0a0a, #1f1f1f);
      font-family: 'Playfair Display', serif;
      color: #f5deb3;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background-color: #111;
      padding: 3rem;
      border-radius: 16px;
      border: 2px solid #d4af37;
      box-shadow: 0 0 20px rgba(212, 175, 55, 0.3);
      width: 100%;
      max-width: 420px;
    }

    h2 {
      text-align: center;
      margin-bottom: 2rem;
      color: #d4af37;
      text-shadow: 0 0 8px #b8860b;
      font-size: 2rem;
    }

    label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold;
      color: #f5deb3;
    }

    input {
      width: 100%;
      padding: 0.75rem;
      border-radius: 10px;
      border: 1.5px solid #d4af37;
      background-color: #1a1a1a;
      color: #f3f1ec;
      font-size: 1rem;
      margin-bottom: 1.3rem;
      box-shadow: inset 0 0 6px #b8860b;
    }

    input:focus {
      outline: none;
      border-color: #f5c518;
      box-shadow: 0 0 10px #f5c518;
    }

    .password-container {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      top: 30%;
      right: 10px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #d4af37;
      font-size: 1.0rem;
      user-select: none;
    }

    button {
      width: 100%;
      padding: 0.9rem;
      background-color: #d4af37;
      color: #111;
      border: none;
      border-radius: 12px;
      font-weight: bold;
      font-size: 1.1rem;
      box-shadow: 0 6px 16px rgba(212, 175, 55, 0.4);
      cursor: pointer;
      transition: 0.3s ease;
    }

    button:hover {
      background-color: #f5c518;
      box-shadow: 0 8px 20px rgba(245, 197, 24, 0.5);
      color: #000;
    }

    .error {
      background-color: #7f1d1d;
      padding: 1rem;
      border-radius: 10px;
      color: #ffe4e6;
      margin-bottom: 1.5rem;
      text-align: center;
      font-weight: 600;
    }

    .register-link {
      display: block;
      margin-top: 1.5rem;
      text-align: center;
      color: #d4af37;
      text-decoration: none;
      font-size: 0.95rem;
    }

    .register-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Login ke Hotel Sederhana</h2>
    
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST" action="">
      <label>Email</label>
      <input type="email" name="email" required placeholder="Masukkan email Anda">

      <label>Password</label>
      <div class="password-container">
        <input type="password" name="password" id="password" required placeholder="Masukkan password Anda">
        <span class="toggle-password" onclick="togglePassword()">👁️</span>
      </div>

      <button type="submit">Masuk</button>
      <a href="register.php" class="register-link">Belum punya akun? Daftar</a>
    </form>
  </div>

  <script>
    function togglePassword() {
      const field = document.getElementById('password');
      const toggle = document.querySelector('.toggle-password');
      if (field.type === 'password') {
        field.type = 'text';
        toggle.textContent = '🙈';
      } else {
        field.type = 'password';
        toggle.textContent = '👁️';
      }
    }
  </script>
</body>
</html>
