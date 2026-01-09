<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . "/../includes/db.php";

// Ambil daftar ruangan
$ruanganResult = $conn->query("SELECT id_ruangan, nama_ruangan FROM ruangan");
$ruanganList = $ruanganResult->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // WAJIB (NOT NULL)
    $kode_aset         = $_POST['kode_aset'];
    $nama_aset         = $_POST['nama_aset'];
    $jenis_aset        = $_POST['jenis_aset'];
    $id_ruangan        = (int) $_POST['id_ruangan'];
    $tanggal_perolehan = $_POST['tanggal_perolehan'];
    $nilai_perolehan   = (float) $_POST['nilai_perolehan'];
    $umur_ekonomis     = (int) $_POST['umur_ekonomis'];
    $kondisi_alat      = $_POST['kondisi_alat'];
    $status_penyusutan = $_POST['status_penyusutan'];

    // BOLEH NULL
    $merek          = $_POST['merek']          ?? NULL;
    $tipe           = $_POST['tipe']           ?? NULL;
    $nomor_seri     = $_POST['nomor_seri']     ?? NULL;
    $kepemilikan    = $_POST['kepemilikan']    ?? NULL;
    $model          = $_POST['model']          ?? NULL;
    $jenis_anggaran = $_POST['jenis_anggaran'] ?? NULL;
    $kalibrasi      = empty($_POST['kalibrasi']) ? NULL : $_POST['kalibrasi'];
    $perusahaan     = $_POST['perusahaan']     ?? NULL;
    $spo            = $_POST['spo']            ?? NULL;
    $kartu_kuning   = $_POST['kartu_kuning']   ?? NULL;
    $akl            = $_POST['akl']            ?? NULL;

    // IMAGE (jika upload)
    $image = !empty($_FILES['image']['name'])
        ? $_FILES['image']['name']
        : NULL;
}


    // Upload Gambar
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $targetDir = __DIR__ . "/../uploads/";
        $image = time() . "_" . basename($_FILES['image']['name']);
        $targetFilePath = $targetDir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath);
    }

    // Upload SPO PDF
    $spo = null;
    if (!empty($_FILES['spo']['name'])) {
        if ($_FILES['spo']['type'] == "application/pdf" && $_FILES['spo']['size'] <= 1048576) {
            $targetDir = __DIR__ . "/../uploads/";
            $spo = time() . "_" . basename($_FILES['spo']['name']);
            $targetFilePath = $targetDir . $spo;
            move_uploaded_file($_FILES["spo"]["tmp_name"], $targetFilePath);
        } else {
            echo "<script>alert('File SPO harus PDF dan < 1MB');</script>";
        }
    }

    $sql = "INSERT INTO aset 
        (kode_aset, nama_aset, jenis_aset, merek, tipe, nomor_seri, id_ruangan, tanggal_perolehan, 
         nilai_perolehan, umur_ekonomis, kondisi_alat, kepemilikan, status_penyusutan, image, 
         model, jenis_anggaran, kalibrasi, perusahaan, spo, kartu_kuning, akl) 
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

  $stmt = $conn->prepare($sql);
 $stmt->bind_param(
  "ssssssisdisssssssssss",
  $kode_aset,
  $nama_aset,
  $jenis_aset,
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
  $model,
  $jenis_anggaran,
  $kalibrasi,
  $perusahaan,
  $spo,
  $kartu_kuning,
  $akl
);

    if ($stmt->execute()) {
        echo "<script>alert('Aset berhasil ditambahkan'); window.location.href='index.php?page=aset';</script>";
    } else {
        echo "Error: " . $stmt->error;
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
                    <div class="col-md-6 mb-3">
                        <label>Kode Aset</label>
                        <input type="text" name="kode_aset" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Nama Aset</label>
                        <input type="text" name="nama_aset" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Jenis Aset</label>
                        <select name="jenis_aset" class="form-control" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="Medis">Medis</option>
                            <option value="Non Medis">Non Medis</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Model</label>
                        <input type="text" name="model" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Merek</label>
                        <input type="text" name="merek" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Tipe</label>
                        <input type="text" name="tipe" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Nomor Seri</label>
                        <input type="text" name="nomor_seri" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Ruangan</label>
                        <select name="id_ruangan" class="form-control" required>
                            <option value="">-- Pilih Ruangan --</option>
                            <?php foreach ($ruanganList as $r): ?>
                                <option value="<?= $r['id_ruangan']; ?>"><?= htmlspecialchars($r['nama_ruangan']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Tanggal Perolehan</label>
                        <input type="date" name="tanggal_perolehan" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Nilai Perolehan</label>
                        <input type="number" name="nilai_perolehan" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Umur Ekonomis (tahun)</label>
                        <input type="number" name="umur_ekonomis" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Kondisi Alat</label>
                        <select name="kondisi_alat" class="form-control" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                            <option value="Rusak">Rusak</option>
							 <option value="Diperbaiki">Diperbaiki</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Kepemilikan</label>
                        <select name="kepemilikan" class="form-control" required>
                            <option value="Milik">Milik</option>
                            <option value="Sewa">Sewa</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Status Penyusutan</label>
                        <select name="status_penyusutan" class="form-control" required>
                            <option value="Disusutkan">Disusutkan</option>
                            <option value="Tidak Disusutkan">Tidak Disusutkan</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Jenis Anggaran</label>
                        <select name="jenis_anggaran" class="form-control" required>
                            <option value="BTT">BTT</option>
                            <option value="BLUD">BLUD</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Tanggal Kalibrasi</label>
                        <input type="date" name="kalibrasi" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Perusahaan</label>
                        <input type="text" name="perusahaan" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Kartu Kuning</label>
                        <select name="kartu_kuning" class="form-control">
                            <option value="Ada">Ada</option>
                            <option value="Tidak">Tidak</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>AKL</label>
                        <input type="text" name="akl" class="form-control">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Upload Gambar Aset</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Upload SPO (PDF &lt; 1MB)</label>
                        <input type="file" name="spo" class="form-control" accept="application/pdf">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
            </form>
        </div>
    </div>
</div>
