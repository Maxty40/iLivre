<?php
// database/migrations/2026_06_05_000004_create_books_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('author', 100);
            $table->string('publisher', 100)->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();

            // Requirement 5: Index 2
            $table->index('title', 'idx_books_title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
