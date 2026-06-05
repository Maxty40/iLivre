<?php
// database/seeders/LoanSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        // Seeding initial 20 loan records across various users and books
        for ($i = 1; $i <= 20; $i++) {
            $userId = ($i % 18) + 3; // Distribute between user_id 3 up to 20
            $bookId = ($i % 20) + 1; // Distribute across all 20 books

            DB::table('loans')->insert([
                'user_id' => $userId,
                'book_id' => $bookId,
                'loan_date' => now()->subDays(20 - $i),
                'due_date' => now()->subDays(20 - $i)->addDays(7),
                'status' => $i <= 15 ? 'returned' : 'borrowed', // 15 returned, 5 active
                'quantity' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Seed 1:1 Return detail if loan status is returned
            if ($i <= 15) {
                DB::table('returns')->insert([
                    'loan_id' => $i,
                    'actual_return_date' => now()->subDays(20 - $i)->addDays(5), // Returned early/on time
                    'fine' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
