<?php
require_once __DIR__ . '/../includes/db.php';

$jenis_aset = $_GET['jenis_aset'] ?? '';
$id_gedung = $_GET['id_gedung'] ?? '';
$id_ruangan = $_GET['id_ruangan'] ?? '';
$kondisi = $_GET['kondisi'] ?? '';
$tanggal_dari = $_GET['tanggal_dari'] ?? '';
$tanggal_sampai = $_GET['tanggal_sampai'] ?? '';

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
if (!empty($id_ruangan)) {
    $sql .= " AND a.id_ruangan = ?";
    $paramTypes .= 'i';
    $params[] = $id_ruangan;
}
if (!empty($kondisi)) {
    $sql .= " AND a.kondisi_alat = ?";
    $paramTypes .= 's';
    $params[] = $kondisi;
}
if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
    $sql .= " AND a.tanggal_perolehan BETWEEN ? AND ?";
    $paramTypes .= 'ss';
    $params[] = $tanggal_dari;
    $params[] = $tanggal_sampai;
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
        <select name="id_gedung" class="form-select" onchange="this.form.submit()">
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
        <select name="id_ruangan" class="form-select">
            <option value="">-- Ruangan --</option>
            <?php
            if (!empty($id_gedung)) {
                $ruanganRes = $conn->query("SELECT * FROM ruangan WHERE id_gedung = $id_gedung");
                while ($r = $ruanganRes->fetch_assoc()) {
                    echo "<option value='{$r['id_ruangan']}' " . ($id_ruangan == $r['id_ruangan'] ? 'selected' : '') . ">{$r['nama_ruangan']}</option>";
                }
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
        <input type="date" name="tanggal_dari" class="form-control" value="<?= $tanggal_dari ?>" placeholder="Dari Tanggal Perolehan">
    </div>
    <div class="col-md-3">
        <input type="date" name="tanggal_sampai" class="form-control" value="<?= $tanggal_sampai ?>" placeholder="Sampai Tanggal Perolehan">
    </div>

    <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari</button>
    </div>
</form>

<div class="mb-3">
    <a href="export/export_excel.php?jenis_aset=<?= $jenis_aset ?>&id_gedung=<?= $id_gedung ?>&id_ruangan=<?= $id_ruangan ?>&kondisi=<?= $kondisi ?>&tanggal_dari=<?= $tanggal_dari ?>&tanggal_sampai=<?= $tanggal_sampai ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
    
    <a href="export/export_pdf.php?jenis_aset=<?= $jenis_aset ?>&id_gedung=<?= $id_gedung ?>&id_ruangan=<?= $id_ruangan ?>&kondisi=<?= $kondisi ?>&tanggal_dari=<?= $tanggal_dari ?>&tanggal_sampai=<?= $tanggal_sampai ?>" class="btn btn-danger btn-sm"><i class="bi bi-file-earmark-pdf"></i> Export PDF</a>
</div>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
			<th>Kode Aset</th>
            <th>Nama Aset</th>
            <th>Nomor Seri</th>
            <th>Model</th>
            <th>Jenis</th>
            <th>Kondisi</th>
            <th>Ruangan</th>
            <th>Kepemilikan</th>
            <th>Jenis Anggaran</th>
            <th>Perusahaan</th>
            <th>Kartu Kuning</th>
            <th>Kalibrasi</th>
            <th>AKL</th>
            <th>Tanggal Perolehan</th>
			<th>Nilai Perolehan</th>
			
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
			<td><?= htmlspecialchars($row['kode_aset']) ?></td>
            <td><?= htmlspecialchars($row['nama_aset']) ?></td>
            <td><?= htmlspecialchars($row['nomor_seri']) ?></td>
            <td><?= htmlspecialchars($row['model']) ?></td>
            <td><?= htmlspecialchars($row['jenis_aset']) ?></td>
            <td><?= htmlspecialchars($row['kondisi_alat']) ?></td>
            <td><?= htmlspecialchars($row['nama_ruangan']) ?></td>
            <td><?= htmlspecialchars($row['kepemilikan']) ?></td>
            <td><?= htmlspecialchars($row['jenis_anggaran']) ?></td>
            <td><?= htmlspecialchars($row['perusahaan']) ?></td>
            <td><?= htmlspecialchars($row['kartu_kuning']) ?></td>
            <td><?= htmlspecialchars($row['kalibrasi']) ?></td>
            <td><?= htmlspecialchars($row['akl']) ?></td>
            <td><?= htmlspecialchars($row['tanggal_perolehan']) ?></td>
			<td><?= htmlspecialchars($row['nilai_perolehan']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
