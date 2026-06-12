<?php 
session_start();
include 'koneksi.php';

// Pastikan yang mengakses halaman ini adalah admin
if (!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil data semua user dari tabel 'users'
$query = mysqli_query($conn, "SELECT id, username, email, created_at FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar User - Admin</title>
    <style>
        body {
            background-color: #0f0f0f;
            color: #f5deb3;
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #ffd700;
            font-size: 32px;
            text-shadow: 2px 2px 5px rgba(255, 215, 0, 0.3);
            margin-bottom: 30px;
        }

        .top-buttons {
            text-align: center;
            margin-bottom: 30px;
        }

        .top-buttons a button {
            background-color: #000;
            color: #ffd700;
            border: 1px solid #ffd700;
            padding: 10px 20px;
            margin: 0 10px;
            font-family: 'Georgia', serif;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.2);
            transition: 0.3s ease;
        }

        .top-buttons a button:hover {
            background-color: #1c1c1c;
            color: #fff8dc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1a1a1a;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.1);
        }

        th, td {
            padding: 14px;
            text-align: center;
            border: 1px solid #333;
        }

        th {
            background-color: #222;
            color: #ffd700;
            text-shadow: 1px 1px 2px #000;
        }

        td {
            color: #f0e68c;
        }
    </style>
</head>
<body>

    <h2>Daftar User Terdaftar</h2>

    <div class="top-buttons">
        <a href="admin_orders.php"><button>Kembali ke Pesanan</button></a>
        <a href="logout.php"><button>Logout</button></a>
    </div>

    <?php if ($query && mysqli_num_rows($query) > 0): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Terdaftar Sejak</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($query)) { ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['username'] ?></td>
            <td><?= $row['email'] ?></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
        <?php } ?>
    </table>
    <?php else: ?>
        <p style="text-align: center; color: #ffcc00;">Tidak ada data user ditemukan.</p>
    <?php endif; ?>

</body>
</html>
