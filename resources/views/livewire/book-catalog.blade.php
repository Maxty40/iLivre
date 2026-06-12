<div class="max-w-7xl mx-auto py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari buku atau penulis..."
            class="w-full md:w-1/3 px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
        <select wire:model.live="sort" class="w-full md:w-48 px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            <option value="title">Urutkan: Judul (A-Z)</option>
            <option value="author">Urutkan: Penulis (A-Z)</option>
            <option value="available_stock">Urutkan: Stok Tersedia</option>
        </select>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse ($books as $book)
            {{-- Setiap card buku membawa state Alpine.js sendiri untuk modal --}}
            <div x-data="{ open: false, qty: 1 }" class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col">
                <a href="{{ url('/books/' . $book->book_id) }}" class="block w-full aspect-[4/5] bg-slate-100 flex-shrink-0">
                    @if($book->cover_image)
                        <img src="/storage/{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                    @endif
                </a>
                <div class="p-5 flex flex-col flex-grow">
                    <a href="{{ url('/books/' . $book->book_id) }}" class="font-bold text-lg text-slate-900 mb-1 truncate hover:text-orange-600 transition" title="{{ $book->title }}">{{ $book->title }}</a>
                    <p class="text-sm text-slate-500 mb-4 flex-grow">{{ $book->author }}</p>

                    <div class="flex flex-col gap-3 mt-4 pt-4 border-t border-slate-100">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full w-max {{ $book->available_stock > 0 ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $book->available_stock > 0 ? 'Stok: ' . $book->available_stock : 'Habis' }}
                        </span>

                        @auth
                            @if($book->available_stock > 0)
                                {{-- Tombol pemicu modal pinjam --}}
                                <button @click="open = true" class="w-full text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 px-3 py-1.5 rounded-lg transition">
                                    Pinjam
                                </button>

                                {{-- Modal Konfirmasi Peminjaman --}}
                                <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="background: rgba(0,0,0,0.5);">
                                    <div @click.outside="open = false" class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-sm mx-4">
                                        <h3 class="text-lg font-bold text-slate-800 mb-1">Konfirmasi Peminjaman</h3>
                                        <p class="text-slate-500 text-sm mb-4">Anda akan meminjam:</p>
                                        <div class="bg-slate-50 rounded-xl p-4 mb-5 border border-slate-100">
                                            <p class="font-bold text-slate-900">{{ $book->title }}</p>
                                            <p class="text-sm text-slate-500">{{ $book->author }}</p>
                                            <p class="text-xs text-slate-400 mt-2">Stok tersedia: {{ $book->available_stock }}</p>
                                        </div>
                                        <div class="mb-5">
                                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Jumlah Pinjam</label>
                                            <div class="flex items-center gap-3">
                                                <button type="button" @click="if(qty > 1) qty--" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center font-bold text-slate-600 transition">−</button>
                                                <span class="w-8 text-center font-bold text-slate-900 text-lg" x-text="qty"></span>
                                                <button type="button" @click="if(qty < {{ min(3, $book->available_stock) }}) qty++" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center font-bold text-slate-600 transition">+</button>
                                                <span class="text-xs text-slate-400">(max {{ min(3, $book->available_stock) }})</span>
                                            </div>
                                        </div>
                                        <p class="text-xs text-slate-400 mb-5">Tenggat pengembalian: <strong>7 hari</strong> dari sekarang.</p>
                                        <div class="flex gap-3">
                                            <button @click="open = false" class="flex-1 px-4 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">Batal</button>
                                            <form action="{{ route('loans.store') }}" method="POST" class="flex-1">
                                                @csrf
                                                <input type="hidden" name="book_id" value="{{ $book->book_id }}">
                                                <input type="hidden" name="quantity" x-bind:value="qty">
                                                <button type="submit" class="w-full px-4 py-2 text-sm font-semibold text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition">
                                                    Ya, Pinjam
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-slate-500">Buku tidak ditemukan.</div>
        @endforelse
    </div>

    <div class="mt-8">{{ $books->links() }}</div>
</div>
