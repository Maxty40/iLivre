<div class="max-w-7xl mx-auto py-8">
    <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari buku atau penulis..." class="w-full md:w-1/3 px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
        
        <select wire:model.live="sort" class="w-full md:w-48 px-4 py-2 rounded-lg border-slate-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
            <option value="title">Urutkan: Judul (A-Z)</option>
            <option value="author">Urutkan: Penulis (A-Z)</option>
            <option value="available_stock">Urutkan: Stok Tersedia</option>
        </select>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @forelse ($books as $book)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="p-6 flex flex-col h-full">
                    <h3 class="font-bold text-lg text-slate-900 mb-1 truncate" title="{{ $book->title }}">{{ $book->title }}</h3>
                    <p class="text-sm text-slate-500 mb-4 flex-grow">{{ $book->author }}</p>
                    
                    <div class="flex justify-between items-center mt-4 pt-4 border-t border-slate-50">
                        <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $book->available_stock > 0 ? 'bg-orange-100 text-orange-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $book->available_stock > 0 ? 'Stok: ' . $book->available_stock : 'Habis' }}
                        </span>
                        
                        @auth
                            @if($book->available_stock > 0)
                            <form action="{{ route('loans.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->book_id }}">
                                <button type="submit" class="text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 px-3 py-1.5 rounded transition">Pinjam</button>
                            </form>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-slate-500">
                Buku tidak ditemukan.
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $books->links() }}
    </div>
</div>
