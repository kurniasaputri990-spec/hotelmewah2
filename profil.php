<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);

// Ambil data pemesanan
$pemesanan_query = mysqli_query($conn, "
    SELECT * FROM pemesanan 
    WHERE user_id = '$user_id' 
    AND status IN ('aktif', 'selesai') 
    ORDER BY tanggal_pesan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Akun Saya - Hotel Mewah</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Playfair Display', serif;
      background-color: #0e0e0e;
      color: #d4af37;
      padding: 2rem;
    }

    .container {
      max-width: 1200px;
      margin: auto;
      background: #1a1a1a;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(212, 175, 55, 0.25);
      padding: 2.5rem;
    }

    h2, h3 {
      text-align: center;
      color: #ffd700;
      margin-bottom: 2rem;
      text-shadow: 0 0 10px #d4af37aa;
    }

    .user-info {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 1.3rem;
      font-weight: bold;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #121212;
      box-shadow: 0 0 20px rgba(212, 175, 55, 0.15);
      border-radius: 12px;
      overflow: hidden;
    }

    th, td {
      padding: 12px 16px;
      text-align: center;
      border-bottom: 1px solid #2d2d2d;
    }

    th {
      background-color: #2c2c2c;
      color: #ffd700;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 1px;
    }

    td {
      color: #f5f5dc;
      font-size: 0.95rem;
    }

    tr:hover {
      background-color: #2e2e2e;
      color: #fff;
    }

    .no-history {
      text-align: center;
      font-style: italic;
      margin: 2rem 0;
      color: #bfa84f;
    }

    .btn-group {
      text-align: center;
      margin-top: 2rem;
    }

    .btn-group a {
      text-decoration: none;
      background-color: #d4af37;
      color: #1a1a1a;
      padding: 12px 24px;
      margin: 0 12px;
      border-radius: 25px;
      font-weight: bold;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(212, 175, 55, 0.5);
    }

    .btn-group a:hover {
      background-color: #ffd700;
      box-shadow: 0 6px 18px rgba(255, 215, 0, 0.6);
      color: #000;
    }

    @media (max-width: 768px) {
      table, th, td {
        font-size: 0.85rem;
      }

      .btn-group a {
        display: block;
        margin: 10px auto;
        width: 80%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>👤 Akun Saya</h2>
    <div class="user-info">
      Username: <?= htmlspecialchars($user['username']) ?>
    </div>

    <h3>🧾 Riwayat Pemesanan</h3>
    <?php if (mysqli_num_rows($pemesanan_query) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Check-in</th>
          <th>Check-out</th>
          <th>Tamu</th>
          <th>Kamar</th>
          <th>Tipe</th>
          <th>Pembayaran</th>
          <th>Total Harga</th>
          <th>Tanggal Pesan</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $no = 1;
        while ($row = mysqli_fetch_assoc($pemesanan_query)):
            $checkin = new DateTime($row['checkin']);
            $checkout = new DateTime($row['checkout']);
            $durasi = $checkout->diff($checkin)->days;

            switch (strtolower($row['tipe_kamar'])) {
                case 'standar': $harga_per_malam = 300000; break;
                case 'deluxe':  $harga_per_malam = 500000; break;
                case 'suite':   $harga_per_malam = 800000; break;
                default:        $harga_per_malam = 0;
            }

            $jumlah_kamar = $row['kamar'];
            $total_harga = $jumlah_kamar * $durasi * $harga_per_malam;
        ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['checkin']) ?></td>
          <td><?= htmlspecialchars($row['checkout']) ?></td>
          <td><?= htmlspecialchars($row['tamu']) ?></td>
          <td><?= htmlspecialchars($row['kamar']) ?></td>
          <td><?= htmlspecialchars(ucfirst($row['tipe_kamar'])) ?></td>
          <td><?= htmlspecialchars(ucfirst($row['metode_pembayaran'])) ?></td>
          <td>Rp <?= number_format($total_harga, 0, ',', '.') ?></td>
          <td><?= htmlspecialchars($row['tanggal_pesan']) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p class="no-history">Belum ada riwayat pemesanan yang aktif atau selesai.</p>
    <?php endif; ?>

    <div class="btn-group">
      <a href="booking.php">← Kembali ke Booking</a>
      <a href="logout.php">Keluar</a>
    </div>
  </div>
</body>
</html>
