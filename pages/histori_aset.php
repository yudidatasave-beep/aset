<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once $_SERVER['DOCUMENT_ROOT'] . '/samcibabat/includes/db.php';

// Validasi login
$id_user = $_SESSION['id_user'] ?? null;
if (!$id_user) {
    header("Location: ../login.php");
    exit;
}

// Ambil ID aset
$id_aset = $_GET['id_aset'] ?? null;
if (!$id_aset) {
    echo "<p class='text-danger'>ID Aset tidak ditemukan.</p>";
    exit;
}

// Ambil data aset
$stmt = $conn->prepare("SELECT a.*, r.nama_ruangan FROM aset a JOIN ruangan r ON a.id_ruangan = r.id_ruangan WHERE a.id_aset = ?");
$stmt->bind_param("i", $id_aset);
$stmt->execute();
$result = $stmt->get_result();
$aset = $result->fetch_assoc();
if (!$aset) {
    echo "<p class='text-danger'>Data aset tidak ditemukan.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Histori Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        body {
            background: #f0f4f8;
        }
        .card {
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
        }
        .img-aset {
            max-width: 250px;
            height: auto;
            object-fit: cover;
        }
    </style>
</head>
<body class="container py-4">

<h3 class="mb-4">Informasi Aset & Histori Kegiatan</h3>

<!-- Tombol Navigasi -->
<div class="mb-3 d-flex flex-wrap gap-2">
    <a href="/samcibabat/index.php?page=informasi_aset" class="btn btn-secondary">← Kembali ke Pencarian Aset</a>
    <button class="btn btn-primary" onclick="printQRCode()">🖨️ Cetak QR Code</button>
    <a href="/samcibabat/index.php?page=kegiatan_detail&id_aset=<?= $id_aset ?>" class="btn btn-success">➕ Input Pemeliharaan / Perbaikan</a>
</div>

<!-- Card Informasi Aset -->
<div class="card p-4">
    <div class="row">
        <div class="col-md-4 text-center">
            <?php
$imagePath = '/samcibabat/uploads/' . $aset['image'];
$fileSystemPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;

if (!empty($aset['image']) && file_exists($fileSystemPath)) {
    echo "<img src='{$imagePath}' class='img-thumbnail img-aset' alt='Gambar Aset'>";
} else {
    echo "<div class='text-muted fst-italic'>Tidak ada gambar tersedia.</div>";
}
?>
        </div>
        <div class="col-md-8">
            <table class="table table-bordered">
                <tr><th>Kode Aset</th><td><?= htmlspecialchars($aset['kode_aset']) ?></td></tr>
                <tr><th>Nama Aset</th><td><?= htmlspecialchars($aset['nama_aset']) ?></td></tr>
                <tr><th>Jenis Aset</th><td><?= htmlspecialchars($aset['jenis_aset']) ?></td></tr>
                <tr><th>Merek</th><td><?= htmlspecialchars($aset['merek']) ?></td></tr>
                <tr><th>Nomor Seri</th><td><?= htmlspecialchars($aset['nomor_seri']) ?></td></tr>
                <tr><th>Ruangan</th><td><?= htmlspecialchars($aset['nama_ruangan']) ?></td></tr>
                <tr><th>Tanggal Perolehan</th><td><?= htmlspecialchars($aset['tanggal_perolehan']) ?></td></tr>
            </table>
        </div>
    </div>
</div>

<!-- Histori Kegiatan -->
<h5 class="mt-5">Histori Pemeliharaan / Perbaikan</h5>
<?php
// Ambil histori kegiatan
$stmt = $conn->prepare("
    SELECT k.*, t.nama_teknisi, a.nama_asisten 
    FROM kegiatan k 
    LEFT JOIN teknisi t ON k.id_teknisi = t.id_teknisi
    LEFT JOIN asisten a ON k.id_asisten = a.id_asisten
    WHERE k.id_aset = ? 
    ORDER BY k.waktu_laporan DESC
");
$stmt->bind_param("i", $id_aset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0): ?>
    <table class="table table-striped mt-3">
        <thead class="table-primary">
            <tr>
                <th>No</th>
                <th>Jenis Kegiatan</th>
                <th>Teknisi</th>
                <th>Asisten</th>
                <th>Waktu Laporan</th>
                <th>Keluhan</th>
                <th>Tindakan</th>
                <th>Kesimpulan</th>
            </tr>
        </thead>
        <tbody>
        <?php $no = 1;
        while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['jenis_kegiatan']) ?></td>
                <td><?= htmlspecialchars($row['nama_teknisi'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['nama_asisten'] ?? '-') ?></td>
                <td><?= htmlspecialchars($row['waktu_laporan']) ?></td>
                <td><?= htmlspecialchars($row['keluhan']) ?></td>
                <td><?= htmlspecialchars($row['tindakan']) ?></td>
                <td><?= htmlspecialchars($row['kesimpulan']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-info mt-3">Belum ada histori kegiatan.</div>
<?php endif; ?>


<!-- QR Print Preview -->
<div id="qr-print-area" style="display:none;">
    <table style="width:100%; border:2px solid black; border-collapse:collapse; font-family:sans-serif;">
        <tr style="border-bottom:2px solid black;">
            <td style="width:100px; text-align:center; padding:10px; border-right:2px solid black;">
                <img src="/samcibabat/assets/logo.png" alt="Logo" style="height:60px;">
            </td>
            <td style="text-align:center; font-size:20px; font-weight:bold;">
                <?= htmlspecialchars($aset['nama_aset']) ?>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center; padding:30px;">
                <div id="qrcode-area" style="display: inline-block;"></div>
            </td>
        </tr>
    </table>
</div>
<script>
function printQRCode() {
    const qrUrl = "https://rsudcibabat.cimahikota.go.id/samcibabat/pages/histori_aset.php?id_aset=<?= $id_aset ?>";
    const qrArea = document.getElementById("qrcode-area");
    qrArea.innerHTML = "";

    new QRCode(qrArea, {
        text: qrUrl,
        width: 200,
        height: 200
    });

    setTimeout(() => {
        const printContent = document.getElementById("qr-print-area").innerHTML;
        const win = window.open('', '', 'width=500,height=600');
        win.document.write(`
            <html>
            <head>
                <title>Cetak QR Aset</title>
                <style>
                    body {
                        margin: 20px;
                        font-family: sans-serif;
                    }
                    table {
                        width: 100%;
                        border: 2px solid black;
                        border-collapse: collapse;
                    }
                    td {
                        padding: 10px;
                        border: 2px solid black;
                    }
                    .logo {
                        text-align: center;
                    }
                    .nama-aset {
                        text-align: center;
                        font-size: 18pt;
                        font-weight: bold;
                    }
                    .qr-cell {
                        text-align: center;
                        padding: 30px;
                    }
                </style>
            </head>
            <body onload="window.print(); window.close();">
                ${printContent}
            </body>
            </html>
        `);
        win.document.close();
    }, 500);
}
</script>

</body>
</html>
