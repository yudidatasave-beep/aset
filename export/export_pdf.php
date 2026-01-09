<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../fpdf/fpdf.php';

$jenis_aset = $_GET['jenis_aset'] ?? '';
$kondisi = $_GET['kondisi'] ?? '';
$id_gedung = $_GET['id_gedung'] ?? '';

$sql = "SELECT a.*, r.nama_ruangan, g.nama_gedung 
        FROM aset a 
        LEFT JOIN ruangan r ON a.id_ruangan = r.id_ruangan 
        LEFT JOIN gedung g ON r.id_gedung = g.id_gedung 
        WHERE 1=1";

if (!empty($jenis_aset)) $sql .= " AND a.jenis_aset = '$jenis_aset'";
if (!empty($kondisi)) $sql .= " AND a.kondisi_alat = '$kondisi'";
if (!empty($id_gedung)) $sql .= " AND g.id_gedung = '$id_gedung'";

$res = $conn->query($sql);

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,10,'Laporan Aset',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,10,'No',1);
$pdf->Cell(40,10,'Nama Aset',1);
$pdf->Cell(30,10,'Jenis',1);
$pdf->Cell(30,10,'Kondisi',1);
$pdf->Cell(40,10,'Ruangan',1);
$pdf->Cell(40,10,'Gedung',1);
$pdf->Ln();

$pdf->SetFont('Arial','',10);
$no = 1;
while ($row = $res->fetch_assoc()) {
    $pdf->Cell(10,8,$no++,1);
    $pdf->Cell(40,8,$row['nama_aset'],1);
    $pdf->Cell(30,8,$row['jenis_aset'],1);
    $pdf->Cell(30,8,$row['kondisi_alat'],1);
    $pdf->Cell(40,8,$row['nama_ruangan'],1);
    $pdf->Cell(40,8,$row['nama_gedung'],1);
    $pdf->Ln();
}

$pdf->Output();
