<?php
require_once 'includes/db.php';

// Proses tambah/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_ruangan = $_POST['id_ruangan'] ?? '';
    $id_gedung = $_POST['id_gedung'];
    $nama_ruangan = $_POST['nama_ruangan'];
    $keterangan = $_POST['keterangan'];

    if ($id_ruangan) {
        $stmt = $conn->prepare("UPDATE ruangan SET id_gedung=?, nama_ruangan=?, keterangan=? WHERE id_ruangan=?");
        $stmt->bind_param("issi", $id_gedung, $nama_ruangan, $keterangan, $id_ruangan);
        $stmt->execute();
        $pesan = "Data ruangan berhasil diperbarui!";
    } else {
        $stmt = $conn->prepare("INSERT INTO ruangan (id_gedung, nama_ruangan, keterangan) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_gedung, $nama_ruangan, $keterangan);
        $stmt->execute();
        $pesan = "Data ruangan berhasil ditambahkan!";
    }
    header("Location: index.php?page=ruangan&pesan=" . urlencode($pesan));
    exit;
}

// Hapus data
if (isset($_GET['hapus'])) {
    $stmt = $conn->prepare("DELETE FROM ruangan WHERE id_ruangan=?");
    $stmt->bind_param("i", $_GET['hapus']);
    $stmt->execute();
    header("Location: index.php?page=ruangan&pesan=" . urlencode("Data ruangan dihapus."));
    exit;
}

// Ambil data untuk form edit
$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM ruangan WHERE id_ruangan=?");
    $stmt->bind_param("i", $_GET['edit']);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}

// Ambil semua ruangan dan daftar gedung
$ruangan = $conn->query("
    SELECT r.*, g.nama_gedung 
    FROM ruangan r 
    JOIN gedung g ON r.id_gedung = g.id_gedung 
    ORDER BY r.id_ruangan DESC
");
$gedung = $conn->query("SELECT * FROM gedung ORDER BY nama_gedung ASC");
?>

<h3>Master Ruangan</h3>

<?php if (!empty($_GET['pesan'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['pesan']) ?></div>
<?php endif; ?>

<form method="POST" class="row g-3 mb-4">
    <input type="hidden" name="id_ruangan" value="<?= $edit['id_ruangan'] ?? '' ?>">

    <div class="col-md-4">
        <label for="id_gedung" class="form-label">Gedung</label>
        <select name="id_gedung" id="id_gedung" class="form-select" required>
            <option value="">-- Pilih Gedung --</option>
            <?php while ($g = $gedung->fetch_assoc()): ?>
                <option value="<?= $g['id_gedung'] ?>" <?= isset($edit) && $edit['id_gedung'] == $g['id_gedung'] ? 'selected' : '' ?>>
                    <?= $g['nama_gedung'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="col-md-4">
        <label for="nama_ruangan" class="form-label">Nama Ruangan</label>
        <input type="text" name="nama_ruangan" id="nama_ruangan" class="form-control" required value="<?= $edit['nama_ruangan'] ?? '' ?>">
    </div>

    <div class="col-md-4">
        <label for="keterangan" class="form-label">Keterangan</label>
        <input type="text" name="keterangan" id="keterangan" class="form-control" value="<?= $edit['keterangan'] ?? '' ?>">
    </div>

    <div class="col-12">
        <button class="btn btn-primary" type="submit"><?= isset($edit) ? 'Update' : 'Simpan' ?></button>
        <?php if (isset($edit)): ?>
            <a href="index.php?page=ruangan" class="btn btn-secondary">Batal</a>
        <?php endif; ?>
    </div>
</form>

<table class="table table-bordered table-striped">
    <thead class="table-primary">
        <tr>
            <th>#</th>
            <th>Gedung</th>
            <th>Nama Ruangan</th>
            <th>Keterangan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; while ($r = $ruangan->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($r['nama_gedung']) ?></td>
                <td><?= htmlspecialchars($r['nama_ruangan']) ?></td>
                <td><?= htmlspecialchars($r['keterangan']) ?></td>
                <td>
                    <a href="index.php?page=ruangan&edit=<?= $r['id_ruangan'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="index.php?page=ruangan&hapus=<?= $r['id_ruangan'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
