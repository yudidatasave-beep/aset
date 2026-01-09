<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . "/../includes/db.php";

// ================= DROPDOWN =================
$ruanganList   = $conn->query("SELECT id_ruangan, nama_ruangan FROM ruangan")->fetch_all(MYSQLI_ASSOC);
$jenisasetList = $conn->query("SELECT id_jenis_aset, nama_jenis_aset FROM jenis_aset")->fetch_all(MYSQLI_ASSOC);
$merekasetList = $conn->query("SELECT id_merek_aset, merek_aset, kode_merek FROM merek_aset")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ================= VALIDASI =================
    if (empty($_POST['nama_aset'])) {
        die("Nama aset wajib diisi");
    }

    // ================= DATA FORM =================
    $nama_aset         = $_POST['nama_aset'];
    $model             = $_POST['model'] ?? null;
    $nomor_kontrak     = $_POST['nomor_kontrak'] ?? null;
    $id_merek_aset     = (int) $_POST['id_merek_aset'];
    $tipe              = $_POST['tipe'] ?? null;
    $nomor_seri        = $_POST['nomor_seri'] ?? null;
    $id_ruangan        = (int) $_POST['id_ruangan'];
    $id_jenis_aset     = (int) $_POST['id_jenis_aset'];
    $tanggal_perolehan = $_POST['tanggal_perolehan'];
    $kondisi_alat      = $_POST['kondisi_alat'];

    // ================= AUTO GENERATE KODE ASET =================
    $tahun = date('Y', strtotime($tanggal_perolehan));

    // Ambil kode jenis
    $qJenis = $conn->prepare("SELECT kode_jenis_aset FROM jenis_aset WHERE id_jenis_aset=?");
    $qJenis->bind_param("i", $id_jenis_aset);
    $qJenis->execute();
    $qJenis->bind_result($kode_jenis);
    $qJenis->fetch();
    $qJenis->close();

    // Ambil kode merek
    $qMerek = $conn->prepare("SELECT kode_merek FROM merek_aset WHERE id_merek_aset=?");
    $qMerek->bind_param("i", $id_merek_aset);
    $qMerek->execute();
    $qMerek->bind_result($kode_merek);
    $qMerek->fetch();
    $qMerek->close();

    // Hitung nomor urut
    $qUrut = $conn->prepare("
        SELECT COUNT(*) 
        FROM aset 
        WHERE id_jenis_aset=? 
          AND id_merek_aset=? 
          AND YEAR(tanggal_perolehan)=?
    ");
    $qUrut->bind_param("iii", $id_jenis_aset, $id_merek_aset, $tahun);
    $qUrut->execute();
    $qUrut->bind_result($urut);
    $qUrut->fetch();
    $qUrut->close();

    $nomor_urut = $urut + 1;

    $kode_aset = "{$kode_jenis}_{$kode_merek}_{$tahun}_{$nomor_urut}";

    // ================= UPLOAD IMAGE =================
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowed)) {
            die("Format gambar tidak valid");
        }

        $image = time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . "/../uploads/" . $image);
    }

    // ================= UPLOAD SPO =================
    $spo = null;
    if (!empty($_FILES['spo']['name'])) {
        if ($_FILES['spo']['type'] !== "application/pdf") {
            die("File SPO harus PDF");
        }
        $spo = time() . "_" . basename($_FILES['spo']['name']);
        move_uploaded_file($_FILES['spo']['tmp_name'], __DIR__ . "/../uploads/" . $spo);
    }

    // ================= INSERT =================
    $sql = "INSERT INTO aset (
                kode_aset, nama_aset, id_jenis_aset, model,
                nomor_kontrak, id_merek_aset, tipe, nomor_seri,
                id_ruangan, tanggal_perolehan, kondisi_alat, image, spo
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssissississss",
        $kode_aset,
        $nama_aset,
        $id_jenis_aset,
        $model,
        $nomor_kontrak,
        $id_merek_aset,
        $tipe,
        $nomor_seri,
        $id_ruangan,
        $tanggal_perolehan,
        $kondisi_alat,
        $image,
        $spo
    );

    if ($stmt->execute()) {
        echo "<script>alert('Aset berhasil ditambahkan\\nKode: $kode_aset');location.href='index.php?page=aset';</script>";
    } else {
        echo $stmt->error;
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

                    <!-- NAMA ASET -->
                    <div class="col-md-6 mb-3">
                        <label>Nama Aset</label>
                        <input type="text" name="nama_aset" class="form-control" required>
                    </div>

                    <!-- JENIS ASET -->
                    <div class="col-md-6 mb-3">
                        <label>Jenis Aset</label>
                        <select name="id_jenis_aset" class="form-control" required>
                            <option value="">-- Pilih Jenis Aset --</option>
                            <?php foreach ($jenisasetList as $r): ?>
                                <option value="<?= $r['id_jenis_aset']; ?>">
                                    <?= htmlspecialchars($r['nama_jenis_aset']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- MEREK -->
                    <div class="col-md-6 mb-3">
                        <label>Merek</label>
                        <select name="id_merek_aset" class="form-control" required>
                            <option value="">-- Pilih Merek --</option>
                            <?php foreach ($merekasetList as $r): ?>
                                <option value="<?= $r['id_merek_aset']; ?>">
                                    <?= htmlspecialchars($r['merek_aset']); ?>
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
                        <label>Lokasi Aset</label>
                        <select name="id_ruangan" class="form-control" required>
                            <option value="">-- Lokasi Aset --</option>
                            <?php foreach ($ruanganList as $r): ?>
                                <option value="<?= $r['id_ruangan']; ?>">
                                    <?= htmlspecialchars($r['nama_ruangan']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- TANGGAL PEROLEHAN -->
                    <div class="col-md-6 mb-3">
                        <label>Tanggal Tahun Anggaran</label>
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
                        <input type="file" name="spo" class="form-control" 
                    </div>

                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
