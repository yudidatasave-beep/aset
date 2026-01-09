/*
 Navicat Premium Data Transfer

 Source Server         : Server PDF
 Source Server Type    : MySQL
 Source Server Version : 100425
 Source Host           : 192.168.100.4:3306
 Source Schema         : manajemen_aset

 Target Server Type    : MySQL
 Target Server Version : 100425
 File Encoding         : 65001

 Date: 08/01/2026 11:50:10
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for aset
-- ----------------------------
DROP TABLE IF EXISTS `aset`;
CREATE TABLE `aset`  (
  `id_aset` int(11) NOT NULL AUTO_INCREMENT,
  `kode_aset` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_aset` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_aset` enum('Medis','Non Medis') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `merek` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tipe` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id_ruangan` int(11) NOT NULL,
  `tanggal_perolehan` date NOT NULL,
  `nilai_perolehan` decimal(15, 2) NOT NULL,
  `umur_ekonomis` tinyint(4) NOT NULL COMMENT 'Dalam tahun',
  `kondisi_alat` enum('aktif','tidak aktif','rusak','diperbaiki') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'aktif',
  `kepemilikan` enum('RSUD Cibabat','KSO') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `status_penyusutan` enum('belum dihitung','dihitung') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'belum dihitung',
  `nomor_seri` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `stok` decimal(30, 0) NULL DEFAULT NULL,
  `jumlah_nilai_perolehan` decimal(30, 0) NULL DEFAULT NULL,
  `id_jenis_aset` int(11) NULL DEFAULT NULL,
  `spo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `model` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `jenis_anggaran` enum('BTT','BLUD') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `kalibrasi` date NULL DEFAULT NULL,
  `perusahaan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `kartu_kuning` enum('Ada','Tidak Ada') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `akl` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_aset`) USING BTREE,
  UNIQUE INDEX `kode_aset`(`kode_aset`) USING BTREE,
  INDEX `id_ruangan`(`id_ruangan`) USING BTREE,
  CONSTRAINT `aset_ibfk_1` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for asisten
-- ----------------------------
DROP TABLE IF EXISTS `asisten`;
CREATE TABLE `asisten`  (
  `id_asisten` int(11) NOT NULL AUTO_INCREMENT,
  `nama_asisten` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nip_asisten` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_user` int(255) NULL DEFAULT NULL,
  `nip` int(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id_asisten`) USING BTREE,
  UNIQUE INDEX `nip_asisten`(`nip_asisten`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of asisten
-- ----------------------------
INSERT INTO `asisten` VALUES (1, 'Asisten 1', '111112234567', NULL, NULL);

-- ----------------------------
-- Table structure for detail_pembelian_aset
-- ----------------------------
DROP TABLE IF EXISTS `detail_pembelian_aset`;
CREATE TABLE `detail_pembelian_aset`  (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_pembelian` int(11) NOT NULL,
  `id_aset` int(11) NOT NULL,
  `nomor_seri` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id_ruangan` int(11) NOT NULL,
  `harga_perolehan` decimal(18, 2) NOT NULL,
  PRIMARY KEY (`id_detail`) USING BTREE,
  INDEX `fk_detail_pembelian`(`id_pembelian`) USING BTREE,
  INDEX `fk_detail_aset`(`id_aset`) USING BTREE,
  INDEX `fk_detail_ruangan`(`id_ruangan`) USING BTREE,
  CONSTRAINT `fk_detail_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id_aset`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_detail_pembelian` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian_aset` (`id_pembelian`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_detail_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gedung
-- ----------------------------
DROP TABLE IF EXISTS `gedung`;
CREATE TABLE `gedung`  (
  `id_gedung` int(11) NOT NULL AUTO_INCREMENT,
  `nama_gedung` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id_gedung`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of gedung
-- ----------------------------
INSERT INTO `gedung` VALUES (1, 'Gedung D Lantai 1', '');
INSERT INTO `gedung` VALUES (2, 'Gedung C Lantai 1', '');

-- ----------------------------
-- Table structure for jenis_aset
-- ----------------------------
DROP TABLE IF EXISTS `jenis_aset`;
CREATE TABLE `jenis_aset`  (
  `id_jenis_aset` int(11) NOT NULL AUTO_INCREMENT,
  `nama_jenis_aset` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_jenis_aset`) USING BTREE,
  UNIQUE INDEX `nama_jenis`(`nama_jenis_aset`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of jenis_aset
-- ----------------------------
INSERT INTO `jenis_aset` VALUES (1, 'Medis');
INSERT INTO `jenis_aset` VALUES (2, 'Non Medis');

-- ----------------------------
-- Table structure for kegiatan
-- ----------------------------
DROP TABLE IF EXISTS `kegiatan`;
CREATE TABLE `kegiatan`  (
  `id_kegiatan` int(11) NOT NULL AUTO_INCREMENT,
  `id_aset` int(11) NULL DEFAULT NULL,
  `id_teknisi` int(11) NULL DEFAULT NULL,
  `id_asisten` int(11) NULL DEFAULT NULL,
  `jenis_kegiatan` enum('Perbaikan','Pemeliharaan') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `uraian` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `tanggal_kegiatan` datetime(0) NULL DEFAULT NULL,
  `keluhan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tindakan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `kesimpulan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id_user_ruangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id_kepala_ipsrs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `waktu_respon` datetime(6) NULL DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `waktu_laporan` datetime(6) NULL DEFAULT NULL,
  `nomor_lk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `tanda_tangan_teknisi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `tanda_tangan_asisten` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `tanda_tangan_kepala_ruangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `tanda_tangan_kepala_instalasi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `waktu_selesai` datetime(6) NULL DEFAULT NULL,
  `biaya_perawatan` int(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id_kegiatan`) USING BTREE,
  INDEX `id_aset`(`id_aset`) USING BTREE,
  INDEX `id_teknisi`(`id_teknisi`) USING BTREE,
  INDEX `id_asisten`(`id_asisten`) USING BTREE,
  CONSTRAINT `kegiatan_ibfk_1` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id_aset`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `kegiatan_ibfk_2` FOREIGN KEY (`id_teknisi`) REFERENCES `teknisi` (`id_teknisi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `kegiatan_ibfk_3` FOREIGN KEY (`id_asisten`) REFERENCES `asisten` (`id_asisten`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for kepala_ipsrs
-- ----------------------------
DROP TABLE IF EXISTS `kepala_ipsrs`;
CREATE TABLE `kepala_ipsrs`  (
  `id_kepala_ipsrs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nama_kepala_ipsrs` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `id_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mutasi_aset
-- ----------------------------
DROP TABLE IF EXISTS `mutasi_aset`;
CREATE TABLE `mutasi_aset`  (
  `id_mutasi` int(11) NOT NULL AUTO_INCREMENT,
  `id_aset` int(11) NOT NULL,
  `dari_ruangan` int(11) NOT NULL,
  `ke_ruangan` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `tanggal_mutasi` date NOT NULL,
  `alasan_mutasi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `id_user_mutasi` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id_mutasi`) USING BTREE,
  INDEX `fk_mutasi_aset`(`id_aset`) USING BTREE,
  INDEX `fk_mutasi_dari`(`dari_ruangan`) USING BTREE,
  INDEX `fk_mutasi_ke`(`ke_ruangan`) USING BTREE,
  CONSTRAINT `fk_mutasi_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id_aset`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_mutasi_dari` FOREIGN KEY (`dari_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_mutasi_ke` FOREIGN KEY (`ke_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pembelian_aset
-- ----------------------------
DROP TABLE IF EXISTS `pembelian_aset`;
CREATE TABLE `pembelian_aset`  (
  `id_pembelian` int(11) NOT NULL AUTO_INCREMENT,
  `id_supplier` int(11) NOT NULL,
  `nomor_faktur` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_pembelian` date NOT NULL,
  `tanggal_jatuh_tempo` date NULL DEFAULT NULL,
  `total_pembayaran` decimal(18, 2) NULL DEFAULT 0,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id_pembelian`) USING BTREE,
  INDEX `fk_pembelian_supplier`(`id_supplier`) USING BTREE,
  CONSTRAINT `fk_pembelian_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pengeluaran_aset
-- ----------------------------
DROP TABLE IF EXISTS `pengeluaran_aset`;
CREATE TABLE `pengeluaran_aset`  (
  `id_pengeluaran` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal_pengeluaran` date NOT NULL,
  `tujuan_pengeluaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` datetime(0) NULL DEFAULT current_timestamp(0),
  PRIMARY KEY (`id_pengeluaran`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for pengeluaran_aset_detail
-- ----------------------------
DROP TABLE IF EXISTS `pengeluaran_aset_detail`;
CREATE TABLE `pengeluaran_aset_detail`  (
  `id_detail` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengeluaran` int(11) NOT NULL,
  `id_aset` int(11) NOT NULL,
  `id_ruangan` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  PRIMARY KEY (`id_detail`) USING BTREE,
  INDEX `fk_pengeluaran_header`(`id_pengeluaran`) USING BTREE,
  INDEX `fk_pengeluaran_aset`(`id_aset`) USING BTREE,
  INDEX `fk_pengeluaran_ruangan`(`id_ruangan`) USING BTREE,
  CONSTRAINT `fk_pengeluaran_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id_aset`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_pengeluaran_header` FOREIGN KEY (`id_pengeluaran`) REFERENCES `pengeluaran_aset` (`id_pengeluaran`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_pengeluaran_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ruangan
-- ----------------------------
DROP TABLE IF EXISTS `ruangan`;
CREATE TABLE `ruangan`  (
  `id_ruangan` int(11) NOT NULL AUTO_INCREMENT,
  `id_gedung` int(11) NOT NULL,
  `nama_ruangan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_ruangan`) USING BTREE,
  INDEX `id_gedung`(`id_gedung`) USING BTREE,
  CONSTRAINT `ruangan_ibfk_1` FOREIGN KEY (`id_gedung`) REFERENCES `gedung` (`id_gedung`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ruangan
-- ----------------------------
INSERT INTO `ruangan` VALUES (1, 1, 'Gudang', '');
INSERT INTO `ruangan` VALUES (3, 1, 'Hemodialisa', NULL);

-- ----------------------------
-- Table structure for stok_aset
-- ----------------------------
DROP TABLE IF EXISTS `stok_aset`;
CREATE TABLE `stok_aset`  (
  `id_stok` int(11) NOT NULL AUTO_INCREMENT,
  `id_aset` int(11) NOT NULL,
  `id_ruangan` int(11) NOT NULL,
  `jumlah` int(11) NULL DEFAULT 0,
  `harga_satuan` decimal(15, 2) NULL DEFAULT 0,
  `last_updated` datetime(0) NULL DEFAULT current_timestamp(0) ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`id_stok`) USING BTREE,
  UNIQUE INDEX `unique_stok`(`id_aset`, `id_ruangan`) USING BTREE,
  INDEX `fk_stok_ruangan`(`id_ruangan`) USING BTREE,
  CONSTRAINT `fk_stok_aset` FOREIGN KEY (`id_aset`) REFERENCES `aset` (`id_aset`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `fk_stok_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for supplier
-- ----------------------------
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier`  (
  `id_supplier` int(11) NOT NULL AUTO_INCREMENT,
  `nama_supplier` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `kontak` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  PRIMARY KEY (`id_supplier`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of supplier
-- ----------------------------
INSERT INTO `supplier` VALUES (1, 'PT.ABC', 'Bandung', '083827660087', 'a@gmail.com', NULL);

-- ----------------------------
-- Table structure for teknisi
-- ----------------------------
DROP TABLE IF EXISTS `teknisi`;
CREATE TABLE `teknisi`  (
  `id_teknisi` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NULL DEFAULT NULL,
  `nama_teknisi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nip` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_teknisi`) USING BTREE,
  INDEX `id_user`(`id_user`) USING BTREE,
  CONSTRAINT `teknisi_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of teknisi
-- ----------------------------
INSERT INTO `teknisi` VALUES (1, 2, 'Ucup', '1234567');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','teknisi','unit') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'unit',
  `status` enum('aktif','nonaktif') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'aktif',
  PRIMARY KEY (`id_user`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'yudi hermawan', 'hermawan', '$2y$10$FsGFHuXb5DE0Uzyy0eB3VuUMdIEE6Fp.jfm/fZrtH4qEdd3WV4686', 'admin', 'aktif');
INSERT INTO `user` VALUES (2, 'Ucup', 'ucup', '$2y$10$Nm56JwVuEvszsiaVrr61TOYjlGaEgk7650Rd3W8hraJT1c40ho2nO', 'teknisi', 'aktif');

-- ----------------------------
-- Table structure for user_ruangan
-- ----------------------------
DROP TABLE IF EXISTS `user_ruangan`;
CREATE TABLE `user_ruangan`  (
  `id_user_ruangan` int(255) NOT NULL AUTO_INCREMENT,
  `nama_user_ruangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `nip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_user_ruangan`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user_ruangan
-- ----------------------------
INSERT INTO `user_ruangan` VALUES (1, 'User1', '1122334455');
INSERT INTO `user_ruangan` VALUES (2, 'user2', '123');

SET FOREIGN_KEY_CHECKS = 1;
