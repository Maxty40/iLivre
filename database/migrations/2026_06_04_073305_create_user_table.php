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
        Schema::create('user', function (Blueprint $table) {
            $table->integer('id_user', true);
            $table->string('nama', 100);
            $table->string('email', 100)->unique('email');
            $table->string('password');
            $table->integer('id_role')->nullable()->index('id_role');

            $table->index(['email'], 'idx_user_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
