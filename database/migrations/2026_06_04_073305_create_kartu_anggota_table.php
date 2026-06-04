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
            $table->integer('id_kartu', true);
            $table->string('nomor_kartu', 20)->unique('nomor_kartu');
            $table->date('tanggal_pembuatan');
            $table->integer('id_user')->nullable()->unique('id_user');
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
