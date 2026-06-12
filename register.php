<?php  
include 'koneksi.php';  
if ($_SERVER["REQUEST_METHOD"] == "POST") {     
    $username = htmlspecialchars($_POST['username']);     
    $email = htmlspecialchars($_POST['email']);     
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);      

    $cek = mysqli_query($conn, "SELECT * FROM users2 WHERE email = '$email'");     
    if (mysqli_num_rows($cek) > 0) {         
        $error = "Email sudah digunakan!";     
    } else {         
        $insert = "INSERT INTO users2 (username, email, password) VALUES ('$username', '$email', '$password')";         
        if (mysqli_query($conn, $insert)) {             
            header("Location: login.php?reg=success");         
        } else {             
            $error = "Gagal mendaftar: " . mysqli_error($conn);         
        }     
    } 
} 
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrasi - Hotel Mewah</title>
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
      margin-bottom: 0.4rem;
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
      right: 5px;
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

    a {
      display: block;
      text-align: center;
      margin-top: 1rem;
      color: #d4af37;
      text-decoration: none;
      font-size: 0.95rem;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Registrasi Akun</h2>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="">
      <label>Username</label>
      <input type="text" name="username" required placeholder="Masukkan nama Anda">

      <label>Email</label>
      <input type="email" name="email" required placeholder="Masukkan email Anda">

      <label>Password</label>
      <div class="password-container">
        <input type="password" name="password" id="password" required placeholder="Masukkan password">
        <span class="toggle-password" onclick="togglePassword()">👁️</span>
      </div>

      <button type="submit">Daftar</button>
      <a href="login.php">Sudah punya akun? Login</a>
    </form>
  </div>

  <script>
    function togglePassword() {
      const passwordField = document.getElementById('password');
      const toggleIcon = document.querySelector('.toggle-password');
      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.textContent = '🙈';
      } else {
        passwordField.type = 'password';
        toggleIcon.textContent = '👁️';
      }
    }
  </script>
</body>
</html>
