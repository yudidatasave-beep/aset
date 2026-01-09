<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Tampilkan semua error
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once $_SERVER['DOCUMENT_ROOT'] . '/samcibabat/includes/db.php';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID aset tidak ditemukan.</div>";
    exit;
}

$id_aset = intval($_GET['id']);

// Cek koneksi database
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Ambil data aset dari database
$query = $conn->prepare("SELECT * FROM aset WHERE id_aset = ?");
if (!$query) {
    die("Prepare statement gagal: " . $conn->error);
}

$query->bind_param("i", $id_aset);
if (!$query->execute()) {
    die("Eksekusi query gagal: " . $query->error);
}

$result = $query->get_result();

if ($result->num_rows == 0) {
    echo "<div class='alert alert-warning'>Data aset tidak ditemukan.</div>";
    exit;
}

$row = $result->fetch_assoc();
?>

<div class="container mt-4">
    <h3>Edit Data Aset</h3>
    <form action="pages/update_aset.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id_aset" value="<?= $row['id_aset'] ?>">

        <div class="mb-3">
            <label for="kode_aset" class="form-label">Kode Aset</label>
            <input type="text" name="kode_aset" class="form-control" value="<?= htmlspecialchars($row['kode_aset']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="nama_aset" class="form-label">Nama Aset</label>
            <input type="text" name="nama_aset" class="form-control" value="<?= htmlspecialchars($row['nama_aset']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="merek" class="form-label">Merek</label>
            <input type="text" name="merek" class="form-control" value="<?= htmlspecialchars($row['merek']) ?>">
        </div>

        <div class="mb-3">
            <label for="tipe" class="form-label">Tipe</label>
            <input type="text" name="tipe" class="form-control" value="<?= htmlspecialchars($row['tipe']) ?>">
        </div>

        <div class="mb-3">
            <label for="nomor_seri" class="form-label">Nomor Seri</label>
            <input type="text" name="nomor_seri" class="form-control" value="<?= htmlspecialchars($row['nomor_seri']) ?>">
        </div>

        <div class="mb-3">
            <label for="tanggal_perolehan" class="form-label">Tanggal Perolehan</label>
            <input type="date" name="tanggal_perolehan" class="form-control" value="<?= $row['tanggal_perolehan'] ?>">
        </div>

        <div class="mb-3">
            <label for="nilai_perolehan" class="form-label">Nilai Perolehan</label>
            <input type="number" name="nilai_perolehan" class="form-control" value="<?= $row['nilai_perolehan'] ?>">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Gambar Aset</label><br>
            <?php if (!empty($row['image'])): ?>
                <img src="uploads/<?= $row['image'] ?>" class="img-thumbnail mb-2" width="150"><br>
            <?php endif; ?>
            <input type="file" name="image" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Update Aset</button>
        <a href="index.php?page=aset" class="btn btn-secondary">Kembali</a>
    </form>
</div>
