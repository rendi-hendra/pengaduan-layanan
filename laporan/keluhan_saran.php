<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';

$sql = "
SELECT
*
FROM
keluhan 
";


$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
include 'layout/header.php';
include 'layout/nav.php';
include 'layout/sidebar.php';
?>

<div id="layoutSidenav_content">
    <main class="container-fluid px-4 mt-4">
        <h1 class="mt-4">Laporan Keluhan dan Saran</h1>
        <div class="mt-4">
            <div class="row">
                <div class="col-3">
                    <label class="form-label">Filter Tanggal</label>
                    <input type="text" id="dateRange" class="form-control" readonly>
                </div>
            </div>
            <div class="table-responsive mt-4">
                <table id="tabelRekap" class="table table-striped">
                    <thead class="table-dark sticky-top">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Pukul</th>
                            <th>Alamat</th>
                            <th>No. HP</th>
                            <th class="text-center">Masukan</th>
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
                                <td><?= htmlspecialchars($row['masukan']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <?php include 'layout/footer.php'; ?>
</div>


<script>
    let startDate = null;
    let endDate = null;

    $(function() {

        $.fn.dataTable.ext.search.push(function(settings, data) {
            if (!startDate || !endDate) return true;

            const tanggal = data[1];

            if (!tanggal) return false;

            const rowDate = moment(tanggal, 'YYYY-MM-DD');

            return rowDate.isSameOrAfter(startDate) && rowDate.isSameOrBefore(endDate);
        });

        // Init DataTable
        table = $('#tabelRekap').DataTable({
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100],
            responsive: true,
            fixedHeader: true,
            order: [
                [0, 'asc']
            ], // default sort tanggal terbaru
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
                    width: "20px",
                    className: "text-center"
                },
                {
                    targets: 1,
                    width: "100px",
                    className: "text-center"
                },
                {
                    targets: 2,
                    width: "50px",
                    className: "text-center"
                },
                {
                    targets: 3,
                    width: "300px",
                    className: "text-center"
                },
                {
                    targets: 4,
                    width: "100px",
                    className: "text-center"
                },
                {
                    targets: 5,
                    width: "700px",
                    className: ""
                },
            ],
            columns: [,
                {
                    orderSequence: ['desc', 'asc']
                },
                {
                    orderSequence: ['', '']
                },
                {
                    orderSequence: ['', '']
                },
                {
                    orderSequence: ['', '']
                },
                {
                    orderSequence: ['', '']
                }
            ]
        });

        table.on('order.dt search.dt draw.dt', function() {
            table.column(0, {
                    search: 'applied',
                    order: 'applied'
                })
                .nodes()
                .each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
        });

        // Default hari ini
        const today = moment().startOf('day');

        $('#dateRange').daterangepicker({
            startDate: today,
            endDate: today,
            autoUpdateInput: true,
            locale: {
                format: 'YYYY-MM-DD',
                separator: ' s/d ',
                applyLabel: 'Terapkan',
                cancelLabel: 'Batal'
            },
            ranges: {
                'Hari Ini': [today, today],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), today],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), today],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')]
            }
        }, function(start, end) {
            startDate = start.startOf('day');
            endDate = end.endOf('day');
            table.draw();
        });
        startDate = today;
        endDate = today;
        table.draw();

        $('#tabelRekap tbody').on('mouseenter', 'td', function() {
            var colIdx = table.cell(this).index().column;

            $(table.cells().nodes()).removeClass('highlight');
            $(table.column(colIdx).nodes()).addClass('highlight');
        });

    });
</script>