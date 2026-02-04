<?php
session_start();

require_once __DIR__ . "/config/config.php";
require_once __DIR__ . "/config/db.php";

$pageTitle = "LAPORAN KELUHAN";
include 'layout/header.php';

// Default tanggal: 7 hari terakhir
$today = date('Y-m-d');
$defaultStart = date('Y-m-d', strtotime('-6 days'));

$tgl_awal  = (string)($_GET['tgl_awal'] ?? $defaultStart);
$tgl_akhir = (string)($_GET['tgl_akhir'] ?? $today);

$error = null;
$rows = [];
$total = 0;

// Validasi format tanggal (YYYY-MM-DD)
function isValidDateYmd(string $d): bool
{
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) return false;
  $t = strtotime($d);
  return $t !== false && date('Y-m-d', $t) === $d;
}

if (!isValidDateYmd($tgl_awal) || !isValidDateYmd($tgl_akhir)) {
  $error = "Format tanggal tidak valid.";
} elseif ($tgl_awal > $tgl_akhir) {
  $error = "Tanggal awal tidak boleh lebih besar dari tanggal akhir.";
} else {
  try {
    // BETWEEN inclusive. Karena kolom "tanggal" format date, ini aman.
    $stmt = $pdo->prepare("
      SELECT alamat, no_hp, masukan, pukul, tanggal
      FROM keluhan
      WHERE tanggal BETWEEN :awal AND :akhir
      ORDER BY tanggal DESC, pukul DESC
    ");
    $stmt->execute([
      ':awal'  => $tgl_awal,
      ':akhir' => $tgl_akhir
    ]);

    $rows = $stmt->fetchAll();
    $total = count($rows);
  } catch (Throwable $e) {
    error_log($e->getMessage(), 3, __DIR__ . "/logs/error.log");
    $error = "Gagal mengambil data laporan.";
  }
}
?>

<div class="form-container">
  <a href="javascript:history.back()" class="btn-back"> ← Kembali</a>
  <h2 class="section-title">Filter Tanggal</h2>

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


  </form>

  <h2 class="section-title" style="margin-top:25px;">Hasil Laporan</h2>

  <div class="complaint-note" style="margin-top:0;">
    Rentang: <b><?= htmlspecialchars($tgl_awal) ?></b> s/d <b><?= htmlspecialchars($tgl_akhir) ?></b> —
    Total: <b><?= (int)$total ?></b> data
  </div>

  <?php if ($total === 0 && empty($error)): ?>
    <div class="complaint-note">
      Tidak ada data keluhan pada rentang tanggal tersebut.
    </div>
  <?php endif; ?>

  <?php foreach ($rows as $i => $r): ?>
    <div class="question-card" style="margin-top:15px;">
      <div class="question-text">
        #<?= $i + 1 ?> —
        <?= htmlspecialchars((string)$r['tanggal']) ?>
        <?= htmlspecialchars((string)$r['pukul']) ?> WIB
      </div>

      <div style="margin-bottom:10px;">
        <b>Alamat:</b> <?= htmlspecialchars((string)$r['alamat']) ?><br>
        <b>No HP:</b> <?= htmlspecialchars((string)$r['no_hp']) ?>
      </div>

      <div>
        <b>Keluhan/Saran:</b><br>
        <?= nl2br(htmlspecialchars((string)$r['masukan'])) ?>
      </div>
    </div>
  <?php endforeach; ?>

</div>