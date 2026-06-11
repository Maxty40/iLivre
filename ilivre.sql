-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 10, 2026 at 11:31 AM
-- Server version: 8.4.3
-- PHP Version: 8.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ilivre`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`Dhia`@`localhost` PROCEDURE `sp_add_book_stock` (IN `p_book_id` INT, IN `p_added_qty` INT)   BEGIN
	UPDATE books
    SET stock = stock + p_added_qty
    WHERE id = p_book_id;
END$$

CREATE DEFINER=`Dhia`@`localhost` PROCEDURE `sp_add_new_book` (IN `p_title` VARCHAR(255), IN `p_author` VARCHAR(255), IN `p_publisher` VARCHAR(255), IN `p_stock` INT)   BEGIN
	INSERT INTO books (title, author, publisher, stock)
    VALUES (p_title, p_author, p_publisher, p_stock);
END$$

CREATE DEFINER=`Dhia`@`localhost` PROCEDURE `sp_create_loan` (IN `p_user_id` INT, IN `p_book_id` INT)   BEGIN
	DECLARE current_stock INT;
    SELECT stock INTO current_stock FROM books WHERE id = p_book_id;
    IF current_stock > 0 THEN
		INSERT INTO loans (user_id, book_id, loan_date, due_date)
        VALUES (p_user_id, p_book_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY));
	ELSE
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Gagal meminjam: Stok buku sedang kosong';
	END IF;
END$$

CREATE DEFINER=`Dhia`@`localhost` PROCEDURE `sp_delete_book` (IN `p_book_id` INT)   BEGIN 
	DECLARE active_loans INT;
    
    SELECT COUNT(l.id) INTO active_loans
    FROM loans l
    LEFT JOIN returns r ON l.id = r.loan_id
    WHERE l.book = p_book_id AND r.id IS NULL;
    
    IF active_loans > 0 THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Gagal menghapus: Buku masih dipinjam oleh anggota';
	ELSE
		DELETE FROM books
        WHERE id = p_book_id;
	END IF;
END$$

CREATE DEFINER=`Dhia`@`localhost` PROCEDURE `sp_get_user_total_fines` (IN `p_user_id` INT, OUT `p_total_fines` INT)   BEGIN
    SELECT SUM(r.fine) INTO p_total_fines
    FROM returns r
    JOIN loans l ON r.loan_id = l.id
    WHERE l.user_id = p_user_id;

    IF p_total_fines IS NULL THEN
        SET p_total_fines = 0;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `publisher`, `stock`, `created_at`, `updated_at`) VALUES
(1, 'Belajar Basis Data Modern', 'Dr. Indrajit', 'Informatika', 4, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(2, 'Pemrograman Web dengan Laravel', 'Eko Khannedy', 'TechMedia', 6, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(3, 'Logika & Algoritma', 'Rinaldi Munir', 'SainsPress', 4, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(4, 'Sistem Operasi Dasar', 'Tanenbaum', 'Erlangga', 2, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(5, 'Jaringan Komputer', 'Forouzan', 'McGrawHill', 8, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(6, 'Pengantar Cybersecurity', 'Andi Utama', 'CyberSec', 3, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(7, 'Sistem Informasi Geografis', 'Randi', 'Erlangga', 5, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(8, 'Bahasa Inggris', 'Alena', 'Arunika', 4, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(9, 'Laut Bercerita', 'Leila S. Chudori', 'Gramedia', 5, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(10, 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 3, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(11, 'Bumi Manusia', 'Pramoedya A. Toer', 'Hasta Mitra', 6, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(12, 'Ayat Ayat Cinta', 'Habiburrahman El Shirazy', 'Republika Penerbit', 4, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(13, 'Filosofi Kopi', 'Dewi Lestari', 'Bentang Pustaka', 4, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(14, 'Charlie and the Chocolate Factory', 'Roald Dahl', 'Puffin Book', 5, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(15, 'Peter Pan', 'J.M. Barrie', 'Hodder & Stoughton', 9, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(16, 'La Tahzan', 'Dr. Aidh al-Qarni', 'Qisthi Press', 4, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(17, 'Matematika Diskrit', 'Dicky Susanto', 'Kemendikbudristek', 8, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(18, 'Statistika untuk Penelitian', 'Prof. Dr. Sugiyono', 'Alfabeta', 1, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(19, 'Geometri Analitik', 'M. Cholik Adinawan', 'Erlangga', 6, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(20, 'Aljabar Linear', 'Howard Anton', 'Erlangga', 3, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(21, 'Ayam Ngantuk', 'Gibran Raka', 'SPPG Matraman', 7, NULL, NULL);

--
-- Triggers `books`
--
DELIMITER $$
CREATE TRIGGER `trg_before_update_book` BEFORE UPDATE ON `books` FOR EACH ROW BEGIN
	IF NEW.stock < 0 THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Gagal: Stok buku tidak boleh bernilai negatif';
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `book_id` bigint UNSIGNED NOT NULL,
  `loan_date` date NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('borrowed','returned') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'borrowed',
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `user_id`, `book_id`, `loan_date`, `due_date`, `status`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 4, 2, '2026-05-17', '2026-05-24', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(2, 5, 3, '2026-05-18', '2026-05-25', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(3, 6, 4, '2026-05-19', '2026-05-26', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(4, 7, 5, '2026-05-20', '2026-05-27', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(5, 8, 6, '2026-05-21', '2026-05-28', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(6, 9, 7, '2026-05-22', '2026-05-29', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(7, 10, 8, '2026-05-23', '2026-05-30', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(8, 11, 9, '2026-05-24', '2026-05-31', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(9, 12, 10, '2026-05-25', '2026-06-01', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(10, 13, 11, '2026-05-26', '2026-06-02', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(11, 14, 12, '2026-05-27', '2026-06-03', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(12, 15, 13, '2026-05-28', '2026-06-04', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(13, 16, 14, '2026-05-29', '2026-06-05', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(14, 17, 15, '2026-05-30', '2026-06-06', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(15, 18, 16, '2026-05-31', '2026-06-07', 'returned', 1, '2026-06-04 20:57:44', '2026-06-05 03:57:44'),
(16, 19, 17, '2026-06-01', '2026-06-08', 'borrowed', 1, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(17, 20, 18, '2026-06-02', '2026-06-09', 'borrowed', 1, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(18, 3, 19, '2026-06-03', '2026-06-10', 'borrowed', 1, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(19, 4, 20, '2026-06-04', '2026-06-11', 'borrowed', 1, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(20, 5, 1, '2026-06-05', '2026-06-12', 'borrowed', 1, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(21, 2, 5, '2026-06-03', '2026-06-07', 'borrowed', 1, NULL, NULL);

--
-- Triggers `loans`
--
DELIMITER $$
CREATE TRIGGER `trg_after_delete_loan` AFTER DELETE ON `loans` FOR EACH ROW BEGIN
	UPDATE books 
    SET stock = stock + 1 
    WHERE id = OLD.book_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_after_insert_loan` AFTER INSERT ON `loans` FOR EACH ROW BEGIN
	UPDATE books
    SET stock = stock -1
    WHERE id = NEW.book_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_before_insert_loan` BEFORE INSERT ON `loans` FOR EACH ROW BEGIN
	DECLARE overdue_count INT;
    
	SELECT COUNT(l.id) INTO overdue_count
    FROM loans l
    LEFT JOIN returns r ON l.id =  r.loan_id
    WHERE l.user_id = NEW. user_id AND r.id IS NULL AND l.due_date < CURDATE();
    
    IF overdue_count > 0  THEN
		SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Ditolak: User masih memiliki buku yang terlambat dikembalikan';
	END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `membership_cards`
--

CREATE TABLE `membership_cards` (
  `id` bigint UNSIGNED NOT NULL,
  `card_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_date` date NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `membership_cards`
--

INSERT INTO `membership_cards` (`id`, `card_number`, `issued_date`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'KRT-001', '2026-01-05', 1, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(2, 'KRT-002', '2026-01-05', 2, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(3, 'KRT-003', '2026-01-05', 3, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(4, 'KRT-004', '2026-01-05', 4, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(5, 'KRT-005', '2026-01-05', 5, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(6, 'KRT-006', '2026-01-05', 6, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(7, 'KRT-007', '2026-01-05', 7, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(8, 'KRT-008', '2026-01-05', 8, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(9, 'KRT-009', '2026-01-05', 9, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(10, 'KRT-010', '2026-01-05', 10, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(11, 'KRT-011', '2026-01-05', 11, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(12, 'KRT-012', '2026-01-05', 12, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(13, 'KRT-013', '2026-01-05', 13, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(14, 'KRT-014', '2026-01-05', 14, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(15, 'KRT-015', '2026-01-05', 15, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(16, 'KRT-016', '2026-01-05', 16, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(17, 'KRT-017', '2026-01-05', 17, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(18, 'KRT-018', '2026-01-05', 18, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(19, 'KRT-019', '2026-01-05', 19, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(20, 'KRT-020', '2026-01-05', 20, '2026-06-04 20:57:44', '2026-06-04 20:57:44');

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `id` bigint UNSIGNED NOT NULL,
  `loan_id` bigint UNSIGNED NOT NULL,
  `actual_return_date` date NOT NULL,
  `fine` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `returns`
--

INSERT INTO `returns` (`id`, `loan_id`, `actual_return_date`, `fine`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-05-22', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(2, 2, '2026-05-23', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(3, 3, '2026-05-24', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(4, 4, '2026-05-25', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(5, 5, '2026-05-26', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(6, 6, '2026-05-27', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(7, 7, '2026-05-28', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(8, 8, '2026-05-29', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(9, 9, '2026-05-30', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(10, 10, '2026-05-31', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(11, 11, '2026-06-01', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(12, 12, '2026-06-02', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(13, 13, '2026-06-03', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(14, 14, '2026-06-04', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(15, 15, '2026-06-05', 0.00, '2026-06-04 20:57:44', '2026-06-04 20:57:44');

--
-- Triggers `returns`
--
DELIMITER $$
CREATE TRIGGER `trg_after_insert_return` AFTER INSERT ON `returns` FOR EACH ROW BEGIN
	UPDATE books
	SET stock = stock + 1
    WHERE id = (SELECT book_id FROM loans WHERE id = NEW.loan_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_process_return_details` AFTER INSERT ON `returns` FOR EACH ROW BEGIN
                -- Update loan status to returned
                UPDATE loans
                SET status = 'returned', updated_at = NOW()
                WHERE id = NEW.loan_id;
            END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(2, 'User', '2026-06-04 20:57:44', '2026-06-04 20:57:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Klaudia Weda', 'klaudia@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 1, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(2, 'Yudo Prasetio', 'yudo@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 1, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(3, 'Ahmad Fauzi', 'ahmad@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(4, 'Siti Aminah', 'siti@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(5, 'Budi Santoso', 'budi@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(6, 'Citra Dewi', 'citra@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(7, 'Dhiauddin Arfa', 'dhia@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(8, 'Ulwan Luthfi', 'ulwan@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(9, 'Ahmad Afifi', 'afifi@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(10, 'Freeze Ad Kaban', 'freeze@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(11, 'Budiyono Siregar', 'budiyono@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(12, 'Bilqis Salsabila', 'bilqis@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(13, 'Bunga Lestari', 'bunga@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(14, 'Amy Adinanta', 'amy@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(15, 'Xavier Wijoyo', 'xavier@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(16, 'Jessica Sudarsono', 'jessica@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(17, 'Michael Alviono', 'michael@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(18, 'Asep Surasep', 'asep@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(19, 'Surti Sumiati', 'surti@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44'),
(20, 'Munaroh', 'munaroh@email.com', '$2y$12$53ZXsadFwA6Rr6FVR1cR5.VdP5rRffhn/AuwbZV9cDVsJLQsD9IWa', 2, NULL, '2026-06-04 20:57:44', '2026-06-04 20:57:44');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_active_loans`
-- (See below for the actual view)
--
CREATE TABLE `v_active_loans` (
`loan_id` bigint unsigned
,`borrower_name` varchar(100)
,`book_title` varchar(255)
,`loan_date` date
,`due_date` date
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_book_catalog`
-- (See below for the actual view)
--
CREATE TABLE `v_book_catalog` (
`book_id` bigint unsigned
,`title` varchar(255)
,`author` varchar(100)
,`total_stock` int
,`available_stock` bigint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_overdue_loans`
-- (See below for the actual view)
--
CREATE TABLE `v_overdue_loans` (
`loan_id` bigint unsigned
,`member_name` varchar(100)
,`book_title` varchar(255)
,`loan_date` date
,`due_date` date
,`days_overdue` int
,`estimated_fine` bigint
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_books_title` (`title`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loans_user_id_foreign` (`user_id`),
  ADD KEY `loans_book_id_foreign` (`book_id`),
  ADD KEY `idx_loans_status` (`status`),
  ADD KEY `idx_loans_due_date` (`due_date`);

--
-- Indexes for table `membership_cards`
--
ALTER TABLE `membership_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `membership_cards_card_number_unique` (`card_number`),
  ADD UNIQUE KEY `membership_cards_user_id_unique` (`user_id`),
  ADD KEY `idx_member_card` (`card_number`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `returns_loan_id_unique` (`loan_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`),
  ADD KEY `idx_users_name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `membership_cards`
--
ALTER TABLE `membership_cards`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

-- --------------------------------------------------------

--
-- Structure for view `v_active_loans`
--
DROP TABLE IF EXISTS `v_active_loans`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_active_loans`  AS SELECT `l`.`id` AS `loan_id`, `u`.`name` AS `borrower_name`, `b`.`title` AS `book_title`, `l`.`loan_date` AS `loan_date`, `l`.`due_date` AS `due_date` FROM ((`loans` `l` join `users` `u` on((`l`.`user_id` = `u`.`id`))) join `books` `b` on((`l`.`book_id` = `b`.`id`))) WHERE (`l`.`status` = 'borrowed') ;

-- --------------------------------------------------------

--
-- Structure for view `v_book_catalog`
--
DROP TABLE IF EXISTS `v_book_catalog`;

CREATE ALGORITHM=UNDEFINED DEFINER=`Dhia`@`localhost` SQL SECURITY DEFINER VIEW `v_book_catalog`  AS SELECT `b`.`id` AS `book_id`, `b`.`title` AS `title`, `b`.`author` AS `author`, `b`.`stock` AS `total_stock`, (`b`.`stock` - coalesce((select count(`l`.`id`) from (`loans` `l` left join `returns` `r` on((`l`.`id` = `r`.`loan_id`))) where ((`l`.`book_id` = `b`.`id`) and (`r`.`id` is null))),0)) AS `available_stock` FROM `books` AS `b` ;

-- --------------------------------------------------------

--
-- Structure for view `v_overdue_loans`
--
DROP TABLE IF EXISTS `v_overdue_loans`;

CREATE ALGORITHM=UNDEFINED DEFINER=`Dhia`@`localhost` SQL SECURITY DEFINER VIEW `v_overdue_loans`  AS SELECT `l`.`id` AS `loan_id`, `u`.`name` AS `member_name`, `b`.`title` AS `book_title`, `l`.`loan_date` AS `loan_date`, `l`.`due_date` AS `due_date`, (to_days(curdate()) - to_days(`l`.`due_date`)) AS `days_overdue`, ((to_days(curdate()) - to_days(`l`.`due_date`)) * 2000) AS `estimated_fine` FROM (((`loans` `l` join `users` `u` on((`l`.`user_id` = `u`.`id`))) join `books` `b` on((`l`.`book_id` = `b`.`id`))) left join `returns` `r` on((`l`.`id` = `r`.`loan_id`))) WHERE ((`r`.`id` is null) AND (`l`.`due_date` < curdate())) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `loans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `membership_cards`
--
ALTER TABLE `membership_cards`
  ADD CONSTRAINT `membership_cards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `returns`
--
ALTER TABLE `returns`
  ADD CONSTRAINT `returns_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
