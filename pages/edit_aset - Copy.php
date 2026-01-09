<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/samcibabat/includes/db.php';

// --- Ambil ID aset ---
$id_aset = isset($_GET['id_aset']) ? (int)$_GET['id_aset'] : 0;
if ($id_aset <= 0) {
    die("ID aset tidak ditemukan!");
}

// --- Ambil data aset lama ---
$stmt = $conn->prepare("SELECT * FROM aset WHERE id_aset = ?");
if (!$stmt) {
    die("Gagal prepare SELECT: " . $conn->error);
}
$stmt->bind_param("i", $id_aset);
$stmt->execute();
$res  = $stmt->get_result();
$aset = $res->fetch_assoc();
$stmt->close();

if (!$aset) {
    die("Data aset tidak ditemukan!");
}

// --- Proses update ---
$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data POST
    $kode_aset         = $_POST['kode_aset'] ?? '';
    $nama_aset         = $_POST['nama_aset'] ?? '';
    $jenis_aset        = $_POST['jenis_aset'] ?? '';
    $model             = $_POST['model'] ?? '';
    $merek             = $_POST['merek'] ?? '';
    $tipe              = $_POST['tipe'] ?? '';
    $nomor_seri        = $_POST['nomor_seri'] ?? '';
    $id_ruangan        = isset($_POST['id_ruangan']) ? (int)$_POST['id_ruangan'] : null;
    $tanggal_perolehan = $_POST['tanggal_perolehan'] ?? null; // YYYY-MM-DD
    $nilai_perolehan   = isset($_POST['nilai_perolehan']) ? (float)$_POST['nilai_perolehan'] : 0;
    $umur_ekonomis     = isset($_POST['umur_ekonomis']) ? (int)$_POST['umur_ekonomis'] : 0;
    $kondisi_alat      = $_POST['kondisi_alat'] ?? '';
    $kepemilikan       = $_POST['kepemilikan'] ?? '';
    $status_penyusutan = $_POST['status_penyusutan'] ?? '';
    $jenis_anggaran    = $_POST['jenis_anggaran'] ?? '';
    $kalibrasi         = $_POST['kalibrasi'] ?? null; // YYYY-MM-DD
    $perusahaan        = $_POST['perusahaan'] ?? '';
    $kartu_kuning      = $_POST['kartu_kuning'] ?? '';
    $akl               = $_POST['akl'] ?? '';

    // Path upload
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0775, true);
    }

    // --- Upload Gambar (opsional) ---
    $image = $aset['image']; // default pakai lama
    if (!empty($_FILES['image']['name']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        // (opsional) validasi ekstensi gambar
        $newImgName = 'img_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newImgName)) {
            $msg .= "<div class='alert alert-warning'>Gagal mengunggah gambar baru. Tetap pakai gambar lama.</div>";
        } else {
            $image = $newImgName;
        }
    }

    // --- Upload SPO (PDF < 1MB, opsional) ---
    $spo = $aset['spo']; // default pakai lama
    if (!empty($_FILES['spo']['name']) && is_uploaded_file($_FILES['spo']['tmp_name'])) {
        $ext = strtolower(pathinfo($_FILES['spo']['name'], PATHINFO_EXTENSION));
        $sizeOk = ($_FILES['spo']['size'] <= 1048576); // 1MB
        if ($ext === 'pdf' && $sizeOk) {
            $newSpoName = 'spo_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';
            if (!move_uploaded_file($_FILES['spo']['tmp_name'], $uploadDir . $newSpoName)) {
                $msg .= "<div class='alert alert-warning'>Gagal mengunggah file SPO. Tetap pakai file lama.</div>";
            } else {
                $spo = $newSpoName;
            }
        } else {
            $msg .= "<div class='alert alert-danger'>SPO harus PDF &lt; 1MB. File baru diabaikan.</div>";
        }
    }

    // --- Query UPDATE ---
    $sql = "UPDATE aset SET
                kode_aset = ?, 
                nama_aset = ?, 
                jenis_aset = ?, 
                model = ?, 
                merek = ?, 
                tipe = ?, 
                nomor_seri = ?, 
                id_ruangan = ?, 
                tanggal_perolehan = ?, 
                nilai_perolehan = ?, 
                umur_ekonomis = ?, 
                kondisi_alat = ?, 
                kepemilikan = ?, 
                status_penyusutan = ?, 
                image = ?, 
                jenis_anggaran = ?, 
                kalibrasi = ?, 
                perusahaan = ?, 
                spo = ?, 
                kartu_kuning = ?, 
                akl = ?
            WHERE id_aset = ?";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("<div class='alert alert-danger'>Gagal prepare UPDATE: " . htmlspecialchars($conn->error) . "</div>");
    }

    // 22 placeholder -> 22 tipe
    // s: string, i: integer, d: double
    // Urutan tipe:
    // s s s s s s s i s d i s s s s s s s s s s i
    $types = "sssssssisdissssssssssi";

    $stmt->bind_param(
        $types,
        $kode_aset,
        $nama_aset,
        $jenis_aset,
        $model,
        $merek,
        $tipe,
        $nomor_seri,
        $id_ruangan,
        $tanggal_perolehan,
        $nilai_perolehan,
        $umur_ekonomis,
        $kondisi_alat,
        $kepemilikan,
        $status_penyusutan,
        $image,
        $jenis_anggaran,
        $kalibrasi,
        $perusahaan,
        $spo,
        $kartu_kuning,
        $akl,
        $id_aset
    );

    if ($stmt->execute()) {
        echo "<script>alert('Data aset berhasil diperbarui'); window.location.href='/samcibabat/index.php?page=aset';</script>";
        exit;
    } else {
        $msg .= "<div class='alert alert-danger'>Gagal memperbarui data: " . htmlspecialchars($stmt->error) . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <h3 class="mb-3">Edit Aset</h3>
    <?= $msg ?>
    <form method="POST" enctype="multipart/form-data" class="row g-3">

        <div class="col-md-6">
            <label class="form-label">Kode Aset</label>
            <input type="text" name="kode_aset" value="<?= htmlspecialchars($aset['kode_aset']) ?>" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Nama Aset</label>
            <input type="text" name="nama_aset" value="<?= htmlspecialchars($aset['nama_aset']) ?>" class="form-control" required>
        </div>

       <div class="col-md-6">
    <label class="form-label">Jenis Aset</label>
    <select name="jenis_aset" class="form-select" required>
        <option value="Medis" <?= ($aset['jenis_aset'] == 'Medis') ? 'selected' : '' ?>>Medis</option>
        <option value="Non Medis" <?= ($aset['jenis_aset'] == 'Non Medis') ? 'selected' : '' ?>>Non Medis</option>
    </select>
</div>

        <div class="col-md-6">
            <label class="form-label">Model</label>
            <input type="text" name="model" value="<?= htmlspecialchars($aset['model']) ?>" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Merek</label>
            <input type="text" name="merek" value="<?= htmlspecialchars($aset['merek']) ?>" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Tipe</label>
            <input type="text" name="tipe" value="<?= htmlspecialchars($aset['tipe']) ?>" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Nomor Seri</label>
            <input type="text" name="nomor_seri" value="<?= htmlspecialchars($aset['nomor_seri']) ?>" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Ruangan</label>
            <select name="id_ruangan" class="form-select" required>
                <?php
                $ru = $conn->query("SELECT id_ruangan, nama_ruangan FROM ruangan ORDER BY nama_ruangan ASC");
                while ($r = $ru->fetch_assoc()):
                    $sel = ((int)$aset['id_ruangan'] === (int)$r['id_ruangan']) ? 'selected' : '';
                ?>
                    <option value="<?= (int)$r['id_ruangan'] ?>" <?= $sel ?>>
                        <?= htmlspecialchars($r['nama_ruangan']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Tanggal Perolehan</label>
            <input type="date" name="tanggal_perolehan" value="<?= htmlspecialchars($aset['tanggal_perolehan']) ?>" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Nilai Perolehan</label>
            <input type="number" step="0.01" name="nilai_perolehan" value="<?= htmlspecialchars($aset['nilai_perolehan']) ?>" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Umur Ekonomis (tahun)</label>
            <input type="number" name="umur_ekonomis" value="<?= htmlspecialchars($aset['umur_ekonomis']) ?>" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Kondisi Alat</label>
            <select name="kondisi_alat" class="form-select" required>
                <?php
                $optKondisi = ['Baik','Rusak Ringan','Rusak Berat','Aktif','Tidak Aktif','Rusak','Diperbaiki'];
                foreach ($optKondisi as $opt):
                    $sel = ($aset['kondisi_alat'] === $opt) ? 'selected' : '';
                    echo "<option value=\"".htmlspecialchars($opt)."\" $sel>".htmlspecialchars($opt)."</option>";
                endforeach;
                ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Kepemilikan</label>
            <select name="kepemilikan" class="form-select" required>
                <?php
                $optKep = ['Milik Sendiri','Milik RS','Sewa','Hibah','Pinjam','RSUD Cibabat','KSO'];
                foreach ($optKep as $opt):
                    $sel = ($aset['kepemilikan'] === $opt) ? 'selected' : '';
                    echo "<option value=\"".htmlspecialchars($opt)."\" $sel>".htmlspecialchars($opt)."</option>";
                endforeach;
                ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Status Penyusutan</label>
            <select name="status_penyusutan" class="form-select" required>
                <?php
                $optSusut = ['Ya','Tidak','Belum Dihitung','Dihitung','Disusutkan','Tidak Disusutkan'];
                foreach ($optSusut as $opt):
                    $sel = ($aset['status_penyusutan'] === $opt) ? 'selected' : '';
                    echo "<option value=\"".htmlspecialchars($opt)."\" $sel>".htmlspecialchars($opt)."</option>";
                endforeach;
                ?>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Jenis Anggaran</label>
            <select name="jenis_anggaran" class="form-select" required>
                <option value="BTT"  <?= $aset['jenis_anggaran']=='BTT'?'selected':''; ?>>BTT</option>
                <option value="BLUD" <?= $aset['jenis_anggaran']=='BLUD'?'selected':''; ?>>BLUD</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Tanggal Kalibrasi</label>
            <input type="date" name="kalibrasi" value="<?= htmlspecialchars($aset['kalibrasi']) ?>" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Perusahaan</label>
            <input type="text" name="perusahaan" value="<?= htmlspecialchars($aset['perusahaan']) ?>" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Kartu Kuning</label>
            <select name="kartu_kuning" class="form-select" required>
                <option value="Ada"   <?= $aset['kartu_kuning']=='Ada'?'selected':''; ?>>Ada</option>
                <option value="Tidak" <?= $aset['kartu_kuning']=='Tidak'?'selected':''; ?>>Tidak</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">AKL</label>
            <input type="text" name="akl" value="<?= htmlspecialchars($aset['akl']) ?>" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Upload Gambar</label><br>
            <?php if (!empty($aset['image'])): ?>
                <img src="/samcibabat/uploads/<?= htmlspecialchars($aset['image']) ?>" width="100" class="mb-2" alt="image aset"><br>
            <?php endif; ?>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <div class="col-md-6">
            <label class="form-label">Upload SPO (PDF &lt; 1MB)</label><br>
            <?php if (!empty($aset['spo'])): ?>
                <a class="small" href="/samcibabat/uploads/<?= htmlspecialchars($aset['spo']) ?>" target="_blank">Lihat SPO saat ini</a><br>
            <?php endif; ?>
            <input type="file" name="spo" class="form-control" accept="application/pdf">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="/samcibabat/index.php?page=aset" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
</body>
</html>
