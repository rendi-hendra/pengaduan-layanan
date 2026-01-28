<?php
session_start();

require_once "config.php";

/* =========================
   KONEKSI POSTGRESQL (1x saja)
   ========================= */
$conn = pg_connect("host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_pass");
if (!$conn) {
  die("Koneksi PostgreSQL gagal: " . htmlspecialchars(pg_last_error()));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $required_fields = [
    'surveyDate',
    'surveyTime',
    'gender',
    'education',
    'nama_pasien',
    'alamat',
    'nomor_hp',
    'q1','q2','q3','q4','q5','q6','q7','q8','q9'
  ];

  $is_valid = true;
  foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || trim((string)$_POST[$field]) === '') {
      $is_valid = false;
      break;
    }
  }

  // jobs masih boleh lebih dari 1 (checkbox)
  if (!isset($_POST['jobs']) || count((array)$_POST['jobs']) === 0) $is_valid = false;

  // layanan sekarang 1 pilihan (radio)
  if (!isset($_POST['service']) || trim((string)$_POST['service']) === '') $is_valid = false;

  if ($is_valid) {

    $jobs = implode(',', array_map('trim', (array)$_POST['jobs']));
    $service = trim((string)$_POST['service']); // satu pilihan saja

    $sql = "INSERT INTO kuesioner
      (survey_date, survey_time, gender, education, jobs, services,
       q1,q2,q3,q4,q5,q6,q7,q8,q9,
       nama_pasien, alamat, nomor_hp, keluhan)
      VALUES
      ($1,$2,$3,$4,$5,$6,
       $7,$8,$9,$10,$11,$12,$13,$14,$15,
       $16,$17,$18,$19)";

    $params = [
      $_POST['surveyDate'],
      $_POST['surveyTime'],
      $_POST['gender'],
      $_POST['education'],
      $jobs,
      $service, // kolom DB tetap "services", tapi isi 1 layanan
      (int)$_POST['q1'],
      (int)$_POST['q2'],
      (int)$_POST['q3'],
      (int)$_POST['q4'],
      (int)$_POST['q5'],
      (int)$_POST['q6'],
      (int)$_POST['q7'],
      (int)$_POST['q8'],
      (int)$_POST['q9'],
      $_POST['nama_pasien'],
      $_POST['alamat'],
      $_POST['nomor_hp'],
      ($_POST['keluhan'] ?? null)
    ];

    $result = pg_query_params($conn, $sql, $params);

    if ($result) {
      header('Location: thank-you.php');
      exit();
    } else {
      $error_message = "Gagal menyimpan ke database: " . pg_last_error($conn);
    }

  } else {
    $error_message = "Mohon lengkapi semua field yang wajib diisi!";
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kuesioner Kepuasan Pasien - RS Ekahusada</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <div class="container">

    <div class="header">
      <div class="header-logo">
        <div class="logo-container">
          <img src="images/logo.png" alt="Logo RS Ekahusada" class="logo-icon">
        </div>

        <div class="header-text">
          <h1>RS EKAHUSADA</h1>
          <p>KUESIONER SURVEI KEPUASAN PASIEN</p>
        </div>
      </div>
    </div>

    <div class="form-container">
      <?php if (isset($error_message)): ?>
        <div class="error-message" style="background: #fee; color: #c00; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #c00; font-weight: 500;">
          ⚠️ <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="date-time-section">
          <div class="input-group">
            <label for="surveyDate">
              <span style="color: #00b3b3; margin-right: 5px;">📅</span> Tanggal Survei
            </label>
            <input type="date" id="surveyDate" name="surveyDate" required>
          </div>
          <div class="input-group">
            <label for="surveyTime">
              <span style="color: #00b3b3; margin-right: 5px;">⏰</span> Jam Survei
            </label>
            <select id="surveyTime" name="surveyTime" required>
              <option value="">Pilih Jam</option>
              <option value="08-12">08.00 - 12.00 WIB</option>
              <option value="12-18">12.00 - 18.00 WIB</option>
            </select>
          </div>
        </div>

        <h2 class="section-title">Profil Pasien</h2>
        <div class="profile-section">
          <div class="form-row">
            <div class="form-field">
              <label>Jenis Kelamin * <span style="color: #e74c3c;">(Pilih salah satu)</span></label>
              <div class="radio-group">
                <div class="radio-option">
                  <input type="radio" id="male" name="gender" value="male" required>
                  <label for="male">Laki-laki</label>
                </div>
                <div class="radio-option">
                  <input type="radio" id="female" name="gender" value="female">
                  <label for="female">Perempuan</label>
                </div>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label>Pendidikan * <span style="color: #e74c3c;">(Pilih salah satu)</span></label>
              <div class="radio-group">
                <div class="radio-option">
                  <input type="radio" id="sd" name="education" value="sd" required>
                  <label for="sd">SD</label>
                </div>
                <div class="radio-option">
                  <input type="radio" id="smp" name="education" value="smp">
                  <label for="smp">SMP</label>
                </div>
                <div class="radio-option">
                  <input type="radio" id="sma" name="education" value="sma">
                  <label for="sma">SMA</label>
                </div>
                <div class="radio-option">
                  <input type="radio" id="s1" name="education" value="s1">
                  <label for="s1">S1</label>
                </div>
                <div class="radio-option">
                  <input type="radio" id="s2" name="education" value="s2">
                  <label for="s2">S2</label>
                </div>
                <div class="radio-option">
                  <input type="radio" id="s3" name="education" value="s3">
                  <label for="s3">S3</label>
                </div>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label>Pekerjaan * <span style="color: #e74c3c;">(Pilih salah satu atau lebih)</span></label>
              <div class="checkbox-group">
                <div class="checkbox-option">
                  <input type="checkbox" id="pns" name="jobs[]" value="pns">
                  <label for="pns">PNS</label>
                </div>
                <div class="checkbox-option">
                  <input type="checkbox" id="tni" name="jobs[]" value="tni">
                  <label for="tni">TNI</label>
                </div>
                <div class="checkbox-option">
                  <input type="checkbox" id="polisi" name="jobs[]" value="polisi">
                  <label for="polisi">POLISI</label>
                </div>
                <div class="checkbox-option">
                  <input type="checkbox" id="swasta" name="jobs[]" value="swasta">
                  <label for="swasta">SWASTA</label>
                </div>
                <div class="checkbox-option">
                  <input type="checkbox" id="wirausaha" name="jobs[]" value="wirausaha">
                  <label for="wirausaha">WIRAUSAHA</label>
                </div>
                <div class="checkbox-option">
                  <input type="checkbox" id="petani" name="jobs[]" value="petani">
                  <label for="petani">PETANI</label>
                </div>
                <div class="checkbox-option">
                  <input type="checkbox" id="pelajar" name="jobs[]" value="pelajar">
                  <label for="pelajar">PELAJAR/MAHASISWA</label>
                </div>
                <div class="checkbox-option">
                  <input type="checkbox" id="lainnya" name="jobs[]" value="lainnya">
                  <label for="lainnya">LAINNYA</label>
                </div>
              </div>
            </div>
          </div>

          <!-- =========================
               JENIS LAYANAN (RADIO)
               ========================= -->
            <div class="form-row">
              <div class="form-field">
                <label>Jenis Layanan * <span style="color: #e74c3c;">(Pilih salah satu)</span></label>
                <div class="radio-group">
                  <div class="radio-option">
                    <input type="radio" id="admissi" name="service" value="admissi" required>
                    <label for="admissi">ADMISI</label>
                  </div>
                  <div class="radio-option">
                    <input type="radio" id="igd" name="service" value="igd">
                    <label for="igd">IGD</label>
                  </div>
                  <div class="radio-option">
                    <input type="radio" id="lab" name="service" value="lab">
                    <label for="lab">LABORATORIUM</label>
                  </div>
                  <div class="radio-option">
                    <input type="radio" id="farmasi" name="service" value="farmasi">
                    <label for="farmasi">FARMASI</label>
                  </div>
                  <div class="radio-option">
                    <input type="radio" id="radiologi" name="service" value="radiologi">
                    <label for="radiologi">RADIOLOGI</label>
                  </div>
                  <div class="radio-option">
                    <input type="radio" id="gizi" name="service" value="gizi">
                    <label for="gizi">GIZI</label>
                  </div>
                  <div class="radio-option">
                    <input type="radio" id="icu" name="service" value="icu">
                    <label for="icu">ICU</label>
                  </div>
                  <div class="radio-option">
                    <input type="radio" id="operasi" name="service" value="operasi">
                    <label for="operasi">OPERASI</label>
                  </div>
                  <div class="radio-option">
                    <input type="radio" id="rawat_jalan" name="service" value="rawat_jalan">
                    <label for="rawat_jalan">RAWAT JALAN</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>


        <h2 class="section-title">Pertanyaan Kepuasan Layanan</h2>
        <div class="scale-note">
          Skala Penilaian: 1 = Tidak Sesuai | 2 = Kurang Sesuai | 3 = Sesuai | 4 = Sangat Sesuai
        </div>

        <div class="questions-section">

          <div class="question-card">
            <div class="question-text">
              1. Bagaimana pendapat saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?
            </div>
            <div class="question-options">
              <div class="option">
                <input type="radio" name="q1" value="1" required>
                <label>1</label>
              </div>
              <div class="option">
                <input type="radio" name="q1" value="2">
                <label>2</label>
              </div>
              <div class="option">
                <input type="radio" name="q1" value="3">
                <label>3</label>
              </div>
              <div class="option">
                <input type="radio" name="q1" value="4">
                <label>4</label>
              </div>
            </div>
          </div>


          <div class="question-card">
            <div class="question-text">
              2. Bagaimana pemahaman Anda tentang kemudahan prosedur pelayanan di unit ini?
            </div>
            <div class="question-options">
              <div class="option">
                <input type="radio" name="q2" value="1" required>
                <label>1</label>
              </div>
              <div class="option">
                <input type="radio" name="q2" value="2">
                <label>2</label>
              </div>
              <div class="option">
                <input type="radio" name="q2" value="3">
                <label>3</label>
              </div>
              <div class="option">
                <input type="radio" name="q2" value="4">
                <label>4</label>
              </div>
            </div>
          </div>


          <div class="question-card">
            <div class="question-text">
              3. Bagaimana pendapat Anda tentang kecepatan waktu dalam memberikan pelayanan?
            </div>
            <div class="question-options">
              <div class="option">
                <input type="radio" name="q3" value="1" required>
                <label>1</label>
              </div>
              <div class="option">
                <input type="radio" name="q3" value="2">
                <label>2</label>
              </div>
              <div class="option">
                <input type="radio" name="q3" value="3">
                <label>3</label>
              </div>
              <div class="option">
                <input type="radio" name="q3" value="4">
                <label>4</label>
              </div>
            </div>
          </div>


          <div class="question-card">
            <div class="question-text">
              4. Bagaimana pendapat Anda tentang kewajaran biaya/tarif dalam pelayanan?
            </div>
            <div class="question-options">
              <div class="option">
                <input type="radio" name="q4" value="1" required>
                <label>1</label>
              </div>
              <div class="option">
                <input type="radio" name="q4" value="2">
                <label>2</label>
              </div>
              <div class="option">
                <input type="radio" name="q4" value="3">
                <label>3</label>
              </div>
              <div class="option">
                <input type="radio" name="q4" value="4">
                <label>4</label>
              </div>
            </div>
          </div>


          <div class="question-card">
            <div class="question-text">
              5. Bagaimana pendapat Anda tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?
            </div>
            <div class="question-options">
              <div class="option">
                <input type="radio" name="q5" value="1" required>
                <label>1</label>
              </div>
              <div class="option">
                <input type="radio" name="q5" value="2">
                <label>2</label>
              </div>
              <div class="option">
                <input type="radio" name="q5" value="3">
                <label>3</label>
              </div>
              <div class="option">
                <input type="radio" name="q5" value="4">
                <label>4</label>
              </div>
            </div>
          </div>


          <div class="question-card">
            <div class="question-text">
              6. Bagaimana pendapat Anda tentang kompetensi/kemampuan petugas dalam pelayanan?
            </div>
            <div class="question-options">
              <div class="option">
                <input type="radio" name="q6" value="1" required>
                <label>1</label>
              </div>
              <div class="option">
                <input type="radio" name="q6" value="2">
                <label>2</label>
              </div>
              <div class="option">
                <input type="radio" name="q6" value="3">
                <label>3</label>
              </div>
              <div class="option">
                <input type="radio" name="q6" value="4">
                <label>4</label>
              </div>
            </div>
          </div>


          <div class="question-card">
            <div class="question-text">
              7. Bagaimana pendapat Anda tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?
            </div>
            <div class="question-options">
              <div class="option">
                <input type="radio" name="q7" value="1" required>
                <label>1</label>
              </div>
              <div class="option">
                <input type="radio" name="q7" value="2">
                <label>2</label>
              </div>
              <div class="option">
                <input type="radio" name="q7" value="3">
                <label>3</label>
              </div>
              <div class="option">
                <input type="radio" name="q7" value="4">
                <label>4</label>
              </div>
            </div>
          </div>


          <div class="question-card">
            <div class="question-text">
              8. Bagaimana pendapat Anda tentang kualitas sarana dan prasarana?
            </div>
            <div class="question-options">
              <div class="option">
                <input type="radio" name="q8" value="1" required>
                <label>1</label>
              </div>
              <div class="option">
                <input type="radio" name="q8" value="2">
                <label>2</label>
              </div>
              <div class="option">
                <input type="radio" name="q8" value="3">
                <label>3</label>
              </div>
              <div class="option">
                <input type="radio" name="q8" value="4">
                <label>4</label>
              </div>
            </div>
          </div>


          <div class="question-card">
            <div class="question-text">
              9. Bagaimana pendapat Anda tentang penanganan pengaduan pengguna layanan?
            </div>
            <div class="question-options">
              <div class="option">
                <input type="radio" name="q9" value="1" required>
                <label>1</label>
              </div>
              <div class="option">
                <input type="radio" name="q9" value="2">
                <label>2</label>
              </div>
              <div class="option">
                <input type="radio" name="q9" value="3">
                <label>3</label>
              </div>
              <div class="option">
                <input type="radio" name="q9" value="4">
                <label>4</label>
              </div>
            </div>
          </div>
        </div>


        <h2 class="section-title">Formulir Keluhan/Saran</h2>
        <div class="complaint-section">
          <div class="form-row">
            <div class="form-field">
              <label>Nama Pasien *</label>
              <input class="form-profil" type="text" name="nama_pasien" required placeholder="Contoh: Ahmad Suryadi">
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label>Alamat *</label>
              <input class="form-profil" type="text" name="alamat" required placeholder="Contoh: Jl. Merdeka No. 123, Jakarta">
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label>Nomor HP *</label>
              <input class="form-profil" type="tel" name="nomor_hp" required placeholder="Contoh: 081234567890">
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label>Uraian Keluhan/Saran/Pertanyaan</label>
              <textarea name="keluhan" placeholder="Tuliskan keluhan, saran, atau pertanyaan Anda di sini..."></textarea>
            </div>
          </div>

          <div class="complaint-note">
            Masukan Anda sangat berarti bagi kami untuk meningkatkan kualitas layanan
          </div>
        </div>


        <button type="submit" class="submit-btn">Kirim Kuesioner</button>
      </form>
    </div>


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
          <p>RS Ekahusada - Melayani Dengan Sepenuh Hati</p>
        </div>
      </div>
    </footer>
  </div>
</body>

</html>
