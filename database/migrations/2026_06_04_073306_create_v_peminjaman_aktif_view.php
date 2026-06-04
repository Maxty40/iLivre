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
        DB::statement("CREATE VIEW `v_peminjaman_aktif` AS select `p`.`id_peminjaman` AS `id_peminjaman`,`u`.`nama` AS `nama_peminjam`,`b`.`judul` AS `judul_buku`,`p`.`tanggal_pinjam` AS `tanggal_pinjam`,`p`.`tanggal_kembali_seharusnya` AS `tanggal_kembali_seharusnya` from ((`perpustakaan_db`.`peminjaman` `p` join `perpustakaan_db`.`user` `u` on(`p`.`id_user` = `u`.`id_user`)) join `perpustakaan_db`.`buku` `b` on(`p`.`id_buku` = `b`.`id_buku`)) where `p`.`status` = 'Dipinjam'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS `v_peminjaman_aktif`");
    }
};
