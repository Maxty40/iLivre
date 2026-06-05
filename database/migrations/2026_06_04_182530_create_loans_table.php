<?php
// database/migrations/2026_06_05_000005_create_loans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('book_id')->constrained('books')->onDelete('restrict');
            $table->date('loan_date');
            $table->date('due_date');
            $table->enum('status', ['borrowed', 'returned'])->default('borrowed');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            // Requirement 5: Index 3
            $table->index('status', 'idx_loans_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
