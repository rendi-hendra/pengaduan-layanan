<?php
session_start();
require_once "config.php";

/* =========================
   KONEKSI POSTGRESQL
   ========================= */
$conn = pg_connect("host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_pass");
if (!$conn) {
  die("Koneksi PostgreSQL gagal");
}
$_SESSION['tipe_form'] = 'keluhan';
/* =========================
   PROSES SUBMIT
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (
    empty($_POST['tanggal']) ||
    empty($_POST['pukul']) ||
    empty($_POST['alamat']) ||
    empty($_POST['no_hp']) ||
    empty($_POST['masukan'])
  ) {
    $error_message = "Tanggal, pukul, alamat, dan nomor HP wajib diisi.";
  } else {

    $sql = "INSERT INTO keluhan (
      alamat, no_hp, masukan, pukul, tanggal
    ) VALUES (
      $1, $2, $3, $4, $5
    )";

    $params = [
      $_POST['alamat'],
      $_POST['no_hp'],
      $_POST['masukan'] ?? null,
      $_POST['pukul'],
      $_POST['tanggal']
    ];

    $result = pg_query_params($conn, $sql, $params);

    if ($result) {
      header("Location: thank-you.php");
      exit;
    } else {
      $error_message = "Gagal menyimpan data ke database.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Keluhan & Saran Pasien - Rumah Sakit Eka Husada</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="container">

    <div class="header">
      <div class="header-logo">
        <div class="logo-container">
          <img src="images/logo.png" class="logo-icon">
        </div>
        <div class="header-text">
          <h1 class="namars">RUMAH SAKIT EKA HUSADA</h1>
          <h5 class="alamatrs"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill maps" viewBox="0 0 16 16">
              <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
            </svg> Jl. Raya Kediri No. 123, Kediri</h5>
          <p>FORMULIR KELUHAN & SARAN</p>
        </div>
      </div>
    </div>

    <div class="form-container">

      <?php if (isset($error_message)): ?>
        <div class="error-message">
          ⚠️ <?= htmlspecialchars($error_message) ?>
        </div>
      <?php endif; ?>

      <form method="POST">

        <h2 class="section-title">Identitas Pasien</h2>

        <!-- TANGGAL -->
        <div class="form-row">
          <div class="form-field">
            <label>Tanggal *</label>
            <input
              type="date"
              name="tanggal"
              id="tanggal"
              readonly
              required>
          </div>
        </div>

        <!-- PUKUL -->
        <div class="form-row">
          <div class="form-field">
            <label>Pukul *</label>
            <input
              class="form-profil"
              type="time"
              name="pukul"
              id="pukul"
              readonly
              required>
          </div>
        </div>

        <!-- ALAMAT -->
        <div class="form-row">
          <div class="form-field">
            <label>Alamat *</label>
            <input class="form-profil" name="alamat" required placeholder="Contoh: Jl. Merdeka No. 123">
          </div>
        </div>

        <!-- NO HP -->
        <div class="form-row">
          <div class="form-field">
            <label>Nomor HP *</label>
            <input
              class="form-profil"
              type="text"
              name="no_hp"
              required
              placeholder="Contoh: 081234567890"
              inputmode="numeric"
              pattern="[0-9]{12,}"
              minlength="12"
              maxlength="15"
              oninput="this.value = this.value.replace(/[^0-9]/g, '')">
          </div>
        </div>

        <h2 class="section-title">Keluhan / Saran</h2>

        <div class="form-row">
          <div class="form-field">
            <label>Uraian Keluhan / Saran</label>
            <textarea name="masukan" placeholder="Tuliskan keluhan atau saran Anda..."></textarea>
          </div>
        </div>

        <button type="submit" class="submit-btn">Kirim & Selesai</button>

      </form>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const now = new Date();

          // TANGGAL (YYYY-MM-DD)
          document.getElementById('tanggal').value =
            now.toISOString().slice(0, 10);

          // JAM (HH:MM)
          document.getElementById('pukul').value =
            now.toTimeString().slice(0, 5);
        });
      </script>
    </div>
  </div>
</body>
<footer class="footer">
  <div class="footer-content">
    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 15px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white" stroke="none">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
      </svg>
      <h2>TERIMA KASIH</h2>
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="white" stroke="none">
        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
      </svg>
    </div>
    <p>Atas partisipasi Anda dalam mengisi kuesioner ini</p>

    <div class="footer-contact">
      <h3>Saluran Keluhan & Saran</h3>
      <div class="contact-cards">
        <div class="contact-card">
          <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 5px; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
              <polyline points="22 6 12 13 2 6" />
            </svg>
            Kotak Saran
          </h3>
          <p>Tersedia di lobi rumah sakit</p>
        </div>
        <div class="contact-card">
          <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 5px; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
            </svg>
            Humas
          </h3>
          <p>Hubungi bagian Humas kami</p>
        </div>
        <div class="contact-card">
          <h3 style="font-size: 16px; font-weight: bold; margin-bottom: 5px; display: flex; align-items: center; justify-content: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
            </svg>
            WA Pengaduan
          </h3>
          <p>082244125457</p>
        </div>
      </div>
    </div>

    <div style="margin-top: 20px; font-size: 14px; color: rgba(255,255,255,0.8);">
      <p>Rumah Sakit Eka Husada - Melayani Dengan Sepenuh Hati</p>
    </div>
  </div>
</footer>

</html>
