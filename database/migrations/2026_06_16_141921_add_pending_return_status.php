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
        //Modifikasi kolom status pada tabel loans untuk menambahkan status 'pending_return'
        DB::unprepared("ALTER TABLE loans MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'borrowed', 'returned', 'pending_return') NOT NULL DEFAULT 'pending'");

        //Update View v_book_catalog
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
                     WHERE l.book_id = b.id AND l.status IN ('approved', 'borrowed', 'pending_return')), 0
                )) AS available_stock
            FROM books b;
        ");

        // 3. Buat Procedure Baru untuk Member mengajukan pengembalian
        DB::unprepared("
            DROP PROCEDURE IF EXISTS sp_request_return;
            CREATE PROCEDURE sp_request_return(IN p_loan_id INT, IN p_user_id INT)
            BEGIN
                -- Pastikan data peminjaman cocok dan statusnya memang sedang dipinjam
                IF (SELECT COUNT(*) FROM loans WHERE id = p_loan_id AND user_id = p_user_id AND status IN ('approved', 'borrowed')) = 0 THEN
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Peminjaman tidak valid atau sudah diproses sebelumnya';
                END IF;

                -- Ubah status menjadi pending_return
                UPDATE loans SET status = 'pending_return', updated_at = NOW() WHERE id = p_loan_id;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_request_return;");
    }
};
