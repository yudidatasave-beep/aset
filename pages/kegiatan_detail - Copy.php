<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/samcibabat/includes/db.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil data teknisi
$stmtTeknisi = $conn->prepare("SELECT id_teknisi, nama_teknisi FROM teknisi WHERE id_user = ?");
$stmtTeknisi->bind_param("i", $id_user);
$stmtTeknisi->execute();
$teknisi = $stmtTeknisi->get_result()->fetch_assoc();
$id_teknisi = $teknisi['id_teknisi'] ?? '';
$nama_teknisi = $teknisi['nama_teknisi'] ?? '';

// Ambil data aset
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
    <title>Form Kegiatan Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
	
    .aset-image {
        width: 100%;
        max-width: 200px;
        height: auto;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

        body {
            background-color: #f0f2f5;
        }
        .navbar-denim {
            background-color: #3a4e63;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #fff;
        }
        .navbar-nav .nav-link:hover {
            color: #d1d1d1;
        }
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #3a4e63;
            color: white;
            font-weight: bold;
        }
        .aset-image {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-denim mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Sistem Aset Rumah Sakit</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="form_kegiatan.php">Form Kegiatan</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h3 class="mb-4">Form Kegiatan Pemeliharaan / Perbaikan</h3>

    <div class="card mb-4">
        <div class="card-header">Informasi Aset</div>
        <div class="card-body">
            <div class="row">
               <div class="col-md-4 mb-3 text-center">
    <?php if (!empty($aset['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/samcibabat/uploads/" . $aset['image'])): ?>
        <img src="/samcibabat/uploads/<?= htmlspecialchars($aset['image']) ?>" class="aset-image" alt="Gambar Aset">
    <?php else: ?>
        <img src="https://via.placeholder.com/300x200?text=No+Image" class="aset-image" alt="No Image">
    <?php endif; ?>
</div>

                <div class="col-md-8">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Kode Aset</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($aset['kode_aset']) ?></dd>

                        <dt class="col-sm-5">Nama Aset</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($aset['nama_aset']) ?></dd>

                        <dt class="col-sm-5">Jenis</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($aset['jenis_aset']) ?></dd>

                        <dt class="col-sm-5">Merek / Tipe</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($aset['merek']) ?> / <?= htmlspecialchars($aset['tipe']) ?></dd>

                        <dt class="col-sm-5">Nomor Seri</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($aset['nomor_seri']) ?></dd>

                        <dt class="col-sm-5">Lokasi</dt>
                        <dd class="col-sm-7"><?= htmlspecialchars($aset['nama_ruangan']) ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-info text-dark">Form Input Kegiatan</div>
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

                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Waktu Laporan</label>
                        <input type="datetime-local" name="waktu_laporan" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Waktu Respon</label>
                        <input type="datetime-local" name="waktu_respon" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Waktu Selesai</label>
                        <input type="datetime-local" name="waktu_selesai" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label>Nomor LK</label>
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
                </div>

                <button type="submit" name="simpan" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
                <a href="form_kegiatan.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php

if (isset($_POST['simpan'])) {
    $imagePaths = [];
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/samcibabat/uploads/';
    $uploadUrl = '/samcibabat/uploads/';

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    foreach ($_FILES['image']['tmp_name'] as $i => $tmp) {
        if ($i >= 3) break;
        if (!empty($tmp) && is_uploaded_file($tmp)) {
            $originalName = basename($_FILES['image']['name'][$i]);
            $filename = uniqid('kegiatan_') . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $originalName);
            $destinationPath = $uploadDir . $filename;

            if (move_uploaded_file($tmp, $destinationPath)) {
                $imagePaths[] = $filename;
            } else {
                error_log("Gagal upload file ke: $destinationPath");
            }
        }
    }

    $imageField = implode(',', $imagePaths);

    $stmt = $conn->prepare("INSERT INTO kegiatan (
        id_aset, id_teknisi, id_asisten, jenis_kegiatan, keluhan, tindakan, kesimpulan,
        waktu_laporan, waktu_respon, waktu_selesai, nomor_lk, id_user_ruangan, image
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
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
            echo "<script>
    alert('Kegiatan berhasil disimpan!');
    window.location.href = '../index.php?page=form_kegiatan';
</script>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Gagal menyimpan: {$stmt->error}</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='alert alert-danger mt-3'>Query error: {$conn->error}</div>";
    }
}
?>

