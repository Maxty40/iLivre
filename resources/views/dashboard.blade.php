<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">{{ session('error') }}</div>
            @endif

            {{-- Greeting Card --}}
            <div class="bg-gradient-to-r from-orange-500 to-orange-400 rounded-2xl p-6 shadow-md text-white flex items-center gap-5">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 overflow-hidden">
                    @if(Auth::user()->photo)
                        <img src="/storage/{{ Auth::user()->photo }}" alt="Foto" class="w-full h-full object-cover">
                    @else
                        <span class="text-2xl font-bold text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    @endif
                </div>
                <div>
                    <p class="text-orange-100 text-sm">Selamat datang kembali,</p>
                    <h2 class="text-2xl font-extrabold">{{ Auth::user()->name }}</h2>
                    <p class="text-orange-100 text-sm mt-1">Kamu sedang meminjam <strong class="text-white">{{ $activeCount }} buku</strong> saat ini.</p>
                </div>
                <a href="{{ route('loans.index') }}" class="ml-auto text-sm font-semibold bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition flex-shrink-0">
                    Lihat Peminjaman →
                </a>
            </div>

            {{-- Katalog --}}
            <div class="bg-white shadow-sm sm:rounded-xl">
                <div class="px-6 py-5 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Katalog Buku</h3>
                </div>
                <div class="p-6">
                    @livewire('book-catalog')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
