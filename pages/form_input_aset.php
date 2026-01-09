<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . "/../includes/db.php";

// ================= DROPDOWN =================
$ruanganList   = $conn->query("SELECT id_ruangan, nama_ruangan FROM ruangan")->fetch_all(MYSQLI_ASSOC);
$jenisasetList = $conn->query("SELECT id_jenis_aset, nama_jenis_aset FROM jenis_aset")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ================= VALIDASI =================
    if (empty($_POST['kode_aset']) || empty($_POST['nama_aset'])) {
        die("Kode aset dan nama aset wajib diisi");
    }

    // ================= DATA FORM =================
    $kode_aset         = $_POST['kode_aset'];
    $nama_aset         = $_POST['nama_aset'];
    $jenis_aset        = $_POST['jenis_aset'];
    $model             = $_POST['model'] ?? null;
    $nomor_kontrak     = $_POST['nomor_kontrak'] ?? null;
    $merek             = $_POST['merek'] ?? null;
    $tipe              = $_POST['tipe'] ?? null;
    $nomor_seri        = $_POST['nomor_seri'] ?? null;
    $id_ruangan        = (int) $_POST['id_ruangan'];
    $tanggal_perolehan = $_POST['tanggal_perolehan'];
    $kondisi_alat      = $_POST['kondisi_alat'];

    // ================= UPLOAD IMAGE =================
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowed)) {
            die("Format gambar tidak valid");
        }

        $image = time() . "_" . uniqid() . "." . $ext;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . "/../uploads/" . $image)) {
            die("Upload gambar gagal");
        }
    }

    // ================= UPLOAD SPO =================
    $spo = null;
    if (!empty($_FILES['spo']['name'])) {
        if ($_FILES['spo']['type'] !== "application/pdf") {
            die("File SPO harus PDF");
        }

        $spo = time() . "_" . basename($_FILES['spo']['name']);
        if (!move_uploaded_file($_FILES['spo']['tmp_name'], __DIR__ . "/../uploads/" . $spo)) {
            die("Upload SPO gagal");
        }
    }

    // ================= SQL =================
    $sql = "INSERT INTO aset (
                kode_aset,
                nama_aset,
                jenis_aset,
                model,
                nomor_kontrak,
                merek,
                tipe,
                nomor_seri,
                id_ruangan,
                tanggal_perolehan,
                kondisi_alat,
                image,
                spo
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);

    // 🔴 PENTING: cegah fatal error
    if (!$stmt) {
        die("Prepare gagal: " . $conn->error);
    }

    // ================= BIND PARAM =================
    $stmt->bind_param(
        "ssssssssissss",
        $kode_aset,
        $nama_aset,
        $jenis_aset,
        $model,
        $nomor_kontrak,
        $merek,
        $tipe,
        $nomor_seri,
        $id_ruangan,
        $tanggal_perolehan,
        $kondisi_alat,
        $image,
        $spo
    );

    // ================= EXECUTE =================
    if ($stmt->execute()) {
        echo "<script>
                alert('Aset berhasil ditambahkan');
                location.href='index.php?page=aset';
              </script>";
    } else {
        echo "Execute error: " . $stmt->error;
    }
}
?>


<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4><i class="fa fa-plus"></i> Form Input Aset</h4>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <div class="row">

                    <!-- KODE ASET -->
                    <div class="col-md-6 mb-3">
                        <label>Kode Aset</label>
                        <input type="text" name="kode_aset" class="form-control" required>
                    </div>

                    <!-- NAMA ASET -->
                    <div class="col-md-6 mb-3">
                        <label>Nama Aset</label>
                        <input type="text" name="nama_aset" class="form-control" required>
                    </div>

                    <!-- JENIS ASET -->
                    <div class="col-md-6 mb-3">
                        <label>Jenis Aset</label>
                        <select name="jenis_aset" class="form-control" required>
                            <option value="">-- Pilih Jenis Aset --</option>
                            <?php foreach ($jenisasetList as $r): ?>
                                <option value="<?= $r['id_jenis_aset']; ?>">
                                    <?= htmlspecialchars($r['nama_jenis_aset']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- MODEL -->
                    <div class="col-md-6 mb-3">
                        <label>Model</label>
                        <input type="text" name="model" class="form-control">
                    </div>

                    <!-- NOMOR KONTRAK -->
                    <div class="col-md-6 mb-3">
                        <label>Nomor Kontrak</label>
                        <input type="text" name="nomor_kontrak" class="form-control">
                    </div>

                    <!-- MEREK -->
                    <div class="col-md-6 mb-3">
                        <label>Merek</label>
                        <input type="text" name="merek" class="form-control">
                    </div>

                    <!-- TIPE -->
                    <div class="col-md-6 mb-3">
                        <label>Tipe</label>
                        <input type="text" name="tipe" class="form-control">
                    </div>

                    <!-- NOMOR SERI -->
                    <div class="col-md-6 mb-3">
                        <label>Nomor Seri</label>
                        <input type="text" name="nomor_seri" class="form-control">
                    </div>

                    <!-- RUANGAN -->
                    <div class="col-md-6 mb-3">
                        <label>Ruangan</label>
                        <select name="id_ruangan" class="form-control" required>
                            <option value="">-- Pilih Ruangan --</option>
                            <?php foreach ($ruanganList as $r): ?>
                                <option value="<?= $r['id_ruangan']; ?>">
                                    <?= htmlspecialchars($r['nama_ruangan']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- TANGGAL PEROLEHAN -->
                    <div class="col-md-6 mb-3">
                        <label>Tanggal Perolehan</label>
                        <input type="date" name="tanggal_perolehan" class="form-control" required>
                    </div>

                    <!-- KONDISI ASET -->
                    <div class="col-md-6 mb-3">
                        <label>Kondisi Aset</label>
                        <select name="kondisi_alat" class="form-control" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Diperbaiki">Diperbaiki</option>
                        </select>
                    </div>

                    <!-- UPLOAD GAMBAR -->
                    <div class="col-md-6 mb-3">
                        <label>Upload Gambar Aset</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>

                    <!-- UPLOAD SPO -->
                    <div class="col-md-6 mb-3">
                        <label>Upload SPO (PDF &lt; 1MB)</label>
                        <input type="file" name="spo" class="form-control" accept="application/pdf">
                    </div>

                </div>

                <!-- SUBMIT -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
