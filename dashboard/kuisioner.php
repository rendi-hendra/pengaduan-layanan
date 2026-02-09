<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

/* ================== DEFAULT SAFE ================== */
$error = null;
$rekapRows = [];
$summary = null;
$titleRekap = "Rekap per Layanan";

$today = date('Y-m-d');
$defaultStart = date('Y-m-d', strtotime('-6 days'));

$tgl_awal  = $_GET['tgl_awal']  ?? $defaultStart;
$tgl_akhir = $_GET['tgl_akhir'] ?? $today;
$mode      = $_GET['mode'] ?? 'layanan';

$layanan_id     = $_GET['layanan_id'] ?? '';
$penjamin_id    = $_GET['penjamin_id'] ?? '';
$jk_id          = $_GET['jk_id'] ?? '';
$pendidikan_id  = $_GET['pendidikan_id'] ?? '';
$pekerjaan_id   = $_GET['pekerjaan_id'] ?? '';

/* ================== LOAD MASTER FILTER ================== */
$pelayanan_rows = $pdo->query("SELECT id,nama FROM pelayanan ORDER BY nama")->fetchAll();
$penjamin_rows  = $pdo->query("SELECT id,nama FROM penjamin ORDER BY nama")->fetchAll();
$jk_rows        = $pdo->query("SELECT id,nama FROM jenis_kelamin ORDER BY id")->fetchAll();
$pend_rows      = $pdo->query("SELECT id,nama FROM pendidikan ORDER BY nama")->fetchAll();
$pkj_rows       = $pdo->query("SELECT id,nama FROM pekerjaan ORDER BY nama")->fetchAll();

/* ================== BUILD WHERE ================== */
$where = ["k.survey_date BETWEEN :awal AND :akhir"];
$params = [
  ':awal' => $tgl_awal,
  ':akhir' => $tgl_akhir
];

if ($layanan_id) {
  $where[] = "p.pelayanan_id = :layanan_id";
  $params[':layanan_id'] = $layanan_id;
}
if ($penjamin_id) {
  $where[] = "p.penjamin_id = :penjamin_id";
  $params[':penjamin_id'] = $penjamin_id;
}
if ($jk_id) {
  $where[] = "p.jenis_kelamin_id = :jk_id";
  $params[':jk_id'] = $jk_id;
}
if ($pendidikan_id) {
  $where[] = "p.pendidikan_id = :pendidikan_id";
  $params[':pendidikan_id'] = $pendidikan_id;
}
if ($pekerjaan_id) {
  $where[] = "p.pekerjaan_id = :pekerjaan_id";
  $params[':pekerjaan_id'] = $pekerjaan_id;
}

$whereSql = implode(" AND ", $where);

/* ================== MODE ================== */
$selectLabel = "pl.nama AS label";
$joinExtra   = "JOIN pelayanan pl ON pl.id = p.pelayanan_id";
$groupBy     = "pl.nama";

if ($mode == 'penjamin') {
  $titleRekap = "Rekap per Penjamin";
  $selectLabel = "pj.nama AS label";
  $joinExtra   = "JOIN penjamin pj ON pj.id = p.penjamin_id";
  $groupBy     = "pj.nama";
}

/* ================== QUERY ================== */
try {

  $stmt = $pdo->prepare("
    SELECT
        $selectLabel,
        COUNT(*) AS jumlah_jawaban,
        COUNT(DISTINCT k.profil_id) AS jumlah_responden,
        ROUND(AVG(k.nilai),2) AS avg_nilai
    FROM kuisioner k
    JOIN profil p ON p.id = k.profil_id
    $joinExtra
    WHERE $whereSql
    GROUP BY $groupBy
    ORDER BY avg_nilai DESC
");
  $stmt->execute($params);
  $rekapRows = $stmt->fetchAll();

  $stmtSum = $pdo->prepare("
    SELECT
        COUNT(*) AS total_jawaban,
        COUNT(DISTINCT k.profil_id) AS total_responden,
        ROUND(AVG(k.nilai),2) AS rata_nilai
    FROM kuisioner k
    JOIN profil p ON p.id = k.profil_id
    WHERE $whereSql
");
  $stmtSum->execute($params);
  $summary = $stmtSum->fetch();
} catch (Throwable $e) {
  $error = "Gagal mengambil data rekap.";
}

/* ================== EXPORT EXCEL ================== */
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=rekap_kepuasan.xls");

  echo "Label\tRata-rata\tJumlah Jawaban\tJumlah Responden\n";
  foreach ($rekapRows as $r) {
    echo "{$r['label']}\t{$r['avg_nilai']}\t{$r['jumlah_jawaban']}\t{$r['jumlah_responden']}\n";
  }
  exit;
}
?>

<?php
include 'layout/header.php';
include 'layout/sidebar.php';
include 'layout/nav.php';
?>

<div id="layoutSidenav_content">
  <main class="container-fluid px-4 mt-4">

    <div class="card shadow mb-4">
      <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Filter Laporan Kepuasan</h5>
      </div>
      <div class="card-body">

        <form method="GET" class="row g-3">

          <div class="col-md-3">
            <label>Tanggal Awal</label>
            <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>" class="form-control">
          </div>

          <div class="col-md-3">
            <label>Tanggal Akhir</label>
            <input type="date" name="tgl_akhir" value="<?= $tgl_akhir ?>" class="form-control">
          </div>

          <div class="col-md-3">
            <label>Mode Rekap</label>
            <select name="mode" class="form-select">
              <option value="layanan" <?= $mode == 'layanan' ? 'selected' : '' ?>>Layanan</option>
              <option value="penjamin" <?= $mode == 'penjamin' ? 'selected' : '' ?>>Penjamin</option>
            </select>
          </div>

          <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary w-100">
              <i class="fas fa-filter"></i> Tampilkan
            </button>
          </div>

          <div class="col-md-3">
            <label>Layanan</label>
            <select name="layanan_id" class="form-select">
              <option value="">Semua</option>
              <?php foreach ($pelayanan_rows as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $layanan_id == $r['id'] ? 'selected' : '' ?>>
                  <?= $r['nama'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-3">
            <label>Penjamin</label>
            <select name="penjamin_id" class="form-select">
              <option value="">Semua</option>
              <?php foreach ($penjamin_rows as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $penjamin_id == $r['id'] ? 'selected' : '' ?>>
                  <?= $r['nama'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2">
            <label>Jenis Kelamin</label>
            <select name="jk_id" class="form-select">
              <option value="">Semua</option>
              <?php foreach ($jk_rows as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $jk_id == $r['id'] ? 'selected' : '' ?>>
                  <?= $r['nama'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2">
            <label>Pendidikan</label>
            <select name="pendidikan_id" class="form-select">
              <option value="">Semua</option>
              <?php foreach ($pend_rows as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $pendidikan_id == $r['id'] ? 'selected' : '' ?>>
                  <?= $r['nama'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-2">
            <label>Pekerjaan</label>
            <select name="pekerjaan_id" class="form-select">
              <option value="">Semua</option>
              <?php foreach ($pkj_rows as $r): ?>
                <option value="<?= $r['id'] ?>" <?= $pekerjaan_id == $r['id'] ? 'selected' : '' ?>>
                  <?= $r['nama'] ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-12 mt-3">
            <a href="?<?= http_build_query($_GET + ['export' => 'excel']) ?>"
              class="btn btn-success">
              <i class="fas fa-file-excel"></i> Export Excel
            </a>
          </div>

        </form>

      </div>
    </div>

    <?php if ($summary): ?>
      <div class="alert alert-info">
        Total Jawaban: <b><?= $summary['total_jawaban'] ?></b> |
        Total Responden: <b><?= $summary['total_responden'] ?></b> |
        Rata-rata Nilai: <b><?= $summary['rata_nilai'] ?></b>
      </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
      <div class="card-body">

        <table id="tabelRekap" class="table table-bordered table-striped table-hover">
          <thead class="table-dark">
            <tr>
              <th>No</th>
              <th>Layanan</th>
              <th>Rata-rata</th>
              <th>Jumlah Jawaban</th>
              <th>Jumlah Responden</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rekapRows as $i => $r): ?>
              <tr>
                <td><?= $i + 1 ?></td>
                <td><?= $r['label'] ?></td>
                <td><?= $r['avg_nilai'] ?></td>
                <td><?= $r['jumlah_jawaban'] ?></td>
                <td><?= $r['jumlah_responden'] ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <hr>

        <h5>Grafik Rata-rata Nilai</h5>
        <canvas id="chartRekap"></canvas>
       
      </div>
    </div>

  </main>

  <?php include 'layout/footer.php'; ?>
</div>


<script>

$(function(){

    $('#tabelRekap').DataTable({
        pageLength: 10,
        dom: 'Bfltip',
        buttons: ['excel', 'pdf', 'print']
    });

});

</script>