<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Riwayat & Peminjaman Aktif
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            {{-- MENUNGGU PERSETUJUAN PETUGAS --}}
            @if ($pendingLoans->count() > 0)
                <div class="bg-white shadow-sm sm:rounded-xl overflow-hidden mb-8 border border-amber-100">
                    <div class="px-6 py-5 bg-amber-50/50 border-b border-slate-100 flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-amber-100">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <h3 class="text-lg font-bold text-slate-800">Menunggu Persetujuan Petugas</h3>
                        <span class="ml-auto text-sm font-semibold bg-amber-100 text-amber-700 px-3 py-1 rounded-full">
                            {{ $pendingLoans->count() }} Buku
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Buku</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Jml</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Tanggal Pengajuan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @foreach ($pendingLoans as $pending)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-800 text-sm">{{ $pending->title }}
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $pending->author }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ $pending->quantity }}x</td>
                                        <td class="px-6 py-4 text-sm text-slate-500">
                                            {{ \Carbon\Carbon::parse($pending->created_at)->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex text-xs font-semibold text-amber-700 bg-amber-100 px-2.5 py-1 rounded-full">
                                                Menunggu Persetujuan
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- PEMINJAMAN AKTIF --}}
            <div class="bg-white shadow-sm sm:rounded-xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-orange-100">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <h3 class="text-lg font-bold text-slate-800">Peminjaman Aktif</h3>
                    <span class="ml-auto text-sm font-semibold bg-orange-100 text-orange-700 px-3 py-1 rounded-full">
                        {{ $activeLoans->count() }} buku
                    </span>
                </div>

                @if ($activeLoans->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Buku</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Jml</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Tgl Pinjam</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Tenggat</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Status</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @foreach ($activeLoans as $loan)
                                    @php
                                        // Normalize both dates to 00:00:00 to match MariaDB's TO_DAYS() behavior
                                        $dueDate = \Carbon\Carbon::parse($loan->due_date)->startOfDay();
                                        $currentDate = now()->startOfDay();

                                        $isOverdue = $currentDate->greaterThan($dueDate);
                                        // Cast explicitly to integer to drop any remaining microsecond variations
                                        $daysOverdue = $isOverdue ? (int) $dueDate->diffInDays($currentDate) : 0;
                                    @endphp
                                    <tr x-data="{ open: false }" class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-800 text-sm">{{ $loan->title }}</div>
                                            <div class="text-xs text-slate-500">{{ $loan->author }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ $loan->quantity }}x</td>
                                        <td class="px-6 py-4 text-sm text-slate-500">
                                            {{ \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-sm {{ $isOverdue ? 'text-red-600 font-bold' : 'text-slate-500' }}">
                                            {{ $dueDate->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($loan->status === 'pending_return')
                                                <span
                                                    class="inline-flex text-xs font-semibold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-full">
                                                    Proses Pengembalian
                                                </span>
                                            @elseif ($isOverdue)
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs font-semibold text-red-700 bg-red-100 px-2.5 py-1 rounded-full">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                    Terlambat {{ $daysOverdue }} hari
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex text-xs font-semibold text-green-700 bg-green-100 px-2.5 py-1 rounded-full">
                                                    Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            {{-- Tombol pemicu modal --}}
                                            @if ($loan->status === 'pending_return')
                                                {{-- Jika sudah diajukan, tampilkan pesan ini dan sembunyikan tombol --}}
                                                <span class="text-xs text-slate-400 italic block">Menunggu konfirmasi
                                                    petugas</span>
                                            @else
                                                {{-- Tombol pemicu modal pengembalian yang lama --}}
                                                <button @click="open = true"
                                                    class="text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 px-4 py-1.5 rounded-lg transition">
                                                    Kembalikan
                                                </button>

                                                {{-- Modal konfirmasi pengembalian --}}
                                                <div x-show="open" x-cloak
                                                    class="fixed inset-0 z-50 flex items-center justify-center text-left"
                                                    style="background: rgba(0,0,0,0.5);">
                                                    <div @click.outside="open = false"
                                                        class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md mx-4">
                                                        <div class="flex items-center gap-3 mb-4">
                                                            <div
                                                                class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center flex-shrink-0">
                                                                <svg class="w-5 h-5 text-orange-600" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            </div>
                                                            <h3 class="text-lg font-bold text-slate-800">Konfirmasi
                                                                Pengembalian</h3>
                                                        </div>
                                                        <p class="text-slate-600 mb-2">Apakah Anda yakin ingin
                                                            mengembalikan buku:</p>
                                                        <p class="font-bold text-slate-900 text-base mb-1">
                                                            {{ $loan->title }}</p>
                                                        <p class="text-sm text-slate-500 mb-4">{{ $loan->author }}</p>

                                                        @if ($isOverdue)
                                                            <div
                                                                class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4 text-sm text-red-700">
                                                                ⚠ Buku ini terlambat <strong>{{ $daysOverdue }}
                                                                    hari</strong>. Denda estimasi: <strong>Rp
                                                                    {{ number_format($daysOverdue * 5000, 0, ',', '.') }}</strong>
                                                            </div>
                                                        @endif

                                                        <div class="flex gap-3 justify-end">
                                                            <button type="button" @click="open = false"
                                                                class="px-5 py-2 text-sm font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">Batal</button>
                                                            <form action="{{ route('loans.return') }}"
                                                                method="POST">
                                                                @csrf
                                                                <input type="hidden" name="loan_id"
                                                                    value="{{ $loan->id }}">
                                                                <button type="submit"
                                                                    class="px-5 py-2 text-sm font-semibold text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition">
                                                                    Ya, Kembalikan
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-6 py-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <p class="font-medium">Tidak ada peminjaman aktif saat ini.</p>
                    </div>
                @endif
            </div>

            {{-- RIWAYAT PENGEMBALIAN --}}
            <div class="bg-white shadow-sm sm:rounded-xl overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </span>
                    <h3 class="text-lg font-bold text-slate-800">Riwayat Pengembalian</h3>
                </div>
                @if ($returnHistory->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50">
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-16">
                                        No
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Buku
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Jml
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Tgl Pinjam
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Tgl Kembali
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                        Denda
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @foreach ($returnHistory as $item)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-4 text-sm font-medium text-slate-400">
                                            {{ $returnHistory->firstItem() + $loop->index }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-semibold text-slate-800 text-sm">{{ $item->title }}
                                            </div>
                                            <div class="text-xs text-slate-500">{{ $item->author }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600">{{ $item->quantity }}x</td>
                                        <td class="px-6 py-4 text-sm text-slate-500">
                                            {{ \Carbon\Carbon::parse($item->loan_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-500">
                                            {{ \Carbon\Carbon::parse($item->actual_return_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @if ($item->fine > 0)
                                                <span
                                                    class="font-semibold text-red-600 bg-red-50 border border-red-100 px-2.5 py-1 rounded-full text-xs inline-block">
                                                    Rp {{ number_format($item->fine, 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span
                                                    class="text-green-600 bg-green-50 border border-green-100 px-2.5 py-1 rounded-full text-xs inline-block font-semibold">
                                                    Tidak ada
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 table-pagination-container">
                        {{ $returnHistory->links() }}
                    </div>
                @else
                    <div class="px-6 py-12 text-center text-slate-400">
                        <p class="font-medium">Belum ada riwayat pengembalian.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
