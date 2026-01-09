<?php
require '../includes/db.php';

$id_aset = $_POST['id_aset'];
$jenis_kegiatan = $_POST['jenis_kegiatan'];
$id_teknisi = $_POST['id_teknisi'];
$nama_asisten = $_POST['nama_asisten'];
$nip_asisten = $_POST['nip_asisten'];
$catatan = $_POST['catatan'];
$tanggal = date('Y-m-d');

$stmt = $conn->prepare("INSERT INTO kegiatan (id_aset, jenis_kegiatan, id_teknisi, nama_asisten, nip_asisten, catatan, tanggal) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssss", $id_aset, $jenis_kegiatan, $id_teknisi, $nama_asisten, $nip_asisten, $catatan, $tanggal);
$stmt->execute();

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
