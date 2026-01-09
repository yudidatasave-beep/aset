<?php
require_once __DIR__ . '/../includes/db.php';

// Hapus User
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM user WHERE id_user=$id");
    echo "<script>alert('User berhasil dihapus'); window.location='index.php?page=user';</script>";
    exit;
}

// Edit User (Update)
if (isset($_POST['update_user'])) {
    $id_user = intval($_POST['id_user']);
    $nama = $conn->real_escape_string($_POST['nama_lengkap']);
    $username = $conn->real_escape_string($_POST['username']);
    $role = $conn->real_escape_string($_POST['role']);
    $status = $conn->real_escape_string($_POST['status']);

    $conn->query("UPDATE user SET 
        nama_lengkap='$nama',
        username='$username',
        role='$role',
        status='$status'
        WHERE id_user=$id_user
    ");

    echo "<script>alert('User berhasil diperbarui'); window.location='index.php?page=user';</script>";
    exit;
}

// Ambil semua data user
$result = $conn->query("SELECT * FROM user");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container mt-4">
    <h3>Manajemen User</h3>
    <a href="index.php?page=tambah_user" class="btn btn-success mb-3">
        <i class="fa fa-plus"></i> Tambah User
    </a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Nama</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th>
                <th width="150">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <!-- Tombol Edit (Modal) -->
                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_user'] ?>">
                            <i class="fa fa-edit"></i>
                        </button>

                        <!-- Tombol Hapus -->
                        <a href="index.php?page=user&delete=<?= $row['id_user'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus user ini?')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editModal<?= $row['id_user'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="post">
                        <div class="modal-header">
                          <h5 class="modal-title">Edit User</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                          <input type="hidden" name="id_user" value="<?= $row['id_user'] ?>">

                          <div class="mb-3">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($row['nama_lengkap']) ?>" required>
                          </div>

                          <div class="mb-3">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username']) ?>" required>
                          </div>

                          <div class="mb-3">
                            <label>Role</label>
                            <select name="role" class="form-control" required>
                              <option value="administrator" <?= $row['role']=='administrator'?'selected':'' ?>>Administrator</option>
                              <option value="teknisi" <?= $row['role']=='teknisi'?'selected':'' ?>>Teknisi</option>
                              <option value="user" <?= $row['role']=='user'?'selected':'' ?>>User</option>
                            </select>
                          </div>

                          <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                              <option value="aktif" <?= $row['status']=='aktif'?'selected':'' ?>>Aktif</option>
                              <option value="nonaktif" <?= $row['status']=='nonaktif'?'selected':'' ?>>Nonaktif</option>
                            </select>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" name="update_user" class="btn btn-primary">Simpan</button>
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
