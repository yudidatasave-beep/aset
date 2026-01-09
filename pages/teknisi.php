<?php
require_once __DIR__ . '/../includes/db.php';

// Tambah data teknisi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $id_user = intval($_POST['id_user']);
    $nama_teknisi = trim($_POST['nama_teknisi']);
    $nip = trim($_POST['nip']);

    // Cek id_user sudah ada?
    $cek = $conn->prepare("SELECT 1 FROM teknisi WHERE id_user = ?");
    $cek->bind_param("i", $id_user);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        echo "<script>alert('User ini sudah terdaftar sebagai teknisi!'); window.location='index.php?page=teknisi';</script>";
        $cek->close();
        exit;
    }
    $cek->close();

    // Insert data
    $stmt = $conn->prepare("INSERT INTO teknisi (id_user, nama_teknisi, nip) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_user, $nama_teknisi, $nip);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Data berhasil ditambahkan'); window.location='index.php?page=teknisi';</script>";
    exit;
}

// Edit data teknisi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $id_teknisi = intval($_POST['id_teknisi']);
    $id_user = intval($_POST['id_user']);
    $nama_teknisi = trim($_POST['nama_teknisi']);
    $nip = trim($_POST['nip']);

    // Cek id_user sudah dipakai teknisi lain?
    $cek = $conn->prepare("SELECT 1 FROM teknisi WHERE id_user = ? AND id_teknisi != ?");
    $cek->bind_param("ii", $id_user, $id_teknisi);
    $cek->execute();
    $cek->store_result();

    if ($cek->num_rows > 0) {
        echo "<script>alert('User ini sudah terdaftar sebagai teknisi lain!'); window.location='index.php?page=teknisi';</script>";
        $cek->close();
        exit;
    }
    $cek->close();

    // Update data
    $stmt = $conn->prepare("UPDATE teknisi SET id_user=?, nama_teknisi=?, nip=? WHERE id_teknisi=?");
    $stmt->bind_param("issi", $id_user, $nama_teknisi, $nip, $id_teknisi);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Data berhasil diupdate'); window.location='index.php?page=teknisi';</script>";
    exit;
}

// Hapus data teknisi
if (isset($_GET['delete'])) {
    $id_teknisi = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM teknisi WHERE id_teknisi=?");
    $stmt->bind_param("i", $id_teknisi);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Data berhasil dihapus'); window.location='index.php?page=teknisi';</script>";
    exit;
}

// Ambil data teknisi + user
$result = $conn->query("
    SELECT t.id_teknisi, t.nama_teknisi, t.nip, u.id_user, u.username 
    FROM teknisi t
    JOIN user u ON t.id_user = u.id_user
    ORDER BY t.id_teknisi DESC
");

// Ambil data user untuk pilihan dropdown
$userList = $conn->query("SELECT id_user, username FROM user ORDER BY username ASC");
?>

<div class="container mt-4">
    <h2 class="mb-4">Manajemen Teknisi</h2>

    <!-- Form Tambah Teknisi -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Tambah Teknisi</div>
        <div class="card-body">
            <form method="post">
                <input type="hidden" name="add" value="1">

                <div class="mb-3">
                    <label class="form-label">User</label>
                    <select name="id_user" class="form-select" required>
                        <option value="">-- Pilih User --</option>
                        <?php while ($u = $userList->fetch_assoc()): ?>
                            <option value="<?= $u['id_user'] ?>"><?= htmlspecialchars($u['username']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Teknisi</label>
                    <input type="text" name="nama_teknisi" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">NIP</label>
                    <input type="text" name="nip" class="form-control">
                </div>

                <button type="submit" class="btn btn-success">Simpan</button>
            </form>
        </div>
    </div>

    <!-- Daftar Teknisi -->
    <div class="card">
        <div class="card-header bg-secondary text-white">Daftar Teknisi</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr class="table-primary">
                        <th>ID</th>
                        <th>Username</th>
                        <th>Nama Teknisi</th>
                        <th>NIP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_teknisi'] ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['nama_teknisi']) ?></td>
                            <td><?= htmlspecialchars($row['nip']) ?></td>
                            <td>
                                <!-- Tombol Edit -->
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_teknisi'] ?>">Edit</button>

                                <!-- Tombol Hapus -->
                                <a href="index.php?page=teknisi&delete=<?= $row['id_teknisi'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus data ini?')">Delete</a>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="editModal<?= $row['id_teknisi'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="post" class="modal-content">
                                    <input type="hidden" name="edit" value="1">
                                    <input type="hidden" name="id_teknisi" value="<?= $row['id_teknisi'] ?>">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Teknisi</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">User</label>
                                            <select name="id_user" class="form-select" required>
                                                <option value="">-- Pilih User --</option>
                                                <?php
                                                $userList2 = $conn->query("SELECT id_user, username FROM user ORDER BY username ASC");
                                                while ($u2 = $userList2->fetch_assoc()):
                                                ?>
                                                    <option value="<?= $u2['id_user'] ?>" <?= ($u2['id_user'] == $row['id_user']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($u2['username']) ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Nama Teknisi</label>
                                            <input type="text" name="nama_teknisi" class="form-control" value="<?= htmlspecialchars($row['nama_teknisi']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">NIP</label>
                                            <input type="text" name="nip" class="form-control" value="<?= htmlspecialchars($row['nip']) ?>">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">Belum ada data teknisi</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
