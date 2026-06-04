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
        Schema::create('kartu_anggota', function (Blueprint $table) {
            $table->id('id_kartu');
            $table->string('nomor_kartu', 20)->unique();
            $table->date('tanggal_pembuatan');

            $table->foreignId('id_user')
                ->nullable()
                ->unique()
                ->constrained('users')
                ->onUpdate('restrict')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_anggota');
    }
};
