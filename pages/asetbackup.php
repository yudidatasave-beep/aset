<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

require dirname(__DIR__) . '/includes/db.php';

// Tambah data aset
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah'])) {
    $imagePath = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = uniqid('aset_') . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = 'uploads/' . $filename;
        }
    }

    $stmt = $conn->prepare("INSERT INTO aset (kode_aset, id_ruangan, nama_aset, jenis_aset, merek, tipe, nomor_seri, kondisi_alat, kepemilikan, tanggal_perolehan, nilai_perolehan, status_penyusutan, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissssssssdss",
        $_POST['kode_aset'],
        $_POST['id_ruangan'],
        $_POST['nama_aset'],
        $_POST['jenis_aset'],
        $_POST['merek'],
        $_POST['tipe'],
        $_POST['nomor_seri'],
        $_POST['kondisi_alat'],
        $_POST['kepemilikan'],
        $_POST['tanggal_perolehan'],
        $_POST['nilai_perolehan'],
        $_POST['status_penyusutan'],
        $imagePath
    );
    $stmt->execute();
    header("Location: aset.php");
    exit;
}

// Hapus data aset
if (isset($_GET['hapus'])) {
    // Hapus gambar dari folder
    $stmt = $conn->prepare("SELECT image FROM aset WHERE id_aset = ?");
    $stmt->bind_param("i", $_GET['hapus']);
    $stmt->execute();
    $stmt->bind_result($imageToDelete);
    $stmt->fetch();
    $stmt->close();
    if ($imageToDelete && file_exists('../' . $imageToDelete)) {
        unlink('../' . $imageToDelete);
    }

    // Hapus dari database
    $stmt = $conn->prepare("DELETE FROM aset WHERE id_aset = ?");
    $stmt->bind_param("i", $_GET['hapus']);
    $stmt->execute();
    header("Location: aset.php");
    exit;
}

$ruangan = $conn->query("SELECT * FROM ruangan");
$aset = $conn->query("SELECT * FROM aset");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3 class="mb-4">Manajemen Aset</h3>

    <!-- Form Tambah Aset -->
    <form method="POST" class="card p-4 mb-4" enctype="multipart/form-data">
        <h5>Tambah Aset</h5>
        <div class="row g-2">
            <!-- kolom-kolom lainnya -->
            <div class="col-md-4">
                <input type="text" name="kode_aset" class="form-control" placeholder="Kode Aset" required>
            </div>
            <div class="col-md-4">
                <select name="id_ruangan" class="form-select" required>
                    <option value="">-- Pilih Ruangan --</option>
                    <?php while ($r = $ruangan->fetch_assoc()): ?>
                        <option value="<?= $r['id_ruangan'] ?>"><?= $r['nama_ruangan'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="nama_aset" class="form-control" placeholder="Nama Aset" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="jenis_aset" class="form-control" placeholder="Jenis Aset">
            </div>
            <div class="col-md-4">
                <input type="text" name="merek" class="form-control" placeholder="Merek">
            </div>
            <div class="col-md-4">
                <input type="text" name="tipe" class="form-control" placeholder="Tipe">
            </div>
            <div class="col-md-4">
                <input type="text" name="nomor_seri" class="form-control" placeholder="Nomor Seri">
            </div>
            <div class="col-md-4">
                <select name="kondisi_alat" class="form-select">
                    <option value="aktif">Aktif</option>
                    <option value="tidak aktif">Tidak Aktif</option>
                    <option value="rusak">Rusak</option>
                    <option value="diperbaiki">Diperbaiki</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="kepemilikan" class="form-select">
                    <option value="RSUD Cibabat">RSUD Cibabat</option>
                    <option value="KSO">KSO</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="date" name="tanggal_perolehan" class="form-control">
            </div>
            <div class="col-md-4">
                <input type="number" step="0.01" name="nilai_perolehan" class="form-control" placeholder="Nilai Perolehan">
            </div>
            <div class="col-md-4">
                <select name="status_penyusutan" class="form-select">
                    <option value="belum dihitung">Belum Dihitung</option>
                    <option value="dihitung">Dihitung</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
        </div>
        <button type="submit" name="tambah" class="btn btn-primary mt-3">Tambah</button>
    </form>

    <!-- Tabel Aset -->
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>Image</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Merek</th>
                <th>Kondisi</th>
                <th>Kepemilikan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($a = $aset->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if ($a['image']): ?>
                        <img src="../<?= htmlspecialchars($a['image']) ?>" width="80" class="img-thumbnail">
                    <?php else: ?>
                        <span class="text-muted">-</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($a['kode_aset']) ?></td>
                <td><?= htmlspecialchars($a['nama_aset']) ?></td>
                <td><?= htmlspecialchars($a['jenis_aset']) ?></td>
                <td><?= htmlspecialchars($a['merek']) ?></td>
                <td><?= htmlspecialchars($a['kondisi_alat']) ?></td>
                <td><?= htmlspecialchars($a['kepemilikan']) ?></td>
                <td>
                    <a href="aset.php?hapus=<?= $a['id_aset'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
