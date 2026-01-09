<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require '../includes/db.php'; // pastikan path benar

$aset = null;
$keyword = $_POST['keyword'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cari'])) {
    $stmt = $conn->prepare("SELECT a.*, r.nama_ruangan 
                            FROM aset a 
                            LEFT JOIN ruangan r ON a.id_ruangan = r.id_ruangan 
                            WHERE a.kode_aset LIKE ? OR a.nama_aset LIKE ?");
    $like = "%$keyword%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $aset = $result->fetch_assoc();
}
?>

<form method="POST" class="mb-3">
    <label for="keyword">Cari Aset (Kode atau Nama):</label>
    <div class="input-group">
        <input type="text" name="keyword" class="form-control" placeholder="Contoh: AS001" required>
        <button type="submit" name="cari" class="btn btn-primary">Cari</button>
    </div>
</form>

<?php if ($aset): ?>
    <div class="card">
        <div class="card-body">
            <h5>Informasi Aset</h5>
            <p><strong>Nama:</strong> <?= htmlspecialchars($aset['nama_aset']) ?></p>
            <p><strong>Kode:</strong> <?= htmlspecialchars($aset['kode_aset']) ?></p>
            <p><strong>Ruangan:</strong> <?= htmlspecialchars($aset['nama_ruangan']) ?></p>
            <p><strong>Merek:</strong> <?= htmlspecialchars($aset['merek']) ?></p>
            <p><strong>Nomor Seri:</strong> <?= htmlspecialchars($aset['nomor_seri']) ?></p>
            <form method="post" action="index.php?page=form_kegiatan&kode=<?= $aset['kode_aset'] ?>">
                <input type="hidden" name="id_aset" value="<?= $aset['id_aset'] ?>">
                <button type="submit" class="btn btn-success">Isi Kegiatan</button>
            </form>
        </div>
    </div>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <div class="alert alert-warning">Aset tidak ditemukan.</div>
<?php endif; ?>
