<?php
require_once __DIR__ . '/../includes/db.php';

$jenis_kegiatan = $_GET['jenis_kegiatan'] ?? '';
$tanggal_dari   = $_GET['tanggal_dari'] ?? '';
$tanggal_sampai = $_GET['tanggal_sampai'] ?? '';
$id_teknisi     = $_GET['id_teknisi'] ?? '';

// Ambil daftar teknisi untuk filter dropdown
$teknisi_list = [];
$res_teknisi = $conn->query("SELECT id_teknisi, nama_teknisi FROM teknisi ORDER BY nama_teknisi ASC");
while ($row_t = $res_teknisi->fetch_assoc()) {
    $teknisi_list[] = $row_t;
}

$sql = "SELECT k.*, t.nama_teknisi, a.nama_aset, r.nama_ruangan 
        FROM kegiatan k
        LEFT JOIN teknisi t ON k.id_teknisi = t.id_teknisi
        LEFT JOIN aset a ON k.id_aset = a.id_aset
        LEFT JOIN ruangan r ON a.id_ruangan = r.id_ruangan
        WHERE 1=1";

$paramTypes = '';
$params = [];

if (!empty($jenis_kegiatan)) {
    $sql .= " AND k.jenis_kegiatan = ?";
    $paramTypes .= 's';
    $params[] = $jenis_kegiatan;
}
if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
    $sql .= " AND DATE(k.waktu_laporan) BETWEEN ? AND ?";
    $paramTypes .= 'ss';
    $params[] = $tanggal_dari;
    $params[] = $tanggal_sampai;
}
if (!empty($id_teknisi)) {
    $sql .= " AND k.id_teknisi = ?";
    $paramTypes .= 'i';
    $params[] = $id_teknisi;
}

$sql .= " ORDER BY k.waktu_laporan DESC";

$stmt = $conn->prepare($sql);
if ($paramTypes) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<h4>Laporan Kegiatan</h4>

<form method="GET" class="row g-2 mb-3">
    <input type="hidden" name="page" value="laporan_kegiatan">

    <!-- Filter Jenis Kegiatan -->
    <div class="col-md-3">
        <select name="jenis_kegiatan" class="form-select">
            <option value="">-- Jenis Kegiatan --</option>
            <option value="Perbaikan" <?= ($jenis_kegiatan == 'Perbaikan') ? 'selected' : '' ?>>Perbaikan</option>
            <option value="Pemeliharaan" <?= ($jenis_kegiatan == 'Pemeliharaan') ? 'selected' : '' ?>>Pemeliharaan</option>
        </select>
    </div>

    <!-- Filter Tanggal Dari -->
    <div class="col-md-2">
        <input type="date" name="tanggal_dari" class="form-control" value="<?= htmlspecialchars($tanggal_dari) ?>" placeholder="Dari Tanggal">
    </div>

    <!-- Filter Tanggal Sampai -->
    <div class="col-md-2">
        <input type="date" name="tanggal_sampai" class="form-control" value="<?= htmlspecialchars($tanggal_sampai) ?>" placeholder="Sampai Tanggal">
    </div>

    <!-- Filter Teknisi -->
    <div class="col-md-3">
        <select name="id_teknisi" class="form-select">
            <option value="">-- Pilih Teknisi --</option>
            <?php foreach ($teknisi_list as $t): ?>
                <option value="<?= $t['id_teknisi'] ?>" <?= ($id_teknisi == $t['id_teknisi']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t['nama_teknisi']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Tombol Cari -->
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
    </div>
</form>

<div class="mb-3">
    <a href="export/export_kegiatan_excel.php?jenis_kegiatan=<?= urlencode($jenis_kegiatan) ?>&tanggal_dari=<?= urlencode($tanggal_dari) ?>&tanggal_sampai=<?= urlencode($tanggal_sampai) ?>&id_teknisi=<?= urlencode($id_teknisi) ?>" 
       class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
    <a href="export/export_kegiatan_pdf.php?jenis_kegiatan=<?= urlencode($jenis_kegiatan) ?>&tanggal_dari=<?= urlencode($tanggal_dari) ?>&tanggal_sampai=<?= urlencode($tanggal_sampai) ?>&id_teknisi=<?= urlencode($id_teknisi) ?>" 
       class="btn btn-danger btn-sm"><i class="bi bi-file-earmark-pdf"></i> Export PDF</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>Jenis Kegiatan</th>
            <th>Nama Aset</th>
            <th>Ruangan</th>
            <th>Keluhan</th>
            <th>Tindakan</th>
            <th>Teknisi</th>
            <th>Waktu Laporan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['jenis_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['nama_aset']) ?></td>
                <td><?= htmlspecialchars($row['nama_ruangan']) ?></td>
                <td><?= htmlspecialchars($row['keluhan']) ?></td>
                <td><?= htmlspecialchars($row['tindakan']) ?></td>
                <td><?= htmlspecialchars($row['nama_teknisi']) ?></td>
                <td><?= date('d-m-Y H:i', strtotime($row['waktu_laporan'])) ?></td>
                <td>
    <!-- Tombol Cetak -->
    <a href="index.php?page=cetak_kegiatan&id_kegiatan=<?= (int)$row['id_kegiatan'] ?>" 
       target="_blank" 
       class="btn btn-sm btn-outline-primary">
        <i class="bi bi-printer"></i> Cetak
    </a>

    <!-- Tombol Edit -->
    <a href="index.php?page=edit_kegiatan&id_kegiatan=<?= (int)$row['id_kegiatan'] ?>" 
       class="btn btn-sm btn-warning">
        <i class="bi bi-pencil-square"></i> Edit
    </a>

    <!-- Tombol Hapus -->
    <a href="pages/hapus_kegiatan.php?id_kegiatan=<?= (int)$row['id_kegiatan'] ?>" 
       class="btn btn-sm btn-danger" 
       onclick="return confirm('Yakin ingin menghapus kegiatan ini?')">
        <i class="bi bi-trash"></i> Hapus
    </a>
</td>

            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" class="text-center">Tidak ada data</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
