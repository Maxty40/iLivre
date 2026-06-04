<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BukuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('buku')->insert([
            [
                'judul' => 'Desain Antarmuka Pengguna (UI/UX)',
                'penulis' => 'Dhiauddin',
                'penerbit' => 'Penerbit 1',
                'stok' => 15,
            ],
            [
                'judul' => 'Algoritma dan Struktur Data',
                'penulis' => 'Arfa',
                'penerbit' => 'Penerbit 2',
                'stok' => 8,
            ],
            [
                'judul' => 'Panduan Implementasi Human-Computer Interaction',
                'penulis' => 'John Doe',
                'penerbit' => 'Penerbit 3',
                'stok' => 12,
            ],
        ]);
    }
}
