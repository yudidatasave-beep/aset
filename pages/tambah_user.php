<?php
// File: pages/tambah_user.php
require_once __DIR__ . '/../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama_lengkap']);
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO user (nama_lengkap, username, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $username, $password, $role);
    $stmt->execute();
    $stmt->close();

    // Redirect robust: header jika belum ada output, jika sudah -> JS + meta refresh (tanpa warning)
    $target = "index.php?page=user&added=1";
    if (!headers_sent()) {
        header("Location: $target");
    } else {
        echo "<script>window.location.href='$target';</script>";
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($target, ENT_QUOTES) . '"></noscript>';
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Tambah User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white text-center">
                    <h4 class="mb-0"><i class="fa fa-user-plus"></i> Tambah User Baru</h4>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="index.php?page=tambah_user">
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-id-card"></i> Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-user"></i> Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-lock"></i> Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><i class="fa fa-users-gear"></i> Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin</option>
                                <option value="teknisi">Teknisi</option>
                                <option value="unit">Unit</option>
                            </select>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                            <a href="index.php?page=user" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <?php if (!empty($_GET['added'])): ?>
                <div class="alert alert-success mt-3"><i class="fa fa-check-circle"></i> User berhasil ditambahkan.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
