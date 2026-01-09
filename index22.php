<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}
$nama = $_SESSION['nama'];
$role = $_SESSION['role'];
$page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pemeliharaan Aset RS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap & Icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --main-color: #2A4D69;
            --main-hover: #1f3b50;
            --sidebar-width: 240px;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: #f1f3f5;
            font-family: 'Segoe UI', sans-serif;
        }

        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background-color: var(--main-color);
            color: white;
            display: flex;
            flex-direction: column;
            box-shadow: 2px 0 6px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar-brand img {
            height: 30px;
            margin-right: 10px;
        }

        .sidebar a {
            color: #e0e0e0;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: background 0.3s, color 0.3s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: var(--main-hover);
            color: #ffffff;
        }

        .sidebar i {
            margin-right: 10px;
        }

        .content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            min-height: 100vh;
            background: #f8f9fa;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <img src="assets/logo.png" alt="Logo">
        <span><strong>MANAJEMEN ASET RS</strong></span>
    </div>

    <a href="index.php?page=dashboard" class="<?= ($page == 'dashboard') ? 'active' : '' ?>"><i class="bi bi-house"></i> Dashboard</a>
    <a href="index.php?page=gedung_ruangan" class="<?= ($page == 'gedung_ruangan') ? 'active' : '' ?>"><i class="bi bi-building"></i> Master Gedung</a>
    <a href="index.php?page=ruangan" class="<?= ($page == 'ruangan') ? 'active' : '' ?>"><i class="bi bi-door-open"></i> Master Ruangan</a>
    <a href="index.php?page=aset" class="<?= ($page == 'aset') ? 'active' : '' ?>"><i class="bi bi-bag-plus"></i> Master Aset</a>
   

    <hr style="border-color: rgba(255,255,255,0.3);">
	 <a href="index.php?page=form_kegiatan" class="<?= ($page == 'form_kegiatan') ? 'active' : '' ?>"><i class="bi bi-clipboard-check"></i> Kegiatan</a>
	 <a href="index.php?page=informasi_aset" class="<?= ($page == 'informasi_aset') ? 'active' : '' ?>"><i class="bi bi-clipboard-check"></i> Informasi Aset</a>
	 
	 <hr style="border-color: rgba(255,255,255,0.3);">
	 <a href="index.php?page=form_pembelian_aset" class="<?= ($page == 'form_pembelian_aset') ? 'active' : '' ?>"><i class="bi bi-clipboard-check"></i> Pembelian Aset</a>
	 <a href="index.php?page=informasi_aset" class="<?= ($page == 'informasi_aset') ? 'active' : '' ?>"><i class="bi bi-clipboard-check"></i> Pengeluaran Aset</a>
	  <a href="index.php?page=informasi_aset" class="<?= ($page == 'informasi_aset') ? 'active' : '' ?>"><i class="bi bi-clipboard-check"></i> Mutasi Aset</a>
	 
	
   <hr style="border-color: rgba(255,255,255,0.3);">
	<a href="index.php?page=laporan_aset" class="<?= ($page == 'laporan_aset') ? 'active' : '' ?>">
    <i class="bi bi-file-earmark-text icon"></i> Laporan Aset </a>
	<a href="index.php?page=laporan_kegiatan" class="<?= ($page == 'laporan_kegiatan') ? 'active' : '' ?>">
    <i class="bi bi-file-earmark-text icon"></i> Laporan Kegiatan </a>
	<hr style="border-color: rgba(255,255,255,0.3);">
	
	

    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="content">
    <?php
    $file = "pages/$page.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<div class='alert alert-danger'><strong>Error:</strong> Halaman tidak ditemukan!</div>";
    }
    ?>
</div>

</body>
</html>
