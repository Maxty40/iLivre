-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2026 at 09:29 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `TambahBuku` (IN `p_judul` VARCHAR(255), IN `p_penulis` VARCHAR(100), IN `p_penerbit` VARCHAR(100), IN `p_stok` INT)   BEGIN
    INSERT INTO buku (judul, penulis, penerbit, stok)
    VALUES (p_judul, p_penulis, p_penerbit, p_stok);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `TambahBukuBaru` (IN `p_judul` VARCHAR(255), IN `p_penulis` VARCHAR(100), IN `p_penerbit` VARCHAR(100), IN `p_stok` INT)   BEGIN
    INSERT INTO buku (judul, penulis, penerbit, stok)
    VALUES (p_judul, p_penulis, p_penerbit, p_stok);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `penulis` varchar(100) NOT NULL,
  `penerbit` varchar(100) DEFAULT NULL,
  `stok` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `judul`, `penulis`, `penerbit`, `stok`) VALUES
(1, 'Belajar Basis Data Modern', 'Dr. Indrajit', 'Informatika', 5),
(2, 'Pemrograman Web dengan Laravel', 'Eko Khannedy', 'TechMedia', 3),
(3, 'Logika & Algoritma', 'Rinaldi Munir', 'SainsPress', 4),
(4, 'Sistem Operasi Dasar', 'Tanenbaum', 'Erlangga', -2),
(5, 'Jaringan Komputer', 'Forouzan', 'McGrawHill', 9),
(6, 'Pengantar Cybersecurity', 'Andi Utama', 'CyberSec', 3),
(7, 'Sistem Informasi Geografis', 'Randi', 'Erlangga', 5),
(8, 'Bahasa Inggris', 'Alena', 'Arunika', 4),
(9, 'Laut Bercerita', 'Leila S. Chudori', 'Gramedia', 5),
(10, 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 3),
(11, 'Bumi Manusia', 'Pramoedya  A. Toer', 'Hasta Mitra', 6),
(12, 'Ayat Ayat Cinta', 'Habiburrahman El Shirazy', 'Republika Penerbit', 4),
(13, 'Filosofi Kopi', 'Dewi Lestari', 'Bentang Pustaka', 4),
(14, 'Charlie and the Chocolate Factory', 'Roald Dahl', 'Puffin Book', 5),
(15, 'Peter Pan', 'J.M. Barrie', 'Hodder & Stoughton', 9),
(16, 'La Tahzan', 'Dr. Aidh al-Qarni', 'Qisthi Press', 4),
(17, 'Matematika Diskrit', 'Dicky Susanto', 'Kemendikbudristek', 9),
(18, 'Statistika untuk Penelitian', 'Prof. Dr. Sugiyono', 'Alfabeta', 2),
(19, 'Geometri Analitik', 'M. Cholik Adinawan', 'Erlangga', 7),
(20, 'Aljabar Linear', 'Howard Anton', 'Erlangga', 4);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6', 'i:1;', 1780370984),
('laravel-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6:timer', 'i:1780370984;', 1780370984);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kartu_anggota`
--

CREATE TABLE `kartu_anggota` (
  `id_kartu` int(11) NOT NULL,
  `nomor_kartu` varchar(20) NOT NULL,
  `tanggal_pembuatan` date NOT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kartu_anggota`
--

INSERT INTO `kartu_anggota` (`id_kartu`, `nomor_kartu`, `tanggal_pembuatan`, `id_user`) VALUES
(1, 'KRT-001', '2026-01-10', 3),
(2, 'KRT-002', '2026-01-12', 4),
(3, 'KRT-003', '2026-01-15', 5),
(4, 'KRT-004', '2026-01-20', 6),
(5, 'KRT-005', '2026-01-25', 7),
(6, 'KRT-006', '2026-01-31', 8),
(7, 'KRT-007', '2026-02-03', 9),
(8, 'KRT-008', '2026-02-07', 10),
(9, 'KRT-009', '2026-02-10', 11),
(10, 'KRT-010', '2026-02-13', 12),
(11, 'KRT-011', '2026-02-15', 13),
(12, 'KRT-012', '2026-02-17', 14),
(13, 'KRT-013', '2026-02-19', 15),
(14, 'KRT-014', '2026-02-21', 16),
(15, 'KRT-015', '2026-02-23', 17),
(16, 'KRT-016', '2026-02-25', 18),
(17, 'KRT-017', '0000-00-00', 19),
(18, 'KRT-018', '0000-00-00', 20);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_buku` int(11) DEFAULT NULL,
  `tanggal_pinjam` date NOT NULL,
  `tanggal_kembali_seharusnya` date NOT NULL,
  `status` enum('Dipinjam','Kembali') DEFAULT 'Dipinjam',
  `jumlah` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_user`, `id_buku`, `tanggal_pinjam`, `tanggal_kembali_seharusnya`, `status`, `jumlah`) VALUES
(1, 3, 1, '2026-05-10', '2026-05-17', 'Kembali', 1),
(2, 4, 2, '2026-05-12', '2026-05-19', 'Kembali', 1),
(3, 5, 3, '2026-05-15', '2026-05-22', 'Dipinjam', 1),
(4, 6, 5, '2026-05-20', '2026-05-27', 'Kembali', 1),
(5, 3, 2, '2026-05-22', '2026-05-29', 'Kembali', 1),
(6, 3, 1, '2026-05-28', '2026-06-04', 'Kembali', 1),
(7, 6, 8, '2026-05-28', '2026-06-04', 'Dipinjam', 1),
(8, 3, 2, '2026-06-02', '2026-06-09', 'Dipinjam', 1),
(9, 3, 4, '2026-06-02', '2026-06-09', 'Dipinjam', 2);

--
-- Triggers `peminjaman`
--
DELIMITER $$
CREATE TRIGGER `KurangStokSetelahPinjam` AFTER INSERT ON `peminjaman` FOR EACH ROW BEGIN
    UPDATE buku 
    SET stok = stok - 1 
    WHERE id_buku = NEW.id_buku;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `Trg_KurangStokPinjam` AFTER INSERT ON `peminjaman` FOR EACH ROW BEGIN
    UPDATE buku 
    SET stok = stok - 1 
    WHERE id_buku = NEW.id_buku;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int(11) NOT NULL,
  `id_peminjaman` int(11) DEFAULT NULL,
  `tanggal_aktual_kembali` date NOT NULL,
  `denda` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_peminjaman`, `tanggal_aktual_kembali`, `denda`) VALUES
(1, 1, '2026-05-16', 0.00),
(2, 2, '2026-05-21', 10000.00),
(3, 5, '2026-05-28', 0.00),
(4, 4, '2026-05-28', 0.00),
(5, 6, '2026-05-28', 0.00);

--
-- Triggers `pengembalian`
--
DELIMITER $$
CREATE TRIGGER `TambahStokSetelahKembali` AFTER INSERT ON `pengembalian` FOR EACH ROW BEGIN
    -- Update status di tabel peminjaman
    UPDATE peminjaman 
    SET status = 'Kembali' 
    WHERE id_peminjaman = NEW.id_peminjaman;

    -- Kembalikan stok buku
    UPDATE buku 
    SET stok = stok + 1 
    WHERE id_buku = (SELECT id_buku FROM peminjaman WHERE id_peminjaman = NEW.id_peminjaman);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `Trg_KembalikanStokBuku` AFTER INSERT ON `pengembalian` FOR EACH ROW BEGIN
    -- 1. Otomatis ubah status di tabel peminjaman jadi 'Kembali'
    UPDATE peminjaman 
    SET status = 'Kembali' 
    WHERE id_peminjaman = NEW.id_peminjaman;

    -- 2. Otomatis kembalikan jumlah stok buku (+1)
    UPDATE buku 
    SET stok = stok + 1 
    WHERE id_buku = (SELECT id_buku FROM peminjaman WHERE id_peminjaman = NEW.id_peminjaman);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `nama_role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id_role`, `nama_role`) VALUES
(1, 'Admin'),
(2, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('lxk12dVqVaDeMlW2gNeiMSFb9jnlQ1jHdeJHX4Hv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.122.1 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVjAza1dyWWpEWXhadzhCS0lyTFNyeEhXZFRiVmRxMUpvMmFaT05vayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1780370372),
('yadEFXYmZ3TbHt0JmMWqDFzx39OKxjxXaXJo1Ap6', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoieDJFZ1dGT0ZlanBaSk9vYkx6WUFzNGdXM0NXVm1Ud3VkTHE3SnhubCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9fQ==', 1780371481);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `id_role` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `password`, `id_role`) VALUES
(1, 'Klaudia Weda', 'klaudia@email.com', '$2y$12$LjAM0lBFPJxyevOHDM39yOOKJsky8v6SiauPbXoc7P0Z3JWHu34De', 1),
(2, 'Yudo Prasetio', 'yudo@email.com', '$2y$12$LjAM0lBFPJxyevOHDM39yOOKJsky8v6SiauPbXoc7P0Z3JWHu34De', 1),
(3, 'Ahmad Fauzi', 'ahmad@email.com', '$2y$12$LjAM0lBFPJxyevOHDM39yOOKJsky8v6SiauPbXoc7P0Z3JWHu34De', 2),
(4, 'Siti Aminah', 'siti@email.com', '$2y$12$LjAM0lBFPJxyevOHDM39yOOKJsky8v6SiauPbXoc7P0Z3JWHu34De', 2),
(5, 'Budi Santoso', 'budi@email.com', '$2y$12$LjAM0lBFPJxyevOHDM39yOOKJsky8v6SiauPbXoc7P0Z3JWHu34De', 2),
(6, 'Citra Dewi', 'citra@email.com', '$2y$12$LjAM0lBFPJxyevOHDM39yOOKJsky8v6SiauPbXoc7P0Z3JWHu34De', 2),
(7, 'Dhiauddin Arfa', 'dhia@email.com', 'password', 2),
(8, 'Ulwan Luthfi', 'ulwan@email.com', 'password', 2),
(9, 'Ahmad Afifi', 'afifi@email.com', 'password', 2),
(10, 'Freeze Ad Kaban', 'freeze@email.com', 'password', 2),
(11, 'Budiyono Siregar', 'budiyono@email.com', 'password', 2),
(12, 'Bilqis Salsabila', 'bilqis@email.com', 'password', 2),
(13, 'Bunga Lestari', 'bunga@email.com', 'password', 2),
(14, 'Amy Adinanta', 'amy@email.com', 'password', 2),
(15, 'Xavier Wijoyo', 'xavier@email.com', 'password', 2),
(16, 'Jessica Sudarsono', 'jessica@email.com', 'password', 2),
(17, 'Michael Alviono', 'michael@email.com', 'password', 2),
(18, 'Asep Surasep', 'asep@email.com', 'password', 2),
(19, 'Surti Sumiati', 'surti@email.com', 'password', 2),
(20, 'Munaroh', 'munaroh@email.com', 'password', 2);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_peminjaman_aktif`
-- (See below for the actual view)
--
CREATE TABLE `v_peminjaman_aktif` (
`id_peminjaman` int(11)
,`nama_peminjam` varchar(100)
,`judul_buku` varchar(255)
,`tanggal_pinjam` date
,`tanggal_kembali_seharusnya` date
);

-- --------------------------------------------------------

--
-- Structure for view `v_peminjaman_aktif`
--
DROP TABLE IF EXISTS `v_peminjaman_aktif`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_peminjaman_aktif`  AS SELECT `p`.`id_peminjaman` AS `id_peminjaman`, `u`.`nama` AS `nama_peminjam`, `b`.`judul` AS `judul_buku`, `p`.`tanggal_pinjam` AS `tanggal_pinjam`, `p`.`tanggal_kembali_seharusnya` AS `tanggal_kembali_seharusnya` FROM ((`peminjaman` `p` join `user` `u` on(`p`.`id_user` = `u`.`id_user`)) join `buku` `b` on(`p`.`id_buku` = `b`.`id_buku`)) WHERE `p`.`status` = 'Dipinjam' ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`),
  ADD KEY `idx_buku_judul` (`judul`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kartu_anggota`
--
ALTER TABLE `kartu_anggota`
  ADD PRIMARY KEY (`id_kartu`),
  ADD UNIQUE KEY `nomor_kartu` (`nomor_kartu`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_buku` (`id_buku`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD UNIQUE KEY `id_peminjaman` (`id_peminjaman`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_role` (`id_role`),
  ADD KEY `idx_user_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buku`
--
ALTER TABLE `buku`
  MODIFY `id_buku` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kartu_anggota`
--
ALTER TABLE `kartu_anggota`
  MODIFY `id_kartu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kartu_anggota`
--
ALTER TABLE `kartu_anggota`
  ADD CONSTRAINT `kartu_anggota_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`);

--
-- Constraints for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
