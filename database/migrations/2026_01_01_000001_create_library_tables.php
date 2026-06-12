<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_cards', function (Blueprint $table) {
            $table->id();
            $table->string('card_number', 20)->unique();
            $table->date('issued_date');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('publisher');
            $table->integer('stock')->default(0);
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->date('loan_date');
            $table->date('due_date');
            $table->enum('status', ['borrowed', 'returned'])->default('borrowed');
            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->date('actual_return_date');
            $table->decimal('fine', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('books');
        Schema::dropIfExists('membership_cards');
    }
};
