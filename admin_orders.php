<?php
session_start();

require_once 'koneksi.php';

// Cek login admin
if (!isset($_SESSION['username']) || $_SESSION['username'] != 'admin') {
    header("Location: login.php");
    exit;
}

// ==================== AKTIFKAN PESANAN ====================
if (isset($_POST['update_status_id'])) {

    $id = intval($_POST['update_status_id']);

    $sql = "UPDATE pemesanan
            SET status='aktif'
            WHERE id=?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {

        $_SESSION['pesan'] =
            "Status pemesanan ID $id berhasil diubah menjadi AKTIF.";

    } else {

        $_SESSION['pesan'] =
            "Gagal mengubah status: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);

    header("Location: admin_orders.php");
    exit;
}


// ==================== HAPUS PESANAN ====================
if (isset($_GET['cancel_id'])) {

    $cancel_id = intval($_GET['cancel_id']);

    $hapus = mysqli_query(
        $conn,
        "DELETE FROM pemesanan WHERE id = $cancel_id"
    );

    if ($hapus) {

        $_SESSION['pesan'] =
            "Pemesanan ID $cancel_id berhasil dihapus.";

    } else {

        $_SESSION['pesan'] =
            "Gagal menghapus data: "
            . mysqli_error($conn);
    }

    header("Location: admin_orders.php");
    exit;
}


// ==================== HAPUS OTOMATIS PENDING ====================
$now = date('Y-m-d H:i:s');

mysqli_query(
    $conn,

    "DELETE FROM pemesanan

    WHERE status='pending'

    AND metode_pembayaran NOT LIKE '%cash%'

    AND TIMESTAMPDIFF(
        MINUTE,
        created_at,
        '$now'
    ) > 60"
);


// ==================== AMBIL DATA ====================
$query = mysqli_query(

    $conn,

    "SELECT *

     FROM pemesanan

     ORDER BY id DESC"

);

?>

<!DOCTYPE html>

<html>

<head>

<title>Riwayat Pemesanan Admin</title>

<style>

body{

background:#0f0f0f;

color:#f5deb3;

font-family:Georgia,serif;

padding:20px;

}

h2{

text-align:center;

color:#ffd700;

font-size:32px;

}

.menu-top{

display:flex;

justify-content:space-between;

margin-bottom:25px;

}

.menu-top a button{

background:#000;

color:#ffd700;

border:1px solid #ffd700;

padding:10px 20px;

cursor:pointer;

}

.menu-top a button:hover{

background:#222;

}

.pesan{

text-align:center;

font-weight:bold;

color:#ffd700;

margin-bottom:20px;

}

table{

width:100%;

border-collapse:collapse;

background:#1a1a1a;

}

th,td{

padding:14px;

border:1px solid #333;

text-align:center;

}

th{

background:#222;

color:#ffd700;

}

td{

color:#f0e68c;

}

button.update{

background:#ffd700;

border:none;

padding:7px 12px;

font-weight:bold;

cursor:pointer;

}

button.update:hover{

background:#ffcc00;

}

a{

color:#ffd700;

text-decoration:none;

}

a:hover{

text-decoration:underline;

}

</style>

</head>

<body>

<h2>Riwayat Pemesanan</h2>

<div class="menu-top">

<a href="admin_users.php">

<button>Daftar User</button>

</a>

<a href="logout.php">

<button>Logout</button>

</a>

</div>

<?php

if(isset($_SESSION['pesan'])){

echo '<div class="pesan">'
     . $_SESSION['pesan'] .
     '</div>';

unset($_SESSION['pesan']);

}

?>

<?php if($query && mysqli_num_rows($query)>0): ?>

<table>

<tr>

<th>ID</th>

<th>User ID</th>

<th>Nama</th>

<th>No HP</th>

<th>Check In</th>

<th>Check Out</th>

<th>Tipe Kamar</th>

<th>Jumlah Kamar</th>

<th>Total Harga</th>

<th>Status</th>

<th>Waktu Tersisa</th>

<th>Aksi</th>

</tr>

<?php while($row = mysqli_fetch_assoc($query)): ?>

<?php

// Ambil data langsung dari database

$jumlah_kamar = $row['kamar'];

$total_harga = $row['total_harga'];


// Hitung waktu sisa

$waktu_sisa = "-";

if($row['status'] == 'pending'){

$created = new DateTime($row['created_at']);

$nowTime = new DateTime();

$selisih =

$nowTime->getTimestamp()

-

$created->getTimestamp();

$sisa = 3600 - $selisih;

if($sisa > 0){

$menit = floor($sisa/60);

$detik = $sisa%60;

$waktu_sisa = sprintf(

"%02d:%02d",

$menit,

$detik

);

}else{

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

<td><?= ucfirst($row['tipe_kamar']) ?></td>

<td><?= $jumlah_kamar ?></td>

<td>

Rp <?= number_format(

$total_harga,

0,

',',

'.'

) ?>

</td>

<td>

<?= ucfirst($row['status']) ?>

</td>

<td>

<?= $waktu_sisa ?>

</td>

<td>

<?php if($row['status']=='pending'): ?>

<form method="POST">

<input
type="hidden"
name="update_status_id"
value="<?= $row['id'] ?>">

<button
type="submit"
class="update">

Aktifkan

</button>

</form>

<?php endif; ?>

<br><br>

<a

href="admin_orders.php?cancel_id=<?= $row['id'] ?>"

onclick="return confirm('Yakin hapus data ini ?')">

Hapus

</a>

</td>

</tr>

<?php endwhile; ?>

</table>

<?php else: ?>

<p style="text-align:center;color:#ffd700;">

Tidak ada data pemesanan.

</p>

<?php endif; ?>

</body>

</html>
