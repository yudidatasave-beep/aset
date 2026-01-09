<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
$nama = $_SESSION['nama'];
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Aset RS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --denim-blue: #2A4D69;
            --denim-hover: #3e5c7c;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            height: 100vh;
            background-color: var(--denim-blue);
            color: #fff;
        }

        .sidebar a {
            color: #ccc;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: var(--denim-hover);
            color: #fff;
        }

        .content {
            padding: 25px;
        }

        .icon {
            margin-right: 10px;
        }

        .logo {
            height: 30px;
            margin-right: 10px;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-title i {
            color: var(--denim-blue);
        }

        .card-body {
            background: #ffffff;
            border-left: 5px solid var(--denim-blue);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar p-0">
            <div class="sidebar-brand">
                <img src="logo.png" alt="Logo" class="logo">
                <h5 class="m-0">MANAJEMEN ASET RS</h5>
            </div>
            <a href="#" class="active"><i class="bi bi-house icon"></i> Dashboard</a>
            <a href="gedung_ruangan.php"><i class="bi bi-building icon"></i> Master Gedung & Ruangan</a>
            <a href="#"><i class="bi bi-bag-plus icon"></i> Inventory Aset Medis</a>
            <a href="#"><i class="bi bi-bag-check icon"></i> Inventory Aset Non-Medis</a>
            <a href="#"><i class="bi bi-gear icon"></i> Kalibrasi</a>
            <a href="#"><i class="bi bi-search icon"></i> Inspeksi</a>
            <a href="#"><i class="bi bi-tools icon"></i> Maintenance</a>
            <a href="#"><i class="bi bi-exclamation-circle icon"></i> Komplain</a>
            <a href="#"><i class="bi bi-wrench-adjustable icon"></i> Perbaikan</a>
            <a href="#"><i class="bi bi-cpu icon"></i> Manajemen Sparepart</a>
            <a href="#"><i class="bi bi-arrow-left-right icon"></i> Mutasi Aset</a>
            <a href="#"><i class="bi bi-box-seam icon"></i> Stock Opname</a>
            <a href="#"><i class="bi bi-upc-scan icon"></i> QR Code Aset</a>
            <a href="#"><i class="bi bi-currency-exchange icon"></i> Penyusutan Aset</a>
            <a href="#"><i class="bi bi-clock-history icon"></i> Riwayat Aset</a>
            <a href="#"><i class="bi bi-upload icon"></i> Upload SOP</a>
            <a href="#"><i class="bi bi-check2-square icon"></i> Form Checklist</a>
            <a href="#"><i class="bi bi-person-badge icon"></i> Pengguna</a>
            <a href="logout.php"><i class="bi bi-box-arrow-right icon"></i> Logout</a>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 content">
            <h3>Selamat datang, <?= htmlspecialchars($nama) ?> 👋</h3>
            <p class="text-muted">Anda login sebagai <strong><?= htmlspecialchars($role) ?></strong></p>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-hospital icon"></i> Total Aset Medis</h5>
                            <p class="card-text">123 aset</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-capsule icon"></i> Total Aset Non-Medis</h5>
                            <p class="card-text">86 aset</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi bi-exclamation-circle icon"></i> Komplain Aktif</h5>
                            <p class="card-text">5 komplain</p>
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-muted small">Versi sistem: 1.0 - Manajemen Aset Rumah Sakit</p>
        </div>
    </div>
</div>

</body>
</html>
