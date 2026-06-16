<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            DROP VIEW IF EXISTS v_book_catalog;
            CREATE VIEW v_book_catalog AS
            SELECT
                b.id AS book_id,
                b.title,
                b.author,
                b.publisher,
                b.cover_image,
                b.stock AS total_stock,
                (b.stock - COALESCE(
                    (SELECT SUM(l.quantity)
                     FROM loans l
                     LEFT JOIN returns r ON l.id = r.loan_id
                     WHERE l.book_id = b.id AND r.id IS NULL), 0
                )) AS available_stock
            FROM books b;
        ");

        DB::unprepared("
            DROP VIEW IF EXISTS v_active_loans;
            CREATE VIEW v_active_loans AS
            SELECT
                l.id AS loan_id,
                u.name AS user_name,
                b.title AS book_title,
                l.loan_date,
                l.due_date,
                l.quantity
            FROM loans l
            JOIN users u ON l.user_id = u.id
            JOIN books b ON l.book_id = b.id
            LEFT JOIN returns r ON l.id = r.loan_id
            WHERE r.id IS NULL;
        ");

        DB::unprepared("
            DROP VIEW IF EXISTS v_overdue_loans;
            CREATE VIEW v_overdue_loans AS
            SELECT
                l.id AS loan_id,
                u.name AS user_name,
                b.title AS book_title,
                l.due_date,
                DATEDIFF(CURDATE(), l.due_date) AS days_overdue,
                (DATEDIFF(CURDATE(), l.due_date) * 5000) AS estimated_fine
            FROM loans l
            JOIN users u ON l.user_id = u.id
            JOIN books b ON l.book_id = b.id
            LEFT JOIN returns r ON l.id = r.loan_id
            WHERE r.id IS NULL AND l.due_date < CURDATE();
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_before_update_book;
            CREATE TRIGGER trg_before_update_book BEFORE UPDATE ON books
            FOR EACH ROW
            BEGIN
                IF NEW.stock < 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Stok buku tidak boleh negatif';
                END IF;
            END
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_before_insert_loan;
            CREATE TRIGGER trg_before_insert_loan BEFORE INSERT ON loans
            FOR EACH ROW
            BEGIN
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
        ");

        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_process_return_details;
            CREATE TRIGGER trg_process_return_details AFTER INSERT ON returns
            FOR EACH ROW
            BEGIN
                UPDATE loans
                SET status = 'returned', updated_at = NOW()
                WHERE id = NEW.loan_id;
            END
        ");

        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_create_loan;
            CREATE PROCEDURE sp_create_loan(IN p_user_id INT, IN p_book_id INT, IN p_quantity INT)
            BEGIN
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
                    -- Added created_at and updated_at initialization
                    INSERT INTO loans (user_id, book_id, loan_date, due_date, quantity, created_at, updated_at)
                    VALUES (p_user_id, p_book_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 7 DAY), p_quantity, NOW(), NOW());
                ELSE
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Gagal meminjam: Stok buku tidak mencukupi';
                END IF;
            END
        ");

        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_add_new_book;
            CREATE PROCEDURE sp_add_new_book(IN p_title VARCHAR(255), IN p_author VARCHAR(255), IN p_publisher VARCHAR(255), IN p_stock INT)
            BEGIN
                -- Added created_at and updated_at initialization
                INSERT INTO books (title, author, publisher, stock, created_at, updated_at)
                VALUES (p_title, p_author, p_publisher, p_stock, NOW(), NOW());
            END
        ");

        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_add_book_stock;
            CREATE PROCEDURE sp_add_book_stock(IN p_book_id INT, IN p_additional_stock INT)
            BEGIN
                UPDATE books
                SET stock = stock + p_additional_stock, updated_at = NOW()
                WHERE id = p_book_id;
            END
        ");

        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_delete_book;
            CREATE PROCEDURE sp_delete_book(IN p_book_id INT)
            BEGIN
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
            END
        ");

        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_get_user_total_fines;
            CREATE PROCEDURE sp_get_user_total_fines(IN p_user_id INT, OUT p_total_fine DECIMAL(10,2))
            BEGIN
                SELECT COALESCE(SUM(fine), 0) INTO p_total_fine
                FROM returns r
                JOIN loans l ON r.loan_id = l.id
                WHERE l.user_id = p_user_id;
            END
        ");

        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_return_loan;
            CREATE PROCEDURE sp_return_loan(IN p_loan_id INT, IN p_user_id INT)
            BEGIN
                DECLARE v_loan_count INT;
                DECLARE v_due_date DATE;
                DECLARE v_days_late INT;
                DECLARE v_fine DECIMAL(10,2);

                SELECT COUNT(*) INTO v_loan_count
                FROM loans
                WHERE id = p_loan_id AND user_id = p_user_id;

                IF v_loan_count = 0 THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Peminjaman tidak ditemukan atau bukan milik Anda';
                END IF;

                SELECT due_date INTO v_due_date FROM loans WHERE id = p_loan_id;

                SET v_days_late = GREATEST(0, DATEDIFF(CURDATE(), v_due_date));
                SET v_fine = v_days_late * 5000;

                INSERT INTO returns (loan_id, actual_return_date, fine, created_at, updated_at)
                VALUES (p_loan_id, CURDATE(), v_fine, NOW(), NOW());
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_get_user_total_fines;
            DROP PROCEDURE IF EXISTS sp_delete_book;
            DROP PROCEDURE IF EXISTS sp_add_book_stock;
            DROP PROCEDURE IF EXISTS sp_add_new_book;
            DROP PROCEDURE IF EXISTS sp_create_loan;
            DROP TRIGGER IF EXISTS trg_process_return_details;
            DROP TRIGGER IF EXISTS trg_before_insert_loan;
            DROP TRIGGER IF EXISTS trg_before_update_book;
            DROP VIEW IF EXISTS v_overdue_loans;
            DROP VIEW IF EXISTS v_active_loans;
            DROP VIEW IF EXISTS v_book_catalog;
        ");
    }
};
