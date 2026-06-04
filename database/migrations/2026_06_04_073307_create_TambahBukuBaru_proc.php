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
        DB::unprepared("
            DROP PROCEDURE IF EXISTS TambahBukuBaru;
            
            CREATE PROCEDURE TambahBukuBaru(IN p_judul VARCHAR(255), IN p_penulis VARCHAR(100), IN p_penerbit VARCHAR(100), IN p_stok INT)
            BEGIN
                INSERT INTO buku (judul, penulis, penerbit, stok)
                VALUES (p_judul, p_penulis, p_penerbit, p_stok);
            END
        "); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS TambahBukuBaru');
    }
};
