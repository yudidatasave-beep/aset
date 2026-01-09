<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../includes/db.php';

// Handle insert ruangan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $nama = $_POST['nama_ruangan'];
    $id_gedung = $_POST['id_gedung'];
    $keterangan = $_POST['keterangan'];

    $stmt = $conn->prepare("INSERT INTO ruangan (nama_ruangan, id_gedung, keterangan) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Query error saat INSERT: " . $conn->error);
    }

    $stmt->bind_param("sis", $nama, $id_gedung, $keterangan);
    $stmt->execute();
    header("Location: index.php?page=ruangan");
    exit;
}

// Handle delete ruangan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM ruangan WHERE id_ruangan = $id");
    header("Location: index.php?page=ruangan");
    exit;
}

// Ambil data ruangan
$ruangan = $conn->query("SELECT r.*, g.nama_gedung FROM ruangan r LEFT JOIN gedung g ON r.id_gedung = g.id_gedung ORDER BY r.id_ruangan DESC");

// Ambil data gedung untuk select
$gedung = $conn->query("SELECT * FROM gedung");
?>

<div class="container">
    <h4 class="mb-4">Manajemen Ruangan</h4>

    <!-- Form Tambah Ruangan -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Tambah Ruangan</div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Ruangan</label>
                    <input type="text" name="nama_ruangan" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Gedung</label>
                    <select name="id_gedung" class="form-select" required>
                        <option value="">-- Pilih Gedung --</option>
                        <?php while ($g = $gedung->fetch_assoc()) : ?>
                            <option value="<?= $g['id_gedung'] ?>"><?= htmlspecialchars($g['nama_gedung']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control"></textarea>
                </div>
                <button type="submit" name="simpan" class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
            </form>
        </div>
    </div>

    <!-- Tabel Data Ruangan -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Daftar Ruangan</div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Ruangan</th>
                        <th>Gedung</th>
                        <th>Keterangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($r = $ruangan->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($r['nama_ruangan']) ?></td>
                            <td><?= htmlspecialchars($r['nama_gedung']) ?></td>
                            <td><?= htmlspecialchars($r['keterangan']) ?></td>
                            <td>
                                <a href="index.php?page=ruangan&hapus=<?= $r['id_ruangan'] ?>" onclick="return confirm('Hapus ruangan ini?')" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
