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
        //
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
                WHERE l.book_id = b.id AND l.status IN ('approved', 'borrowed')), 0
            )) AS available_stock
            FROM books b;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
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
    }
};
