<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../fpdf/fpdf.php';

$jenis_kegiatan = $_GET['jenis_kegiatan'] ?? '';
$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';
$id_teknisi = $_GET['id_teknisi'] ?? '';

$sql = "SELECT k.*, a.nama_aset, t.nama_teknisi 
        FROM kegiatan k
        LEFT JOIN aset a ON k.id_aset = a.id_aset
        LEFT JOIN teknisi t ON k.id_teknisi = t.id_teknisi
        WHERE 1=1";

$params = [];
if (!empty($jenis_kegiatan)) $sql .= " AND k.jenis_kegiatan = '" . $conn->real_escape_string($jenis_kegiatan) . "'";
if (!empty($dari) && !empty($sampai)) $sql .= " AND DATE(k.waktu_laporan) BETWEEN '$dari' AND '$sampai'";
if (!empty($id_teknisi)) $sql .= " AND k.id_teknisi = '" . intval($id_teknisi) . "'";

$result = $conn->query($sql);

$pdf = new FPDF();
$pdf->AddPage('L');
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Laporan Kegiatan Aset',0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(10,10,'No',1);
$pdf->Cell(50,10,'Nama Aset',1);
$pdf->Cell(30,10,'Jenis',1);
$pdf->Cell(30,10,'Tindakan',1);
$pdf->Cell(30,10,'Teknisi',1);
$pdf->Cell(40,10,'Waktu Laporan',1);
$pdf->Ln();

$pdf->SetFont('Arial','',10);
$no = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(10,8,$no++,1);
    $pdf->Cell(50,8,$row['nama_aset'],1);
    $pdf->Cell(30,8,$row['jenis_kegiatan'],1);
    $pdf->Cell(30,8,substr($row['tindakan'], 0, 20),1);
    $pdf->Cell(30,8,$row['nama_teknisi'],1);
    $pdf->Cell(40,8,date('d-m-Y H:i', strtotime($row['waktu_laporan'])),1);
    $pdf->Ln();
}
$pdf->Output();
