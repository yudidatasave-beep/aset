<?php
require '../includes/db.php';
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
<div class="d-flex">
    <div class="bg-dark text-white p-3" style="width: 250px; height: 100vh;">
        <h4><i class="fa fa-hospital"></i> Aset RS</h4>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link text-white" href="../dashboard.php"><i class="fa fa-home"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link text-white" href="index.php"><i class="fa fa-users"></i> User</a></li>
        </ul>
    </div>
    <div class="container mt-4">
        <h3>Manajemen User</h3>
        <a href="tambah.php" class="btn btn-success mb-3"><i class="fa fa-plus"></i> Tambah User</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
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
                            <a href="edit.php?id=<?= $row['id_user'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
                            <a href="hapus.php?id=<?= $row['id_user'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus user ini?')"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>