<?php
session_start();
include_once __DIR__ . "/../includes/db.php"; // koneksi database stabil

if (isset($_GET['id_aset'])) {
    $id_aset = intval($_GET['id_aset']);

    // Ambil data gambar
    $stmt = $conn->prepare("SELECT image FROM aset WHERE id_aset = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id_aset);
        $stmt->execute();
        $result = $stmt->get_result();
        $aset = $result->fetch_assoc();
        $stmt->close();

        if ($aset) {
            // Hapus file gambar jika ada
            if (!empty($aset['image']) && file_exists(__DIR__ . "/../uploads/" . $aset['image'])) {
                unlink(__DIR__ . "/../uploads/" . $aset['image']);
            }

            // Hapus record dari database
            $stmt = $conn->prepare("DELETE FROM aset WHERE id_aset = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id_aset);
                if ($stmt->execute()) {
                    header("Location: ../index.php?page=aset&msg=deleted");
                    exit;
                } else {
                    echo "❌ Gagal menghapus data aset.";
                }
                $stmt->close();
            }
        } else {
            echo "⚠️ ID aset tidak ditemukan.";
        }
    } else {
        echo "❌ Query tidak valid: " . $conn->error;
    }
} else {
    echo "⚠️ Parameter ID aset tidak ada.";
}
