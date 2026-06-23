<?php
session_start();

// Gunakan koneksi dari koneksi.php
require_once 'koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Proses update status ke AKTIF
if (isset($_POST['update_status_id'])) {

    $id = intval($_POST['update_status_id']);

    $sql = "UPDATE pemesanan2
            SET status = 'aktif'
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {

        $_SESSION['pesan'] =
            "Status pemesanan ID $id berhasil diubah menjadi AKTIF.";

    } else {

        $_SESSION['pesan'] =
            "Gagal mengubah status pemesanan: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);

    header("Location: admin_orders.php");
    exit;
}
// Batalkan pemesanan
if (isset($_GET['cancel_id'])) {
    $cancel_id = intval($_GET['cancel_id']);
    $query_cancel = mysqli_query($conn, "DELETE FROM pemesanan2 WHERE id = $cancel_id");

    if ($query_cancel) {
        $_SESSION['pesan'] = "Pemesanan ID $cancel_id berhasil dihapus.";
    } else {
        $_SESSION['pesan'] = "Gagal menghapus pemesanan. Error: " . mysqli_error($conn);
    }

    header("Location: admin_orders.php");
    exit;
}

// Hapus otomatis pemesanan pending lebih dari 1 jam
$now = date('Y-m-d H:i:s');
mysqli_query($conn, "
    DELETE FROM pemesanan2 
    WHERE status = 'pending' 
      AND metode_pembayaran NOT LIKE '%cash%' 
      AND TIMESTAMPDIFF(MINUTE, created_at, '$now') > 60
");

// Ambil semua data pemesanan
$query = mysqli_query($conn, "SELECT * FROM pemesanan2 ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Riwayat Pemesanan - Admin</title>
    <style>
        body { background-color: #0f0f0f; color: #f5deb3; font-family: 'Georgia', serif; padding: 20px; }
        h2 { text-align: center; color: #ffd700; font-size: 32px; margin-bottom: 30px; }
        .menu-top { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .menu-top a button {
            background-color: #000; color: #ffd700; border: 1px solid #ffd700;
            padding: 10px 20px; cursor: pointer;
        }
        .menu-top a button:hover { background-color: #1c1c1c; color: #fff8dc; }
        .pesan { text-align: center; color: #ffcc00; font-weight: bold; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; background-color: #1a1a1a; }
        th, td { padding: 14px; text-align: center; border: 1px solid #333; }
        th { background-color: #222; color: #ffd700; }
        td { color: #f0e68c; }
        a { color: #ffcc00; text-decoration: none; }
        a:hover { text-decoration: underline; color: #fffacd; }
        form { display: inline; }
        button.update {
            background-color: #ffd700; border: none; padding: 5px 10px;
            cursor: pointer; font-weight: bold;
        }
        button.update:hover { background-color: #ffcc00; }
    </style>
</head>
<body>

<h2>Riwayat Pemesanan</h2>

<div class="menu-top">
    <a href="admin_users.php"><button>Lihat Daftar User</button></a>
    <a href="logout.php"><button>Logout</button></a>
</div>

<?php if (isset($_SESSION['pesan'])) { ?>
    <div class="pesan"><?= $_SESSION['pesan']; ?></div>
    <?php unset($_SESSION['pesan']); ?>
<?php } ?>

<?php if ($query && mysqli_num_rows($query) > 0): ?>
<table>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Nama</th>
        <th>No HP</th>
        <th>Check-in</th>
        <th>Check-out</th>
        <th>Tipe</th>
        <th>Jumlah Kamar</th>
        <th>Status</th>
        <th>Total Harga</th>
        <th>Waktu Tersisa</th>
        <th>Aksi</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($query)) {
        // Hitung durasi
        $checkin = new DateTime($row['checkin']);
        $checkout = new DateTime($row['checkout']);
        $durasi = $checkout->diff($checkin)->days;

        // Harga berdasarkan tipe kamar
        switch (strtolower($row['tipe_kamar'])) {
            case 'standard': $harga_per_malam = 300000; break;
            case 'deluxe':  $harga_per_malam = 500000; break;
            case 'suite':   $harga_per_malam = 800000; break;
            default:        $harga_per_malam = 0;
        }

        $jumlah_kamar = isset($row['jumlah_kamar']) ? $row['jumlah_kamar'] : 1;
        $total_harga = $jumlah_kamar * $durasi * $harga_per_malam;

        // Hitung waktu sisa (jika pending)
        $waktu_sisa = "-";
        if ($row['status'] === 'pending') {
            $created = new DateTime($row['created_at']);
            $nowTime = new DateTime();
            $selisih = $nowTime->getTimestamp() - $created->getTimestamp();
            $sisa = 3600 - $selisih;
            if ($sisa > 0) {
                $menit = floor($sisa / 60);
                $detik = $sisa % 60;
                $waktu_sisa = sprintf("%02d:%02d", $menit, $detik);
            } else {
                $waktu_sisa = "Expired";
            }
        }
    ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['user_id'] ?></td>
        <td><?= $row['nama_lengkap'] ?></td>
        <td><?= $row['no_hp'] ?></td>
        <td><?= $row['checkin'] ?></td>
        <td><?= $row['checkout'] ?></td>
        <td><?= $row['tipe_kamar'] ?></td>
        <td><?= $jumlah_kamar ?></td>
        <td><?= ucfirst($row['status']) ?></td>
        <td>Rp <?= number_format($total_harga, 0, ',', '.') ?></td>
        <td><?= $waktu_sisa ?></td>
        <td>
            <?php if ($row['status'] === 'pending') { ?>
                <form method="POST">
                    <input type="hidden" name="update_status_id" value="<?= $row['id'] ?>">
                    <button type="submit" class="update">Aktifkan</button>
                </form>
            <?php } ?>
            <a href="admin_orders.php?cancel_id=<?= $row['id'] ?>" onclick="return confirm('Yakin batalkan pesanan ini?')">Hapus</a>
        </td>
    </tr>
    <?php } ?>
</table>
<?php else: ?>
    <p style="text-align:center; color:#ffcc00;">Tidak ada data pemesanan ditemukan.</p>
<?php endif; ?>

</body>
</html>
