<?php
require_once 'includes/db.php';

// Tambah atau Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_gedung'] ?? '';
    $nama = $_POST['nama_gedung'];
    $keterangan = $_POST['keterangan'];

    if ($id) {
        $stmt = $conn->prepare("UPDATE gedung SET nama_gedung=?, keterangan=? WHERE id_gedung=?");
        $stmt->bind_param("ssi", $nama, $keterangan, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO gedung (nama_gedung, keterangan) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama, $keterangan);
    }

    $stmt->execute();
    header("Location: index.php?page=gedung_ruangan");
    exit;
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM gedung WHERE id_gedung=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php?page=gedung_ruangan");
    exit;
}

// Ambil data
$gedung = $conn->query("SELECT * FROM gedung ORDER BY nama_gedung ASC");
?>


<div class="container">
    <h4 class="mb-3"><i class="bi bi-building"></i> Data Gedung</h4>

    <form method="POST" class="row g-3 mb-4">
        <input type="hidden" name="id_gedung" id="id_gedung">
        <div class="col-md-4">
            <label class="form-label">Nama Gedung</label>
            <input type="text" class="form-control" name="nama_gedung" id="nama_gedung" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Keterangan</label>
            <input type="text" class="form-control" name="keterangan" id="keterangan">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save"></i> Simpan</button>
        </div>
    </form>

    <table class="table table-bordered table-striped table-hover">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Nama Gedung</th>
                <th>Keterangan</th>
                <th style="width:120px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($gedung as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_gedung']) ?></td>
                    <td><?= htmlspecialchars($row['keterangan']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit" 
                            data-id="<?= $row['id_gedung'] ?>"
                            data-nama="<?= htmlspecialchars($row['nama_gedung']) ?>"
                            data-ket="<?= htmlspecialchars($row['keterangan']) ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="index.php?page=gedung_ruangan&hapus=<?= $row['id_gedung'] ?>" 
                           onclick="return confirm('Yakin ingin menghapus?')" 
                           class="btn btn-sm btn-danger">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
document.querySelectorAll('.btn-edit').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('id_gedung').value = btn.dataset.id;
        document.getElementById('nama_gedung').value = btn.dataset.nama;
        document.getElementById('keterangan').value = btn.dataset.ket;
    });
});
</script>

