<?php
// database/seeders/BookSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            ['title' => 'Belajar Basis Data Modern', 'author' => 'Dr. Indrajit', 'publisher' => 'Informatika', 'stock' => 5],
            ['title' => 'Pemrograman Web dengan Laravel', 'author' => 'Eko Khannedy', 'publisher' => 'TechMedia', 'stock' => 6],
            ['title' => 'Logika & Algoritma', 'author' => 'Rinaldi Munir', 'publisher' => 'SainsPress', 'stock' => 4],
            ['title' => 'Sistem Operasi Dasar', 'author' => 'Tanenbaum', 'publisher' => 'Erlangga', 'stock' => 2],
            ['title' => 'Jaringan Komputer', 'author' => 'Forouzan', 'publisher' => 'McGrawHill', 'stock' => 9],
            ['title' => 'Pengantar Cybersecurity', 'author' => 'Andi Utama', 'publisher' => 'CyberSec', 'stock' => 3],
            ['title' => 'Sistem Informasi Geografis', 'author' => 'Randi', 'publisher' => 'Erlangga', 'stock' => 5],
            ['title' => 'Bahasa Inggris', 'author' => 'Alena', 'publisher' => 'Arunika', 'stock' => 4],
            ['title' => 'Laut Bercerita', 'author' => 'Leila S. Chudori', 'publisher' => 'Gramedia', 'stock' => 5],
            ['title' => 'Laskar Pelangi', 'author' => 'Andrea Hirata', 'publisher' => 'Bentang Pustaka', 'stock' => 3],
            ['title' => 'Bumi Manusia', 'author' => 'Pramoedya A. Toer', 'publisher' => 'Hasta Mitra', 'stock' => 6],
            ['title' => 'Ayat Ayat Cinta', 'author' => 'Habiburrahman El Shirazy', 'publisher' => 'Republika Penerbit', 'stock' => 4],
            ['title' => 'Filosofi Kopi', 'author' => 'Dewi Lestari', 'publisher' => 'Bentang Pustaka', 'stock' => 4],
            ['title' => 'Charlie and the Chocolate Factory', 'author' => 'Roald Dahl', 'publisher' => 'Puffin Book', 'stock' => 5],
            ['title' => 'Peter Pan', 'author' => 'J.M. Barrie', 'publisher' => 'Hodder & Stoughton', 'stock' => 9],
            ['title' => 'La Tahzan', 'author' => 'Dr. Aidh al-Qarni', 'publisher' => 'Qisthi Press', 'stock' => 4],
            ['title' => 'Matematika Diskrit', 'author' => 'Dicky Susanto', 'publisher' => 'Kemendikbudristek', 'stock' => 9],
            ['title' => 'Statistika untuk Penelitian', 'author' => 'Prof. Dr. Sugiyono', 'publisher' => 'Alfabeta', 'stock' => 2],
            ['title' => 'Geometri Analitik', 'author' => 'M. Cholik Adinawan', 'publisher' => 'Erlangga', 'stock' => 7],
            ['title' => 'Aljabar Linear', 'author' => 'Howard Anton', 'publisher' => 'Erlangga', 'stock' => 4],
        ];

        foreach ($books as $book) {
            $book['created_at'] = now();
            $book['updated_at'] = now();
            DB::table('books')->insert($book);
        }
    }
}
