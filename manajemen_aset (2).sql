-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2026 at 05:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

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
  `akl` varchar(255) DEFAULT NULL,
  `nomor_kontrak` varchar(255) DEFAULT NULL,
  `id_merek_aset` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `aset`
--

INSERT INTO `aset` (`id_aset`, `kode_aset`, `nama_aset`, `merek`, `tipe`, `id_ruangan`, `tanggal_perolehan`, `nilai_perolehan`, `umur_ekonomis`, `kondisi_alat`, `kepemilikan`, `status_penyusutan`, `nomor_seri`, `image`, `stok`, `jumlah_nilai_perolehan`, `id_jenis_aset`, `spo`, `model`, `jenis_anggaran`, `kalibrasi`, `perusahaan`, `kartu_kuning`, `akl`, `nomor_kontrak`, `id_merek_aset`) VALUES
(19, '004', 'tes', NULL, '123', 1, '2026-01-09', 0.00, 0, 'aktif', NULL, 'belum dihitung', '123456', '1767931008_69607c80be315.png', NULL, NULL, 5, NULL, '123', NULL, NULL, NULL, NULL, NULL, '123333', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `gedung`
--

CREATE TABLE `gedung` (
  `id_gedung` int(11) NOT NULL,
  `nama_gedung` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

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
  `nama_jenis_aset` varchar(100) NOT NULL,
  `kode_jenis_aset` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `jenis_aset`
--

INSERT INTO `jenis_aset` (`id_jenis_aset`, `nama_jenis_aset`, `kode_jenis_aset`) VALUES
(1, 'Switch Unmanage', 'SWU'),
(2, 'Switch Manage', 'SWM'),
(3, 'Rack Wallmount 8U', 'RW8U'),
(4, 'UPS Server 10K 230V', 'UPSS10K'),
(5, 'Kamera Vicon', 'CMVC'),
(6, 'Sound Card 2in 2out', 'SNC2X2'),
(7, 'Speaker Vicon', 'SPVC'),
(8, 'Mikrofon Kondenser Studio', 'MKKD'),
(9, 'Stand Mikrofon', 'STMK'),
(10, 'Mixer 4 Channel', 'MX4C'),
(11, 'LAN Tester Multifunction', 'LANTSML'),
(12, 'Router Wireless', 'RTWR'),
(13, 'SFP 1.25G 850nm 550m', 'SFP1GS'),
(14, 'Server Rack', 'SVR'),
(15, 'SFP 1.25G 20KM TX-1550nm', 'SFP1G20KM'),
(16, 'Optical Splicer', 'OSPL');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `kepala_ipsrs`
--

CREATE TABLE `kepala_ipsrs` (
  `id_kepala_ipsrs` varchar(255) DEFAULT NULL,
  `nama_kepala_ipsrs` varchar(255) DEFAULT NULL,
  `nip` varchar(255) DEFAULT NULL,
  `id_user` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `merek_aset`
--

CREATE TABLE `merek_aset` (
  `id_merek_aset` int(10) NOT NULL,
  `merek_aset` varchar(255) DEFAULT NULL,
  `kode_merek` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `merek_aset`
--

INSERT INTO `merek_aset` (`id_merek_aset`, `merek_aset`, `kode_merek`) VALUES
(1, 'TP-Link', 'TP'),
(2, 'D-Link', 'DL'),
(3, 'Indorak', 'IDR'),
(4, 'APC', 'APC'),
(5, 'Logitech', 'LOG'),
(6, 'Scarlett', 'SCR'),
(7, 'Tenveo', 'TNV'),
(8, 'Taffware', 'TFW');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `id_ruangan` int(11) NOT NULL,
  `id_gedung` int(11) NOT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `keterangan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

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
-- Indexes for table `merek_aset`
--
ALTER TABLE `merek_aset`
  ADD PRIMARY KEY (`id_merek_aset`);

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
  MODIFY `id_aset` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
  MODIFY `id_jenis_aset` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `merek_aset`
--
ALTER TABLE `merek_aset`
  MODIFY `id_merek_aset` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
