<?php  
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Beranda Hotel</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Merriweather:wght@300;700&family=Open+Sans&display=swap');

    body {
      margin: 0;
      background: linear-gradient(135deg, #0d0b08, #362f1a);
      font-family: 'Merriweather', serif;
      color: #f5e6c4;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      background: #2e2300;
      padding: 1.5rem 3rem;
      box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
      border-bottom: 2px solid #d4af37;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    header h1 {
      margin: 0;
      font-size: 2rem;
      font-weight: 700;
      letter-spacing: 2px;
      color: #d4af37;
      text-shadow: 0 0 5px #d4af37;
    }

    nav {
      margin-top: 0.8rem;
    }

    nav a {
      color: #f5e6c4;
      margin-right: 30px;
      text-decoration: none;
      font-weight: 600;
      font-family: 'Open Sans', sans-serif;
      font-size: 1.1rem;
      transition: color 0.3s ease;
      padding-bottom: 4px;
      border-bottom: 2px solid transparent;
    }

    nav a:hover, nav a:focus {
      color: #d4af37;
      border-bottom: 2px solid #d4af37;
    }

    main {
      flex-grow: 1;
      padding: 3rem 1rem 4rem;
      max-width: 900px;
      margin: 0 auto;
    }

    .hotel-location {
      text-align: center;
      margin-top: 2rem;
      margin-bottom: 2rem;
      font-family: 'Open Sans', sans-serif;
    }

    .hotel-location h2 {
      color: #d4af37;
      text-shadow: 0 0 6px #d4af37;
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
    }

    .hotel-location p {
      font-size: 1.1rem;
      color: #f5e6c4;
    }

    .welcome {
      text-align: center;
      margin-bottom: 3rem;
      box-shadow: 0 0 25px 5px rgba(212, 175, 55, 0.5);
      border-radius: 20px;
      overflow: hidden;
      background: #1a1400;
    }

    .hotel-photo {
      width: 100%;
      max-height: 450px;
      object-fit: cover;
      display: block;
      border-radius: 20px;
      transition: transform 0.5s ease;
      cursor: pointer;
    }

    .hotel-photo:hover {
      transform: scale(1.05);
      box-shadow: 0 0 40px 10px rgba(212, 175, 55, 0.8);
    }

    .form-section {
      background: rgba(45, 35, 5, 0.85);
      padding: 2.5rem 3rem;
      border-radius: 20px;
      box-shadow: 0 0 30px 5px rgba(212, 175, 55, 0.6);
      font-family: 'Open Sans', sans-serif;
    }

    .form-section h2 {
      margin-bottom: 1.8rem;
      font-weight: 700;
      font-size: 2rem;
      color: #d4af37;
      text-shadow: 0 0 8px #d4af37;
      letter-spacing: 1.2px;
    }

    label {
      display: block;
      margin-top: 1.4rem;
      font-weight: 600;
      color: #f5e6c4;
      font-size: 1.05rem;
      letter-spacing: 0.03em;
    }

    input, select {
      width: 100%;
      padding: 0.7rem 1rem;
      margin-top: 0.3rem;
      border: none;
      border-radius: 12px;
      background: #3b2f00;
      color: #f5e6c4;
      font-size: 1rem;
      box-shadow: inset 2px 2px 5px rgba(0,0,0,0.7);
      transition: background-color 0.3s ease;
    }

    input:focus, select:focus {
      outline: none;
      background-color: #4d3f00;
      box-shadow: 0 0 10px #d4af37;
      color: #fff;
    }

    .fasilitas, .lokasi {
      margin-top: 2.5rem;
      color: #f5e6c4;
    }

    .fasilitas h3, .lokasi h3 {
      font-weight: 700;
      color: #d4af37;
      text-shadow: 0 0 6px #d4af37;
      margin-bottom: 1rem;
    }

    .fasilitas ul {
      list-style: none;
      padding-left: 1rem;
    }

    .fasilitas li {
      position: relative;
      padding-left: 25px;
      margin-bottom: 0.8rem;
      font-size: 1.1rem;
    }

    .fasilitas li::before {
      content: "✔";
      position: absolute;
      left: 0;
      top: 0;
      color: #d4af37;
      font-weight: 900;
      font-size: 1.3rem;
      text-shadow: 0 0 5px #d4af37;
    }

    button {
      margin-top: 3rem;
      width: 100%;
      padding: 1rem;
      background: linear-gradient(45deg, #d4af37, #a67c00);
      border: none;
      border-radius: 15px;
      font-size: 1.25rem;
      font-weight: 700;
      color: #1a1400;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.7);
      transition: background 0.3s ease, color 0.3s ease;
    }

    button:hover {
      background: linear-gradient(45deg, #a67c00, #d4af37);
      color: #fff;
      box-shadow: 0 6px 25px rgba(212, 175, 55, 1);
    }

    @media (max-width: 600px) {
      header, main {
        padding: 1rem 1.5rem;
      }

      .form-section {
        padding: 2rem 1.5rem;
      }

      nav a {
        margin-right: 15px;
        font-size: 1rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['username']); ?> 👋</h1>
    <nav>
      <a href="index.php">Beranda</a>
      <a href="booking.php">Booking</a>
      <a href="profil.php">Profil</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <div class="hotel-location">
    <h2>📍 Lokasi Hotel</h2>
    <p>Jl. Raya Pantai Indah No.88, Kuta Selatan, Bali 80361, Indonesia</p>
    <p><a href="https://www.google.com/maps?q=Jl.+Raya+Pantai+Indah+No.88,+Bali" target="_blank" style="color:#d4af37; text-decoration: underline;">Lihat di Google Maps</a></p>
  </div>

  <main>
    <div class="welcome">
      <img src="resort.jpeg" alt="Foto Hotel" class="hotel-photo" />
    </div>

    <div class="form-section">
      <h2>Pemesanan Hotel</h2>

      <form action="booking.php" method="GET">
        <label for="checkin">Tanggal Check-In</label>
        <input type="date" id="checkin" name="checkin" required />

        <label for="checkout">Tanggal Check-Out</label>
        <input type="date" id="checkout" name="checkout" required />

        <label for="tamu">Jumlah Tamu</label>
        <input type="number" id="tamu" name="tamu" min="1" value="1" required />

        <label for="kamar">Jumlah Kamar</label>
        <input type="number" id="kamar" name="kamar" min="1" value="1" required />

        <label for="tipe">Jenis Kamar</label>
        <select id="tipe" name="tipe">
          <option value="standard">Standard - Rp 500.000/malam</option>
          <option value="deluxe">Deluxe - Rp 750.000/malam</option>
          <option value="suite">Suite - Rp 1.200.000/malam</option>
        </select>

        <div class="fasilitas">
          <h3>Fasilitas Kamar:</h3>
          <ul id="fasilitas-list"></ul>
        </div>

        <div class="lokasi" id="lokasi-info">
          <h3>Lokasi Kamar:</h3>
          <p id="lokasi-text">-</p>
        </div>

        <button type="submit">Lanjut ke Pemesanan</button>
      </form>
    </div>
  </main>

  <script>
    const tipeSelect = document.getElementById("tipe");
    const fasilitasList = document.getElementById("fasilitas-list");
    const lokasiText = document.getElementById("lokasi-text");

    const kamarData = {
      standard: {
        fasilitas: [
          "WiFi Cepat",
          "AC",
          "TV Kabel",
          "Kamar Mandi Dalam"
        ],
        lokasi: "Tower A - Lantai 1-3, dekat lobby utama"
      },
      deluxe: {
        fasilitas: [
          "WiFi Cepat",
          "AC & Smart TV",
          "Mini Bar",
          "Kamar Mandi Dalam (Bathtub)",
          "Pemandangan Taman"
        
        ],
        lokasi: "Tower B - Lantai 4-6, menghadap kolam renang"
      },
      suite: {
        fasilitas: [
          "WiFi Cepat",
          "AC & Smart TV 55 inch",
          "Living Room",
          "Mini Bar",
          "Kamar Mandi Dalam (Bathtub & Jacuzzi) ",
          "Pemandangan Laut",
          "Sarapan Premium"
        ],
        lokasi: "Tower C - Penthouse, lantai paling atas dengan private lift"
      }
    };

    function updateInfo() {
      const selected = tipeSelect.value;
      const data = kamarData[selected];

      fasilitasList.innerHTML = "";
      data.fasilitas.forEach(item => {
        const li = document.createElement("li");
        li.textContent = item;
        fasilitasList.appendChild(li);
      });

      lokasiText.textContent = data.lokasi;
    }

    updateInfo();
    tipeSelect.addEventListener("change", updateInfo);
  </script>
</body>
</html>
