<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->foreign(['id_user'], 'peminjaman_ibfk_1')->references(['id_user'])->on('user')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_buku'], 'peminjaman_ibfk_2')->references(['id_buku'])->on('buku')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropForeign('peminjaman_ibfk_1');
            $table->dropForeign('peminjaman_ibfk_2');
        });
    }
};
