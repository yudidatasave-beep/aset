<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require __DIR__ . "/../includes/db.php";

$id_aset = (int)($_GET['id_aset'] ?? 0);
if ($id_aset <= 0) die("ID aset tidak valid");

// dropdown
$ruanganList   = $conn->query("SELECT id_ruangan, nama_ruangan FROM ruangan")->fetch_all(MYSQLI_ASSOC);
$jenisasetList = $conn->query("SELECT id_jenis_aset, nama_jenis_aset FROM jenis_aset")->fetch_all(MYSQLI_ASSOC);
$merekasetList = $conn->query("SELECT id_merek_aset, merek_aset FROM merek_aset")->fetch_all(MYSQLI_ASSOC);

// data lama
$stmt = $conn->prepare("SELECT * FROM aset WHERE id_aset=?");
$stmt->bind_param("i", $id_aset);
$stmt->execute();
$aset = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$aset) die("Data tidak ditemukan");

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama_aset         = $_POST['nama_aset'];
    $id_jenis_aset     = (int)$_POST['id_jenis_aset'];
    $id_merek_aset     = (int)$_POST['id_merek_aset'];
    $model             = $_POST['model'] ?? null;
    $nomor_kontrak     = $_POST['nomor_kontrak'] ?? null;
    $tipe              = $_POST['tipe'] ?? null;
    $nomor_seri        = $_POST['nomor_seri'] ?? null;
    $id_ruangan        = (int)$_POST['id_ruangan'];
    $tanggal_perolehan = $_POST['tanggal_perolehan'];
    $kondisi_alat      = $_POST['kondisi_alat'];

    // upload dir
    $uploadDir = __DIR__ . "/../uploads/";

    // IMAGE
    $image = $aset['image'];
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $image = time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $image);
    }

    // SPO
    $spo = $aset['spo'];
    if (!empty($_FILES['spo']['name'])) {
        if ($_FILES['spo']['type'] !== "application/pdf") {
            $msg = "<div class='alert alert-danger'>SPO harus PDF</div>";
        } else {
            $spo = time() . "_" . basename($_FILES['spo']['name']);
            move_uploaded_file($_FILES['spo']['tmp_name'], $uploadDir . $spo);
        }
    }

    $sql = "UPDATE aset SET
                nama_aset=?,
                id_jenis_aset=?,
                id_merek_aset=?,
                model=?,
                nomor_kontrak=?,
                tipe=?,
                nomor_seri=?,
                id_ruangan=?,
                tanggal_perolehan=?,
                kondisi_alat=?,
                image=?,
                spo=?
            WHERE id_aset=?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "siissssissssi",
        $nama_aset,
        $id_jenis_aset,
        $id_merek_aset,
        $model,
        $nomor_kontrak,
        $tipe,
        $nomor_seri,
        $id_ruangan,
        $tanggal_perolehan,
        $kondisi_alat,
        $image,
        $spo,
        $id_aset
    );

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui');location.href='index.php?page=aset';</script>";
        exit;
    } else {
        $msg = "<div class='alert alert-danger'>{$stmt->error}</div>";
    }
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
<h4>Edit Aset</h4>
<?= $msg ?>

<form method="POST" enctype="multipart/form-data" class="row g-3">

<div class="col-md-6">
<label>Nama Aset</label>
<input type="text" name="nama_aset" value="<?= htmlspecialchars($aset['nama_aset']) ?>" class="form-control" required>
</div>

<div class="col-md-6">
<label>Jenis Aset</label>
<select name="id_jenis_aset" class="form-control" required>
<?php foreach ($jenisasetList as $r): ?>
<option value="<?= $r['id_jenis_aset'] ?>" <?= $aset['id_jenis_aset']==$r['id_jenis_aset']?'selected':'' ?>>
<?= htmlspecialchars($r['nama_jenis_aset']) ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-6">
<label>Merek</label>
<select name="id_merek_aset" class="form-control" required>
<?php foreach ($merekasetList as $r): ?>
<option value="<?= $r['id_merek_aset'] ?>" <?= $aset['id_merek_aset']==$r['id_merek_aset']?'selected':'' ?>>
<?= htmlspecialchars($r['merek_aset']) ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-6">
<label>Model</label>
<input type="text" name="model" value="<?= htmlspecialchars($aset['model']) ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Nomor Kontrak</label>
<input type="text" name="nomor_kontrak" value="<?= htmlspecialchars($aset['nomor_kontrak']) ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Tipe</label>
<input type="text" name="tipe" value="<?= htmlspecialchars($aset['tipe']) ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Nomor Seri</label>
<input type="text" name="nomor_seri" value="<?= htmlspecialchars($aset['nomor_seri']) ?>" class="form-control">
</div>

<div class="col-md-6">
<label>Lokasi Aset</label>
<select name="id_ruangan" class="form-control" required>
<?php foreach ($ruanganList as $r): ?>
<option value="<?= $r['id_ruangan'] ?>" <?= $aset['id_ruangan']==$r['id_ruangan']?'selected':'' ?>>
<?= htmlspecialchars($r['nama_ruangan']) ?>
</option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-6">
<label>Tanggal Perolehan</label>
<input type="date" name="tanggal_perolehan" value="<?= $aset['tanggal_perolehan'] ?>" class="form-control" required>
</div>

<div class="col-md-6">
<label>Kondisi Aset</label>
<select name="kondisi_alat" class="form-control" required>
<?php foreach (['Aktif','Tidak Aktif','Rusak','Diperbaiki'] as $k): ?>
<option value="<?= $k ?>" <?= $aset['kondisi_alat']==$k?'selected':'' ?>><?= $k ?></option>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-6">
<label>Upload Gambar</label>
<?php if ($aset['image']): ?>
<img src="../uploads/<?= $aset['image'] ?>" width="100" class="mb-2">
<?php endif; ?>
<input type="file" name="image" class="form-control">
</div>

<div class="col-md-6">
<label>Upload SPO (PDF)</label>
<?php if ($aset['spo']): ?>
<a href="../uploads/<?= $aset['spo'] ?>" target="_blank">Lihat SPO</a>
<?php endif; ?>
<input type="file" name="spo" class="form-control">
</div>

<div class="col-12 text-end">
<button class="btn btn-primary">Simpan</button>
<a href="index.php?page=aset" class="btn btn-secondary">Batal</a>
</div>

</form>
</div>
</body>
</html>

