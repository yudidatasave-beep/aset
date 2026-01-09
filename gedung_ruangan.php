<?php
// File: gedung_ruangan.php
session_start();
require 'includes/db.php';
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$pesan = '';

// Handle Tambah/Edit Gedung
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'gedung') {
    $id = $_POST['id_gedung'] ?? '';
    $nama_gedung = $_POST['nama_gedung'];
    $keterangan = $_POST['keterangan'];

    if ($id == '') {
        $stmt = $conn->prepare("INSERT INTO gedung (nama_gedung, keterangan) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama_gedung, $keterangan);
        $stmt->execute();
        $pesan = "Gedung berhasil ditambahkan.";
    } else {
        $stmt = $conn->prepare("UPDATE gedung SET nama_gedung=?, keterangan=? WHERE id_gedung=?");
        $stmt->bind_param("ssi", $nama_gedung, $keterangan, $id);
        $stmt->execute();
        $pesan = "Gedung berhasil diubah.";
    }
    header("Location: gedung_ruangan.php?pesan=" . urlencode($pesan));
    exit;
}

// Handle Hapus Gedung
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM gedung WHERE id_gedung=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $pesan = "Gedung berhasil dihapus.";
    header("Location: gedung_ruangan.php?pesan=" . urlencode($pesan));
    exit;
}

$result = $conn->query("SELECT * FROM gedung ORDER BY id_gedung DESC");
$rows = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Gedung</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
    <h3>Master Gedung</h3>
    <?php if (isset($_GET['pesan'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']) ?></div>
    <?php endif; ?>

    <form method="post" class="row g-3 mb-4">
        <input type="hidden" name="form_type" value="gedung">
        <input type="hidden" name="id_gedung" id="id_gedung">
        <div class="col-md-4">
            <input type="text" name="nama_gedung" id="nama_gedung" class="form-control" placeholder="Nama Gedung" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Keterangan">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <button type="reset" onclick="resetForm()" class="btn btn-secondary">Reset</button>
        </div>
    </form>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>Nama Gedung</th>
            <th>Keterangan</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $index => $row): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($row['nama_gedung']) ?></td>
                <td><?= htmlspecialchars($row['keterangan']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick='editData(<?= json_encode($row) ?>)'>Edit</button>
                    <a href="?hapus=<?= $row['id_gedung'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function editData(data) {
        document.getElementById('id_gedung').value = data.id_gedung;
        document.getElementById('nama_gedung').value = data.nama_gedung;
        document.getElementById('keterangan').value = data.keterangan;
    }
    function resetForm() {
        document.getElementById('id_gedung').value = '';
        document.getElementById('nama_gedung').value = '';
        document.getElementById('keterangan').value = '';
    }
</script>
</body>
</html>
