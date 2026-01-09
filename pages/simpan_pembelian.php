<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';

// Validasi input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_supplier = $_POST['id_supplier'] ?? null;
    $nomor_faktur = $_POST['nomor_faktur'] ?? '';
    $tanggal_pembelian = $_POST['tanggal_pembelian'] ?? '';
    $tanggal_jatuh_tempo = $_POST['tanggal_jatuh_tempo'] ?? '';
    $total_pembayaran = $_POST['total_pembayaran'] ?? 0;
    $id_user = $_SESSION['id_user'] ?? null;

    $id_aset = $_POST['id_aset'] ?? [];
    $nomor_seri = $_POST['nomor_seri'] ?? [];
    $jumlah = $_POST['jumlah'] ?? [];
    $satuan = $_POST['satuan'] ?? [];
    $harga_perolehan = $_POST['harga_perolehan'] ?? [];

    if (!$id_supplier || !$tanggal_pembelian || !$id_user) {
        die("Data tidak lengkap.");
    }

    try {
        $conn->begin_transaction();

        // Simpan ke tabel pembelian_aset
        $stmt = $conn->prepare("INSERT INTO pembelian_aset (id_supplier, nomor_faktur, tanggal_pembelian, tanggal_jatuh_tempo, total_pembayaran, id_user) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssii", $id_supplier, $nomor_faktur, $tanggal_pembelian, $tanggal_jatuh_tempo, $total_pembayaran, $id_user);
        $stmt->execute();
        $id_pembelian = $stmt->insert_id;

        // Loop setiap baris detail pembelian
        for ($i = 0; $i < count($id_aset); $i++) {
            $idAset = $id_aset[$i];
            $nomorSeri = $nomor_seri[$i];
            $jumlahItem = $jumlah[$i];
            $satuanItem = $satuan[$i];
            $harga = $harga_perolehan[$i];

            // Simpan ke detail_pembelian_aset
            $stmtDetail = $conn->prepare("INSERT INTO detail_pembelian_aset (id_pembelian, id_aset, nomor_seri, jumlah, satuan, harga_perolehan) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtDetail->bind_param("iisisi", $id_pembelian, $idAset, $nomorSeri, $jumlahItem, $satuanItem, $harga);
            $stmtDetail->execute();

            // Tambah stok ke Gudang (id_ruangan = 1)
            $id_ruangan = 1;
            $cekStok = $conn->prepare("SELECT jumlah FROM stok_aset WHERE id_aset = ? AND id_ruangan = ?");
            $cekStok->bind_param("ii", $idAset, $id_ruangan);
            $cekStok->execute();
            $result = $cekStok->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $jumlahBaru = $row['jumlah'] + $jumlahItem;

                $updateStok = $conn->prepare("UPDATE stok_aset SET jumlah = ?, last_updated = NOW() WHERE id_aset = ? AND id_ruangan = ?");
                $updateStok->bind_param("iii", $jumlahBaru, $idAset, $id_ruangan);
                $updateStok->execute();
            } else {
                $insertStok = $conn->prepare("INSERT INTO stok_aset (id_aset, id_ruangan, jumlah) VALUES (?, ?, ?)");
                $insertStok->bind_param("iii", $idAset, $id_ruangan, $jumlahItem);
                $insertStok->execute();
            }
        }

        $conn->commit();
        $_SESSION['success'] = "Pembelian berhasil disimpan.";
        header("Location: ../pages/form_pembelian_aset.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        die("Gagal menyimpan pembelian: " . $e->getMessage());
    }
} else {
    die("Akses tidak sah.");
}
