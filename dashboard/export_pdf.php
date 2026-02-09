<?php
session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/library/fpdf.php';

/* =========================
   AMBIL FILTER
========================= */

$tgl_awal   = $_GET['tgl_awal']   ?? date('Y-m-d');
$tgl_akhir  = $_GET['tgl_akhir']  ?? date('Y-m-d');
$layanan    = $_GET['layanan']    ?? '';
$penjamin   = $_GET['penjamin']   ?? '';
$jk         = $_GET['jk']         ?? '';
$pendidikan = $_GET['pendidikan'] ?? '';
$pekerjaan  = $_GET['pekerjaan']  ?? '';

$where = "WHERE DATE(tanggal) BETWEEN :tgl_awal AND :tgl_akhir";

$params = [
    ':tgl_awal'  => $tgl_awal,
    ':tgl_akhir' => $tgl_akhir
];

if($layanan != ''){
    $where .= " AND layanan = :layanan";
    $params[':layanan'] = $layanan;
}
if($penjamin != ''){
    $where .= " AND penjamin = :penjamin";
    $params[':penjamin'] = $penjamin;
}
if($jk != ''){
    $where .= " AND jenis_kelamin = :jk";
    $params[':jk'] = $jk;
}
if($pendidikan != ''){
    $where .= " AND pendidikan = :pendidikan";
    $params[':pendidikan'] = $pendidikan;
}
if($pekerjaan != ''){
    $where .= " AND pekerjaan = :pekerjaan";
    $params[':pekerjaan'] = $pekerjaan;
}

$sql = "
SELECT layanan, COUNT(*) as jumlah, ROUND(AVG(nilai),2) as rata_rata
FROM kuisioner
$where
GROUP BY layanan
ORDER BY rata_rata DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   GENERATE PDF
========================= */

$pdf = new FPDF('L','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'REKAP KUISIONER',0,1,'C');

$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,"Periode: $tgl_awal s/d $tgl_akhir",0,1,'C');
$pdf->Ln(5);

/* HEADER TABLE */
$pdf->SetFont('Arial','B',12);
$pdf->Cell(100,10,'Layanan',1);
$pdf->Cell(50,10,'Jumlah Responden',1);
$pdf->Cell(50,10,'Rata-rata Nilai',1);
$pdf->Ln();

/* DATA */
$pdf->SetFont('Arial','',12);

foreach($data as $row){
    $pdf->Cell(100,10,$row['layanan'],1);
    $pdf->Cell(50,10,$row['jumlah'],1);
    $pdf->Cell(50,10,$row['rata_rata'],1);
    $pdf->Ln();
}

$pdf->Output('I','rekap_kuisioner.pdf');
exit;