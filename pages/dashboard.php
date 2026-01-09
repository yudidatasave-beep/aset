<?php
if (!defined('DASHBOARD_LOADED')) define('DASHBOARD_LOADED', true);

// Sesuaikan path ke db.php
require_once __DIR__ . '/../includes/db.php';


// Hitung total aset medis
$sqlMedis = "
    SELECT COUNT(*) as total 
    FROM aset a
    JOIN jenis_aset j ON a.id_jenis_aset = j.id_jenis_aset
    WHERE j.nama_jenis_aset LIKE '%medis%'
";
$totalMedis = $conn->query($sqlMedis)->fetch_assoc()['total'] ?? 0;

// Hitung total aset non-medis
$sqlNonMedis = "
    SELECT COUNT(*) as total 
    FROM aset a
    JOIN jenis_aset j ON a.id_jenis_aset = j.id_jenis_aset
    WHERE j.nama_jenis_aset LIKE '%non%'
";
$totalNonMedis = $conn->query($sqlNonMedis)->fetch_assoc()['total'] ?? 0;

// Hitung komplain aktif
$sqlKomplain = "
    SELECT COUNT(*) as total 
    FROM kegiatan 
    WHERE jenis_kegiatan = 'Komplain' 
      AND (waktu_selesai IS NULL OR waktu_selesai = '')
";
$totalKomplain = $conn->query($sqlKomplain)->fetch_assoc()['total'] ?? 0;
?>

<h3>Selamat datang, <?= htmlspecialchars($nama) ?> 👋</h3>
<p class="text-muted">Anda login sebagai <strong><?= htmlspecialchars($role) ?></strong></p>
<hr>

<div class="row">
    <!-- Aset Medis -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-4 border-primary">
            <div class="card-body text-center">
                <h5 class="card-title text-primary">
                    <i class="bi bi-hospital icon"></i> Total Aset Medis
                </h5>
                <p class="card-text fs-4 fw-bold"><?= number_format($totalMedis, 0, ',', '.') ?> aset</p>
            </div>
        </div>
    </div>

    <!-- Aset Non-Medis -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-4 border-success">
            <div class="card-body text-center">
                <h5 class="card-title text-success">
                    <i class="bi bi-capsule icon"></i> Total Aset Non-Medis
                </h5>
                <p class="card-text fs-4 fw-bold"><?= number_format($totalNonMedis, 0, ',', '.') ?> aset</p>
            </div>
        </div>
    </div>

    <!-- Komplain Aktif -->
    <div class="col-md-4">
        <div class="card shadow-sm mb-4 border-danger">
            <div class="card-body text-center">
                <h5 class="card-title text-danger">
                    <i class="bi bi-exclamation-circle icon"></i> Komplain Aktif
                </h5>
                <p class="card-text fs-4 fw-bold"><?= number_format($totalKomplain, 0, ',', '.') ?> komplain</p>
            </div>
        </div>
    </div>
</div>

<p class="text-muted small">Versi sistem: 1.0 - Manajemen Aset Rumah Sakit</p>
