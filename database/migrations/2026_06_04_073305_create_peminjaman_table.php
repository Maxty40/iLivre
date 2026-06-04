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
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->integer('id_peminjaman', true);
            $table->integer('id_user')->nullable()->index('id_user');
            $table->integer('id_buku')->nullable()->index('id_buku');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali_seharusnya');
            $table->enum('status', ['Dipinjam', 'Kembali'])->nullable()->default('Dipinjam');
            $table->integer('jumlah')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
