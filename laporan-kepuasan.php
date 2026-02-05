<?php
session_start();

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/db.php";

$pageTitle = "LAPORAN KEPUASAN (REKAP)";
include 'layout/header.php';

// =========================
// Helper
// =========================
function isValidDateYmd(string $d): bool
{
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return false;
  $t = strtotime($d);
  return $t !== false && date('Y-m-d', $t) === $d;
}

// =========================
// Default filter
// =========================
$today = date('Y-m-d');
$defaultStart = date('Y-m-d', strtotime('-6 days'));

$tgl_awal  = (string)($_GET['tgl_awal'] ?? $defaultStart);
$tgl_akhir = (string)($_GET['tgl_akhir'] ?? $today);

// Optional filter profil (boleh kosong)
$pelayanan_id     = (string)($_GET['pelayanan_id'] ?? '');
$jenis_kelamin_id = (string)($_GET['jenis_kelamin_id'] ?? '');
$pendidikan_id    = (string)($_GET['pendidikan_id'] ?? '');
$pekerjaan_id     = (string)($_GET['pekerjaan_id'] ?? '');
$penjamin_id      = (string)($_GET['penjamin_id'] ?? '');

// Mode rekap
$mode = (string)($_GET['mode'] ?? 'layanan');
$allowedModes = ['layanan', 'pertanyaan', 'penjamin', 'jenis_kelamin', 'pendidikan', 'pekerjaan'];
if (!in_array($mode, $allowedModes, true)) $mode = 'layanan';

// Sorting untuk rekap
$sort = (string)($_GET['sort'] ?? 'avg_desc');
$allowedSort = ['avg_desc', 'avg_asc', 'jumlah_desc', 'jumlah_asc', 'nama_asc'];
if (!in_array($sort, $allowedSort, true)) $sort = 'avg_desc';

$error = null;

// =========================
// Ambil master dropdown (untuk filter)
/// ========================
try {
  $pelayanan_rows = $pdo->query("SELECT id, nama FROM pelayanan ORDER BY nama ASC")->fetchAll();
  $jk_rows        = $pdo->query("SELECT id, nama FROM jenis_kelamin ORDER BY id ASC")->fetchAll();
  $pend_rows      = $pdo->query("SELECT id, nama FROM pendidikan ORDER BY id ASC")->fetchAll();
  $pkj_rows       = $pdo->query("SELECT id, nama FROM pekerjaan ORDER BY nama ASC")->fetchAll();
  $penj_rows      = $pdo->query("SELECT id, nama FROM penjamin ORDER BY nama ASC")->fetchAll();
} catch (Throwable $e) {
  error_log($e->getMessage(), 3, __DIR__ . "/logs/error.log");
  $error = "Gagal mengambil master data filter.";
}

// =========================
// Validasi tanggal
// =========================
if (!$error) {
  if (!isValidDateYmd($tgl_awal) || !isValidDateYmd($tgl_akhir)) {
    $error = "Format tanggal tidak valid.";
  } elseif ($tgl_awal > $tgl_akhir) {
    $error = "Tanggal awal tidak boleh lebih besar dari tanggal akhir.";
  }
}

// =========================
// Build WHERE + params
// =========================
$where = [];
$params = [];

$where[] = "k.survey_date BETWEEN :awal AND :akhir";
$params[':awal']  = $tgl_awal;
$params[':akhir'] = $tgl_akhir;

// Filter profil (opsional)
if ($pelayanan_id !== '') {
  $where[] = "p.pelayanan_id = :pelayanan_id";
  $params[':pelayanan_id'] = (int)$pelayanan_id;
}
if ($jenis_kelamin_id !== '') {
  $where[] = "p.jenis_kelamin_id = :jk_id";
  $params[':jk_id'] = (int)$jenis_kelamin_id;
}
if ($pendidikan_id !== '') {
  $where[] = "p.pendidikan_id = :pend_id";
  $params[':pend_id'] = (int)$pendidikan_id;
}
if ($pekerjaan_id !== '') {
  $where[] = "p.pekerjaan_id = :pkj_id";
  $params[':pkj_id'] = (int)$pekerjaan_id;
}
if ($penjamin_id !== '') {
  $where[] = "p.penjamin_id = :penj_id";
  $params[':penj_id'] = (int)$penjamin_id;
}

$whereSql = implode(" AND ", $where);

// =========================
// Order By (whitelist)
// =========================
$orderBy = "avg_nilai DESC, jumlah_jawaban DESC";
if ($sort === 'avg_asc')     $orderBy = "avg_nilai ASC, jumlah_jawaban DESC";
if ($sort === 'jumlah_desc') $orderBy = "jumlah_jawaban DESC, avg_nilai DESC";
if ($sort === 'jumlah_asc')  $orderBy = "jumlah_jawaban ASC, avg_nilai DESC";
if ($sort === 'nama_asc')    $orderBy = "label ASC, avg_nilai DESC";

// =========================
// Map mode -> SELECT/GROUP/JOIN label
// =========================
$selectLabel = "";
$joinExtra   = "";
$groupBy     = "";
$titleRekap  = "";

switch ($mode) {
  case 'layanan':
    $titleRekap  = "Rekap per Jenis Layanan";
    $selectLabel = "pl.nama AS label";
    $joinExtra   = "JOIN pelayanan pl ON pl.id = p.pelayanan_id";
    $groupBy     = "pl.nama";
    break;

  case 'pertanyaan':
    $titleRekap  = "Rekap per Pertanyaan";
    $selectLabel = "pr.deskripsi AS label";
    $joinExtra   = "JOIN pertanyaan pr ON pr.id = k.pertanyaan_id";
    $groupBy     = "pr.deskripsi";
    break;

  case 'penjamin':
    $titleRekap  = "Rekap per Penjamin";
    $selectLabel = "pj.nama AS label";
    $joinExtra   = "JOIN penjamin pj ON pj.id = p.penjamin_id";
    $groupBy     = "pj.nama";
    break;

  case 'jenis_kelamin':
    $titleRekap  = "Rekap per Jenis Kelamin";
    // biar tampil rapi L/P jadi label
    $selectLabel = "CASE WHEN jk.nama = 'L' THEN 'Laki-laki' ELSE 'Perempuan' END AS label";
    $joinExtra   = "JOIN jenis_kelamin jk ON jk.id = p.jenis_kelamin_id";
    // GROUP BY harus sesuai ekspresi
    $groupBy     = "CASE WHEN jk.nama = 'L' THEN 'Laki-laki' ELSE 'Perempuan' END";
    break;

  case 'pendidikan':
    $titleRekap  = "Rekap per Pendidikan";
    $selectLabel = "pd.nama AS label";
    $joinExtra   = "JOIN pendidikan pd ON pd.id = p.pendidikan_id";
    $groupBy     = "pd.nama";
    break;

  case 'pekerjaan':
    $titleRekap  = "Rekap per Pekerjaan";
    $selectLabel = "pk.nama AS label";
    $joinExtra   = "JOIN pekerjaan pk ON pk.id = p.pekerjaan_id";
    $groupBy     = "pk.nama";
    break;
}

$rekapRows = [];
$summary = null;

if (!$error) {
  try {
    // Ringkasan global
    $stmtSum = $pdo->prepare("
      SELECT
        COUNT(*) AS total_jawaban,
        COUNT(DISTINCT k.profil_id) AS total_responden,
        AVG(k.nilai)::numeric(10,2) AS rata_nilai
      FROM kuisioner k
      JOIN profil p ON p.id = k.profil_id
      WHERE $whereSql
    ");
    $stmtSum->execute($params);
    $summary = $stmtSum->fetch();

    // Rekap sesuai mode
    $stmt = $pdo->prepare("
      SELECT
        $selectLabel,
        COUNT(*) AS jumlah_jawaban,
        COUNT(DISTINCT k.profil_id) AS jumlah_responden,
        AVG(k.nilai)::numeric(10,2) AS avg_nilai
      FROM kuisioner k
      JOIN profil p ON p.id = k.profil_id
      $joinExtra
      WHERE $whereSql
      GROUP BY $groupBy
      ORDER BY $orderBy
    ");
    $stmt->execute($params);
    $rekapRows = $stmt->fetchAll();
  } catch (Throwable $e) {
    error_log($e->getMessage(), 3, __DIR__ . "/logs/error.log");
    $error = "Gagal mengambil data rekap kepuasan.";
  }
}

// Helper buat bikin URL switch mode tanpa ngilangin filter
function buildUrl(array $override = []): string
{
  $q = array_merge($_GET, $override);
  return '?' . http_build_query($q);
}
?>

<div class="form-container">
  <a href="dashboard.php" class="btn-back">
   ← Kembali
  </a>
  <h2 class="section-title">Filter Laporan Kepuasan</h2>

  <?php if (!empty($error)): ?>
    <div class="error-message" style="background:#fee;color:#c00;padding:15px;border-radius:8px;margin-bottom:20px;border-left:4px solid #c00;font-weight:500;">
      ⚠️ <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form method="GET" action="">
    <div class="date-time-section">
      <div class="input-group">
        <label>Tanggal Awal</label>
        <input type="date" name="tgl_awal" value="<?= htmlspecialchars($tgl_awal) ?>" required>
      </div>
      <div class="input-group">
        <label>Tanggal Akhir</label>
        <input type="date" name="tgl_akhir" value="<?= htmlspecialchars($tgl_akhir) ?>" required>
      </div>
    </div>

    <div class="form-row">
      <div class="form-field">
        <label>Rekap Berdasarkan</label>
        <select name="mode" required>
          <option value="layanan" <?= $mode === 'layanan' ? 'selected' : ''; ?>>Layanan</option>
          <option value="pertanyaan" <?= $mode === 'pertanyaan' ? 'selected' : ''; ?>>Pertanyaan</option>
          <option value="penjamin" <?= $mode === 'penjamin' ? 'selected' : ''; ?>>Penjamin</option>
          <option value="jenis_kelamin" <?= $mode === 'jenis_kelamin' ? 'selected' : ''; ?>>Jenis Kelamin</option>
          <option value="pendidikan" <?= $mode === 'pendidikan' ? 'selected' : ''; ?>>Pendidikan</option>
          <option value="pekerjaan" <?= $mode === 'pekerjaan' ? 'selected' : ''; ?>>Pekerjaan</option>
        </select>
      </div>

      <div class="form-field">
        <label>Urutkan</label>
        <select name="sort">
          <option value="avg_desc" <?= $sort === 'avg_desc' ? 'selected' : ''; ?>>Nilai rata-rata tertinggi</option>
          <option value="avg_asc" <?= $sort === 'avg_asc' ? 'selected' : ''; ?>>Nilai rata-rata terendah</option>
          <option value="jumlah_desc" <?= $sort === 'jumlah_desc' ? 'selected' : ''; ?>>Jumlah jawaban terbanyak</option>
          <option value="jumlah_asc" <?= $sort === 'jumlah_asc' ? 'selected' : ''; ?>>Jumlah jawaban tersedikit</option>
          <option value="nama_asc" <?= $sort === 'nama_asc' ? 'selected' : ''; ?>>Nama A-Z</option>
        </select>
      </div>
    </div>

    <!-- Filter profil (opsional, biar fleksibel) -->
    <div class="form-row">
      <div class="form-field">
        <label>Filter Layanan (opsional)</label>
        <select name="pelayanan_id">
          <option value="">Semua</option>
          <?php foreach ($pelayanan_rows as $r): ?>
            <option value="<?= (int)$r['id'] ?>" <?= ((string)$r['id'] === $pelayanan_id) ? 'selected' : '' ?>>
              <?= htmlspecialchars((string)$r['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-field">
        <label>Filter Penjamin (opsional)</label>
        <select name="penjamin_id">
          <option value="">Semua</option>
          <?php foreach ($penj_rows as $r): ?>
            <option value="<?= (int)$r['id'] ?>" <?= ((string)$r['id'] === $penjamin_id) ? 'selected' : '' ?>>
              <?= htmlspecialchars((string)$r['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-field">
        <label>Filter Jenis Kelamin (opsional)</label>
        <select name="jenis_kelamin_id">
          <option value="">Semua</option>
          <?php foreach ($jk_rows as $r): ?>
            <?php $label = ((string)$r['nama'] === 'L') ? 'Laki-laki' : 'Perempuan'; ?>
            <option value="<?= (int)$r['id'] ?>" <?= ((string)$r['id'] === $jenis_kelamin_id) ? 'selected' : '' ?>>
              <?= htmlspecialchars($label) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-field">
        <label>Filter Pendidikan (opsional)</label>
        <select name="pendidikan_id">
          <option value="">Semua</option>
          <?php foreach ($pend_rows as $r): ?>
            <option value="<?= (int)$r['id'] ?>" <?= ((string)$r['id'] === $pendidikan_id) ? 'selected' : '' ?>>
              <?= htmlspecialchars((string)$r['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-field">
        <label>Filter Pekerjaan (opsional)</label>
        <select name="pekerjaan_id">
          <option value="">Semua</option>
          <?php foreach ($pkj_rows as $r): ?>
            <option value="<?= (int)$r['id'] ?>" <?= ((string)$r['id'] === $pekerjaan_id) ? 'selected' : '' ?>>
              <?= htmlspecialchars((string)$r['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <button type="submit" class="submit-btn">Tampilkan Rekap</button>
  </form>

  <h2 class="section-title" style="margin-top:25px;">Ringkasan</h2>

  <?php if (!$error && $summary): ?>
    <div class="complaint-note" style="margin-top:0;">
      Rentang: <b><?= htmlspecialchars($tgl_awal) ?></b> s/d <b><?= htmlspecialchars($tgl_akhir) ?></b> —
      Total jawaban: <b><?= (int)$summary['total_jawaban'] ?></b> —
      Total responden: <b><?= (int)$summary['total_responden'] ?></b> —
      Rata-rata nilai: <b><?= htmlspecialchars((string)$summary['rata_nilai']) ?></b>
    </div>
  <?php endif; ?>

  <h2 class="section-title" style="margin-top:25px;"><?= htmlspecialchars($titleRekap) ?></h2>

  <?php if (!$error && empty($rekapRows)): ?>
    <div class="complaint-note">Tidak ada data pada filter tersebut.</div>
  <?php endif; ?>

  <?php foreach ($rekapRows as $i => $r): ?>
    <div class="question-card" style="margin-top:15px;">
      <div class="question-text">
        #<?= $i + 1 ?> — <?= htmlspecialchars((string)$r['label']) ?>
      </div>
      <div style="margin-bottom:10px;">
        <b>Rata-rata Nilai:</b> <?= htmlspecialchars((string)$r['avg_nilai']) ?><br>
        <b>Jumlah Jawaban:</b> <?= (int)$r['jumlah_jawaban'] ?><br>
        <b>Jumlah Responden:</b> <?= (int)$r['jumlah_responden'] ?>
      </div>
    </div>
  <?php endforeach; ?>

</div>