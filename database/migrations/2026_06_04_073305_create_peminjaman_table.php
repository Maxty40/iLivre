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
            $table->id('id_peminjaman');

            $table->foreignId('id_user')
                ->nullable()
                ->references('id')->on('users')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreignId('id_buku')
                ->nullable()
                ->references('id_buku')->on('buku')
                ->onUpdate('restrict')
                ->onDelete('restrict');

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
