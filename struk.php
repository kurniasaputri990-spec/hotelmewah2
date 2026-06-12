<?php 
session_start();
include 'koneksi.php';

if (!isset($_GET['id'])) {
    echo "ID tidak ditemukan.";
    exit;
}

$id = (int) $_GET['id'];
$query = "SELECT * FROM pemesanan2 WHERE id = $id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "Data pemesanan tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Struk Pemesanan</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Playfair Display', serif;
      background-color: #0e0e0e;
      color: #f5c518;
      padding: 2rem;
      margin: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .struk {
      background: #1a1a1a;
      padding: 3rem;
      max-width: 700px;
      width: 100%;
      border-radius: 18px;
      border: 2px solid #d4af37;
      box-shadow: 0 0 30px rgba(212, 175, 55, 0.3), inset 0 0 20px rgba(212, 175, 55, 0.15);
    }

    h2 {
      text-align: center;
      color: #ffd700;
      margin-bottom: 2rem;
      font-size: 2.5rem;
      text-shadow: 0 0 15px #d4af37;
    }

    p {
      margin: 0.7rem 0;
      font-size: 1.1rem;
      letter-spacing: 0.03em;
      color: #ffe98a;
      text-shadow: 0 0 3px #bfa347;
    }

    .btn {
      margin-top: 2.5rem;
      text-align: center;
    }

    .btn a {
      text-decoration: none;
      background: #d4af37;
      color: #121212;
      padding: 0.9rem 1.5rem;
      border-radius: 10px;
      font-weight: bold;
      letter-spacing: 0.05em;
      transition: background 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 0 5px 15px rgba(212, 175, 55, 0.6);
    }

    .btn a:hover {
      background: #ffd700;
      box-shadow: 0 8px 20px rgba(255, 215, 0, 0.85);
      color: #000;
    }

    em {
      display: block;
      margin-top: 1.8rem;
      text-align: center;
      color: #f5deb3;
      font-style: italic;
      text-shadow: 0 0 4px #bfa347;
    }

    @media print {
      .btn {
        display: none;
      }
      body {
        background-color: white;
        color: black;
        text-shadow: none;
      }
    }

  </style>
</head>
<body>

<div class="struk">
  <h2>Struk Pemesanan Hotel</h2>
  <p><strong>Nama:</strong> <?= htmlspecialchars($data['nama_lengkap']) ?></p>
  <p><strong>Nomor HP:</strong> <?= htmlspecialchars($data['no_hp']) ?></p>
  <p><strong>Check-in:</strong> <?= $data['checkin'] ?></p>
  <p><strong>Check-out:</strong> <?= $data['checkout'] ?></p>
  <p><strong>Jumlah Tamu:</strong> <?= $data['tamu'] ?></p>
  <p><strong>Jumlah Kamar:</strong> <?= $data['kamar'] ?></p>
  <p><strong>Tipe Kamar:</strong> <?= ucfirst($data['tipe_kamar']) ?></p>
  <p><strong>Metode Pembayaran:</strong> <?= $data['metode_pembayaran'] ?></p>
  <p><strong>Total Harga:</strong> Rp <?= number_format($data['total_harga'], 0, ',', '.') ?></p>
  <p><strong>Status:</strong> <?= ucfirst($data['status']) ?></p>
  <em>Tunjukkan struk ini saat check-in sebagai bukti pemesanan.</em>

  <div class="btn">
    <a href="index.php">Kembali ke Beranda</a>
    <a href="#" onclick="window.print()">Download Struk</a>
  </div>


</body>
</html>
