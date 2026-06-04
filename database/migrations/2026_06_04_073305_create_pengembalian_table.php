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
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id('id_pengembalian');

            $table->foreignId('id_peminjaman')
                ->nullable()
                ->constrained('peminjaman', 'id_peminjaman')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->date('tanggal_aktual_kembali');
            $table->decimal('denda', 10)->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
