<nav x-data="{ open: false }" class="bg-white border-b border-slate-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- Logo --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <span class="text-orange-600 font-extrabold text-xl tracking-tight">i<span class="text-slate-800">Livre</span></span>
                    </a>
                </div>

                {{-- Desktop Nav Links --}}
                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex items-center">
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('dashboard') ? 'text-orange-600 bg-orange-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('loans.index') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg transition {{ request()->routeIs('loans.*') ? 'text-orange-600 bg-orange-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                        Peminjaman
                        @auth
                        @php
                            $activeLoanCount = \Illuminate\Support\Facades\DB::table('loans')
                                ->leftJoin('returns', 'loans.id', '=', 'returns.loan_id')
                                ->where('loans.user_id', Auth::id())
                                ->whereNull('returns.id')
                                ->count();
                        @endphp
                        @if($activeLoanCount > 0)
                            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-orange-500 rounded-full">{{ $activeLoanCount }}</span>
                        @endif
                        @endauth
                    </a>
                </div>
            </div>

            {{-- Desktop User Dropdown --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2.5 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-lg text-slate-600 bg-white hover:bg-slate-50 hover:text-slate-900 focus:outline-none transition">
                            @if(Auth::user()->photo)
                                <img src="/storage/{{ Auth::user()->photo }}" alt="Avatar" class="w-8 h-8 rounded-full object-cover ring-2 ring-orange-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-sm ring-2 ring-orange-200">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Hamburger --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-100">
        <div class="pt-2 pb-3 space-y-1 px-3">
            <a href="{{ route('dashboard') }}"
                class="block px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'text-orange-600 bg-orange-50' : 'text-slate-600 hover:bg-slate-50' }}">
                Dashboard
            </a>
            <a href="{{ route('loans.index') }}"
                class="block px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('loans.*') ? 'text-orange-600 bg-orange-50' : 'text-slate-600 hover:bg-slate-50' }}">
                Peminjaman
            </a>
        </div>

        <div class="pt-4 pb-3 border-t border-slate-100">
            <div class="px-4 flex items-center gap-3 mb-3">
                @if(Auth::user()->photo)
                    <img src="/storage/{{ Auth::user()->photo }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover ring-2 ring-orange-200">
                @else
                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-lg ring-2 ring-orange-200">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <div class="font-semibold text-sm text-slate-800">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-500">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="space-y-1 px-3">
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50">Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm font-medium rounded-lg text-slate-600 hover:bg-slate-50">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
