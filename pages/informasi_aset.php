<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../includes/db.php';

// Validasi login
$id_user = $_SESSION['id_user'] ?? null;
if (!$id_user) {
    header("Location: login.php");
    exit;
}

// Ambil teknisi dari user login (jika nanti perlu diteruskan)
$stmt = $conn->prepare("SELECT id_teknisi, nama_teknisi FROM teknisi WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$teknisi = $result->fetch_assoc();
?>

<h4>Histori Aset dan Cetak QR</h4>

<!-- ✅ FORM PENCARIAN -->
<form method="get" action="index.php">
    <input type="hidden" name="page" value="informasi_aset">
    <div class="mb-3">
        <label for="keyword">Cari Aset (nama / kode / id / ruangan)</label>
        <input type="text" class="form-control" id="keyword" name="keyword" placeholder="Contoh: infus, 003, ICU" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
    </div>
    <button type="submit" class="btn btn-primary">Cari</button>
</form>

<?php
// ✅ TAMPILKAN HASIL PENCARIAN
if (!empty($_GET['keyword'])) {
    $keyword = '%' . $_GET['keyword'] . '%';
    $query = $conn->prepare("
        SELECT a.*, r.nama_ruangan 
        FROM aset a 
        JOIN ruangan r ON a.id_ruangan = r.id_ruangan 
        WHERE a.nama_aset LIKE ? 
           OR a.kode_aset LIKE ?
           OR a.id_aset LIKE ?
           OR r.nama_ruangan LIKE ?
    ");
    $query->bind_param("ssss", $keyword, $keyword, $keyword, $keyword);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='mt-3'><strong>Hasil Pencarian:</strong></div>";
        echo "<table class='table table-bordered'>";
        echo "<thead><tr><th>Kode</th><th>Nama</th><th>Jenis</th><th>Ruangan</th><th>Aksi</th></tr></thead><tbody>";

        while ($aset = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$aset['kode_aset']}</td>
                <td>{$aset['nama_aset']}</td>
                <td>{$aset['jenis_aset']}</td>
                <td>{$aset['nama_ruangan']}</td>
                <td>
                    <a href='pages/histori_aset.php?id_aset={$aset['id_aset']}' class='btn btn-sm btn-success'>Informasi Aset</a>
                </td>
		
            </tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<p class='text-danger mt-3'>Tidak ada aset yang cocok.</p>";
    }
}
?>
