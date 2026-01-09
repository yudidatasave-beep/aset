<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require '../includes/db.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Cek teknisi
$stmtTeknisi = $conn->prepare("SELECT id_teknisi, nama_teknisi FROM teknisi WHERE id_user = ?");
$stmtTeknisi->bind_param("i", $id_user);
$stmtTeknisi->execute();
$resTeknisi = $stmtTeknisi->get_result();
$teknisi = $resTeknisi->fetch_assoc();
$id_teknisi = $teknisi['id_teknisi'] ?? '';
$nama_teknisi = $teknisi['nama_teknisi'] ?? '';

// Cek ID aset
$id_aset = $_GET['id_aset'] ?? '';
$stmt = $conn->prepare("SELECT a.*, r.nama_ruangan FROM aset a JOIN ruangan r ON a.id_ruangan = r.id_ruangan WHERE a.id_aset = ?");
$stmt->bind_param("i", $id_aset);
$stmt->execute();
$aset = $stmt->get_result()->fetch_assoc();

if (!$aset) {
    echo "<div class='alert alert-danger'>Aset tidak ditemukan.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Kegiatan Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h3 class="mb-4 text-primary">📋 Form Kegiatan Pemeliharaan / Perbaikan</h3>

    <!-- Detail Aset -->
    <div class="card mb-4 shadow">
        <div class="card-header bg-primary text-white">Informasi Aset</div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-4">Kode Aset</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($aset['kode_aset']) ?></dd>

                <dt class="col-sm-4">Nama Aset</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($aset['nama_aset']) ?></dd>

                <dt class="col-sm-4">Jenis</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($aset['jenis_aset']) ?></dd>

                <dt class="col-sm-4">Merek / Tipe</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($aset['merek']) ?> / <?= htmlspecialchars($aset['tipe']) ?></dd>

                <dt class="col-sm-4">Nomor Seri</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($aset['nomor_seri']) ?></dd>

                <dt class="col-sm-4">Lokasi</dt>
                <dd class="col-sm-8"><?= htmlspecialchars($aset['nama_ruangan']) ?></dd>
            </dl>
        </div>
    </div>

    <!-- Form Kegiatan -->
    <div class="card shadow">
        <div class="card-header bg-success text-white">Form Input Kegiatan</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id_aset" value="<?= $aset['id_aset'] ?>">
                <input type="hidden" name="id_teknisi" value="<?= $id_teknisi ?>">

                <div class="mb-3">
                    <label>Teknisi</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($nama_teknisi) ?>" readonly>
                </div>

                <div class="mb-3">
                    <label>Asisten</label>
                    <select name="id_asisten" class="form-select" required>
                        <option value="">-- Pilih Asisten --</option>
                        <?php
                        $q = $conn->query("SELECT id_asisten, nama_asisten FROM asisten");
                        while ($row = $q->fetch_assoc()) {
                            echo "<option value='{$row['id_asisten']}'>{$row['nama_asisten']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Jenis Kegiatan</label>
                    <select name="jenis_kegiatan" class="form-select" required>
                        <option value="pemeliharaan">Pemeliharaan</option>
                        <option value="perbaikan">Perbaikan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Keluhan</label>
                    <input type="text" name="keluhan" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Tindakan</label>
                    <input type="text" name="tindakan" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Kesimpulan</label>
                    <input type="text" name="kesimpulan" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Waktu Laporan</label>
                    <input type="datetime-local" name="waktu_laporan" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Waktu Respon</label>
                    <input type="datetime-local" name="waktu_respon" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Waktu Selesai</label>
                    <input type="datetime-local" name="waktu_selesai" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Nomor Laporan Kerja (LK)</label>
                    <input type="text" name="nomor_lk" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>User Ruangan</label>
                    <select name="id_user_ruangan" class="form-select" required>
                        <option value="">-- Pilih User Ruangan --</option>
                        <?php
                        $uq = $conn->query("SELECT id_user_ruangan, nama_user_ruangan FROM user_ruangan");
                        while ($ur = $uq->fetch_assoc()) {
                            echo "<option value='{$ur['id_user_ruangan']}'>{$ur['nama_user_ruangan']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Upload Gambar (maks 3)</label>
                    <input type="file" name="image[]" class="form-control" accept="image/*" multiple required>
                    <small class="text-muted">Boleh upload hingga 3 gambar</small>
                </div>

                <button type="submit" name="simpan" class="btn btn-success">💾 Simpan Kegiatan</button>
                <a href="form_kegiatan.php" class="btn btn-secondary">⬅ Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php
if (isset($_POST['simpan'])) {
    // Upload image (max 3)
    $imagePaths = [];
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    foreach ($_FILES['image']['tmp_name'] as $i => $tmp) {
        if ($i >= 3) break;
        if ($tmp) {
            $filename = uniqid() . '_' . $_FILES['image']['name'][$i];
            $path = $uploadDir . $filename;
            if (move_uploaded_file($tmp, $path)) {
                $imagePaths[] = $filename;
            }
        }
    }

    $imageField = implode(',', $imagePaths);

    $stmt = $conn->prepare("INSERT INTO kegiatan (
        id_aset, id_teknisi, id_asisten, jenis_kegiatan, keluhan, tindakan, kesimpulan,
        waktu_laporan, waktu_respon, waktu_selesai, nomor_lk, id_user_ruangan, image
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        echo "<div class='alert alert-danger mt-3'>Gagal menyiapkan query: " . $conn->error . "</div>";
    } else {
        $stmt->bind_param(
            "iiissssssssss",
            $_POST['id_aset'],
            $_POST['id_teknisi'],
            $_POST['id_asisten'],
            $_POST['jenis_kegiatan'],
            $_POST['keluhan'],
            $_POST['tindakan'],
            $_POST['kesimpulan'],
            $_POST['waktu_laporan'],
            $_POST['waktu_respon'],
            $_POST['waktu_selesai'],
            $_POST['nomor_lk'],
            $_POST['id_user_ruangan'],
            $imageField
        );

        if ($stmt->execute()) {
            echo "<script>alert('Kegiatan berhasil disimpan!'); window.location='form_kegiatan.php';</script>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Gagal menyimpan: " . $stmt->error . "</div>";
        }
    }
}
?>
