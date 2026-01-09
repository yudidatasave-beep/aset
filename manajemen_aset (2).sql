-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 15, 2025 at 04:47 AM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `manajemen_aset`
--

-- --------------------------------------------------------

--
-- Table structure for table `aset`
--

CREATE TABLE `aset` (
  `id_aset` int(11) NOT NULL,
  `kode_aset` varchar(50) NOT NULL,
  `nama_aset` varchar(100) NOT NULL,
  `jenis_aset` enum('Medis','Non Medis') NOT NULL,
  `merek` varchar(100) DEFAULT NULL,
  `tipe` varchar(100) DEFAULT NULL,
  `id_ruangan` int(11) NOT NULL,
  `tanggal_perolehan` date NOT NULL,
  `nilai_perolehan` decimal(15,2) NOT NULL,
  `umur_ekonomis` tinyint(4) NOT NULL COMMENT 'Dalam tahun',
  `kondisi_alat` enum('aktif','tidak aktif','rusak','diperbaiki') DEFAULT 'aktif',
  `kepemilikan` enum('RSUD Cibabat','KSO') DEFAULT NULL,
  `status_penyusutan` enum('belum dihitung','dihitung') DEFAULT 'belum dihitung',
  `nomor_seri` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stok` decimal(30,0) DEFAULT NULL,
  `jumlah_nilai_perolehan` decimal(30,0) DEFAULT NULL,
  `id_jenis_aset` int(11) DEFAULT NULL,
  `spo` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `jenis_anggaran` enum('BTT','BLUD') DEFAULT NULL,
  `kalibrasi` date DEFAULT NULL,
  `perusahaan` varchar(255) DEFAULT NULL,
  `kartu_kuning` enum('Ada','Tidak Ada') DEFAULT NULL,
  `akl` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `aset`
--

INSERT INTO `aset` (`id_aset`, `kode_aset`, `nama_aset`, `jenis_aset`, `merek`, `tipe`, `id_ruangan`, `tanggal_perolehan`, `nilai_perolehan`, `umur_ekonomis`, `kondisi_alat`, `kepemilikan`, `status_penyusutan`, `nomor_seri`, `image`, `stok`, `jumlah_nilai_perolehan`, `id_jenis_aset`, `spo`, `model`, `jenis_anggaran`, `kalibrasi`, `perusahaan`, `kartu_kuning`, `akl`) VALUES
(1, 'RSCBBT/HD/AC/001', 'AC Bro', 'Non Medis', 'PHILIPS', 'A21', 1, '2025-07-25', '5000000.00', 0, '', 'RSUD Cibabat', '', '123456', 'img_1756181780_6c34b827.jpg', NULL, NULL, NULL, 'spo_1756187096_4eeca694.pdf', '', 'BTT', '0000-00-00', '', 'Ada', ''),
(11, '00910', 'USG 12345', 'Medis', '0', 'SS', 1, '2025-07-31', '120000000.00', 1, '', '', '', '112', 'img_1756181760_8bc07e81.jpg', NULL, NULL, 1, 'spo_1756187083_49f08caa.pdf', '', 'BTT', '0000-00-00', '', 'Ada', ''),
(12, 'RSCBBT-IP-001', 'Infusion Pump', 'Medis', '0', 'LF-700', 3, '2024-02-21', '24000000.00', 5, '', '', '', 'TEST123456', 'img_1756181791_e5bac9cb.jpg', NULL, NULL, 1, 'spo_1756187066_6463239a.pdf', '', 'BTT', '0000-00-00', '', 'Ada', '');

-- --------------------------------------------------------

--
-- Table structure for table `asisten`
--

CREATE TABLE `asisten` (
  `id_asisten` int(11) NOT NULL,
  `nama_asisten` varchar(100) NOT NULL,
  `nip_asisten` varchar(50) NOT NULL,
  `id_user` int(255) DEFAULT NULL,
  `nip` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `asisten`
--

INSERT INTO `asisten` (`id_asisten`, `nama_asisten`, `nip_asisten`, `id_user`, `nip`) VALUES
(1, 'Asisten 1', '111112234567', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `detail_pembelian_aset`
--

CREATE TABLE `detail_pembelian_aset` (
  `id_detail` int(11) NOT NULL,
  `id_pembelian` int(11) NOT NULL,
  `id_aset` int(11) NOT NULL,
  `nomor_seri` varchar(100) DEFAULT NULL,
  `id_ruangan` int(11) NOT NULL,
  `harga_perolehan` decimal(18,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `gedung`
--

CREATE TABLE `gedung` (
  `id_gedung` int(11) NOT NULL,
  `nama_gedung` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `gedung`
--

INSERT INTO `gedung` (`id_gedung`, `nama_gedung`, `keterangan`) VALUES
(1, 'Gedung D Lantai 1', ''),
(2, 'Gedung C Lantai 1', '');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_aset`
--

CREATE TABLE `jenis_aset` (
  `id_jenis_aset` int(11) NOT NULL,
  `nama_jenis_aset` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `jenis_aset`
--

INSERT INTO `jenis_aset` (`id_jenis_aset`, `nama_jenis_aset`) VALUES
(1, 'Medis'),
(2, 'Non Medis');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id_kegiatan` int(11) NOT NULL,
  `id_aset` int(11) DEFAULT NULL,
  `id_teknisi` int(11) DEFAULT NULL,
  `id_asisten` int(11) DEFAULT NULL,
  `jenis_kegiatan` enum('Perbaikan','Pemeliharaan') DEFAULT NULL,
  `uraian` text DEFAULT NULL,
  `tanggal_kegiatan` datetime DEFAULT NULL,
  `keluhan` varchar(255) DEFAULT NULL,
  `tindakan` varchar(255) DEFAULT NULL,
  `kesimpulan` varchar(255) DEFAULT NULL,
  `id_user_ruangan` varchar(255) DEFAULT NULL,
  `id_kepala_ipsrs` varchar(255) DEFAULT NULL,
  `waktu_respon` datetime(6) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `waktu_laporan` datetime(6) DEFAULT NULL,
  `nomor_lk` varchar(255) DEFAULT NULL,
  `tanda_tangan_teknisi` text DEFAULT NULL,
  `tanda_tangan_asisten` text DEFAULT NULL,
  `tanda_tangan_kepala_ruangan` text DEFAULT NULL,
  `tanda_tangan_kepala_instalasi` text DEFAULT NULL,
  `waktu_selesai` datetime(6) DEFAULT NULL,
  `biaya_perawatan` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id_kegiatan`, `id_aset`, `id_teknisi`, `id_asisten`, `jenis_kegiatan`, `uraian`, `tanggal_kegiatan`, `keluhan`, `tindakan`, `kesimpulan`, `id_user_ruangan`, `id_kepala_ipsrs`, `waktu_respon`, `image`, `waktu_laporan`, `nomor_lk`, `tanda_tangan_teknisi`, `tanda_tangan_asisten`, `tanda_tangan_kepala_ruangan`, `tanda_tangan_kepala_instalasi`, `waktu_selesai`, `biaya_perawatan`) VALUES
(4, 1, 1, 1, 'Perbaikan', NULL, NULL, 'tes', 'tes', 'tes', '1', NULL, '2025-07-28 23:52:00.000000', '6887aac38e940_2.jpg', '2025-07-28 23:52:00.000000', '1234', NULL, NULL, NULL, NULL, '2025-07-28 23:52:00.000000', NULL),
(5, 1, 1, 1, 'Perbaikan', NULL, NULL, 'tes', 'tes', 'tes', '1', NULL, '2025-07-29 00:10:00.000000', '6887af2658e52_foto.jpg', '2025-07-29 00:10:00.000000', '1234', NULL, NULL, NULL, NULL, '2025-07-29 00:10:00.000000', NULL),
(6, 11, 1, 1, 'Perbaikan', NULL, NULL, 'Keluh Kesah', 'Teke', 'Mantap', '1', NULL, '0000-00-00 00:00:00.000000', '', '0000-00-00 00:00:00.000000', '110', NULL, NULL, NULL, NULL, '0000-00-00 00:00:00.000000', 0),
(7, 11, 1, 1, 'Perbaikan', NULL, NULL, 'Nyeri Lambung', 'Patri', 'OK', '1', NULL, '2025-07-31 21:28:00.000000', 'kegiatan_688b7db8aecb1_Screenshot_2025-07-26_221126.png', '2025-07-31 15:28:00.000000', '008', NULL, NULL, NULL, NULL, '2025-07-31 21:28:00.000000', NULL),
(8, 11, 1, 1, 'Perbaikan', NULL, NULL, 'Alat Mati ', 'Perbaikan', 'Dapat Diselesaikan', '1', NULL, '0000-00-00 00:00:00.000000', '', '0000-00-00 00:00:00.000000', '009', 'tanda_tangan_teknisi_689e04a04a5b7.png', 'tanda_tangan_asisten_689e04a04a972.png', 'tanda_tangan_kepala_ruangan_689e04a04ac8e.png', 'tanda_tangan_kepala_instalasi_689e04a04b040.png', '0000-00-00 00:00:00.000000', 0);

-- --------------------------------------------------------

--
-- Table structure for table `kepala_ipsrs`
--

CREATE TABLE `kepala_ipsrs` (
  `id_kepala_ipsrs` varchar(255) DEFAULT NULL,
  `nama_kepala_ipsrs` varchar(255) DEFAULT NULL,
  `nip` varchar(255) DEFAULT NULL,
  `id_user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `mutasi_aset`
--

CREATE TABLE `mutasi_aset` (
  `id_mutasi` int(11) NOT NULL,
  `id_aset` int(11) NOT NULL,
  `dari_ruangan` int(11) NOT NULL,
  `ke_ruangan` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `tanggal_mutasi` date NOT NULL,
  `alasan_mutasi` text DEFAULT NULL,
  `id_user_mutasi` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `pembelian_aset`
--

CREATE TABLE `pembelian_aset` (
  `id_pembelian` int(11) NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `nomor_faktur` varchar(100) NOT NULL,
  `tanggal_pembelian` date NOT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `total_pembayaran` decimal(18,2) DEFAULT 0.00,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran_aset`
--

CREATE TABLE `pengeluaran_aset` (
  `id_pengeluaran` int(11) NOT NULL,
  `tanggal_pengeluaran` date NOT NULL,
  `tujuan_pengeluaran` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `pengeluaran_aset_detail`
--

CREATE TABLE `pengeluaran_aset_detail` (
  `id_detail` int(11) NOT NULL,
  `id_pengeluaran` int(11) NOT NULL,
  `id_aset` int(11) NOT NULL,
  `id_ruangan` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id_ruangan` int(11) NOT NULL,
  `id_gedung` int(11) NOT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `keterangan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`id_ruangan`, `id_gedung`, `nama_ruangan`, `keterangan`) VALUES
(1, 1, 'Gudang', ''),
(3, 1, 'Hemodialisa', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stok_aset`
--

CREATE TABLE `stok_aset` (
  `id_stok` int(11) NOT NULL,
  `id_aset` int(11) NOT NULL,
  `id_ruangan` int(11) NOT NULL,
  `jumlah` int(11) DEFAULT 0,
  `harga_satuan` decimal(15,2) DEFAULT 0.00,
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `alamat`, `kontak`, `email`, `keterangan`) VALUES
(1, 'PT.ABC', 'Bandung', '083827660087', 'a@gmail.com', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teknisi`
--

CREATE TABLE `teknisi` (
  `id_teknisi` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama_teknisi` varchar(100) DEFAULT NULL,
  `nip` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `teknisi`
--

INSERT INTO `teknisi` (`id_teknisi`, `id_user`, `nama_teknisi`, `nip`) VALUES
(1, 2, 'Ucup', '1234567');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teknisi','unit') DEFAULT 'unit',
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama_lengkap`, `username`, `password`, `role`, `status`) VALUES
(1, 'yudi hermawan', 'hermawan', '$2y$10$FsGFHuXb5DE0Uzyy0eB3VuUMdIEE6Fp.jfm/fZrtH4qEdd3WV4686', 'admin', 'aktif'),
(2, 'Ucup', 'ucup', '$2y$10$Nm56JwVuEvszsiaVrr61TOYjlGaEgk7650Rd3W8hraJT1c40ho2nO', 'teknisi', 'aktif');

-- --------------------------------------------------------

--
-- Table structure for table `user_ruangan`
--

CREATE TABLE `user_ruangan` (
  `id_user_ruangan` int(255) NOT NULL,
  `nama_user_ruangan` varchar(255) DEFAULT NULL,
  `nip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `user_ruangan`
--

INSERT INTO `user_ruangan` (`id_user_ruangan`, `nama_user_ruangan`, `nip`) VALUES
(1, 'User1', '1122334455'),
(2, 'user2', '123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aset`
--
ALTER TABLE `aset`
  ADD PRIMARY KEY (`id_aset`) USING BTREE,
  ADD UNIQUE KEY `kode_aset` (`kode_aset`) USING BTREE,
  ADD KEY `id_ruangan` (`id_ruangan`) USING BTREE;

--
-- Indexes for table `asisten`
--
ALTER TABLE `asisten`
  ADD PRIMARY KEY (`id_asisten`) USING BTREE,
  ADD UNIQUE KEY `nip_asisten` (`nip_asisten`) USING BTREE;

--
-- Indexes for table `detail_pembelian_aset`
--
ALTER TABLE `detail_pembelian_aset`
  ADD PRIMARY KEY (`id_detail`) USING BTREE,
  ADD KEY `fk_detail_pembelian` (`id_pembelian`) USING BTREE,
  ADD KEY `fk_detail_aset` (`id_aset`) USING BTREE,
  ADD KEY `fk_detail_ruangan` (`id_ruangan`) USING BTREE;

--
-- Indexes for table `gedung`
--
ALTER TABLE `gedung`
  ADD PRIMARY KEY (`id_gedung`) USING BTREE;

--
-- Indexes for table `jenis_aset`
--
ALTER TABLE `jenis_aset`
  ADD PRIMARY KEY (`id_jenis_aset`) USING BTREE,
  ADD UNIQUE KEY `nama_jenis` (`nama_jenis_aset`) USING BTREE;

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id_kegiatan`) USING BTREE,
  ADD KEY `id_aset` (`id_aset`) USING BTREE,
  ADD KEY `id_teknisi` (`id_teknisi`) USING BTREE,
  ADD KEY `id_asisten` (`id_asisten`) USING BTREE;

--
-- Indexes for table `mutasi_aset`
--
ALTER TABLE `mutasi_aset`
  ADD PRIMARY KEY (`id_mutasi`) USING BTREE,
  ADD KEY `fk_mutasi_aset` (`id_aset`) USING BTREE,
  ADD KEY `fk_mutasi_dari` (`dari_ruangan`) USING BTREE,
  ADD KEY `fk_mutasi_ke` (`ke_ruangan`) USING BTREE;

--
-- Indexes for table `pembelian_aset`
--
ALTER TABLE `pembelian_aset`
  ADD PRIMARY KEY (`id_pembelian`) USING BTREE,
  ADD KEY `fk_pembelian_supplier` (`id_supplier`) USING BTREE;

--
-- Indexes for table `pengeluaran_aset`
--
ALTER TABLE `pengeluaran_aset`
  ADD PRIMARY KEY (`id_pengeluaran`) USING BTREE;

--
-- Indexes for table `pengeluaran_aset_detail`
--
ALTER TABLE `pengeluaran_aset_detail`
  ADD PRIMARY KEY (`id_detail`) USING BTREE,
  ADD KEY `fk_pengeluaran_header` (`id_pengeluaran`) USING BTREE,
  ADD KEY `fk_pengeluaran_aset` (`id_aset`) USING BTREE,
  ADD KEY `fk_pengeluaran_ruangan` (`id_ruangan`) USING BTREE;

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id_ruangan`) USING BTREE,
  ADD KEY `id_gedung` (`id_gedung`) USING BTREE;

--
-- Indexes for table `stok_aset`
--
ALTER TABLE `stok_aset`
  ADD PRIMARY KEY (`id_stok`) USING BTREE,
  ADD UNIQUE KEY `unique_stok` (`id_aset`,`id_ruangan`) USING BTREE,
  ADD KEY `fk_stok_ruangan` (`id_ruangan`) USING BTREE;

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`) USING BTREE;

--
-- Indexes for table `teknisi`
--
ALTER TABLE `teknisi`
  ADD PRIMARY KEY (`id_teknisi`) USING BTREE,
  ADD KEY `id_user` (`id_user`) USING BTREE;

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`) USING BTREE,
  ADD UNIQUE KEY `username` (`username`) USING BTREE;

--
-- Indexes for table `user_ruangan`
--
ALTER TABLE `user_ruangan`
  ADD PRIMARY KEY (`id_user_ruangan`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aset`
--
ALTER TABLE `aset`
  MODIFY `id_aset` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `asisten`
--
ALTER TABLE `asisten`
  MODIFY `id_asisten` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `detail_pembelian_aset`
--
ALTER TABLE `detail_pembelian_aset`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gedung`
--
ALTER TABLE `gedung`
  MODIFY `id_gedung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jenis_aset`
--
ALTER TABLE `jenis_aset`
  MODIFY `id_jenis_aset` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mutasi_aset`
--
ALTER TABLE `mutasi_aset`
  MODIFY `id_mutasi` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembelian_aset`
--
ALTER TABLE `pembelian_aset`
  MODIFY `id_pembelian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengeluaran_aset`
--
ALTER TABLE `pengeluaran_aset`
  MODIFY `id_pengeluaran` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengeluaran_aset_detail`
--
ALTER TABLE `pengeluaran_aset_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id_ruangan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `stok_aset`
--
ALTER TABLE `stok_aset`
  MODIFY `id_stok` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `teknisi`
--
ALTER TABLE `teknisi`
  MODIFY `id_teknisi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_ruangan`
--
ALTER TABLE `user_ruangan`
  MODIFY `id_user_ruangan` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `aset`
--
ALTER TABLE `aset`
  ADD CONSTRAINT `aset_ibfk_1` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`);

--
-- Constraints for table `detail_pembelian_aset`
--
ALTER TABLE `detail_pembelian_aset`
  ADD CONSTRAINT `fk_detail_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id_aset`),
  ADD CONSTRAINT `fk_detail_pembelian` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian_aset` (`id_pembelian`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`);

--
-- Constraints for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD CONSTRAINT `kegiatan_ibfk_1` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id_aset`),
  ADD CONSTRAINT `kegiatan_ibfk_2` FOREIGN KEY (`id_teknisi`) REFERENCES `teknisi` (`id_teknisi`),
  ADD CONSTRAINT `kegiatan_ibfk_3` FOREIGN KEY (`id_asisten`) REFERENCES `asisten` (`id_asisten`);

--
-- Constraints for table `mutasi_aset`
--
ALTER TABLE `mutasi_aset`
  ADD CONSTRAINT `fk_mutasi_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id_aset`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mutasi_dari` FOREIGN KEY (`dari_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mutasi_ke` FOREIGN KEY (`ke_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE CASCADE;

--
-- Constraints for table `pembelian_aset`
--
ALTER TABLE `pembelian_aset`
  ADD CONSTRAINT `fk_pembelian_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`);

--
-- Constraints for table `pengeluaran_aset_detail`
--
ALTER TABLE `pengeluaran_aset_detail`
  ADD CONSTRAINT `fk_pengeluaran_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id_aset`),
  ADD CONSTRAINT `fk_pengeluaran_header` FOREIGN KEY (`id_pengeluaran`) REFERENCES `pengeluaran_aset` (`id_pengeluaran`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pengeluaran_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`);

--
-- Constraints for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD CONSTRAINT `ruangan_ibfk_1` FOREIGN KEY (`id_gedung`) REFERENCES `gedung` (`id_gedung`);

--
-- Constraints for table `stok_aset`
--
ALTER TABLE `stok_aset`
  ADD CONSTRAINT `fk_stok_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id_aset`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_stok_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE CASCADE;

--
-- Constraints for table `teknisi`
--
ALTER TABLE `teknisi`
  ADD CONSTRAINT `teknisi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
