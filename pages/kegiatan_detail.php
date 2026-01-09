<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/samcibabat/includes/db.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = (int)($_SESSION['id_user'] ?? 0);

/* ===========================
   AMBIL DATA TEKNISI DARI USER
   =========================== */
$stmtTeknisi = $conn->prepare("SELECT id_teknisi, nama_teknisi FROM teknisi WHERE id_user = ?");
$stmtTeknisi->bind_param("i", $id_user);
$stmtTeknisi->execute();
$teknisi = $stmtTeknisi->get_result()->fetch_assoc();
$id_teknisi = $teknisi['id_teknisi'] ?? null;
$nama_teknisi = $teknisi['nama_teknisi'] ?? '';
$stmtTeknisi->close();

/* ===========================
   AMBIL DATA ASET
   =========================== */
$id_aset = isset($_GET['id_aset']) ? (int)$_GET['id_aset'] : 0;
if (!$id_aset) {
    echo "<div class='alert alert-danger m-3'>ID Aset tidak ditemukan.</div>";
    exit;
}

$stmt = $conn->prepare("SELECT a.*, r.nama_ruangan 
                        FROM aset a 
                        JOIN ruangan r ON a.id_ruangan = r.id_ruangan 
                        WHERE a.id_aset = ?");
$stmt->bind_param("i", $id_aset);
$stmt->execute();
$aset = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$aset) {
    echo "<div class='alert alert-danger m-3'>Aset tidak ditemukan.</div>";
    exit;
}

/* ===========================
   PROSES SIMPAN (SEBELUM OUTPUT)
   =========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    // Direktori upload
    $baseUploadDir = $_SERVER['DOCUMENT_ROOT'] . '/samcibabat/uploads/';
    $signDir       = $baseUploadDir . 'signatures/';
    if (!is_dir($baseUploadDir)) { @mkdir($baseUploadDir, 0755, true); }
    if (!is_dir($signDir))       { @mkdir($signDir, 0755, true); }

    // Upload foto kegiatan (maks 3)
    $imageFiles = [];
    if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && is_array($_FILES['image']['tmp_name'])) {
        foreach ($_FILES['image']['tmp_name'] as $i => $tmp) {
            if ($i >= 3) break;
            if (!empty($tmp) && is_uploaded_file($tmp)) {
                $original = $_FILES['image']['name'][$i] ?? 'image.png';
                $ext = pathinfo($original, PATHINFO_EXTENSION);
                if ($ext === '') $ext = 'png';
                $fname = 'kegiatan_' . uniqid() . '.' . strtolower($ext);
                $dest  = $baseUploadDir . $fname;
                if (move_uploaded_file($tmp, $dest)) {
                    $imageFiles[] = $fname; // Simpan hanya nama file
                }
            }
        }
    }
    $imageField = implode(',', $imageFiles);

    // Simpan signature base64 -> file
    // Empat pihak: 1=Teknisi, 2=Asisten, 3=User Ruangan, 4=Kepala IPSRS
    function saveSignature($dataUrl, $signDir, $prefix) {
        if (!$dataUrl) return null;
        if (strpos($dataUrl, 'data:image') !== 0) return null;
        [$meta, $content] = explode(',', $dataUrl);
        $bin = base64_decode($content);
        if ($bin === false) return null;
        $filename = $prefix . '_' . uniqid() . '.png';
        $path = $signDir . $filename;
        if (file_put_contents($path, $bin) !== false) {
            return $filename; // Simpan hanya nama file
        }
        return null;
    }

    $ttd1 = saveSignature($_POST['ttd1'] ?? '', $signDir, 'tanda_tangan_teknisi');
    $ttd2 = saveSignature($_POST['ttd2'] ?? '', $signDir, 'tanda_tangan_asisten');
    $ttd3 = saveSignature($_POST['ttd3'] ?? '', $signDir, 'tanda_tangan_kepala_ruangan');
    $ttd4 = saveSignature($_POST['ttd4'] ?? '', $signDir, 'tanda_tangan_kepala_instalasi');

    // Insert kegiatan
    $sql = "INSERT INTO kegiatan (
                id_aset, id_teknisi, id_asisten, jenis_kegiatan, keluhan, tindakan, kesimpulan,
                waktu_laporan, waktu_respon, waktu_selesai, nomor_lk, id_user_ruangan, image,biaya_perawatan,
                tanda_tangan_teknisi, tanda_tangan_asisten, tanda_tangan_kepala_ruangan, tanda_tangan_kepala_instalasi
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmtIns = $conn->prepare($sql);
    if (!$stmtIns) {
        echo "<div class='alert alert-danger m-3'>Gagal prepare: " . htmlspecialchars($conn->error) . "</div>";
        exit;
    }

    $id_asisten       = (int)($_POST['id_asisten'] ?? 0);
    $jenis_kegiatan   = $_POST['jenis_kegiatan'] ?? '';
    $keluhan          = $_POST['keluhan'] ?? '';
    $tindakan         = $_POST['tindakan'] ?? '';
    $kesimpulan       = $_POST['kesimpulan'] ?? '';
    $w_laporan        = $_POST['waktu_laporan'] ?? '';
    $w_respon         = $_POST['waktu_respon'] ?? '';
    $w_selesai        = $_POST['waktu_selesai'] ?? '';
    $nomor_lk         = $_POST['nomor_lk'] ?? '';
    $biaya_perawatan         = $_POST['biaya_perawatan'] ?? '';
    $id_user_ruangan  = (int)($_POST['id_user_ruangan'] ?? 0);

    $stmtIns->bind_param(
        "iiissssssssisssss",
        $id_aset,
        $id_teknisi,
        $id_asisten,
        $jenis_kegiatan,
        $keluhan,
        $tindakan,
        $kesimpulan,
        $w_laporan,
        $w_respon,
        $w_selesai,
        $nomor_lk,
		$biaya_perawatan,
        $id_user_ruangan,
        $imageField,
        $ttd1, $ttd2, $ttd3, $ttd4
    );

    if ($stmtIns->execute()) {
        echo "<script>
            alert('Kegiatan berhasil disimpan!');
            window.location.href = '../index.php?page=form_kegiatan';
        </script>";
        exit;
    } else {
        echo "<div class='alert alert-danger m-3'>Gagal menyimpan: " . htmlspecialchars($stmtIns->error) . "</div>";
    }
    $stmtIns->close();
}

/* ===========================
   DATA DROPDOWN
   =========================== */
$asistenQ = $conn->query("SELECT id_asisten, nama_asisten FROM asisten ORDER BY nama_asisten");
$userRQ   = $conn->query("SELECT id_user_ruangan, nama_user_ruangan FROM user_ruangan ORDER BY nama_user_ruangan");
$kepalaQ  = $conn->query("SELECT id_kepala_ipsrs, nama_kepala_ipsrs FROM kepala_ipsrs ORDER BY nama_kepala_ipsrs"); // opsional, untuk label
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Kegiatan Aset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .navbar-denim { background-color: #3a4e63; }
        .navbar-brand, .navbar-nav .nav-link { color: #fff; }
        .navbar-nav .nav-link:hover { color: #d1d1d1; }
        .card { border-radius: 0.75rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .card-header { background-color: #3a4e63; color: #fff; font-weight: 600; }
        .aset-image {
            width: 100%; max-width: 220px; height: auto; border-radius: 8px;
            object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,0.08);
        }
        .sig-card .card-header { background: #e7f1ff; color: #2b4c7e; }
        .sig-pad {
            width: 100%; height: 160px; background: #fff; border: 1px dashed #92a1b0;
            border-radius: 8px; touch-action: none; cursor: crosshair;
        }
        .badge-soft { background: #eef4ff; color: #2b4c7e; border: 1px solid #cfe0ff; }
        .form-section-title {
            font-size: 1rem; font-weight: 700; color: #2b4c7e; letter-spacing: .3px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-denim mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Sistem Aset RS</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="../index.php?page=form_kegiatan">Form Kegiatan</a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h3 class="mb-4">Form Kegiatan Pemeliharaan / Perbaikan</h3>

    <!-- Informasi Aset -->
    <div class="card mb-4">
        <div class="card-header">Informasi Aset</div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 mb-3 text-center">
                    <?php
                    $imgOk = false;
                    if (!empty($aset['image'])) {
                        $try1 = $_SERVER['DOCUMENT_ROOT'] . "/samcibabat/uploads/" . $aset['image']; // jika hanya nama file
                        $try2 = $_SERVER['DOCUMENT_ROOT'] . "/samcibabat/" . $aset['image'];        // jika sudah ada prefix uploads/
                        if (file_exists($try1)) { $imgSrc = "/samcibabat/uploads/" . $aset['image']; $imgOk = true; }
                        elseif (file_exists($try2)) { $imgSrc = "/samcibabat/" . $aset['image']; $imgOk = true; }
                    }
                    if ($imgOk): ?>
                        <img src="<?= htmlspecialchars($imgSrc) ?>" class="aset-image" alt="Gambar Aset">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/320x220?text=No+Image" class="aset-image" alt="No Image">
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <div class="row g-2">
                        <div class="col-6"><span class="badge badge-soft">Kode Aset</span><div><?= htmlspecialchars($aset['kode_aset']) ?></div></div>
                        <div class="col-6"><span class="badge badge-soft">Nama Aset</span><div><?= htmlspecialchars($aset['nama_aset']) ?></div></div>
                        <div class="col-6"><span class="badge badge-soft">Merek</span><div><?= htmlspecialchars($aset['merek']) ?></div></div>
                        <div class="col-6"><span class="badge badge-soft">Tipe</span><div><?= htmlspecialchars($aset['tipe']) ?></div></div>
                        <div class="col-6"><span class="badge badge-soft">Nomor Seri</span><div><?= htmlspecialchars($aset['nomor_seri']) ?></div></div>
                        <div class="col-6"><span class="badge badge-soft">Ruangan</span><div><?= htmlspecialchars($aset['nama_ruangan']) ?></div></div>
                        <div class="col-6"><span class="badge badge-soft">Tgl Perolehan</span><div><?= htmlspecialchars($aset['tanggal_perolehan']) ?></div></div>
                        <div class="col-6"><span class="badge badge-soft">Nilai</span><div><?= htmlspecialchars($aset['nilai_perolehan']) ?></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Input Kegiatan -->
    <div class="card mb-4">
        <div class="card-header">Form Input Kegiatan</div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data" id="formKegiatan">
                <input type="hidden" name="id_aset" value="<?= (int)$aset['id_aset'] ?>">
                <input type="hidden" name="id_teknisi" value="<?= (int)$id_teknisi ?>">

                <div class="mb-2 form-section-title">Identitas Petugas</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Teknisi</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($nama_teknisi) ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Asisten</label>
                        <select name="id_asisten" class="form-select" required>
                            <option value="">-- Pilih Asisten --</option>
                            <?php while ($row = $asistenQ->fetch_assoc()): ?>
                                <option value="<?= (int)$row['id_asisten'] ?>"><?= htmlspecialchars($row['nama_asisten']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-2 mt-3 form-section-title">Detail Kegiatan</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Jenis Kegiatan</label>
                        <select name="jenis_kegiatan" class="form-select" required>
                            <option value="pemeliharaan">Pemeliharaan</option>
                            <option value="perbaikan">Perbaikan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nomor LK</label>
                        <input type="text" name="nomor_lk" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">User Ruangan</label>
                        <select name="id_user_ruangan" class="form-select" required>
                            <option value="">-- Pilih User Ruangan --</option>
                            <?php while ($ur = $userRQ->fetch_assoc()): ?>
                                <option value="<?= (int)$ur['id_user_ruangan'] ?>"><?= htmlspecialchars($ur['nama_user_ruangan']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Keluhan</label>
                        <input type="text" name="keluhan" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Tindakan</label>
                        <input type="text" name="tindakan" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Kesimpulan</label>
                        <input type="text" name="kesimpulan" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Waktu Laporan</label>
                        <input type="datetime-local" name="waktu_laporan" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Waktu Respon</label>
                        <input type="datetime-local" name="waktu_respon" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Waktu Selesai</label>
                        <input type="datetime-local" name="waktu_selesai" class="form-control" required>
                    </div>
					<div class="col-md-4">
					<label class="form-label">Biaya Perawatan (Rp)</label>
				<input type="text" name="biaya_perawatan" id="biaya_perawatan" class="form-control" placeholder="Rp 0" required>
				</div>

<script>
// Format otomatis ke Rupiah
document.getElementById('biaya_perawatan').addEventListener('keyup', function(e) {
    let angka = this.value.replace(/[^,\d]/g, '');
    let rupiah = '';
    let sisa = angka.length % 3;
    let ribuan = angka.substr(sisa).match(/\d{3}/g);

    if (sisa) {
        rupiah = angka.substr(0, sisa);
    }
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    this.value = rupiah ? 'Rp ' + rupiah : '';
});
</script>


                    <div class="col-md-12">
                        <label class="form-label">Upload Foto (maks 3)</label>
                        <input type="file" name="image[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Foto akan disimpan di /samcibabat/uploads</small>
                    </div>
                </div>

                <!-- Tanda Tangan 4 Orang -->
                <div class="row g-3 mt-4">
                    <div class="col-md-6">
                        <div class="card sig-card">
                            <div class="card-header">Tanda Tangan Teknisi</div>
                            <div class="card-body">
                                <canvas id="pad1" class="sig-pad"></canvas>
                                <div class="mt-2 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearPad(1)"><i class="bi bi-eraser"></i> Hapus</button>
                                </div>
                                <input type="hidden" name="ttd1" id="ttd1">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card sig-card">
                            <div class="card-header">Tanda Tangan Asisten</div>
                            <div class="card-body">
                                <canvas id="pad2" class="sig-pad"></canvas>
                                <div class="mt-2 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearPad(2)"><i class="bi bi-eraser"></i> Hapus</button>
                                </div>
                                <input type="hidden" name="ttd2" id="ttd2">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card sig-card">
                            <div class="card-header">Tanda Tangan User Ruangan</div>
                            <div class="card-body">
                                <canvas id="pad3" class="sig-pad"></canvas>
                                <div class="mt-2 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearPad(3)"><i class="bi bi-eraser"></i> Hapus</button>
                                </div>
                                <input type="hidden" name="ttd3" id="ttd3">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card sig-card">
                            <div class="card-header">Tanda Tangan Kepala IPSRS</div>
                            <div class="card-body">
                                <canvas id="pad4" class="sig-pad"></canvas>
                                <div class="mt-2 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearPad(4)"><i class="bi bi-eraser"></i> Hapus</button>
                                </div>
                                <input type="hidden" name="ttd4" id="ttd4">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" name="simpan" class="btn btn-success" onclick="captureAll()"><i class="bi bi-save"></i> Simpan</button>
                    <a href="../index.php?page=form_kegiatan" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Signature Pad sederhana (tanpa library eksternal)
function SigPad(canvasId) {
    const c = document.getElementById(canvasId);
    const ctx = c.getContext('2d');
    let drawing = false, last = null;

    // Resize to device pixel ratio for crisp lines
    function fitCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const w = c.clientWidth, h = c.clientHeight;
        c.width = w * ratio; c.height = h * ratio;
        ctx.scale(ratio, ratio);
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.lineWidth = 2;
        ctx.strokeStyle = '#1f3b50';
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0,0,w,h);
    }
    fitCanvas(); window.addEventListener('resize', fitCanvas);

    function pos(e) {
        if (e.touches && e.touches.length) {
            const rect = c.getBoundingClientRect();
            return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
        } else {
            const rect = c.getBoundingClientRect();
            return { x: e.clientX - rect.left, y: e.clientY - rect.top };
        }
    }

    function start(e){ drawing = true; last = pos(e); e.preventDefault(); }
    function move(e){
        if (!drawing) return;
        const p = pos(e);
        ctx.beginPath();
        ctx.moveTo(last.x, last.y);
        ctx.lineTo(p.x, p.y);
        ctx.stroke();
        last = p;
        e.preventDefault();
    }
    function end(){ drawing = false; }

    c.addEventListener('mousedown', start);
    c.addEventListener('mousemove', move);
    window.addEventListener('mouseup', end);

    c.addEventListener('touchstart', start, {passive:false});
    c.addEventListener('touchmove', move, {passive:false});
    window.addEventListener('touchend', end);

    return {
        clear: () => { ctx.clearRect(0,0,c.width,c.height); fitCanvas(); },
        dataURL: () => c.toDataURL('image/png')
    };
}

const pad1 = SigPad('pad1');
const pad2 = SigPad('pad2');
const pad3 = SigPad('pad3');
const pad4 = SigPad('pad4');

function clearPad(n){
    ({1:pad1,2:pad2,3:pad3,4:pad4}[n]).clear();
}

function captureAll(){
    document.getElementById('ttd1').value = pad1.dataURL();
    document.getElementById('ttd2').value = pad2.dataURL();
    document.getElementById('ttd3').value = pad3.dataURL();
    document.getElementById('ttd4').value = pad4.dataURL();
}
</script>

</body>
</html>
