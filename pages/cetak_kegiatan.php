<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/db.php';

$id_kegiatan = isset($_GET['id_kegiatan']) ? (int)$_GET['id_kegiatan'] : 0;
if ($id_kegiatan <= 0) {
    die("ID Kegiatan tidak valid.");
}

// Query data lengkap + NIP
$sql = "SELECT k.*, 
               a.nama_aset, a.merek, a.tipe AS model, a.kode_aset AS no_inventori, a.nomor_seri, r.nama_ruangan,
               t.nama_teknisi, t.nip AS nip_teknisi,
               s.nama_asisten, s.nip AS nip_asisten,
               u.nama_user_ruangan, u.nip AS nip_user_ruangan,
               ki.nama_kepala_ipsrs, ki.nip AS nip_kepala_instalasi
        FROM kegiatan k
        LEFT JOIN aset a ON k.id_aset = a.id_aset
        LEFT JOIN ruangan r ON a.id_ruangan = r.id_ruangan
        LEFT JOIN teknisi t ON k.id_teknisi = t.id_teknisi
        LEFT JOIN asisten s ON k.id_asisten = s.id_asisten
        LEFT JOIN user_ruangan u ON k.id_user_ruangan = u.id_user_ruangan
        LEFT JOIN kepala_ipsrs ki ON k.id_kepala_ipsrs = ki.id_kepala_ipsrs
        WHERE k.id_kegiatan = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Query gagal dipersiapkan: " . $conn->error);
}
$stmt->bind_param("i", $id_kegiatan);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    die("ID Kegiatan tidak ditemukan.");
}

// Fungsi untuk path gambar
function getImagePath($file, $subfolder = '') {
    if (!$file) return '';
    $path = "/samcibabat/uploads/" . ($subfolder ? $subfolder . '/' : '') . $file;
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
        return $path;
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cetak Kegiatan</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
    body { font-family: Arial, sans-serif; font-size: 14px; }
    .header-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
    .header-table td { vertical-align: middle; border: 1px solid #000; padding: 4px; }
    .header-logo { text-align: center; }
    .header-logo img { height: 80px; }
    .header-title { font-weight: bold; font-size: 18px; }
    .section-title { font-weight: bold; margin-top: 20px; border-bottom: 1px solid #000; }
    table.info-table td { padding: 4px 6px; vertical-align: top; }
    .foto-kegiatan img { max-width: 200px; margin: 5px; border: 1px solid #ccc; }
    table.signature-table { width: 100%; border-collapse: collapse; text-align: center; }
    table.signature-table td, table.signature-table th { border: 1px solid #000; padding: 5px; vertical-align: middle; }
    table.signature-table img { max-height: 80px; display: block; margin: auto; }
</style>
</head>
<body onload="window.print()">

<!-- HEADER -->
<table class="header-table">
    <tr>
        <td rowspan="3" class="header-logo" width="120">
            <img src="/samcibabat/assets/logo.png" alt="Logo RS">
        </td>
        <td class="header-title"><?= htmlspecialchars("RUMAH SAKIT CIBABAT") ?></td>
    </tr>
    <tr>
        <td>
            <strong>Laporan Kegiatan:</strong> <?= htmlspecialchars($data['jenis_kegiatan']) ?>
            (<?= date('d-m-Y H:i', strtotime($data['waktu_laporan'])) ?>)
        </td>
    </tr>
    <tr>
        <td><strong>Nomor LK:</strong> <?= htmlspecialchars($data['nomor_lk']) ?></td>
    </tr>
</table>

<!-- SPESIFIKASI ALAT -->
<h5 class="section-title">Spesifikasi Alat</h5>
<table class="table table-bordered info-table">
    <tr>
        <td>Nama Alat</td><td><?= htmlspecialchars($data['nama_aset']) ?></td>
        <td>Merek</td><td><?= htmlspecialchars($data['merek']) ?></td>
    </tr>
    <tr>
        <td>Model</td><td><?= htmlspecialchars($data['model']) ?></td>
        <td>No Inventori</td><td><?= htmlspecialchars($data['no_inventori']) ?></td>
    </tr>
    <tr>
        <td>Nomor Seri</td><td><?= htmlspecialchars($data['nomor_seri']) ?></td>
        <td>Ruangan</td><td><?= htmlspecialchars($data['nama_ruangan']) ?></td>
    </tr>
</table>

<!-- DETAIL KEGIATAN -->
<h5 class="section-title">Detail Kegiatan</h5>
<table class="table table-bordered info-table">
    <tr>
        <td>Keluhan</td><td><?= htmlspecialchars($data['keluhan']) ?></td>
    </tr>
    <tr>
        <td>Tindakan</td><td><?= htmlspecialchars($data['tindakan']) ?></td>
    </tr>
    <tr>
        <td>Kesimpulan</td><td><?= htmlspecialchars($data['kesimpulan']) ?></td>
    </tr>
</table>

<!-- FOTO -->
<h5 class="section-title">Foto Kegiatan</h5>
<div class="foto-kegiatan">
<?php
if (!empty($data['image'])) {
    $files = explode(',', $data['image']);
    foreach ($files as $file) {
        $path = getImagePath(trim($file));
        if ($path) {
            echo "<img src='$path'>";
        }
    }
} else {
    echo "<p><em>Tidak ada foto kegiatan.</em></p>";
}
?>
</div>

<!-- TANDA TANGAN -->
<h5 class="section-title">Tanda Tangan</h5>
<table class="signature-table">
    <tr>
        <th>Teknisi<br><?= htmlspecialchars($data['nama_teknisi']) ?></th>
        <th>Asisten<br><?= htmlspecialchars($data['nama_asisten']) ?></th>
        <th>User Ruangan<br><?= htmlspecialchars($data['nama_user_ruangan']) ?></th>
        <th>Kepala Instalasi<br><?= htmlspecialchars($data['nama_kepala_ipsrs']) ?></th>
    </tr>
    <tr>
        <td><?= ($path = getImagePath($data['tanda_tangan_teknisi'], 'signatures')) ? "<img src='$path'>" : '-' ?></td>
        <td><?= ($path = getImagePath($data['tanda_tangan_asisten'], 'signatures')) ? "<img src='$path'>" : '-' ?></td>
        <td><?= ($path = getImagePath($data['tanda_tangan_kepala_ruangan'], 'signatures')) ? "<img src='$path'>" : '-' ?></td>
        <td><?= ($path = getImagePath($data['tanda_tangan_kepala_instalasi'], 'signatures')) ? "<img src='$path'>" : '-' ?></td>
    </tr>
    <tr>
        <td>NIP: <?= htmlspecialchars($data['nip_teknisi'] ?? '-') ?></td>
        <td>NIP: <?= htmlspecialchars($data['nip_asisten'] ?? '-') ?></td>
        <td>NIP: <?= htmlspecialchars($data['nip_user_ruangan'] ?? '-') ?></td>
        <td>NIP: <?= htmlspecialchars($data['nip_kepala_instalasi'] ?? '-') ?></td>
    </tr>
</table>

</body>
</html>
