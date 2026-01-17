<?php
require_once __DIR__ . '/../includes/db.php';

/* =======================
   AMBIL FILTER
======================= */
$id_jenis_aset   = $_GET['id_jenis_aset'] ?? '';
$id_gedung       = $_GET['id_gedung'] ?? '';
$id_ruangan      = $_GET['id_ruangan'] ?? '';
$kondisi         = $_GET['kondisi'] ?? '';
$tanggal_dari    = $_GET['tanggal_dari'] ?? '';
$tanggal_sampai  = $_GET['tanggal_sampai'] ?? '';

/* =======================
   QUERY UTAMA
======================= */
$sql = "SELECT 
            a.kode_aset,
            a.nama_aset,
            a.nomor_seri,
            a.model,
            ja.nama_jenis_aset,
            a.kondisi_alat,
            r.nama_ruangan,
            g.nama_gedung,
            a.kalibrasi,
            a.tanggal_perolehan
        FROM aset a
        LEFT JOIN jenis_aset ja ON a.id_jenis_aset = ja.id_jenis_aset
        LEFT JOIN ruangan r ON a.id_ruangan = r.id_ruangan
        LEFT JOIN gedung g ON r.id_gedung = g.id_gedung
        WHERE 1=1";

$paramTypes = '';
$params = [];

/* =======================
   FILTER DINAMIS
======================= */
if (!empty($id_jenis_aset)) {
    $sql .= " AND a.id_jenis_aset = ?";
    $paramTypes .= 'i';
    $params[] = $id_jenis_aset;
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

/* =======================
   EKSEKUSI QUERY
======================= */
$stmt = $conn->prepare($sql);
if (!empty($paramTypes)) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<h4 class="mb-3">Laporan Aset</h4>

<!-- =======================
     FORM FILTER
======================= -->
<form method="GET" class="row g-2 mb-3">
    <input type="hidden" name="page" value="laporan_aset">

    <!-- JENIS ASET -->
    <div class="col-md-3">
        <select name="id_jenis_aset" class="form-select">
            <option value="">-- Jenis Aset --</option>
            <?php
            $jenisRes = $conn->query("SELECT * FROM jenis_aset ORDER BY nama_jenis_aset");
            while ($j = $jenisRes->fetch_assoc()):
                $selected = ($id_jenis_aset == $j['id_jenis_aset']) ? 'selected' : '';
            ?>
                <option value="<?= $j['id_jenis_aset'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($j['nama_jenis_aset']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- GEDUNG -->
    <div class="col-md-3">
        <select name="id_gedung" class="form-select" onchange="this.form.submit()">
            <option value="">-- Gedung --</option>
            <?php
            $gedungRes = $conn->query("SELECT * FROM gedung ORDER BY nama_gedung");
            while ($g = $gedungRes->fetch_assoc()):
                $selected = ($id_gedung == $g['id_gedung']) ? 'selected' : '';
            ?>
                <option value="<?= $g['id_gedung'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($g['nama_gedung']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!-- RUANGAN -->
    <div class="col-md-3">
        <select name="id_ruangan" class="form-select">
            <option value="">-- Ruangan --</option>
            <?php
            if (!empty($id_gedung)) {
                $ruanganRes = $conn->query("SELECT * FROM ruangan WHERE id_gedung = $id_gedung ORDER BY nama_ruangan");
                while ($r = $ruanganRes->fetch_assoc()):
                    $selected = ($id_ruangan == $r['id_ruangan']) ? 'selected' : '';
            ?>
                <option value="<?= $r['id_ruangan'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($r['nama_ruangan']) ?>
                </option>
            <?php endwhile; } ?>
        </select>
    </div>

    <!-- KONDISI -->
    <div class="col-md-3">
        <select name="kondisi" class="form-select">
            <option value="">-- Kondisi --</option>
            <option value="Baik" <?= $kondisi=='Baik'?'selected':'' ?>>Baik</option>
            <option value="Rusak Ringan" <?= $kondisi=='Rusak Ringan'?'selected':'' ?>>Rusak Ringan</option>
            <option value="Rusak Berat" <?= $kondisi=='Rusak Berat'?'selected':'' ?>>Rusak Berat</option>
        </select>
    </div>

    <!-- TANGGAL -->
    <div class="col-md-3">
        <input type="date" name="tanggal_dari" class="form-control" value="<?= $tanggal_dari ?>">
    </div>
    <div class="col-md-3">
        <input type="date" name="tanggal_sampai" class="form-control" value="<?= $tanggal_sampai ?>">
    </div>

    <div class="col-md-3">
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-search"></i> Cari
        </button>
    </div>
</form>

<!-- =======================
     EXPORT
======================= -->
<div class="mb-3">
    <a href="export/export_excel.php?id_jenis_aset=<?= $id_jenis_aset ?>&id_gedung=<?= $id_gedung ?>&id_ruangan=<?= $id_ruangan ?>&kondisi=<?= $kondisi ?>&tanggal_dari=<?= $tanggal_dari ?>&tanggal_sampai=<?= $tanggal_sampai ?>"
       class="btn btn-success btn-sm">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
    </a>

    <a href="export/export_pdf.php?id_jenis_aset=<?= $id_jenis_aset ?>&id_gedung=<?= $id_gedung ?>&id_ruangan=<?= $id_ruangan ?>&kondisi=<?= $kondisi ?>&tanggal_dari=<?= $tanggal_dari ?>&tanggal_sampai=<?= $tanggal_sampai ?>"
       class="btn btn-danger btn-sm">
        <i class="bi bi-file-earmark-pdf"></i> Export PDF
    </a>
</div>

<!-- =======================
     TABEL HASIL
======================= -->
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Aset</th>
            <th>Nama Aset</th>
            <th>Nomor Seri</th>
            <th>Model</th>
            <th>Jenis Aset</th>
            <th>Kondisi</th>
            <th>Ruangan</th>
            <th>Kalibrasi</th>
            <th>Tanggal Perolehan</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; while($row=$result->fetch_assoc()): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['kode_aset']) ?></td>
            <td><?= htmlspecialchars($row['nama_aset']) ?></td>
            <td><?= htmlspecialchars($row['nomor_seri']) ?></td>
            <td><?= htmlspecialchars($row['model']) ?></td>
            <td><?= htmlspecialchars($row['nama_jenis_aset']) ?></td>
            <td><?= htmlspecialchars($row['kondisi_alat']) ?></td>
            <td><?= htmlspecialchars($row['nama_ruangan']) ?></td>
            <td><?= htmlspecialchars($row['kalibrasi']) ?></td>
            <td><?= htmlspecialchars($row['tanggal_perolehan']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
