<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard Anggota') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-slate-900 border-b border-slate-200">
                    <h3 class="text-lg font-bold text-orange-600 mb-4">Buku Sedang Dipinjam</h3>
                    
                    @php
                        $activeLoans = DB::table('loans')
                            ->join('books', 'loans.book_id', '=', 'books.id')
                            ->leftJoin('returns', 'loans.id', '=', 'returns.loan_id')
                            ->where('loans.user_id', Auth::id())
                            ->whereNull('returns.id')
                            ->select('loans.*', 'books.title', 'books.author')
                            ->get();
                    @endphp

                    @if($activeLoans->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-slate-50 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Judul Buku</th>
                                        <th class="px-6 py-3 bg-slate-50 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tanggal Pinjam</th>
                                        <th class="px-6 py-3 bg-slate-50 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tenggat Pengembalian</th>
                                        <th class="px-6 py-3 bg-slate-50 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    @foreach($activeLoans as $loan)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $loan->title }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $loan->loan_date }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                                <span class="{{ $loan->due_date < now() ? 'text-red-600 font-bold' : '' }}">
                                                    {{ $loan->due_date }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                                <form action="{{ route('loans.return') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="loan_id" value="{{ $loan->id }}">
                                                    <button type="submit" class="text-orange-600 hover:text-orange-900 font-bold border border-orange-500 px-3 py-1 rounded">Kembalikan</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-slate-500">Kamu belum meminjam buku apapun.</p>
                    @endif
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    <h3 class="text-lg font-bold text-orange-600 mb-4">Katalog Buku</h3>
                    @livewire('book-catalog')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
