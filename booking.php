<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = htmlspecialchars($_POST['nama']);
    $no_hp    = htmlspecialchars($_POST['no_hp']);
    $checkin  = trim($_POST['checkin'] ?? '');
    $checkout = trim($_POST['checkout'] ?? '');
    $tamu     = (int) ($_POST['tamu'] ?? 1);
    $kamar    = (int) ($_POST['kamar'] ?? 1);
    $tipe     = $_POST['tipe'] ?? 'standard';

    // ✅ FIX 1: Validasi tanggal sebelum dipakai
    if (empty($checkin) || empty($checkout)) {
        $error = "Tanggal check-in dan check-out harus diisi.";
    } elseif (!strtotime($checkin) || !strtotime($checkout)) {
        $error = "Format tanggal tidak valid.";
    } else {
        $metode = $_POST['pembayaran'] ?? '';

        // ✅ FIX 2: Ambil detail pembayaran sesuai metode yang dipilih
        if ($metode === 'transfer') {
            $pembayaran = $_POST['bank_detail'] ?? 'Transfer Bank';
        } elseif ($metode === 'ewallet') {
            $pembayaran = $_POST['ewallet_detail'] ?? 'E-Wallet';
        } elseif ($metode === 'cash') {
            $pembayaran = 'Cash';
        } else {
            $pembayaran = 'Tidak diketahui';
        }

        $start     = new DateTime($checkin);
        $end       = new DateTime($checkout);
        $lama_inap = $start->diff($end)->days;
        if ($lama_inap <= 0) $lama_inap = 1;

        switch (strtolower($tipe)) {
            case 'standard': $harga_per_malam = 500000;  break;
            case 'deluxe':   $harga_per_malam = 750000;  break;
            case 'suite':    $harga_per_malam = 1200000; break;
            default:         $harga_per_malam = 400000;  break;
        }

        $total_harga = $lama_inap * $kamar * $harga_per_malam;

        if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] == 0) {
            $upload_dir = 'uploads/';
            $nama_file  = $_FILES['bukti']['name'];
            $tmp_file   = $_FILES['bukti']['tmp_name'];
            $path_file  = $upload_dir . time() . '_' . basename($nama_file);

            if (move_uploaded_file($tmp_file, $path_file)) {
                $status = 'aktif';
                $query = "INSERT INTO pemesanan 
                    (user_id, nama_lengkap, no_hp, checkin, checkout, tamu, kamar, tipe_kamar, metode_pembayaran, total_harga, bukti_pembayaran, status)
                    VALUES 
                    ('$user_id', '$nama', '$no_hp', '$checkin', '$checkout', '$tamu', '$kamar', '$tipe', '$pembayaran', '$total_harga', '$path_file', '$status')";

                if (mysqli_query($conn, $query)) {
                    header("Location: profil.php?pesan=berhasil");
                    exit;
                } else {
                    $error = "Gagal menyimpan data: " . mysqli_error($conn);
                }
            } else {
                $error = "Gagal mengunggah bukti pembayaran.";
            }
        } else {
            $status = 'pending';
            $query = "INSERT INTO pemesanan 
                (user_id, nama_lengkap, no_hp, checkin, checkout, tamu, kamar, tipe_kamar, metode_pembayaran, total_harga, status)
                VALUES 
                ('$user_id', '$nama', '$no_hp', '$checkin', '$checkout', '$tamu', '$kamar', '$tipe', '$pembayaran', '$total_harga', '$status')";

            if (mysqli_query($conn, $query)) {
                $id_pemesanan = mysqli_insert_id($conn);
                header("Location: struk.php?id=$id_pemesanan");
                exit;
            } else {
                $error = "Gagal menyimpan data: " . mysqli_error($conn);
            }
        }
    }
} else {
    $checkin  = $_GET['checkin']  ?? '';
    $checkout = $_GET['checkout'] ?? '';
    $tamu     = (int) ($_GET['tamu']   ?? 1);
    $kamar    = (int) ($_GET['kamar']  ?? 1);
    $tipe     = $_GET['tipe']     ?? 'standard';

    // ✅ FIX 3: Validasi tanggal di GET juga sebelum new DateTime
    if (!empty($checkin) && !empty($checkout) && strtotime($checkin) && strtotime($checkout)) {
        $start     = new DateTime($checkin);
        $end       = new DateTime($checkout);
        $lama_inap = $start->diff($end)->days;
        if ($lama_inap <= 0) $lama_inap = 1;
    } else {
        $lama_inap = 1;
    }

    switch (strtolower($tipe)) {
        case 'standard': $harga_per_malam = 500000;  break;
        case 'deluxe':   $harga_per_malam = 750000;  break;
        case 'suite':    $harga_per_malam = 1200000; break;
        default:         $harga_per_malam = 400000;  break;
    }

    $total_harga = $lama_inap * $kamar * $harga_per_malam;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Form Pemesanan - Hotel Mewah</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display&display=swap');

    *, *::before, *::after { box-sizing: border-box; }

    body {
      font-family: 'Playfair Display', serif;
      background: linear-gradient(135deg, #0a0a0a, #1a1a1a);
      padding: 2rem;
      color: #d4af37;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-shadow: 0 0 8px rgba(212, 175, 55, 0.9);
    }

    .form-container {
      background: #121212;
      max-width: 620px;
      width: 100%;
      padding: 3.2rem 3.6rem;
      border-radius: 20px;
      box-shadow: 0 0 15px rgba(212, 175, 55, 0.7), inset 0 0 20px rgba(212, 175, 55, 0.15);
      border: 2.5px solid #d4af37;
      transition: box-shadow 0.4s ease;
    }
    .form-container:hover {
      box-shadow: 0 0 25px rgba(255, 215, 0, 0.95), inset 0 0 30px rgba(255, 215, 0, 0.3);
    }

    h2 {
      font-size: 3rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 2.8rem;
      color: #f5c518;
      letter-spacing: 0.15em;
      text-shadow: 0 0 10px #f5c518, 0 0 25px #d4af37;
      user-select: none;
    }

    label {
      display: block;
      margin-top: 1.6rem;
      font-weight: 700;
      color: #f0db7d;
      text-shadow: 0 0 3px #b8860b;
      font-size: 1.15rem;
      letter-spacing: 0.05em;
      user-select: none;
    }

    input, select {
      width: 100%;
      padding: 1rem 1.2rem;
      margin-top: 0.6rem;
      border-radius: 16px;
      border: 2px solid #d4af37;
      background-color: #222222;
      color: #f5deb3;
      font-size: 1.15rem;
      font-family: 'Playfair Display', serif;
      box-shadow: inset 0 0 15px rgba(212, 175, 55, 0.5);
      transition: all 0.3s ease;
    }

    input::placeholder { color: #d4af37aa; font-style: italic; }

    input:focus, select:focus {
      outline: none;
      border-color: #f5c518;
      box-shadow: 0 0 18px #f5c518, inset 0 0 20px #f5c518;
      background-color: #2c2c2c;
      color: #ffeb99;
    }

    .ringkasan {
      background: #2f2f2f;
      padding: 1.8rem 2.2rem;
      border-radius: 18px;
      margin-bottom: 2.5rem;
      box-shadow: inset 0 0 18px #d4af37;
      color: #ffe97d;
      font-weight: 700;
      font-size: 1.2rem;
      letter-spacing: 0.05em;
      text-shadow: 0 0 4px #b8860b;
      user-select: none;
      line-height: 1.6;
    }
    .ringkasan p { margin: 0.35rem 0; }

    button {
      margin-top: 3rem;
      width: 100%;
      padding: 1.3rem 0;
      background-color: #d4af37;
      color: #121212;
      font-weight: 900;
      font-size: 1.3rem;
      border: none;
      border-radius: 20px;
      cursor: pointer;
      box-shadow: 0 6px 16px rgba(212, 175, 55, 0.85), inset 0 -3px 8px #b8860b;
      transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.14em;
      font-family: 'Playfair Display', serif;
      user-select: none;
    }
    button:hover {
      background-color: #f5c518;
      color: #1b1b1b;
      box-shadow: 0 8px 24px rgba(245, 197, 24, 1), inset 0 -3px 10px #f5c518;
    }

    .error {
      background-color: #7f1a1a;
      padding: 1.2rem 1.8rem;
      border-radius: 18px;
      margin-bottom: 1.5rem;
      text-align: center;
      font-weight: 700;
      color: #ffbebe;
      box-shadow: 0 0 16px #f87171;
      letter-spacing: 0.04em;
      user-select: none;
    }

    .sub-payment {
      background: #292929;
      border: 2px solid #d4af37;
      border-radius: 18px;
      padding: 1.6rem 2rem;
      margin-top: 1.8rem;
      color: #f5deb3;
      box-shadow: inset 0 0 18px #b8860b;
      display: none;
      font-family: 'Playfair Display', serif;
      user-select: none;
    }

    .payment-info {
      background: #1b1b1b;
      padding: 1.2rem 1.5rem;
      margin-top: 1rem;
      border-radius: 14px;
      border: 1.5px dashed #d4af37;
      color: #ffea8f;
      box-shadow: inset 0 0 12px #d4af37;
      font-size: 1.05rem;
      text-shadow: 0 0 4px #b8860b;
      user-select: none;
    }
    .payment-info p { margin: 0.45rem 0; font-weight: 600; }

    #qr-container { margin-top: 1.4rem; text-align: center; }
    #qr-image {
      max-width: 160px;
      border-radius: 16px;
      box-shadow: 0 0 24px #d4af37;
      filter: drop-shadow(0 0 8px #f5c518);
      user-select: none;
    }

    a {
      display: block;
      margin-top: 1.6rem;
      text-align: center;
      color: #d4af37;
      font-weight: 600;
      text-decoration: none;
      font-family: 'Playfair Display', serif;
      font-size: 1rem;
      letter-spacing: 0.06em;
      user-select: none;
      transition: color 0.3s ease;
    }
    a:hover { color: #f5c518; text-decoration: underline; }

    .custom-file-label {
      display: inline-block;
      padding: 0.8rem 1.6rem;
      background-color: #d4af37;
      color: #121212;
      border-radius: 10px;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 4px 12px rgba(212, 175, 55, 0.5);
      transition: background 0.3s ease;
      text-align: center;
      user-select: none;
    }
    .custom-file-label:hover { background-color: #f5c518; }

    @media (max-width: 640px) {
      .form-container { padding: 2.2rem 2.8rem; }
      h2 { font-size: 2.4rem; }
      input, select { font-size: 1rem; }
      button { font-size: 1.15rem; }
    }
  </style>
</head>
<body>
  <main class="form-container" role="main" aria-labelledby="form-title">
    <h2 id="form-title">
      <a href="profil.php" style="all: unset; cursor: pointer; display: inline-block;">
        Konfirmasi Pemesanan
      </a>
    </h2>

    <?php if (!empty($error)) echo "<div class='error' role='alert'>" . htmlspecialchars($error) . "</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
      <div class="ringkasan">
        <p><strong>Check-in:</strong> <?= htmlspecialchars($checkin) ?></p>
        <p><strong>Check-out:</strong> <?= htmlspecialchars($checkout) ?></p>
        <p><strong>Jumlah Tamu:</strong> <?= $tamu ?></p>
        <p><strong>Jumlah Kamar:</strong> <?= $kamar ?></p>
        <p><strong>Tipe Kamar:</strong> <?= ucfirst(htmlspecialchars($tipe)) ?></p>
        <p><strong>Lama Inap:</strong> <?= $lama_inap ?> malam</p>
        <p><strong>Total Harga:</strong> Rp <?= number_format($total_harga, 0, ',', '.') ?></p>
      </div>

      <input type="hidden" name="checkin"  value="<?= htmlspecialchars($checkin) ?>">
      <input type="hidden" name="checkout" value="<?= htmlspecialchars($checkout) ?>">
      <input type="hidden" name="tamu"     value="<?= $tamu ?>">
      <input type="hidden" name="kamar"    value="<?= $kamar ?>">
      <input type="hidden" name="tipe"     value="<?= htmlspecialchars($tipe) ?>">

      <label for="nama">Nama Lengkap</label>
      <input type="text" id="nama" name="nama" required autocomplete="name" placeholder="Masukkan nama lengkap Anda" />

      <label for="no_hp">Nomor HP</label>
      <input type="tel" id="no_hp" name="no_hp" required autocomplete="tel" placeholder="Masukkan nomor HP Anda" />

      <label for="pembayaran">Metode Pembayaran</label>
      <select name="pembayaran" id="pembayaran" required onchange="tampilkanSubPembayaran()">
        <option value="">-- Pilih Metode --</option>
        <option value="transfer">Transfer Bank</option>
        <option value="ewallet">E-Wallet</option>
        <option value="cash">Bayar di Tempat</option>
      </select>

      <!-- Transfer -->
      <div id="sub-transfer" class="sub-payment" aria-hidden="true">
        <label for="bank">Pilih Bank</label>
        <select name="bank_detail" id="bank">
          <option value="Transfer - BCA">BCA</option>
          <option value="Transfer - BRI">BRI</option>
          <option value="Transfer - Mandiri">Mandiri</option>
        </select>
        <div class="payment-info" aria-label="Informasi Rekening Bank">
          <p><strong>No. Rekening:</strong> 1234567890</p>
          <p><strong>Atas Nama:</strong> Hotel Mewah Indonesia</p>
        </div>
      </div>

      <!-- E-Wallet -->
      <div id="sub-ewallet" class="sub-payment" aria-hidden="true">
        <label for="ewallet">Pilih E-Wallet</label>
        <select name="ewallet_detail" id="ewallet" onchange="tampilkanQRCode(this.value)">
          <option value="E-Wallet - OVO">OVO</option>
          <option value="E-Wallet - DANA">DANA</option>
          <option value="E-Wallet - GoPay">GoPay</option>
        </select>
        <div class="payment-info" aria-label="Informasi E-Wallet">
          <p><strong>Nomor:</strong> 0812-3456-7890</p>
          <p><strong>Atas Nama:</strong> Hotel Mewah Indonesia</p>
          <div id="qr-container" aria-live="polite" aria-atomic="true">
            <img id="qr-image" src="barcode.jpg" alt="QR Code Pembayaran" />
          </div>
        </div>
      </div>

      <!-- Cash -->
      <div id="sub-cash" class="sub-payment" aria-hidden="true">
        <div class="payment-info" aria-label="Informasi Pembayaran di Tempat">
          <p><strong>Pembayaran dilakukan saat check-in di lokasi.</strong></p>
        </div>
      </div>

      <!-- Notifikasi batas waktu -->
      <div id="notifikasi-batas-waktu" style="display:none; margin-top: 1.5rem; padding: 1.2rem; background-color: #3d2c00; color: #ffdd57; border-radius: 14px; box-shadow: inset 0 0 12px #f5c518; font-weight: bold; text-align: center; text-shadow: 0 0 4px #b8860b;">
        Anda belum mengunggah bukti pembayaran.<br>
        Pemesanan Anda tetap diproses dan diberi waktu <strong>2 jam</strong>.<br>
        Jika dalam 2 jam tidak dibayar, pemesanan akan dibatalkan otomatis.<br>
        <div style="margin-top: 1rem;">
          <button type="button" id="btn-ok"     style="padding: 0.6rem 1.4rem; margin: 0.3rem; font-weight: bold; border-radius: 8px; border: none; background-color: #d4af37; color: #121212; cursor: pointer;">OK</button>
          <button type="button" id="btn-cancel" style="padding: 0.6rem 1.4rem; margin: 0.3rem; font-weight: bold; border-radius: 8px; border: none; background-color: #7f1a1a; color: #fff0f0; cursor: pointer;">Cancel</button>
        </div>
      </div>

      <!-- Upload bukti -->
      <div id="bukti-pembayaran-group" style="margin-top: 1.6rem; display: none;">
        <label for="bukti">Bukti Pembayaran (JPG/PNG/PDF):</label><br>
        <label for="bukti" class="custom-file-label">Pilih File</label>
        <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" id="bukti" style="display: none;">
        <span id="nama-file" style="display: block; margin-top: 0.5rem; font-size: 0.9rem; color: #f5deb3;"></span>
      </div>

      <button type="submit" aria-label="Konfirmasi Pesanan">Konfirmasi Pesanan</button>
      <a href="index.php">Kembali ke Beranda</a>
    </form>
  </main>

  <script>
    // Custom file input label
    document.querySelector('.custom-file-label').addEventListener('click', function () {
      document.getElementById('bukti').click();
    });

    document.getElementById('bukti').addEventListener('change', function () {
      const fileName = this.files[0] ? this.files[0].name : "";
      document.getElementById('nama-file').textContent = fileName;

      // Sembunyikan notifikasi kalau file sudah dipilih
      const metode = document.getElementById("pembayaran").value;
      if ((metode === 'transfer' || metode === 'ewallet') && this.files.length > 0) {
        document.getElementById("notifikasi-batas-waktu").style.display = 'none';
      }
    });

    function tampilkanSubPembayaran() {
      const metode   = document.getElementById('pembayaran').value;
      const transfer = document.getElementById('sub-transfer');
      const ewallet  = document.getElementById('sub-ewallet');
      const cash     = document.getElementById('sub-cash');
      const buktiGroup = document.getElementById('bukti-pembayaran-group');
      const buktiInput = document.getElementById('bukti');
      const notif    = document.getElementById("notifikasi-batas-waktu");

      // Sembunyikan semua dulu
      [transfer, ewallet, cash].forEach(el => {
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
      });
      notif.style.display = 'none';

      if (metode === 'transfer') {
        transfer.style.display = 'block';
        transfer.setAttribute('aria-hidden', 'false');
        buktiGroup.style.display = 'block';
        buktiInput.required = false;
      } else if (metode === 'ewallet') {
        ewallet.style.display = 'block';
        ewallet.setAttribute('aria-hidden', 'false');
        buktiGroup.style.display = 'block';
        buktiInput.required = false;
      } else if (metode === 'cash') {
        cash.style.display = 'block';
        cash.setAttribute('aria-hidden', 'false');
        buktiGroup.style.display = 'none';
        buktiInput.required = false;
      } else {
        buktiGroup.style.display = 'none';
        buktiInput.required = false;
      }
    }

    function tampilkanQRCode(value) {
      document.getElementById("qr-image").src = "barcode.jpg";
    }

    // ✅ FIX 4: Script notifikasi hanya satu, tidak duplikat
    const form = document.querySelector("form");
    let formShouldSubmit = false;

    function showNotifikasi() { document.getElementById('notifikasi-batas-waktu').style.display = 'block'; }
    function hideNotifikasi() { document.getElementById('notifikasi-batas-waktu').style.display = 'none'; }

    window.addEventListener('DOMContentLoaded', () => {
      document.getElementById("btn-ok").addEventListener("click", function () {
        formShouldSubmit = true;
        hideNotifikasi();
        form.submit();
      });
      document.getElementById("btn-cancel").addEventListener("click", function () {
        formShouldSubmit = false;
        hideNotifikasi();
      });
    });

    form.addEventListener("submit", function (e) {
      const metode = document.getElementById("pembayaran").value;
      const bukti  = document.getElementById("bukti");

      if (!formShouldSubmit && (metode === 'transfer' || metode === 'ewallet') && bukti.files.length === 0) {
        e.preventDefault();
        showNotifikasi();
      }
    });
  </script>
</body>
</html>
