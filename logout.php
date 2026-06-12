<?php 
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Keluar - Hotel Mewah</title>
  <meta http-equiv="refresh" content="3;url=login.php"> <!-- Redirect otomatis ke login -->
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap');

    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(to bottom, #0d0d0d, #1a1a1a);
      color: #f5deb3;
      font-family: 'Playfair Display', serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow: hidden;
    }

    .logout-box {
      background-color: #111;
      padding: 3rem 4rem;
      border-radius: 18px;
      border: 2px solid #d4af37;
      box-shadow: 0 0 24px rgba(212, 175, 55, 0.3);
      text-align: center;
      max-width: 500px;
    }

    h1 {
      font-size: 2.4rem;
      color: #d4af37;
      margin-bottom: 1.2rem;
      text-shadow: 0 0 12px #b8860b;
    }

    p {
      font-size: 1.1rem;
      margin-bottom: 0.5rem;
      color: #f3f1ec;
    }

    .note {
      font-size: 0.9rem;
      color: #aaa;
      margin-top: 1.5rem;
      font-style: italic;
    }

    .gold-spinner {
      margin: 1.5rem auto 0 auto;
      width: 40px;
      height: 40px;
      border: 4px solid #d4af37;
      border-top: 4px solid transparent;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }
  </style>
</head>
<body>
  <div class="logout-box">
    <h1>Terima Kasih</h1>
    <p>Anda telah berhasil keluar dari akun.</p>
    <div class="gold-spinner"></div>
    <p class="note">Mengalihkan ke halaman login...</p>
  </div>
</body>
</html>
