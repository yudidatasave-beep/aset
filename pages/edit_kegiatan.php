<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . "/../includes/db.php";

// Ambil id_kegiatan
$id_kegiatan = $_GET['id_kegiatan'] ?? 0;
if (!$id_kegiatan) {
    die("ID kegiatan tidak ditemukan!");
}

// Ambil data kegiatan
$sql = "SELECT * FROM kegiatan WHERE id_kegiatan = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_kegiatan);
$stmt->execute();
$result = $stmt->get_result();
$kegiatan = $result->fetch_assoc();

if (!$kegiatan) {
    die("Data kegiatan tidak ditemukan!");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_aset        = $_POST['id_aset'];
    $id_teknisi     = $_POST['id_teknisi'];
    $id_asisten     = $_POST['id_asisten'];
    $jenis_kegiatan = $_POST['jenis_kegiatan'];
    $keluhan        = $_POST['keluhan'];
    $tindakan       = $_POST['tindakan'];
    $kesimpulan     = $_POST['kesimpulan'];
    $waktu_laporan  = $_POST['waktu_laporan'];
    $waktu_respon   = $_POST['waktu_respon'];
    $waktu_selesai  = $_POST['waktu_selesai'];
    $nomor_lk       = $_POST['nomor_lk'];
    $id_user_ruangan= $_POST['id_user_ruangan'];
    $biaya_perawatan= $_POST['biaya_perawatan'];
    $image          = $_POST['image_lama'] ?? '';

    // Proses upload gambar baru (jika ada)
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $image = $fileName;
        }
    }

    $sql = "UPDATE kegiatan SET 
                id_aset = ?, 
                id_teknisi = ?, 
                id_asisten = ?, 
                jenis_kegiatan = ?, 
                keluhan = ?, 
                tindakan = ?, 
                image = ?, 
                kesimpulan = ?, 
                waktu_laporan = ?, 
                waktu_respon = ?, 
                waktu_selesai = ?, 
                nomor_lk = ?, 
                id_user_ruangan = ?, 
                biaya_perawatan = ?
            WHERE id_kegiatan = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "iiisssssssssidi", 
        $id_aset, 
        $id_teknisi, 
        $id_asisten, 
        $jenis_kegiatan, 
        $keluhan, 
        $tindakan, 
        $image, 
        $kesimpulan, 
        $waktu_laporan, 
        $waktu_respon, 
        $waktu_selesai, 
        $nomor_lk, 
        $id_user_ruangan, 
        $biaya_perawatan, 
        $id_kegiatan
    );

    if ($stmt->execute()) {
    echo "<script>
            alert('Data kegiatan berhasil diperbarui!');
            window.location.href='../index.php?page=laporan_kegiatan';
          </script>";
    exit;
}
}
?>

<div class="container mt-4">
    <h3 class="mb-4">Edit Kegiatan</h3>
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <!-- ID Aset -->
            <div class="col-md-6 mb-3">
                <label class="form-label">ID Aset</label>
                <input type="number" name="id_aset" value="<?= htmlspecialchars($kegiatan['id_aset']) ?>" class="form-control" required>
            </div>

            <!-- Teknisi -->
            <div class="col-md-6 mb-3">
                <label class="form-label">ID Teknisi</label>
                <input type="number" name="id_teknisi" value="<?= htmlspecialchars($kegiatan['id_teknisi']) ?>" class="form-control" required>
            </div>

            <!-- Asisten -->
            <div class="col-md-6 mb-3">
                <label class="form-label">ID Asisten</label>
                <input type="number" name="id_asisten" value="<?= htmlspecialchars($kegiatan['id_asisten']) ?>" class="form-control">
            </div>

            <!-- Jenis Kegiatan -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Jenis Kegiatan</label>
                <select name="jenis_kegiatan" class="form-control" required>
                    <option value="Perbaikan" <?= $kegiatan['jenis_kegiatan'] === 'Perbaikan' ? 'selected' : '' ?>>Perbaikan</option>
                    <option value="Pemeliharaan" <?= $kegiatan['jenis_kegiatan'] === 'Pemeliharaan' ? 'selected' : '' ?>>Pemeliharaan</option>
                </select>
            </div>

            <!-- Keluhan -->
            <div class="col-md-12 mb-3">
                <label class="form-label">Keluhan</label>
                <textarea name="keluhan" class="form-control"><?= htmlspecialchars($kegiatan['keluhan']) ?></textarea>
            </div>

            <!-- Tindakan -->
            <div class="col-md-12 mb-3">
                <label class="form-label">Tindakan</label>
                <textarea name="tindakan" class="form-control"><?= htmlspecialchars($kegiatan['tindakan']) ?></textarea>
            </div>

            <!-- Gambar -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Gambar Kegiatan</label><br>
                <?php if (!empty($kegiatan['image']) && file_exists("../uploads/" . $kegiatan['image'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($kegiatan['image']) ?>" alt="Gambar Kegiatan" 
                         class="img-thumbnail mb-2" style="max-height: 200px;">
                    <input type="hidden" name="image_lama" value="<?= htmlspecialchars($kegiatan['image']) ?>">
                <?php else: ?>
                    <p class="text-muted">Belum ada gambar.</p>
                <?php endif; ?>
                <input type="file" name="image" class="form-control" accept="image/*">
                <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar.</small>
            </div>

            <!-- Kesimpulan -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Kesimpulan</label>
                <input type="text" name="kesimpulan" value="<?= htmlspecialchars($kegiatan['kesimpulan']) ?>" class="form-control">
            </div>

            <!-- Waktu -->
            <div class="col-md-4 mb-3">
                <label class="form-label">Waktu Laporan</label>
                <input type="datetime-local" name="waktu_laporan" value="<?= $kegiatan['waktu_laporan'] ?>" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Waktu Respon</label>
                <input type="datetime-local" name="waktu_respon" value="<?= $kegiatan['waktu_respon'] ?>" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Waktu Selesai</label>
                <input type="datetime-local" name="waktu_selesai" value="<?= $kegiatan['waktu_selesai'] ?>" class="form-control">
            </div>

            <!-- Nomor LK -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor LK</label>
                <input type="text" name="nomor_lk" value="<?= htmlspecialchars($kegiatan['nomor_lk']) ?>" class="form-control">
            </div>

            <!-- User Ruangan -->
            <div class="col-md-6 mb-3">
                <label class="form-label">ID User Ruangan</label>
                <input type="number" name="id_user_ruangan" value="<?= htmlspecialchars($kegiatan['id_user_ruangan']) ?>" class="form-control">
            </div>

            <!-- Biaya Perawatan -->
            <div class="col-md-6 mb-3">
                <label class="form-label">Biaya Perawatan (Rp)</label>
                <input type="number" name="biaya_perawatan" value="<?= htmlspecialchars($kegiatan['biaya_perawatan']) ?>" class="form-control">
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="../index.php?page=laporan_kegiatan" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
