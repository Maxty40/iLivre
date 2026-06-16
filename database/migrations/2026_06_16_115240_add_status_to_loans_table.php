<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //Kolom status baru
            DB::statement("ALTER TABLE loans
                MODIFY COLUMN status
                ENUM('pending', 'approved', 'rejected', 'borrowed', 'returned')
                NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE loans 
                MODIFY COLUMN status 
                ENUM('borrowed', 'returned') 
                NOT NULL DEFAULT 'borrowed'");
        });
    }
};
