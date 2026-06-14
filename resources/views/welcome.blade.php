<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>iLivre - Jelajahi Dunia Lewat Kata</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-slate-100 text-slate-800 font-sans selection:bg-orange-500 selection:text-white">

    <nav class="w-full bg-white shadow-sm fixed top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center gap-2">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                    <span class="text-orange-600 font-bold text-xl tracking-tight">i<span
                            class="text-slate-800">Livre</span>
                    </span>
                </div>

                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-semibold text-slate-600 hover:text-orange-600 transition">Ke Katalog
                                Buku</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-semibold text-slate-600 hover:text-orange-600 transition">Masuk</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="px-4 py-2 rounded-lg bg-orange-600 text-white text-sm font-semibold hover:bg-orange-700 transition shadow-md">Daftar
                                    Anggota</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <main class="pt-24 pb-16 sm:pt-32 sm:pb-24 lg:pb-32 px-4 mx-auto max-w-7xl text-center">
        <h1 class="mx-auto max-w-4xl font-extrabold tracking-tight text-slate-900 text-5xl sm:text-7xl">
            Sistem Informasi <span class="text-orange-600">Perpustakaan Modern.</span>
        </h1>
        <p class="mx-auto mt-6 max-w-2xl text-lg tracking-tight text-slate-600 sm:text-xl">
            iLivre memberikan kemudahan akses ke ribuan koleksi buku. Kelola peminjaman, cek ketersediaan, dan jadikan
            membaca bagian dari gaya hidupmu dengan antarmuka yang ramah pengguna.
        </p>
        <div class="mt-10 flex justify-center gap-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}"
                        class="px-8 py-3 rounded-full bg-orange-600 text-white font-semibold text-lg hover:bg-orange-700 hover:scale-105 transition-all shadow-lg">Cari
                        Buku Sekarang</a>
                @else
                    <a href="{{ route('login') }}"
                        class="px-8 py-3 rounded-full bg-orange-600 text-white font-semibold text-lg hover:bg-orange-700 hover:scale-105 transition-all shadow-lg">Mulai
                        Membaca</a>
                @endauth
            @endif
        </div>
    </main>

    <section class="bg-white py-16 sm:py-24 border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div
                    class="p-6 bg-gray-100 rounded-2xl border border-slate-200 hover:shadow-lg transition duration-300">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Pencarian Cepat</h3>
                    <p class="text-slate-600">Temukan buku yang kamu butuhkan dalam hitungan detik melalui katalog
                        digital interaktif kami.</p>
                </div>
                <div
                    class="p-6 bg-gray-100 rounded-2xl border border-slate-200 hover:shadow-lg transition duration-300">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Pantau Peminjaman</h3>
                    <p class="text-slate-600">Lacak status buku yang kamu pinjam dan tanggal pengembalian langsung dari
                        dashboard-mu.</p>
                </div>
                <div
                    class="p-6 bg-gray-100 rounded-2xl border border-slate-200 hover:shadow-lg transition duration-300">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Aman & Terpusat</h3>
                    <p class="text-slate-600">Sistem keamanan database yang solid memastikan data anggota dan riwayat
                        sirkulasi tetap aman.</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-white border-t border-slate-200 py-8 text-center">
        <p class="text-slate-500 text-sm">© 2026 iLivre Library Management System. By Team iLivre.</p>
    </footer>

</body>

</html>
