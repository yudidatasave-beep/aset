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
    <title>Manajemen Aset</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --main-color: #2A4D69;
            --hover-color: #1f3b50;
            --sidebar-width: 250px;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--main-color);
            color: white;
            overflow-y: auto;
            transition: all 0.3s;
            z-index: 1050;
        }

        .sidebar .brand {
            padding: 1rem;
            font-size: 1.2rem;
            font-weight: bold;
            background-color: var(--hover-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar a {
            color: #f1f1f1;
            display: block;
            padding: 0.75rem 1rem;
            text-decoration: none;
            transition: 0.2s;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: var(--hover-color);
        }

        .content {
            margin-left: var(--sidebar-width);
            padding: 2rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }

            .sidebar.show {
                left: 0;
            }

            .content {
                margin-left: 0;
            }

            #sidebarToggle {
                display: inline-block;
                position: fixed;
                top: 10px;
                left: 10px;
                z-index: 1060;
                background: var(--main-color);
                border: none;
                color: white;
                padding: 6px 10px;
                border-radius: 4px;
            }
        }
    </style>
</head>
<body>

<!-- Sidebar Toggle for Mobile -->
<button id="sidebarToggle" class="d-md-none">
    <i class="bi bi-list"></i>
</button>

<!-- Sidebar -->
<div class="sidebar d-flex flex-column" id="sidebar">
    <div class="brand">
        <img src="assets/logo.png" alt="Logo" height="30">
        Manajemen Aset
    </div>

    <a href="index.php?page=dashboard" class="<?= ($page == 'dashboard') ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i> Dashboard
    </a>

    <div class="dropdown">
        <a class="dropdown-toggle <?= in_array($page, ['gedung_ruangan', 'ruangan', 'aset']) ? 'active' : '' ?>" data-bs-toggle="dropdown" href="#">
            <i class="bi bi-gear"></i> Master Data
        </a>
        <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item <?= ($page == 'aset') ? 'active' : '' ?>" href="index.php?page=aset">Aset</a></li>
			<li><a class="dropdown-item <?= ($page == 'gedung_ruangan') ? 'active' : '' ?>" href="index.php?page=gedung_ruangan">Gedung</a></li>
            <li><a class="dropdown-item <?= ($page == 'ruangan') ? 'active' : '' ?>" href="index.php?page=ruangan">Ruangan</a></li>
			<li><a class="dropdown-item <?= ($page == 'teknisi') ? 'active' : '' ?>" href="index.php?page=teknisi">Teknisi</a></li>
			<li><a class="dropdown-item <?= ($page == 'user') ? 'active' : '' ?>" href="index.php?page=user">User Login</a></li>
        </ul>
    </div>

    <div class="dropdown">
        <a class="dropdown-toggle <?= in_array($page, ['form_kegiatan', 'informasi_aset']) ? 'active' : '' ?>" data-bs-toggle="dropdown" href="#">
            <i class="bi bi-tools"></i> Kegiatan Aset
        </a>
        <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item <?= ($page == 'form_kegiatan') ? 'active' : '' ?>" href="index.php?page=form_kegiatan">Input Kegiatan</a></li>
            <li><a class="dropdown-item <?= ($page == 'informasi_aset') ? 'active' : '' ?>" href="index.php?page=informasi_aset">Informasi Aset</a></li>
        </ul>
    </div>

    <div class="dropdown">
        <a class="dropdown-toggle <?= in_array($page, ['form_pembelian_aset']) ? 'active' : '' ?>" data-bs-toggle="dropdown" href="#">
            <i class="bi bi-cart-check"></i> Pengadaan Aset
        </a>
        <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item <?= ($page == 'form_pembelian_aset') ? 'active' : '' ?>" href="index.php?page=form_pembelian_aset">Pembelian Aset</a></li>
            <li><a class="dropdown-item" href="#">Pengeluaran Aset</a></li>
            <li><a class="dropdown-item" href="#">Mutasi Aset</a></li>
        </ul>
    </div>

    <div class="dropdown">
        <a class="dropdown-toggle <?= in_array($page, ['laporan_aset', 'laporan_kegiatan']) ? 'active' : '' ?>" data-bs-toggle="dropdown" href="#">
            <i class="bi bi-clipboard-data"></i> Laporan
        </a>
        <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item <?= ($page == 'laporan_aset') ? 'active' : '' ?>" href="index.php?page=laporan_aset">Laporan Aset</a></li>
            <li><a class="dropdown-item <?= ($page == 'laporan_kegiatan') ? 'active' : '' ?>" href="index.php?page=laporan_kegiatan">Laporan Kegiatan</a></li>
        </ul>
    </div>

    <hr class="text-white mx-3">

    <a href="logout.php" onclick="return confirm('Yakin ingin logout?')">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>

<!-- Main Content -->
<div class="content">
    <?php
    $file = "pages/$page.php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<div class='alert alert-danger'>Halaman tidak ditemukan!</div>";
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle on mobile
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>
</body>
</html>
