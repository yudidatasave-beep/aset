<?php
require_once __DIR__ . '/../includes/db.php';

header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laporan_aset.xls");

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
?>

<table border="1">
    <tr>
        <th>No</th>
        <th>Nama Aset</th>
        <th>Jenis</th>
        <th>Kondisi</th>
        <th>Ruangan</th>
        <th>Gedung</th>
    </tr>
    <?php $no = 1; while ($row = $res->fetch_assoc()): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= $row['nama_aset'] ?></td>
        <td><?= $row['jenis_aset'] ?></td>
        <td><?= $row['kondisi_alat'] ?></td>
        <td><?= $row['nama_ruangan'] ?></td>
        <td><?= $row['nama_gedung'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>
