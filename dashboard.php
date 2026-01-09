<?php if (!defined('DASHBOARD_LOADED')) define('DASHBOARD_LOADED', true); ?>

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
