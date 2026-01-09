<?php
require_once __DIR__ . '/../includes/db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=laporan_kegiatan.xls");

$jenis_kegiatan = $_GET['jenis_kegiatan'] ?? '';
$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';
$id_teknisi = $_GET['id_teknisi'] ?? '';

$sql = "SELECT k.*, a.nama_aset, t.nama_teknisi 
        FROM kegiatan k
        LEFT JOIN aset a ON k.id_aset = a.id_aset
        LEFT JOIN teknisi t ON k.id_teknisi = t.id_teknisi
        WHERE 1=1";

if (!empty($jenis_kegiatan)) $sql .= " AND k.jenis_kegiatan = '" . $conn->real_escape_string($jenis_kegiatan) . "'";
if (!empty($dari) && !empty($sampai)) $sql .= " AND DATE(k.waktu_laporan) BETWEEN '$dari' AND '$sampai'";
if (!empty($id_teknisi)) $sql .= " AND k.id_teknisi = '" . intval($id_teknisi) . "'";

$result = $conn->query($sql);
?>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Aset</th>
            <th>Jenis Kegiatan</th>
            <th>Tindakan</th>
            <th>Teknisi</th>
            <th>Waktu Laporan</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_aset']) ?></td>
            <td><?= htmlspecialchars($row['jenis_kegiatan']) ?></td>
            <td><?= htmlspecialchars($row['tindakan']) ?></td>
            <td><?= htmlspecialchars($row['nama_teknisi']) ?></td>
            <td><?= date('d-m-Y H:i', strtotime($row['waktu_laporan'])) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
