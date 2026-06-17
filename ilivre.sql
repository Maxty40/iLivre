-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 17, 2026 at 05:50 PM
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
-- Database: `ilivre_2`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_book_stock` (IN `p_book_id` INT, IN `p_additional_stock` INT)   BEGIN
                UPDATE books
                SET stock = stock + p_additional_stock
                WHERE id = p_book_id;
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_add_new_book` (IN `p_title` VARCHAR(255), IN `p_author` VARCHAR(255), IN `p_publisher` VARCHAR(255), IN `p_stock` INT)   BEGIN
                INSERT INTO books (title, author, publisher, stock)
                VALUES (p_title, p_author, p_publisher, p_stock);
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_approval_loan` (IN `p_loan_id` BIGINT)   BEGIN
    DECLARE v_book_id BIGINT;

    -- 1. Ambil book_id dari peminjaman yang mau di-approve
    SELECT book_id INTO v_book_id FROM loans WHERE id = p_loan_id;

    -- 2. Ubah status peminjaman jadi 'approved'
    UPDATE loans SET status = 'approved' WHERE id = p_loan_id;

    -- 3. Kurangi stok buku
    UPDATE books SET stock = stock - 1 WHERE id = v_book_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_approve_loan` (IN `p_loan_id` INT)   BEGIN
                DECLARE v_book_id INT;
                DECLARE v_qty INT;
                DECLARE v_available_stock INT;
                DECLARE v_status VARCHAR(20);

                SELECT book_id, quantity, status INTO v_book_id, v_qty, v_status
                FROM loans WHERE id = p_loan_id;

                IF v_status != 'pending' THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Hanya peminjaman berstatus pending yang bisa disetujui';
                END IF;

                SELECT available_stock INTO v_available_stock
                FROM v_book_catalog WHERE book_id = v_book_id;

                IF v_available_stock >= v_qty THEN
                    UPDATE loans 
                    SET status = 'approved', updated_at = CURRENT_TIMESTAMP() 
                    WHERE id = p_loan_id;
                ELSE
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Gagal disetujui: Stok buku tidak mencukupi saat ini';
                END IF;
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_create_loan` (IN `p_user_id` INT, IN `p_book_id` INT, IN `p_quantity` INT)   BEGIN
                DECLARE v_available_stock INT;
                
                IF p_quantity < 1 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kuantitas peminjaman minimal 1';
                END IF;

                IF p_quantity > 3 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Maksimal meminjam 3 buku dalam satu transaksi';
                END IF;
                
                SELECT available_stock INTO v_available_stock 
                FROM v_book_catalog 
                WHERE book_id = p_book_id;
                
                IF v_available_stock >= p_quantity THEN
                    INSERT INTO loans (user_id, book_id, loan_date, due_date, quantity)
                    VALUES (p_user_id, p_book_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), p_quantity);
                ELSE
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Gagal meminjam: Stok buku tidak mencukupi';
                END IF;
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_delete_book` (IN `p_book_id` INT)   BEGIN
                DECLARE active_loans INT;
                SELECT COUNT(l.id) INTO active_loans
                FROM loans l
                LEFT JOIN returns r ON l.id = r.loan_id
                WHERE l.book_id = p_book_id AND r.id IS NULL;
                
                IF active_loans > 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Tidak dapat menghapus buku yang sedang dipinjam';
                ELSE
                    DELETE FROM books WHERE id = p_book_id;
                END IF;
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_user_total_fines` (IN `p_user_id` INT, OUT `p_total_fine` DECIMAL(10,2))   BEGIN
                SELECT COALESCE(SUM(fine), 0) INTO p_total_fine
                FROM returns r
                JOIN loans l ON r.loan_id = l.id
                WHERE l.user_id = p_user_id;
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_reject_loan` (IN `p_loan_id` INT)   BEGIN
                UPDATE loans 
                SET status = 'rejected', updated_at = CURRENT_TIMESTAMP() 
                WHERE id = p_loan_id;
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_request_return` (IN `p_loan_id` INT, IN `p_user_id` INT)   BEGIN
                -- Pastikan data peminjaman cocok dan statusnya memang sedang dipinjam
                IF (SELECT COUNT(*) FROM loans WHERE id = p_loan_id AND user_id = p_user_id AND status IN ('approved', 'borrowed')) = 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Peminjaman tidak valid atau sudah diproses sebelumnya';
                END IF;

                -- Ubah status menjadi pending_return
                UPDATE loans SET status = 'pending_return', updated_at = NOW() WHERE id = p_loan_id;
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_return_loan` (IN `p_loan_id` INT, IN `p_user_id` INT)   BEGIN DECLARE v_loan_count INT; DECLARE v_due_date DATE; DECLARE v_days_late INT; DECLARE v_fine DECIMAL(10,2); SELECT COUNT(*) INTO v_loan_count FROM loans WHERE id = p_loan_id AND user_id = p_user_id; IF v_loan_count = 0 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Peminjaman tidak ditemukan atau bukan milik Anda'; END IF; SELECT due_date INTO v_due_date FROM loans WHERE id = p_loan_id; SET v_days_late = GREATEST(0, DATEDIFF(CURDATE(), v_due_date)); SET v_fine = v_days_late * 5000; INSERT INTO returns (loan_id, actual_return_date, fine, created_at, updated_at) VALUES (p_loan_id, CURDATE(), v_fine, NOW(), NOW()); END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `publisher` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `cover_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `publisher`, `stock`, `cover_image`, `created_at`, `updated_at`) VALUES
(1, 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 15, 'book-covers/01KV32DG5HWYXYSHKG0C779D8T.jpg', '2026-06-12 01:48:55', '2026-06-16 06:38:05'),
(2, 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 5, 'book-covers/01KV4ENMJ3Y6G1AT9QYA85BDSS.jpg', '2026-06-12 01:48:55', '2026-06-14 18:34:53'),
(3, 'Filosofi Teras', 'Henry Manampiring', 'Kompas', 8, 'book-covers/01KV4EPW8ZBCVDGWD9EKH3X907.jpeg', '2026-06-12 01:48:55', '2026-06-14 18:35:33'),
(4, 'Laut Bercerita', 'Leila S. Chudori', 'KPG', 12, 'book-covers/01KV4ERPZF6RFX77M57VTX3Y3D.jpg', '2026-06-12 01:48:55', '2026-06-14 18:36:33'),
(5, 'Atomic Habits', 'James Clear', 'Gramedia', 15, 'book-covers/01KV4ETZX74MDECEJ0RZZTNK7S.jpeg', '2026-06-12 01:48:55', '2026-06-14 18:37:48'),
(6, 'Hujan', 'Tere Liye', 'Gramedia Pustaka Utama', 10, 'book-covers/01KV4FTKA0F26EX2D6TJ49JEYH.jpg', '2026-06-14 18:55:04', '2026-06-14 18:55:04'),
(7, 'The Architecture of Love', 'Ika Natassa', 'Gramedia Pustaka Utama', 10, 'book-covers/01KVB2H932726JBSE8C9J1SM4V.jpg', '2026-06-17 08:17:28', '2026-06-17 08:17:28');

--
-- Triggers `books`
--
DELIMITER $$
CREATE TRIGGER `trg_before_update_book` BEFORE UPDATE ON `books` FOR EACH ROW BEGIN
                IF NEW.stock < 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Stok buku tidak boleh negatif';
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
  `status` enum('pending','approved','rejected','borrowed','returned','pending_return') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `user_id`, `book_id`, `loan_date`, `due_date`, `status`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 2, 5, '2026-06-12', '2026-06-19', 'returned', 2, NULL, '2026-06-12 08:52:02'),
(2, 2, 5, '2026-06-12', '2026-06-19', 'returned', 1, NULL, '2026-06-12 08:52:21'),
(5, 2, 5, '2026-06-14', '2026-06-21', 'returned', 1, NULL, '2026-06-14 11:33:14'),
(6, 4, 1, '2026-06-14', '2026-06-21', 'returned', 3, NULL, '2026-06-14 12:50:37'),
(7, 4, 5, '2026-06-15', '2026-06-22', 'returned', 3, NULL, '2026-06-16 13:17:34'),
(8, 2, 5, '2026-06-15', '2026-06-22', 'returned', 1, NULL, '2026-06-16 09:47:57'),
(9, 2, 6, '2026-06-15', '2026-06-22', 'returned', 1, NULL, '2026-06-16 07:36:57'),
(10, 4, 3, '2026-06-16', '2026-06-23', 'returned', 1, NULL, '2026-06-16 13:17:36'),
(11, 2, 4, '2026-06-16', '2026-06-23', 'returned', 2, NULL, '2026-06-16 07:51:48'),
(12, 2, 5, '2026-06-16', '2026-06-23', 'returned', 1, NULL, '2026-06-16 09:49:55'),
(13, 2, 5, '2026-06-16', '2026-06-23', 'returned', 1, NULL, '2026-06-16 09:53:15'),
(14, 2, 5, '2026-06-16', '2026-06-23', 'returned', 1, NULL, '2026-06-16 10:01:17'),
(15, 2, 5, '2026-06-16', '2026-06-23', 'returned', 1, '2026-06-16 10:03:53', '2026-06-16 10:08:36'),
(16, 2, 5, '2026-06-16', '2026-06-23', 'returned', 1, NULL, '2026-06-16 10:08:52'),
(17, 2, 5, '2026-06-16', '2026-06-23', 'returned', 1, '2026-06-16 10:12:13', '2026-06-16 10:21:27'),
(18, 2, 5, '2026-06-16', '2026-06-23', 'returned', 1, '2026-06-16 10:46:29', '2026-06-16 10:46:36'),
(19, 2, 5, '2026-06-16', '2026-06-23', 'returned', 1, '2026-06-16 12:51:47', '2026-06-16 13:09:02'),
(20, 2, 5, '2026-06-16', '2026-06-23', 'returned', 1, '2026-06-16 13:09:11', '2026-06-16 13:17:13'),
(21, 2, 1, '2026-06-16', '2026-06-23', 'returned', 2, '2026-06-16 13:22:33', '2026-06-17 02:29:14'),
(22, 2, 2, '2026-06-16', '2026-06-23', 'returned', 1, '2026-06-16 15:13:35', '2026-06-17 09:51:04'),
(23, 2, 5, '2026-06-17', '2026-06-24', 'returned', 2, '2026-06-17 06:01:10', '2026-06-16 23:08:26'),
(24, 2, 5, '2026-06-17', '2026-06-24', 'rejected', 1, '2026-06-17 06:09:16', '2026-06-16 23:09:39'),
(25, 4, 5, '2026-06-17', '2026-06-24', 'returned', 2, '2026-06-17 06:12:55', '2026-06-16 23:13:53'),
(26, 4, 6, '2026-06-17', '2026-06-24', 'returned', 1, '2026-06-17 06:25:40', '2026-06-17 06:26:52'),
(27, 2, 3, '2026-06-17', '2026-06-24', 'returned', 1, '2026-06-17 09:29:39', '2026-06-17 09:36:04'),
(28, 2, 4, '2026-06-17', '2026-06-24', 'returned', 1, '2026-06-17 09:36:38', '2026-06-17 14:22:37'),
(29, 2, 7, '2026-06-17', '2026-06-24', 'returned', 2, '2026-06-17 15:17:50', '2026-06-17 15:47:00'),
(30, 2, 6, '2026-06-17', '2026-06-24', 'borrowed', 1, '2026-06-17 16:02:03', '2026-06-17 09:02:20'),
(31, 2, 7, '2026-06-17', '2026-06-24', 'borrowed', 1, '2026-06-17 16:02:32', '2026-06-17 09:02:42'),
(32, 2, 2, '2026-06-17', '2026-06-24', 'pending', 1, '2026-06-17 16:02:56', '2026-06-17 16:02:56');

--
-- Triggers `loans`
--
DELIMITER $$
CREATE TRIGGER `trg_before_insert_loan` BEFORE INSERT ON `loans` FOR EACH ROW BEGIN
                DECLARE overdue_count INT;
                SELECT COUNT(*) INTO overdue_count
                FROM loans l
                LEFT JOIN returns r ON l.id = r.loan_id
                WHERE l.user_id = NEW.user_id AND r.id IS NULL AND l.due_date < CURDATE();
                
                IF overdue_count > 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Tidak dapat meminjam buku. Ada buku yang terlambat dikembalikan.';
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
  `card_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `issued_date` date NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_01_01_000000_create_base_tables', 1),
(2, '2026_01_01_000001_create_library_tables', 1),
(3, '2026_01_01_000002_create_database_logic', 1),
(4, '2026_06_16_043859_create_permission_tables', 2),
(5, '2026_06_16_045037_move_old_roles_to_spatie', 3),
(6, '2026_06_16_115240_add_status_to_loans_table', 4),
(7, '2026_06_16_121847_update_loan_procedures_and_views', 5),
(8, '2026_06_16_131305_update_view_v_book_catalog', 6),
(9, '2026_06_16_141921_add_pending_return_status', 7);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(2, 'App\\Models\\User', 7);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'create-book', 'web', '2026-06-15 22:34:53', '2026-06-15 22:34:53'),
(2, 'edit-book', 'web', '2026-06-15 22:34:53', '2026-06-15 22:34:53'),
(3, 'delete-book', 'web', '2026-06-15 22:34:53', '2026-06-15 22:34:53'),
(4, 'view-loans', 'web', '2026-06-15 22:34:53', '2026-06-15 22:34:53'),
(5, 'process-loan', 'web', '2026-06-15 22:34:53', '2026-06-15 22:34:53'),
(6, 'view-books', 'web', '2026-06-16 00:42:37', '2026-06-16 00:42:37'),
(7, 'edit-loans', 'web', '2026-06-16 00:58:34', '2026-06-16 00:58:34'),
(8, 'delete-loans', 'web', '2026-06-16 00:58:47', '2026-06-16 00:58:47');

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
(1, 1, '2026-06-12', 0.00, '2026-06-12 01:52:02', '2026-06-12 01:52:02'),
(2, 2, '2026-06-12', 0.00, '2026-06-12 01:52:21', '2026-06-12 01:52:21'),
(5, 5, '2026-06-14', 0.00, '2026-06-14 11:33:14', '2026-06-14 11:33:14'),
(6, 6, '2026-06-14', 0.00, '2026-06-14 12:50:37', '2026-06-14 12:50:37'),
(7, 8, '2026-06-16', 0.00, '2026-06-16 09:47:57', '2026-06-16 09:47:57'),
(8, 12, '2026-06-16', 0.00, '2026-06-16 09:49:55', '2026-06-16 09:49:55'),
(9, 13, '2026-06-16', 0.00, '2026-06-16 09:53:15', '2026-06-16 09:53:15'),
(10, 14, '2026-06-16', 0.00, '2026-06-16 10:01:17', '2026-06-16 10:01:17'),
(11, 15, '2026-06-16', 0.00, '2026-06-16 10:08:36', '2026-06-16 10:08:36'),
(12, 16, '2026-06-16', 0.00, '2026-06-16 10:08:52', '2026-06-16 10:08:52'),
(13, 17, '2026-06-16', 0.00, '2026-06-16 10:21:27', '2026-06-16 10:21:27'),
(14, 18, '2026-06-16', 0.00, '2026-06-16 10:46:36', '2026-06-16 10:46:36'),
(15, 19, '2026-06-16', 0.00, '2026-06-16 13:09:02', '2026-06-16 13:09:02'),
(16, 20, '2026-06-16', 0.00, '2026-06-16 13:17:13', '2026-06-16 13:17:13'),
(17, 7, '2026-06-16', 0.00, '2026-06-16 13:17:34', '2026-06-16 13:17:34'),
(18, 10, '2026-06-16', 0.00, '2026-06-16 13:17:36', '2026-06-16 13:17:36'),
(19, 26, '2026-06-17', 0.00, '2026-06-17 06:26:52', '2026-06-17 06:26:52'),
(20, 27, '2026-06-17', 0.00, '2026-06-17 09:36:04', '2026-06-17 09:36:04'),
(21, 22, '2026-06-17', 0.00, '2026-06-17 09:51:04', '2026-06-17 09:51:04'),
(22, 28, '2026-06-17', 0.00, '2026-06-17 14:22:37', '2026-06-17 14:22:37'),
(23, 29, '2026-06-17', 0.00, '2026-06-17 15:47:00', '2026-06-17 15:47:00');

--
-- Triggers `returns`
--
DELIMITER $$
CREATE TRIGGER `trg_process_return_details` AFTER INSERT ON `returns` FOR EACH ROW BEGIN
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
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'web', '2026-06-15 21:50:24', '2026-06-15 21:50:24'),
(2, 'Member', 'web', '2026-06-15 21:50:24', '2026-06-15 21:50:24'),
(3, 'Officer', 'web', '2026-06-16 00:27:39', '2026-06-16 00:27:39');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(4, 2),
(6, 2),
(4, 3),
(5, 3),
(6, 3),
(7, 3);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `photo`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Klaudia', 'klaudia@admin.com', '$2y$12$P1N9ooFHVo3u5cSRp78eouym3ZWrd9h7tXiUh.I.0IFe4GFJaVqUe', 'user-photos/01KV4FEYRHZKYSVBG2TTKXE8WE.png', NULL, '2026-06-12 01:48:54', '2026-06-16 00:15:44'),
(2, 'Member', 'member@test.com', '$2y$12$VwJtlQlNewQZlqAqI4DbZOMnSi6UG/qdhNsznKWr5SBz3UhJDBJ16', 'user-photos/01KV4FDDHSBZXFYW2WWXSMKDYG.png', NULL, '2026-06-12 01:48:55', '2026-06-14 18:48:10'),
(4, 'Dhia', 'dhia@test.com', '$2y$12$DKGxgleEqr51oOGpvM8WdujIX9SvM9V2j6LeUy4gwrYmvdiBr7YLW', 'user-photos/01KV32PRVT4KZ2ZMMQYNZZ7K5F.jpg', NULL, '2026-06-14 05:46:33', '2026-06-14 05:46:33'),
(5, 'Ulwan', 'ulwan@test.com', '$2y$12$xd/xeL.INe.uU8BAsBTEteSiJP...PAG7nzuc2fxlNM4FyGV4fLc2', 'user-photos/01KV7P74RF7QFY3CGRBE55WZZR.png', NULL, '2026-06-16 00:44:30', '2026-06-16 00:44:30'),
(7, 'Afifi', 'afifi@test.com', '$2y$12$bqGthbXB79O8dypsZ5pSpO8cRPvXbVBuSYHb3BLkJtFJi4QPmeIOC', NULL, NULL, '2026-06-17 08:59:25', '2026-06-17 08:59:25');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_active_loans`
-- (See below for the actual view)
--
CREATE TABLE `v_active_loans` (
`book_title` varchar(255)
,`due_date` date
,`loan_date` date
,`loan_id` bigint unsigned
,`quantity` int
,`user_name` varchar(255)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_book_catalog`
-- (See below for the actual view)
--
CREATE TABLE `v_book_catalog` (
`author` varchar(255)
,`available_stock` decimal(33,0)
,`book_id` bigint unsigned
,`cover_image` varchar(255)
,`publisher` varchar(255)
,`title` varchar(255)
,`total_stock` int
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_overdue_loans`
-- (See below for the actual view)
--
CREATE TABLE `v_overdue_loans` (
`book_title` varchar(255)
,`days_overdue` int
,`due_date` date
,`estimated_fine` bigint
,`loan_id` bigint unsigned
,`user_name` varchar(255)
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `loans_user_id_foreign` (`user_id`),
  ADD KEY `loans_book_id_foreign` (`book_id`);

--
-- Indexes for table `membership_cards`
--
ALTER TABLE `membership_cards`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `membership_cards_card_number_unique` (`card_number`),
  ADD KEY `membership_cards_user_id_foreign` (`user_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `returns_loan_id_foreign` (`loan_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `membership_cards`
--
ALTER TABLE `membership_cards`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

-- --------------------------------------------------------

--
-- Structure for view `v_active_loans`
--
DROP TABLE IF EXISTS `v_active_loans`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_active_loans`  AS SELECT `l`.`id` AS `loan_id`, `u`.`name` AS `user_name`, `b`.`title` AS `book_title`, `l`.`loan_date` AS `loan_date`, `l`.`due_date` AS `due_date`, `l`.`quantity` AS `quantity` FROM (((`loans` `l` join `users` `u` on((`l`.`user_id` = `u`.`id`))) join `books` `b` on((`l`.`book_id` = `b`.`id`))) left join `returns` `r` on((`l`.`id` = `r`.`loan_id`))) WHERE (`r`.`id` is null) ;

-- --------------------------------------------------------

--
-- Structure for view `v_book_catalog`
--
DROP TABLE IF EXISTS `v_book_catalog`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_book_catalog`  AS SELECT `b`.`id` AS `book_id`, `b`.`title` AS `title`, `b`.`author` AS `author`, `b`.`publisher` AS `publisher`, `b`.`cover_image` AS `cover_image`, `b`.`stock` AS `total_stock`, (`b`.`stock` - coalesce((select sum(`l`.`quantity`) from `loans` `l` where ((`l`.`book_id` = `b`.`id`) and (`l`.`status` in ('approved','borrowed','pending_return')))),0)) AS `available_stock` FROM `books` AS `b` ;

-- --------------------------------------------------------

--
-- Structure for view `v_overdue_loans`
--
DROP TABLE IF EXISTS `v_overdue_loans`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_overdue_loans`  AS SELECT `l`.`id` AS `loan_id`, `u`.`name` AS `user_name`, `b`.`title` AS `book_title`, `l`.`due_date` AS `due_date`, (to_days(curdate()) - to_days(`l`.`due_date`)) AS `days_overdue`, ((to_days(curdate()) - to_days(`l`.`due_date`)) * 5000) AS `estimated_fine` FROM (((`loans` `l` join `users` `u` on((`l`.`user_id` = `u`.`id`))) join `books` `b` on((`l`.`book_id` = `b`.`id`))) left join `returns` `r` on((`l`.`id` = `r`.`loan_id`))) WHERE ((`r`.`id` is null) AND (`l`.`due_date` < curdate())) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_book_id_foreign` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `loans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `membership_cards`
--
ALTER TABLE `membership_cards`
  ADD CONSTRAINT `membership_cards_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `returns`
--
ALTER TABLE `returns`
  ADD CONSTRAINT `returns_loan_id_foreign` FOREIGN KEY (`loan_id`) REFERENCES `loans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
