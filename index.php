<?php
session_start();
require_once "config.php";

/* =========================
   KONEKSI DATABASE
   ========================= */
$conn = pg_connect("host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_pass");
if (!$conn) {
  die("Koneksi PostgreSQL gagal: " . htmlspecialchars(pg_last_error()));
}

/* =========================
   LOCK SERVICE VIA URL ?service=...
   ========================= */
$allowed_services = [
  'admisi',
  'igd',
  'lab',
  'farmasi',
  'radiologi',
  'gizi',
  'icu',
  'operasi',
  'rawat_jalan'
];

$service_locked = false;
$service_from_url = '';

if (isset($_GET['service'])) {
  $candidate = strtolower(trim((string)$_GET['service']));
  if (in_array($candidate, $allowed_services, true)) {
    $service_locked = true;
    $service_from_url = $candidate;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Field teks wajib (bukan angka)
  $requiredText = ['surveyDate', 'surveyTime', 'gender', 'education', 'penjamin'];
  $valid = true;

  foreach ($requiredText as $r) {
    if (!isset($_POST[$r]) || trim((string)$_POST[$r]) === '') {
      $valid = false;
      break;
    }
  }

  // Ambil service (kalau locked, ambil dari URL dan abaikan POST)
  $service_value = $service_locked ? $service_from_url : trim((string)($_POST['service'] ?? ''));
  if ($service_value === '') $valid = false;

  // jobs checkbox minimal 1
  if (!isset($_POST['jobs']) || count((array)$_POST['jobs']) === 0) {
    $valid = false;
  }

  // q wajib (kecuali q4 khusus)
  $requiredQ = ['q1', 'q2', 'q3', 'q5', 'q6', 'q7', 'q8', 'q9'];
  foreach ($requiredQ as $q) {
    if (!isset($_POST[$q]) || (string)$_POST[$q] === '') {
      $valid = false;
      break;
    }
  }

  // q4: jika BPJS -> 0, jika UMUM -> wajib pilih
  $penjamin = $_POST['penjamin'] ?? '';
  $q4 = null;
  if ($penjamin === 'BPJS') {
    $q4 = null;
  } else {
    if (!isset($_POST['q4']) || (string)$_POST['q4'] === '') {
      $valid = false;
    }
    $q4 = isset($_POST['q4']) ? (int)$_POST['q4'] : null;
  }

  if ($valid) {
    $jobs = implode(',', array_map('trim', (array)$_POST['jobs']));

    $sql = "INSERT INTO kepuasan (
      survey_date, survey_time, gender, education, jobs, services,
      q1,q2,q3,q4,q5,q6,q7,q8,q9, penjamin
    ) VALUES (
      $1,$2,$3,$4,$5,$6,
      $7,$8,$9,$10,$11,$12,$13,$14,$15,$16
    )";

    $params = [
      $_POST['surveyDate'],
      $_POST['surveyTime'],
      $_POST['gender'],
      $_POST['education'],
      $jobs,
      $service_value,
      (int)$_POST['q1'],
      (int)$_POST['q2'],
      (int)$_POST['q3'],
      $q4,
      (int)$_POST['q5'],
      (int)$_POST['q6'],
      (int)$_POST['q7'],
      (int)$_POST['q8'],
      (int)$_POST['q9'],
      $_POST['penjamin']
    ];

    $result = pg_query_params($conn, $sql, $params);

    if ($result) {
      header("Location: thank-you.php");
      exit;
    } else {
      $error = "Gagal menyimpan data kepuasan: " . pg_last_error($conn);
    }
  } else {
    $error = "Mohon lengkapi semua pertanyaan survei yang wajib diisi.";
  }
}

$date = date('Y-m-d');
$time = date('H:i');

function is_time_in_range($time, $start, $end)
{
  $time = strtotime($time);
  $start = strtotime($start);
  $end = strtotime($end);
  return ($time >= $start) && ($time <= $end);
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
      <?php if (isset($error)): ?>
        <div class="error-message" style="background:#fee;color:#c00;padding:15px;border-radius:8px;margin-bottom:20px;border-left:4px solid #c00;font-weight:500;">
          ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="date-time-section">
          <div class="input-group">
            <label for="surveyDate">Tanggal Survei</label>
            <input type="date" id="surveyDate" name="surveyDate" value="<?= $date ?>" required>
          </div>
          <div class="input-group">
            <label for="surveyTime">Jam Survei</label>
            <select id="surveyTime" name="surveyTime" required>
              <option value="">Pilih Jam</option>
              <?php
              if (is_time_in_range($time, '08:00', '12:00')) {
                echo '<option value="08-12" selected>08.00 - 12.00 WIB</option>';
                echo '<option value="12-18">12.00 - 18.00 WIB</option>';
              } elseif (is_time_in_range($time, '12:00', '18:00')) {
                echo '<option value="08-12">08.00 - 12.00 WIB</option>';
                echo '<option value="12-18" selected>12.00 - 18.00 WIB</option>';
              } else {
                echo '<option value="08-12">08.00 - 12.00 WIB</option>';
                echo '<option value="12-18">12.00 - 18.00 WIB</option>';
              }
              ?>
            </select>
          </div>
        </div>

        <h2 class="section-title">Profil Pasien</h2>
        <div class="profile-section">

          <div class="form-row">
            <div class="form-field">
              <label>Jenis Kelamin * <span style="color:#e74c3c;">(Pilih salah satu)</span></label>
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
              <label>Pendidikan * <span style="color:#e74c3c;">(Pilih salah satu)</span></label>
              <div class="radio-group">
                <div class="radio-option"><input type="radio" id="sd" name="education" value="sd" required><label for="sd">SD</label></div>
                <div class="radio-option"><input type="radio" id="smp" name="education" value="smp"><label for="smp">SMP</label></div>
                <div class="radio-option"><input type="radio" id="sma" name="education" value="sma"><label for="sma">SMA</label></div>
                <div class="radio-option"><input type="radio" id="s1" name="education" value="s1"><label for="s1">S1</label></div>
                <div class="radio-option"><input type="radio" id="s2" name="education" value="s2"><label for="s2">S2</label></div>
                <div class="radio-option"><input type="radio" id="s3" name="education" value="s3"><label for="s3">S3</label></div>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label>Pekerjaan * <span style="color:#e74c3c;">(Pilih salah satu atau lebih)</span></label>
              <div class="checkbox-group">
                <div class="checkbox-option"><input type="checkbox" id="pns" name="jobs[]" value="pns"><label for="pns">PNS</label></div>
                <div class="checkbox-option"><input type="checkbox" id="tni" name="jobs[]" value="tni"><label for="tni">TNI</label></div>
                <div class="checkbox-option"><input type="checkbox" id="polisi" name="jobs[]" value="polisi"><label for="polisi">POLISI</label></div>
                <div class="checkbox-option"><input type="checkbox" id="swasta" name="jobs[]" value="swasta"><label for="swasta">SWASTA</label></div>
                <div class="checkbox-option"><input type="checkbox" id="wirausaha" name="jobs[]" value="wirausaha"><label for="wirausaha">WIRAUSAHA</label></div>
                <div class="checkbox-option"><input type="checkbox" id="petani" name="jobs[]" value="petani"><label for="petani">PETANI</label></div>
                <div class="checkbox-option"><input type="checkbox" id="pelajar" name="jobs[]" value="pelajar"><label for="pelajar">PELAJAR/MAHASISWA</label></div>
                <div class="checkbox-option"><input type="checkbox" id="lainnya" name="jobs[]" value="lainnya"><label for="lainnya">LAINNYA</label></div>
              </div>
            </div>
          </div>

          <!-- =========================
               JENIS LAYANAN (RADIO) + LOCK
               ========================= -->
          <div class="form-row">
            <div class="form-field">
              <label>Jenis Layanan * <span style="color:#e74c3c;">(Pilih salah satu)</span></label>

              <?php if ($service_locked): ?>
                <div class="complaint-note" style="margin-top:10px;">
                  Jenis layanan sudah ditentukan oleh link dan tidak bisa diubah.
                </div>
                <!-- radio disabled gak ikut POST, jadi kirim via hidden -->
                <input type="hidden" name="service" value="<?php echo htmlspecialchars($service_from_url); ?>">
              <?php endif; ?>

              <div class="radio-group">
                <?php
                function svc_checked($svc, $current)
                {
                  return $svc === $current ? 'checked' : '';
                }
                function svc_disabled($locked)
                {
                  return $locked ? 'disabled' : '';
                }
                $current_service = $service_locked ? $service_from_url : '';
                ?>

                <div class="radio-option">
                  <input type="radio" id="admisi" name="service" value="admisi"
                    <?php echo svc_checked('admisi', $current_service); ?>
                    <?php echo $service_locked ? 'disabled' : 'required'; ?>>
                  <label for="admisi">ADMISI</label>
                </div>

                <div class="radio-option">
                  <input type="radio" id="igd" name="service" value="igd"
                    <?php echo svc_checked('igd', $current_service); ?>
                    <?php echo svc_disabled($service_locked); ?>>
                  <label for="igd">IGD</label>
                </div>

                <div class="radio-option">
                  <input type="radio" id="lab" name="service" value="lab"
                    <?php echo svc_checked('lab', $current_service); ?>
                    <?php echo svc_disabled($service_locked); ?>>
                  <label for="lab">LABORATORIUM</label>
                </div>

                <div class="radio-option">
                  <input type="radio" id="farmasi" name="service" value="farmasi"
                    <?php echo svc_checked('farmasi', $current_service); ?>
                    <?php echo svc_disabled($service_locked); ?>>
                  <label for="farmasi">FARMASI</label>
                </div>

                <div class="radio-option">
                  <input type="radio" id="radiologi" name="service" value="radiologi"
                    <?php echo svc_checked('radiologi', $current_service); ?>
                    <?php echo svc_disabled($service_locked); ?>>
                  <label for="radiologi">RADIOLOGI</label>
                </div>

                <div class="radio-option">
                  <input type="radio" id="gizi" name="service" value="gizi"
                    <?php echo svc_checked('gizi', $current_service); ?>
                    <?php echo svc_disabled($service_locked); ?>>
                  <label for="gizi">GIZI</label>
                </div>

                <div class="radio-option">
                  <input type="radio" id="icu" name="service" value="icu"
                    <?php echo svc_checked('icu', $current_service); ?>
                    <?php echo svc_disabled($service_locked); ?>>
                  <label for="icu">ICU</label>
                </div>

                <div class="radio-option">
                  <input type="radio" id="operasi" name="service" value="operasi"
                    <?php echo svc_checked('operasi', $current_service); ?>
                    <?php echo svc_disabled($service_locked); ?>>
                  <label for="operasi">OPERASI</label>
                </div>

                <div class="radio-option">
                  <input type="radio" id="rawat_jalan" name="service" value="rawat_jalan"
                    <?php echo svc_checked('rawat_jalan', $current_service); ?>
                    <?php echo svc_disabled($service_locked); ?>>
                  <label for="rawat_jalan">RAWAT JALAN</label>
                </div>
              </div>
            </div>
          </div>

          <div class="form-row">
            <div class="form-field">
              <label>Penjamin * <span style="color:#e74c3c;">(Pilih salah satu)</span></label>
              <div class="radio-group">
                <div class="radio-option"><input type="radio" id="bpjs" name="penjamin" value="BPJS" required><label for="bpjs">BPJS</label></div>
                <div class="radio-option"><input type="radio" id="umum" name="penjamin" value="UMUM"><label for="umum">UMUM</label></div>
              </div>
            </div>
          </div>

        </div>

        <h2 class="section-title">Pertanyaan Kepuasan Layanan</h2>
        <div class="scale-note">Skala Penilaian: 1 = Tidak Sesuai | 2 = Kurang Sesuai | 3 = Sesuai | 4 = Sangat Sesuai</div>

        <div class="questions-section">

          <div class="question-card">
            <div class="question-text">
              1. Bagaimana pendapat saudara tentang kesesuaian persyaratan pelayanan dengan jenis pelayanannya?
            </div>
            <div class="question-options">
              <div class="option"><input type="radio" name="q1" value="1" required><label>1</label></div>
              <div class="option"><input type="radio" name="q1" value="2"><label>2</label></div>
              <div class="option"><input type="radio" name="q1" value="3"><label>3</label></div>
              <div class="option"><input type="radio" name="q1" value="4"><label>4</label></div>
            </div>
          </div>

          <div class="question-card">
            <div class="question-text">
              2. Bagaimana pemahaman Anda tentang kemudahan prosedur pelayanan di unit ini?
            </div>
            <div class="question-options">
              <div class="option"><input type="radio" name="q2" value="1" required><label>1</label></div>
              <div class="option"><input type="radio" name="q2" value="2"><label>2</label></div>
              <div class="option"><input type="radio" name="q2" value="3"><label>3</label></div>
              <div class="option"><input type="radio" name="q2" value="4"><label>4</label></div>
            </div>
          </div>

          <div class="question-card">
            <div class="question-text">
              3. Bagaimana pendapat Anda tentang kecepatan waktu dalam memberikan pelayanan?
            </div>
            <div class="question-options">
              <div class="option"><input type="radio" name="q3" value="1" required><label>1</label></div>
              <div class="option"><input type="radio" name="q3" value="2"><label>2</label></div>
              <div class="option"><input type="radio" name="q3" value="3"><label>3</label></div>
              <div class="option"><input type="radio" name="q3" value="4"><label>4</label></div>
            </div>
          </div>

          <div class="question-card" id="question-q4">
            <div class="question-text">4. Bagaimana pendapat Anda tentang kewajaran biaya/tarif dalam pelayanan?</div>
            <div class="question-options">
              <div class="option"><input type="radio" name="q4" value="1"><label>1</label></div>
              <div class="option"><input type="radio" name="q4" value="2"><label>2</label></div>
              <div class="option"><input type="radio" name="q4" value="3"><label>3</label></div>
              <div class="option"><input type="radio" name="q4" value="4"><label>4</label></div>
            </div>
          </div>

          <div class="question-card">
            <div class="question-text">
              5. Bagaimana pendapat Anda tentang kesesuaian produk pelayanan antara yang tercantum dalam standar pelayanan dengan hasil yang diberikan?
            </div>
            <div class="question-options">
              <div class="option"><input type="radio" name="q5" value="1" required><label>1</label></div>
              <div class="option"><input type="radio" name="q5" value="2"><label>2</label></div>
              <div class="option"><input type="radio" name="q5" value="3"><label>3</label></div>
              <div class="option"><input type="radio" name="q5" value="4"><label>4</label></div>
            </div>
          </div>

          <div class="question-card">
            <div class="question-text">
              6. Bagaimana pendapat Anda tentang kompetensi/kemampuan petugas dalam pelayanan?
            </div>
            <div class="question-options">
              <div class="option"><input type="radio" name="q6" value="1" required><label>1</label></div>
              <div class="option"><input type="radio" name="q6" value="2"><label>2</label></div>
              <div class="option"><input type="radio" name="q6" value="3"><label>3</label></div>
              <div class="option"><input type="radio" name="q6" value="4"><label>4</label></div>
            </div>
          </div>

          <div class="question-card">
            <div class="question-text">
              7. Bagaimana pendapat Anda tentang perilaku petugas dalam pelayanan terkait kesopanan dan keramahan?
            </div>
            <div class="question-options">
              <div class="option"><input type="radio" name="q7" value="1" required><label>1</label></div>
              <div class="option"><input type="radio" name="q7" value="2"><label>2</label></div>
              <div class="option"><input type="radio" name="q7" value="3"><label>3</label></div>
              <div class="option"><input type="radio" name="q7" value="4"><label>4</label></div>
            </div>
          </div>

          <div class="question-card">
            <div class="question-text">
              8. Bagaimana pendapat Anda tentang kualitas sarana dan prasarana?
            </div>
            <div class="question-options">
              <div class="option"><input type="radio" name="q8" value="1" required><label>1</label></div>
              <div class="option"><input type="radio" name="q8" value="2"><label>2</label></div>
              <div class="option"><input type="radio" name="q8" value="3"><label>3</label></div>
              <div class="option"><input type="radio" name="q8" value="4"><label>4</label></div>
            </div>
          </div>

          <div class="question-card">
            <div class="question-text">
              9. Bagaimana pendapat Anda tentang penanganan pengaduan pengguna layanan?
            </div>
            <div class="question-options">
              <div class="option"><input type="radio" name="q9" value="1" required><label>1</label></div>
              <div class="option"><input type="radio" name="q9" value="2"><label>2</label></div>
              <div class="option"><input type="radio" name="q9" value="3"><label>3</label></div>
              <div class="option"><input type="radio" name="q9" value="4"><label>4</label></div>
            </div>
          </div>

        </div>

        <input type="hidden" name="q4_bpjs" id="q4-bpjs" value="">

        <div class="complaint-note">
          Masukan Anda sangat berarti bagi kami untuk meningkatkan kualitas layanan
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

  <script>
    const bpjsRadio = document.getElementById('bpjs');
    const umumRadio = document.getElementById('umum');
    const q4Section = document.getElementById('question-q4');
    const q4Radios = document.querySelectorAll('input[name="q4"]');
    const q4Bpjs = document.getElementById('q4-bpjs');
    const SERVICE_LOCKED = <?php echo $service_locked ? 'true' : 'false'; ?>;

    if (!SERVICE_LOCKED) {
      document.querySelectorAll('input[name="service"]').forEach(radio => {
        radio.addEventListener('change', () => {
          const val = radio.value;
          const url = new URL(window.location.href);
          url.searchParams.set('service', val);
          // arahkan ke URL baru
          window.location.href = url.toString();
        });
      });
    }

    function disableQ4() {
      if (!q4Section) return;
      q4Section.style.opacity = '0.5';
      q4Radios.forEach(r => {
        r.checked = false;
        r.disabled = true;
      });
      q4Bpjs.value = '0';
    }

    function enableQ4() {
      if (!q4Section) return;
      q4Section.style.opacity = '1';
      q4Radios.forEach(r => {
        r.disabled = false;
      });
      q4Bpjs.value = '';
    }

    bpjsRadio?.addEventListener('change', disableQ4);
    umumRadio?.addEventListener('change', enableQ4);

    // optional: kalau user reload dalam kondisi BPJS sudah kepilih, langsung disable q4
    if (bpjsRadio && bpjsRadio.checked) disableQ4();
  </script>

</body>

</html>