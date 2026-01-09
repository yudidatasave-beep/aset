<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/samcibabat/includes/db.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

// Ambil kata kunci pencarian
$search = $_GET['search'] ?? '';

// Query dasar
$where = "WHERE 1=1";
$params = [];
$paramTypes = "";

if (!empty($search)) {
    $where .= " AND (a.kode_aset LIKE ? 
                  OR a.nama_aset LIKE ? 
                  OR j.nama_jenis_aset LIKE ?
                  OR a.merek LIKE ?
                  OR a.tipe LIKE ?
                  OR r.nama_ruangan LIKE ?)";
    $searchParam = "%" . $search . "%";
    $params = [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam];
    $paramTypes = "ssssss";
}

// Hitung total data
$totalQuery = $conn->prepare("SELECT COUNT(*) as total 
                              FROM aset a
                              LEFT JOIN jenis_aset j ON a.id_jenis_aset = j.id_jenis_aset
                              LEFT JOIN ruangan r ON a.id_ruangan = r.id_ruangan
                              $where");
if (!empty($params)) {
    $totalQuery->bind_param($paramTypes, ...$params);
}
$totalQuery->execute();
$totalResult = $totalQuery->get_result();
$totalData = $totalResult->fetch_assoc()['total'] ?? 0;
$totalPages = ceil($totalData / $limit);

// Ambil data aset
$sql = "
SELECT a.*, j.nama_jenis_aset, r.nama_ruangan, m.merek_aset
FROM aset a
LEFT JOIN jenis_aset j ON a.id_jenis_aset = j.id_jenis_aset
LEFT JOIN ruangan r ON a.id_ruangan = r.id_ruangan
LEFT JOIN merek_aset m ON a.id_merek_aset = m.id_merek_aset
$where
ORDER BY a.id_aset DESC
LIMIT $start, $limit
";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($paramTypes, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .img-aset {
            max-width: 100px;
            height: auto;
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">Data Aset</h2>

    <!-- Form Pencarian -->
    <form method="GET" class="row g-2 mb-3">
        <input type="hidden" name="page" value="aset">
        <div class="col-md-10">
            <input type="text" name="search" class="form-control" placeholder="Cari aset (kode, nama, jenis, merek, tipe, ruangan)" value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Cari</button>
        </div>
    </form>

    <!-- Tombol Aksi -->
    <div class="mb-3">
        <a href="index.php?page=form_input_aset" class="btn btn-primary">+ Tambah Aset</a>
    </div>

    <table class="table table-bordered table-striped table-hover align-middle text-center">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Gambar</th>
                <th>Kode Aset</th>
                <th>Nama Aset</th>
                <th>Jenis Aset</th>
                <th>Merek</th>
                <th>Tipe</th>
                <th>Lokasi Aset</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php $no = $start + 1; ?>
                <?php while ($aset = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <?php
                            $imageName = $aset['image'] ?? '';
                            $imageURL = '/aset/uploads/' . $imageName;
                            $imagePath = $_SERVER['DOCUMENT_ROOT'] . '/aset/uploads/' . $imageName;

                            if (!empty($imageName) && file_exists($imagePath)) {
                                echo "<img src='$imageURL' class='img-thumbnail' style='width: 80px; height: auto;' alt='Gambar Aset'>";
                            } else {
                                echo "<div class='text-muted fst-italic'>Tidak ada gambar</div>";
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($aset['kode_aset']) ?></td>
                        <td><?= htmlspecialchars($aset['nama_aset']) ?></td>
                        <td><?= htmlspecialchars($aset['nama_jenis_aset']) ?></td>
                        <td><?= htmlspecialchars($aset['merek_aset']) ?></td>
                        <td><?= htmlspecialchars($aset['tipe']) ?></td>
                        <td><?= htmlspecialchars($aset['nama_ruangan']) ?></td>
                        <td>
                            <a href="index.php?page=edit_aset&id_aset=<?= $aset['id_aset'] ?>" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="pages/hapus_aset.php?id_aset=<?= $aset['id_aset'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus aset ini?')">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                            <a href="pages/histori_aset.php?id_aset=<?= $aset['id_aset'] ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-info-circle"></i> Detail
                            </a>
                            <button class="btn btn-primary btn-sm" onclick="printQRCode(<?= $aset['id_aset'] ?>)">
                                🖨️ QR Aset
                            </button>
                            <?php if (!empty($aset['spo'])): ?>
                                <button class="btn btn-success btn-sm" onclick="printSOPQRCode('<?= $aset['spo'] ?>')">
                                    📄 QR SOP
                                </button>
                            <?php endif; ?>
                            <a href="/samcibabat/index.php?page=kegiatan_detail&id_aset=<?= $aset['id_aset'] ?>" class="btn btn-secondary btn-sm">
                                📋 Kegiatan
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center text-danger">Data tidak ditemukan</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Sebelumnya</a></li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Berikutnya</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
function printQRCode(id_aset) {
    const url = "https://rsudcibabat.cimahikota.go.id/samcibabat/pages/histori_aset.php?id_aset=" + id_aset;
    QRCode.toDataURL(url, { width: 200 }, function (err, dataUrl) {
        if (err) return alert("Gagal generate QR");
        let w = window.open();
        w.document.write("<h3>QR Code Aset #" + id_aset + "</h3>");
        w.document.write("<img src='" + dataUrl + "'>");
        w.print();
    });
}

function printSOPQRCode(spoUrl) {
    const url = "http://localhost/samcibabat/" + spoUrl;
    QRCode.toDataURL(url, { width: 200 }, function (err, dataUrl) {
        if (err) return alert("Gagal generate QR");
        let w = window.open();
        w.document.write("<h3>QR Code SOP</h3>");
        w.document.write("<img src='" + dataUrl + "'>");
        w.print();
    });
}
</script>

</body>
</html>
