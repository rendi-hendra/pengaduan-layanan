<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

$sql = "SELECT * FROM keluhan ORDER BY tanggal DESC, pukul DESC";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'layout/header.php'; ?>

<div id="layoutSidenav_content">
    <main class="container-fluid px-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Laporan Keluhan & Saran</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Data Keluhan & Saran Pasien
                </h6>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Filter Tanggal</label>
                        <input type="text" id="dateRange" class="form-control" readonly>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="tabelRekap" class="table table-bordered" width="100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pukul</th>
                                <th>Alamat</th>
                                <th>No. HP</th>
                                <th>Masukan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rows as $row): ?>
                                <tr>
                                    <td></td>
                                    <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($row['pukul']) ?></td>
                                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                    <td><?= nl2br(htmlspecialchars($row['masukan'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include 'layout/footer.php'; ?>

<script>
    let table;
    let startDate = null;
    let endDate = null;

    $(function() {

        table = $('#tabelRekap').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            responsive: true,
            fixedHeader: true,
            order: [
                [1, 'desc']
            ], // SORT TANGGAL
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                paginate: {
                    previous: "‹",
                    next: "›"
                },
                emptyTable: "Tidak ada data",
                zeroRecords: "Data tidak ditemukan"
            },
            columnDefs: [{
                    targets: 0,
                    orderable: false,
                    searchable: false,
                    className: "text-center"
                },
                {
                    targets: [1, 2, 4],
                    className: "text-center"
                },
                {
                    targets: 5,
                    width: "40%"
                }
            ]
        });

        // ================= NOMOR OTOMATIS =================
        table.on('order.dt search.dt draw.dt', function() {
            table.column(0, {
                    search: 'applied',
                    order: 'applied'
                })
                .nodes()
                .each((cell, i) => cell.innerHTML = i + 1);
        });

        // ================= FILTER TANGGAL =================
        $.fn.dataTable.ext.search.push(function(settings, data) {
            if (!startDate || !endDate) return true;

            const rowDate = moment(data[1], 'YYYY-MM-DD', true);
            if (!rowDate.isValid()) return false;

            return rowDate.isBetween(startDate, endDate, null, '[]');
        });

        const today = moment().startOf('day');

        $('#dateRange').daterangepicker({
            startDate: today,
            endDate: today,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }, function(start, end) {
            startDate = start.startOf('day');
            endDate = end.endOf('day');
            table.draw();
        });

        startDate = today;
        endDate = today;
        table.draw();

    });
</script>