<?php
require_once __DIR__ . '/../includes/db.php';

$jenis_aset = $_GET['jenis_aset'] ?? '';
$id_gedung = $_GET['id_gedung'] ?? '';
$kondisi = $_GET['kondisi'] ?? '';

$sql = "SELECT a.*, r.nama_ruangan, g.nama_gedung 
        FROM aset a
        LEFT JOIN ruangan r ON a.id_ruangan = r.id_ruangan
        LEFT JOIN gedung g ON r.id_gedung = g.id_gedung
        WHERE 1=1";

$paramTypes = '';
$params = [];

if (!empty($jenis_aset)) {
    $sql .= " AND a.jenis_aset = ?";
    $paramTypes .= 's';
    $params[] = $jenis_aset;
}
if (!empty($id_gedung)) {
    $sql .= " AND g.id_gedung = ?";
    $paramTypes .= 'i';
    $params[] = $id_gedung;
}
if (!empty($kondisi)) {
    $sql .= " AND a.kondisi_alat = ?";
    $paramTypes .= 's';
    $params[] = $kondisi;
}

$stmt = $conn->prepare($sql);
if ($paramTypes) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<h4>Laporan Aset</h4>

<form method="GET" class="row g-2 mb-3">
    <input type="hidden" name="page" value="laporan_aset">
    <div class="col-md-3">
        <select name="jenis_aset" class="form-select">
            <option value="">-- Jenis Aset --</option>
            <option value="Medis" <?= ($jenis_aset == 'Medis') ? 'selected' : '' ?>>Medis</option>
            <option value="Non Medis" <?= ($jenis_aset == 'Non Medis') ? 'selected' : '' ?>>Non Medis</option>
        </select>
    </div>
    <div class="col-md-3">
        <select name="id_gedung" class="form-select">
            <option value="">-- Gedung --</option>
            <?php
            $gedungRes = $conn->query("SELECT * FROM gedung");
            while ($g = $gedungRes->fetch_assoc()) {
                echo "<option value='{$g['id_gedung']}' " . ($id_gedung == $g['id_gedung'] ? 'selected' : '') . ">{$g['nama_gedung']}</option>";
            }
            ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="kondisi" class="form-select">
            <option value="">-- Kondisi --</option>
            <option value="Baik" <?= ($kondisi == 'Baik') ? 'selected' : '' ?>>Baik</option>
            <option value="Rusak Ringan" <?= ($kondisi == 'Rusak Ringan') ? 'selected' : '' ?>>Rusak Ringan</option>
            <option value="Rusak Berat" <?= ($kondisi == 'Rusak Berat') ? 'selected' : '' ?>>Rusak Berat</option>
        </select>
    </div>
    <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
    </div>
</form>

<div class="mb-3">
    <a href="export/export_excel.php?jenis_aset=<?= $jenis_aset ?>&id_gedung=<?= $id_gedung ?>&kondisi=<?= $kondisi ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
    <a href="export/export_pdf.php?jenis_aset=<?= $jenis_aset ?>&id_gedung=<?= $id_gedung ?>&kondisi=<?= $kondisi ?>" class="btn btn-danger btn-sm"><i class="bi bi-file-earmark-pdf"></i> Export PDF</a>
</div>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Aset</th>
            <th>Jenis</th>
            <th>Kondisi</th>
            <th>Ruangan</th>
            <th>Gedung</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['nama_aset']) ?></td>
            <td><?= htmlspecialchars($row['jenis_aset']) ?></td>
            <td><?= htmlspecialchars($row['kondisi_alat']) ?></td>
            <td><?= htmlspecialchars($row['nama_ruangan']) ?></td>
            <td><?= htmlspecialchars($row['nama_gedung']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
