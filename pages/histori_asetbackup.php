<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/db.php';

// Validasi login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_aset = $_GET['id_aset'] ?? null;
if (!$id_aset) {
    echo "<div class='alert alert-danger'>ID Aset tidak ditemukan.</div>";
    exit;
}

// Ambil data aset
$stmt = $conn->prepare("
    SELECT a.*, r.nama_ruangan 
    FROM aset a 
    JOIN ruangan r ON a.id_ruangan = r.id_ruangan 
    WHERE a.id_aset = ?
");
$stmt->bind_param("i", $id_aset);
$stmt->execute();
$data_aset = $stmt->get_result()->fetch_assoc();

if (!$data_aset) {
    echo "<div class='alert alert-danger'>Data aset tidak ditemukan.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Histori Aset</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f3f6fb;
        }
        .card {
            border-radius: 12px;
        }
        .header {
            background: linear-gradient(to right, #0077b6, #00b4d8);
            color: white;
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
        }
        .image-box img {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .table-history th {
            background-color: #0096c7;
            color: white;
        }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="card shadow">
        <div class="header">
            <h4>Informasi Aset dan Riwayat Kegiatan</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- ✅ Gambar Aset -->
                <div class="col-md-4 image-box">
                    <?php
                    $image = $data_aset['image'];
                    $imagePath = "../uploads/" . $image;

                    if (!empty($image) && file_exists($imagePath)) {
                        echo "<img src='{$imagePath}' alt='Gambar Aset'>";
                    } else {
                        echo "<p class='text-muted'>Tidak ada gambar tersedia.</p>";
                    }
                    ?>
                </div>

                <!-- ✅ Informasi Aset -->
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tr><th>Kode Aset</th><td><?= htmlspecialchars($data_aset['kode_aset']) ?></td></tr>
                        <tr><th>Nama Aset</th><td><?= htmlspecialchars($data_aset['nama_aset']) ?></td></tr>
                        <tr><th>Jenis Aset</th><td><?= htmlspecialchars($data_aset['jenis_aset']) ?></td></tr>
                        <tr><th>Merk / Tipe</th><td><?= htmlspecialchars($data_aset['merek'] . ' / ' . $data_aset['tipe']) ?></td></tr>
                        <tr><th>Nomor Seri</th><td><?= htmlspecialchars($data_aset['nomor_seri']) ?></td></tr>
                        <tr><th>Ruangan</th><td><?= htmlspecialchars($data_aset['nama_ruangan']) ?></td></tr>
                        <tr><th>Tanggal Perolehan</th><td><?= htmlspecialchars($data_aset['tanggal_perolehan']) ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- ✅ Histori Kegiatan -->
            <hr>
            <h5 class="mt-4">Histori Pemeliharaan / Perbaikan</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-history mt-3">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis</th>
                            <th>Teknisi</th>
                            <th>Asisten</th>
                            <th>Keluhan</th>
                            <th>Tindakan</th>
                            <th>Waktu Laporan</th>
                            <th>Waktu Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $stmt = $conn->prepare("
                        SELECT k.*, 
                            t.nama_teknisi, 
                            a.nama_asisten 
                        FROM kegiatan k
                        LEFT JOIN teknisi t ON k.id_teknisi = t.id_teknisi
                        LEFT JOIN asisten a ON k.id_asisten = a.id_asisten
                        WHERE k.id_aset = ?
                        ORDER BY k.waktu_laporan DESC
                    ");
                    $stmt->bind_param("i", $id_aset);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['jenis_kegiatan']}</td>
                                <td>{$row['nama_teknisi']}</td>
                                <td>{$row['nama_asisten']}</td>
                                <td>{$row['keluhan']}</td>
                                <td>{$row['tindakan']}</td>
                                <td>{$row['waktu_laporan']}</td>
                                <td>{$row['waktu_selesai']}</td>
                            </tr>";
                            $no++;
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center'>Belum ada kegiatan yang tercatat.</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>

            <a href="../index.php?page=form_kegiatan" class="btn btn-secondary mt-3">← Kembali ke Pencarian Aset</a>
        </div>
    </div>
</div>
</body>
</html>
