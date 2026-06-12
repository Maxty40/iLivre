<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Detail Buku</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
            @endif

            <div x-data="{ open: false, qty: 1 }" class="bg-white overflow-hidden shadow-sm sm:rounded-2xl">
                <div class="p-6 md:p-10 flex flex-col md:flex-row gap-8">

                    {{-- Cover --}}
                    <div class="w-full md:w-64 flex-shrink-0">
                        @if($book->cover_image)
                            <img src="/storage/{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full rounded-xl shadow-md object-cover aspect-[3/4]">
                        @else
                            <div class="w-full rounded-xl shadow-md bg-slate-100 flex items-center justify-center text-slate-300 aspect-[3/4]">
                                <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            </div>
                        @endif
                    </div>

                    {{-- Detail --}}
                    <div class="flex-grow flex flex-col">
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 mb-2 leading-tight">{{ $book->title }}</h1>
                        <p class="text-xl text-orange-500 font-semibold mb-6">{{ $book->author }}</p>

                        <div class="grid grid-cols-2 gap-4 mb-8 bg-slate-50 p-5 rounded-xl border border-slate-100">
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold tracking-wider mb-1">Penerbit</p>
                                <p class="font-semibold text-slate-800">{{ $book->publisher ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold tracking-wider mb-1">Total Stok</p>
                                <p class="font-semibold text-slate-800">{{ $book->total_stock }} buah</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase font-semibold tracking-wider mb-1">Tersedia</p>
                                <p class="font-bold text-lg {{ $book->available_stock > 0 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $book->available_stock }} buah
                                </p>
                            </div>
                        </div>

                        <div class="mt-auto">
                            @auth
                                @if($book->available_stock > 0)
                                    {{-- Tombol trigger modal --}}
                                    <button @click="open = true"
                                        class="inline-flex items-center gap-2 px-8 py-3 bg-orange-600 text-white font-bold rounded-xl hover:bg-orange-700 transition shadow-md shadow-orange-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                        Pinjam Buku Ini
                                    </button>
                                    <p class="text-xs text-slate-400 mt-3">Maks. 3 buku per transaksi · Tenggat pengembalian 7 hari</p>

                                    {{-- Modal Konfirmasi Peminjaman --}}
                                    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="background: rgba(0,0,0,0.5);">
                                        <div @click.outside="open = false" class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md mx-4">
                                            <h3 class="text-xl font-bold text-slate-800 mb-1">Konfirmasi Peminjaman</h3>
                                            <p class="text-slate-500 text-sm mb-5">Periksa detail sebelum memproses peminjaman.</p>

                                            <div class="flex gap-4 mb-6 bg-slate-50 p-4 rounded-xl border border-slate-100">
                                                @if($book->cover_image)
                                                    <img src="/storage/{{ $book->cover_image }}" class="w-14 h-20 object-cover rounded-lg shadow-sm flex-shrink-0">
                                                @endif
                                                <div>
                                                    <p class="font-bold text-slate-900">{{ $book->title }}</p>
                                                    <p class="text-sm text-slate-500">{{ $book->author }}</p>
                                                    <p class="text-xs text-slate-400 mt-1.5">Stok tersedia: <strong class="text-slate-600">{{ $book->available_stock }}</strong></p>
                                                </div>
                                            </div>

                                            <div class="mb-6">
                                                <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah Pinjam</label>
                                                <div class="flex items-center gap-4">
                                                    <button type="button" @click="if(qty > 1) qty--"
                                                        class="w-9 h-9 rounded-full bg-slate-100 hover:bg-orange-100 flex items-center justify-center font-bold text-slate-700 text-lg transition">−</button>
                                                    <span class="text-2xl font-extrabold text-slate-900 w-8 text-center" x-text="qty"></span>
                                                    <button type="button" @click="if(qty < {{ min(3, $book->available_stock) }}) qty++"
                                                        class="w-9 h-9 rounded-full bg-slate-100 hover:bg-orange-100 flex items-center justify-center font-bold text-slate-700 text-lg transition">+</button>
                                                    <span class="text-sm text-slate-400">maks. {{ min(3, $book->available_stock) }} buku</span>
                                                </div>
                                            </div>

                                            <div class="bg-orange-50 border border-orange-100 rounded-lg px-4 py-3 text-sm text-orange-800 mb-6">
                                                📅 Tenggat pengembalian: <strong>7 hari</strong> sejak hari ini.
                                            </div>

                                            <div class="flex gap-3">
                                                <button @click="open = false" class="flex-1 px-5 py-2.5 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                                                    Batal
                                                </button>
                                                <form action="{{ route('loans.store') }}" method="POST" class="flex-1">
                                                    @csrf
                                                    <input type="hidden" name="book_id" value="{{ $book->book_id }}">
                                                    <input type="hidden" name="quantity" x-bind:value="qty">
                                                    <button type="submit" class="w-full px-5 py-2.5 text-sm font-bold text-white bg-orange-600 hover:bg-orange-700 rounded-xl transition shadow-md shadow-orange-200">
                                                        Ya, Pinjam Sekarang
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                @else
                                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-center">
                                        <p class="text-slate-500 font-medium">Maaf, buku ini sedang habis dipinjam.</p>
                                    </div>
                                @endif
                            @else
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 text-center">
                                    <p class="text-slate-600 mb-4">Silakan masuk untuk meminjam buku ini.</p>
                                    <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-slate-800 text-white font-semibold rounded-lg hover:bg-slate-900 transition">
                                        Masuk ke Akun
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
