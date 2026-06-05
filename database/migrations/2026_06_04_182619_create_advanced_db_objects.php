// database/migrations/2026_06_04_182619_create_advanced_db_objects.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ==========================================
        // DROP EXISTING OBJECTS (Idempotency Guard)
        // ==========================================
        DB::unprepared("DROP PROCEDURE IF EXISTS add_new_book");
        DB::unprepared("DROP PROCEDURE IF EXISTS extend_loan_duration");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_reduce_stock_after_loan");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_process_return_details");
        DB::unprepared("DROP VIEW IF EXISTS v_active_loans");

        // ==========================================
        // STORED PROCEDURES
        // ==========================================

        // 1. Procedure: Add New Book
        DB::unprepared("
            CREATE PROCEDURE add_new_book(
                IN p_title VARCHAR(255),
                IN p_author VARCHAR(100),
                IN p_publisher VARCHAR(100),
                IN p_stock INT
            )
            BEGIN
                INSERT INTO books (title, author, publisher, stock, created_at, updated_at)
                VALUES (p_title, p_author, p_publisher, p_stock, NOW(), NOW());
            END
        ");

        // 2. Procedure: Extend Loan Due Date
        DB::unprepared("
            CREATE PROCEDURE extend_loan_duration(
                IN p_loan_id INT,
                IN p_days_to_add INT
            )
            BEGIN
                UPDATE loans
                SET due_date = DATE_ADD(due_date, INTERVAL p_days_to_add DAY), updated_at = NOW()
                WHERE id = p_loan_id AND status = 'borrowed';
            END
        ");

        // ==========================================
        // TRIGGERS
        // ==========================================

        // 1. Trigger: Reduce Stock After Loan Insert
        DB::unprepared("
            CREATE TRIGGER trg_reduce_stock_after_loan
            AFTER INSERT ON loans
            FOR EACH ROW
            BEGIN
                UPDATE books
                SET stock = stock - NEW.quantity
                WHERE id = NEW.book_id;
            END
        ");

        // 2. Trigger: Auto Process Returns & Restore Stock
        DB::unprepared("
            CREATE TRIGGER trg_process_return_details
            AFTER INSERT ON returns
            FOR EACH ROW
            BEGIN
                -- Update loan status to returned
                UPDATE loans
                SET status = 'returned', updated_at = NOW()
                WHERE id = NEW.loan_id;

                -- Restore book stock based on quantity inside loans table
                UPDATE books
                SET stock = stock + (SELECT quantity FROM loans WHERE id = NEW.loan_id)
                WHERE id = (SELECT book_id FROM loans WHERE id = NEW.loan_id);
            END
        ");

        // ==========================================
        // VIEW
        // ==========================================

        // View: Active Loans Information
        DB::unprepared("
            CREATE VIEW v_active_loans AS
            SELECT
                l.id AS loan_id,
                u.name AS borrower_name,
                b.title AS book_title,
                l.loan_date,
                l.due_date
            FROM loans l
            JOIN users u ON l.user_id = u.id
            JOIN books b ON l.book_id = b.id
            WHERE l.status = 'borrowed'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS v_active_loans");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_process_return_details");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_reduce_stock_after_loan");
        DB::unprepared("DROP PROCEDURE IF EXISTS extend_loan_duration");
        DB::unprepared("DROP PROCEDURE IF EXISTS add_new_book");
    }
};
