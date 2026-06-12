<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID pesanan tidak ditemukan.";
    exit;
}

$id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil data pesanan
$query = mysqli_query($conn, "SELECT * FROM pemesanan WHERE id = '$id' AND user_id = '$user_id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data pesanan tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Konfirmasi Pemesanan</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f3f4f6;
      padding: 2rem;
    }

    .container {
      max-width: 600px;
      margin: auto;
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      text-align: center;
    }

    h2 {
      color: #1e40af;
    }

    .details {
      text-align: left;
      margin-top: 1.5rem;
    }

    .details p {
      margin: 0.5rem 0;
    }

    a {
      display: inline-block;
      margin-top: 1.5rem;
      padding: 0.7rem 1.2rem;
      background-color: #1e40af;
      color: white;
      text-decoration: none;
      border-radius: 8px;
    }

    a:hover {
      background-color: #1e3a8a;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>✅ Pemesanan Berhasil!</h2>
    <p>Terima kasih, <strong><?= htmlspecialchars($data['nama_lengkap']) ?></strong></p>

    <div class="details">
      <p><strong>Check-in:</strong> <?= $data['checkin'] ?></p>
      <p><strong>Check-out:</strong> <?= $data['checkout'] ?></p>
      <p><strong>Jumlah Tamu:</strong> <?= $data['tamu'] ?></p>
      <p><strong>Jumlah Kamar:</strong> <?= $data['kamar'] ?></p>
      <p><strong>Jenis Kamar:</strong> <?= ucfirst($data['tipe_kamar']) ?></p>
      <p><strong>Metode Pembayaran:</strong> <?= ucfirst($data['metode_pembayaran']) ?></p>
    </div>

    <a href="profil.php">Lihat Riwayat Pemesanan</a>
    <br>
    <a href="index.php" style="margin-top:10px;">Kembali ke Beranda</a>
    <br>
  </div>
</body>
</html>