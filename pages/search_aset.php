<?php
require dirname(__DIR__) . '/includes/db.php'; // Pastikan path ini benar

$term = $_GET['term'] ?? '';

$query = $conn->prepare("SELECT id_aset, kode_aset, nama_aset FROM aset WHERE nama_aset LIKE CONCAT('%', ?, '%') OR kode_aset LIKE CONCAT('%', ?, '%') LIMIT 20");
$query->bind_param("ss", $term, $term);
$query->execute();
$result = $query->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'label' => "{$row['nama_aset']} - {$row['kode_aset']}",
        'value' => $row['id_aset']
    ];
}
echo json_encode($data);
