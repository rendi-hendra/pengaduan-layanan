<?php
require_once "config.php";

$conn = pg_connect("host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_pass");
if (!$conn) die("Koneksi gagal");

$limit = 10;
$page  = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$page  = max($page, 1);
$offset = ($page - 1) * $limit;


$q = pg_query($conn, "
    SELECT id, created_at, nama_pasien, alamat, survey_date, survey_time,
           gender, education, jobs, services, keluhan, nomor_hp,
           q1,q2,q3,q4,q5,q6,q7,q8,q9
    FROM kuesioner
    ORDER BY id DESC
    LIMIT $limit OFFSET $offset
");
$total_q = pg_query($conn, "SELECT COUNT(*) FROM kuesioner");
$total_row = pg_fetch_row($total_q);
$total_data = (int)$total_row[0];
$total_page = ceil($total_data / $limit);

if (!$q) die(pg_last_error($conn));
?>
<!doctype html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <title>Data Kuesioner</title>

  <style>
    :root {
      --primary: #0d6efd;
      --soft: #e7f1ff;
      --border: #dbe7ff;
      --text: #0f172a;
      --muted: #64748b;
    }

    * {
      box-sizing: border-box;
      font-family: "Segoe UI", system-ui, sans-serif;
    }

    body {
      background: #f4f8ff;
      margin: 0;
      padding: 30px;
      color: var(--text);
    }

    /* ===== CARD ===== */
    .card {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
      padding: 24px;
    }

    .card-header {
      background: var(--soft);
      border-left: 6px solid var(--primary);
      padding: 16px 20px;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .card-header h2 {
      margin: 0;
      font-size: 20px;
      font-weight: 700;
    }

    .card-header p {
      margin: 6px 0 0;
      font-size: 14px;
      color: var(--muted);
    }

    /* ===== TABLE ===== */
    .table-wrap {
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 1000px;
    }

    thead th {
      background: var(--primary);
      color: #fff;
      padding: 12px;
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: .5px;
      position: sticky;
      top: 0;
      z-index: 1;
    }

    tbody td {
      padding: 12px;
      border-bottom: 1px solid var(--border);
      font-size: 14px;
      text-align: center;
    }

    tbody tr:hover {
      background: #f8fbff;
    }

    td.left {
      text-align: left;
      white-space: nowrap;
    }

    /* ===== BADGE NILAI ===== */
    .badge {
      display: inline-block;
      min-width: 28px;
      padding: 4px 8px;
      border-radius: 6px;
      font-weight: 700;
      font-size: 13px;
      color: #fff;
    }

    .n1 {
      background: #dc2626;
    }

    /* Tidak Sesuai */
    .n2 {
      background: #f59e0b;
    }

    /* Kurang */
    .n3 {
      background: #0ea5e9;
    }

    /* Sesuai */
    .n4 {
      background: #16a34a;
    }

    /* Sangat */

    /* ===== FOOTNOTE ===== */
    .legend {
      margin-top: 16px;
      font-size: 13px;
      color: var(--muted);
    }

    .legend span {
      margin-right: 3px;
      margin-left: 10px;
    }

    /* ===== BUTTON ===== */
    .btn-detail {
      background: #0d6efd;
      color: #fff;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 13px;
    }

    .btn-detail:hover {
      background: #0b5ed7;
    }

    /* ===== MODAL ===== */
    .modal-overlay {
      position: fixed;
      inset: 0;
      background: rgba(15, 23, 42, 0.5);
      display: none;
      align-items: center;
      justify-content: center;
      z-index: 999;
    }

    .modal-box {
      background: #fff;
      width: 420px;
      border-radius: 14px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, .2);
      animation: zoom .2s ease;
    }

    @keyframes zoom {
      from {
        transform: scale(.95);
        opacity: 0
      }

      to {
        transform: scale(1);
        opacity: 1
      }
    }

    .modal-header {
      padding: 16px 20px;
      background: #e7f1ff;
      border-left: 6px solid #0d6efd;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-header h3 {
      margin: 0;
      font-size: 18px;
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 22px;
      cursor: pointer;
    }

    .modal-body {
      padding: 20px;
    }

    .detail-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
    }

    .detail-table td {
      padding: 8px 6px;
      font-size: 14px;
    }

    .detail-table td:first-child {
      width: 35%;
      color: #64748b;
    }

    .keluhan-box {
      background: #f8fbff;
      border-left: 4px solid #0d6efd;
      padding: 12px;
      border-radius: 8px;
      font-size: 14px;
    }

    /* ===== PAGINATION ===== */
    .pagination {
      margin-top: 24px;
      display: flex;
      justify-content: center;
      gap: 6px;
      flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
      padding: 8px 14px;
      border-radius: 8px;
      font-size: 14px;
      text-decoration: none;
      color: var(--primary);
      background: #fff;
      border: 1px solid var(--border);
    }

    .pagination a:hover {
      background: var(--soft);
    }

    .pagination .active {
      background: var(--primary);
      color: #fff;
      font-weight: 700;
      border-color: var(--primary);
    }

    .pagination .disabled {
      color: #94a3b8;
      background: #f1f5f9;
      border-color: #e2e8f0;
      cursor: not-allowed;
    }


    /* ===== INFO LIST ===== */
    .info-list {
      display: grid;
      row-gap: 10px;
      margin-bottom: 16px;
    }

    .info-item {
      display: grid;
      grid-template-columns: 120px 1fr;
      gap: 10px;
      font-size: 14px;
    }

    .info-item .label {
      color: #64748b;
    }

    .info-item .value {
      font-weight: 600;
      color: #0f172a;
      word-break: break-word;
    }

    /* MOBILE FRIENDLY */
    @media (max-width: 480px) {
      .info-item {
        grid-template-columns: 1fr;
      }

      .info-item .label {
        font-size: 12px;
        text-transform: uppercase;
      }
    }
  </style>
</head>

<body>

  <div class="card">
    <div class="card-header">
      <h2>Data Kuesioner Kepuasan Pasien</h2>
      <p>10 data terbaru · Skala penilaian 1–4</p>
    </div>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>No.</th>
            <th>Created</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Tanggal</th>
            <th>Jam</th>
            <th>Detail</th>
            <th>Q1</th>
            <th>Q2</th>
            <th>Q3</th>
            <th>Q4</th>
            <th>Q5</th>
            <th>Q6</th>
            <th>Q7</th>
            <th>Q8</th>
            <th>Q9</th>

          </tr>
        </thead>

        <tbody>
          <?php $no = $offset + 1; ?>
          <?php while ($r = pg_fetch_assoc($q)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td class="left"><?= $r['created_at'] ?></td>
              <td class="left"><?= htmlspecialchars($r['nama_pasien']) ?></td>
              <td class="left"><?= htmlspecialchars($r['alamat']) ?></td>
              <td><?= $r['survey_date'] ?></td>
              <td><?= $r['survey_time'] ?></td>
              <td>
                <button class="btn-detail"
                  data-nama="<?= htmlspecialchars($r['nama_pasien']) ?>"
                  data-alamat="<?= htmlspecialchars($r['alamat'] ?? '-') ?>"
                  data-education="<?= htmlspecialchars($r['education'] ?? '-') ?>"
                  data-jobs="<?= htmlspecialchars($r['jobs'] ?? '-') ?>"
                  data-services="<?= htmlspecialchars($r['services'] ?? '-') ?>"
                  data-keluhan="<?= htmlspecialchars($r['keluhan'] ?? 'Tidak ada keluhan') ?>">
                  👁
                </button>
              </td>


              <?php for ($i = 1; $i <= 9; $i++):
                $v = (int)$r["q$i"]; ?>
                <td>
                  <span class="badge n<?= $v ?>"><?= $v ?></span>
                </td>
              <?php endfor; ?>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="legend">
      <strong>Skala:</strong>
      <span class="badge n1">1</span>Tidak Sesuai
      <span class="badge n2">2</span>Kurang Sesuai
      <span class="badge n3">3</span>Sesuai
      <span class="badge n4">4</span>Sangat Sesuai
    </div>
    <?php if ($total_page > 1): ?>
            <div class="pagination">
  
              <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">‹ Prev</a>
              <?php else: ?>
                <span class="disabled">‹ Prev</span>
              <?php endif; ?>
  
              <?php for ($i = 1; $i <= $total_page; $i++): ?>
                <?php if ($i == $page): ?>
                  <span class="active"><?= $i ?></span>
                <?php else: ?>
                  <a href="?page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
              <?php endfor; ?>
  
              <?php if ($page < $total_page): ?>
                <a href="?page=<?= $page + 1 ?>">Next ›</a>
              <?php else: ?>
                <span class="disabled">Next ›</span>
              <?php endif; ?>
  
            </div>
          <?php endif; ?>
  </div>

  <div class="modal-overlay" id="modalDetail">
    <div class="modal-box">
      <div class="modal-header">
        <h3>Detail Responden</h3>
        <button class="modal-close" onclick="closeModal()">×</button>
      </div>

      <div class="modal-body">

        <div class="info-list">
          <div class="info-item">
            <span class="label">Nama</span>
            <span class="value" id="d_nama"></span>
          </div>

          <div class="info-item">
            <span class="label">Pendidikan</span>
            <span class="value" id="d_education"></span>
          </div>

          <div class="info-item">
            <span class="label">Pekerjaan</span>
            <span class="value" id="d_jobs"></span>
          </div>

          <div class="info-item">
            <span class="label">Alamat</span>
            <span class="value" id="d_alamat"></span>
          </div>

          <div class="info-item">
            <span class="label">Layanan</span>
            <span class="value" id="d_services"></span>
          </div>
        </div>

        <div class="keluhan-box">
          <strong>Keluhan / Saran</strong>
          <p id="d_keluhan"></p>
        </div>
      </div>
      
      <script>
        const modal = document.getElementById('modalDetail');

        document.querySelectorAll('.btn-detail').forEach(btn => {
          btn.addEventListener('click', () => {
            document.getElementById('d_nama').textContent = btn.dataset.nama;
            document.getElementById('d_education').textContent = btn.dataset.education;
            document.getElementById('d_jobs').textContent = btn.dataset.jobs;
            document.getElementById('d_alamat').textContent = btn.dataset.alamat;
            document.getElementById('d_keluhan').textContent = btn.dataset.keluhan;
            document.getElementById('d_services').textContent = btn.dataset.services;
            modal.style.display = 'flex';
          });
        });

        function closeModal() {
          modal.style.display = 'none';
        }

        modal.addEventListener('click', e => {
          if (e.target === modal) closeModal();
        });
      </script>

</body>

</html>