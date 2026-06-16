<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //Update View v_book_catalog
        DB::unprepared("
            DROP VIEW IF EXISTS v_book_catalog;
            CREATE OR REPLACE VIEW v_book_catalog AS
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
                     WHERE l.book_id = b.id AND l.status = 'approved'), 0
                )) AS available_stock
            FROM books b;");
        
        //Make Procedure sp_approve_loan
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_approve_loan;
            
            CREATE PROCEDURE sp_approve_loan(IN p_loan_id INT)
            BEGIN
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
            END;
        ");

        //Make Procedure sp_reject_loan
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_reject_loan;
            
            CREATE PROCEDURE sp_reject_loan(IN p_loan_id INT)
            BEGIN
                UPDATE loans 
                SET status = 'rejected', updated_at = CURRENT_TIMESTAMP() 
                WHERE id = p_loan_id;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("
            CREATE OR REPLACE VIEW v_book_catalog AS
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

        // Hapus procedure baru jika di-rollback
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_approve_loan;");
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_reject_loan;");
    }
};
