<?php
include_once 'includes/db.php';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Query data aset dengan join
$sql = "
SELECT a.nama_aset,a.kode_aset, j.nama_jenis_aset,r.nama_ruangan,a.merek,a.tipe,a.nilai_perolehan,a.tanggal_perolehan, a.kondisi_alat,a.image
FROM aset a
LEFT JOIN jenis_aset j ON a.id_jenis_aset = j.id_jenis_aset
LEFT JOIN ruangan r ON a.id_ruangan = r.id_ruangan
ORDER BY a.id_aset DESC
LIMIT $start, $limit
";

$result = $conn->query($sql);
if (!$result) {
    die("Query Error: " . $conn->error);
}

// Total record
$totalResult = $conn->query("SELECT COUNT(*) as total FROM aset");
$totalRows = ($totalResult && $totalResult->num_rows > 0) ? $totalResult->fetch_assoc()['total'] : 0;
$totalPages = ceil($totalRows / $limit);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f9ff; }
        .table img { max-width: 80px; max-height: 80px; object-fit: cover; border-radius: 8px; }
        .card { box-shadow: 0 2px 10px rgba(0,0,0,0.1); border-radius: 16px; }
        .pagination a { margin: 0 5px; }
        .nav-blue { background-color: #254E70; color: white; padding: 10px 20px; }
    </style>
</head>
<body>
    <div class="nav-blue">
        <h3>📋 Daftar Aset</h3>
    </div>

    <div class="container mt-4 mb-5">
        <div class="card p-4">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Gambar</th>
                            <th>Kode Aset</th>
                            <th>Nama Aset</th>
                            <th>Jenis Aset</th>
                            <th>Merek</th>
                            <th>Tipe</th>
                            <th>Ruangan</th>
                            <th>Tgl Perolehan</th>
                            <th>Nilai Perolehan</th>
                            <th>Kondisi</th>
                     
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $no = $start + 1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
    <?php if (!empty($row['image']) && file_exists(__DIR__ . '/../uploads/' . $row['image'])): ?>
        <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" width="80" alt="Gambar Aset">
    <?php else: ?>
        <span>-</span>
    <?php endif; ?>
</td>
                                    <td><?= htmlspecialchars($row['kode_aset']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_aset']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_jenis_aset'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['merek']) ?></td>
                                    <td><?= htmlspecialchars($row['tipe']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_ruangan'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['tanggal_perolehan']) ?></td>
                                    <td><?= number_format($row['nilai_perolehan'], 0, ',', '.') ?></td>
                                    <td><?= htmlspecialchars($row['kondisi_alat']) ?></td>
                                    
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="12">Data tidak ditemukan</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</body>
</html>
