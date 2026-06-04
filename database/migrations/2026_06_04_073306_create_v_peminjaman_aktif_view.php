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
        DB::statement("CREATE OR REPLACE VIEW v_peminjaman_aktif AS 
            SELECT 
                p.id_peminjaman AS id_peminjaman,
                u.name AS nama_peminjam, 
                b.judul AS judul_buku,
                p.tanggal_pinjam AS tanggal_pinjam,
                p.tanggal_kembali_seharusnya AS tanggal_kembali_seharusnya 
            FROM peminjaman p 
            JOIN users u ON p.id_user = u.id 
            JOIN buku b ON p.id_buku = b.id_buku 
            WHERE p.status = 'Dipinjam'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS `v_peminjaman_aktif`');
    }
};
