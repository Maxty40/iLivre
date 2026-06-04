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
        Schema::table('kartu_anggota', function (Blueprint $table) {
            $table->foreign(['id_user'], 'kartu_anggota_ibfk_1')->references(['id_user'])->on('user')->onUpdate('restrict')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kartu_anggota', function (Blueprint $table) {
            $table->dropForeign('kartu_anggota_ibfk_1');
        });
    }
};
