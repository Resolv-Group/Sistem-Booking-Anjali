@extends('components.layouts.app')

@section('title', 'Direktori Cabang')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{ search: '' }">

        {{-- 1. HEADER WITH ADD ACTION --}}
        <div class="sticky top-0 z-50 bg-white/85 backdrop-blur-xl border-b border-slate-100/80 shadow-sm">
            <div class="h-1 w-full bg-gradient-to-r from-teal-500 via-teal-700 to-emerald-500"></div>

            <div class="px-6 pt-5 pb-4">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1.5">
                            Anjali Sadina Mulyo
                        </span>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight leading-none uppercase">
                            Direktori Kolaborasi
                        </h1>
                    </div>

                    {{-- Right Slot: Decorative Icon --}}
                    <div class="w-10 h-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-700 border border-teal-100 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>

                {{-- Integrated Search Bar --}}
                <div class="relative group">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400 group-focus-within:text-teal-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" x-model="search" placeholder="Cari nama kolaborasi atau kota..."
                        class="w-full pl-11 pr-5 py-3.5 bg-slate-100/50 border border-slate-200/50 rounded-2xl text-xs font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white focus:border-teal-200 transition-all outline-none shadow-inner">
                </div>
            </div>
        </div>

        {{-- 2. BRANCH LIST (SCALABLE) --}}
        <div class="px-6 py-8 space-y-4 pb-32">

            @foreach ($cabangs as $b)
                <a href="{{ route('admin-global.cabang.menu', ['id_kolaborasi' => $b->id]) }}"
                    x-show="search === '' ||
                    '{{ strtolower($b['nama_kolaborasi']) }}'.includes(search.toLowerCase()) ||
                    '{{ strtolower($b['kota_kolaborasi']) }}'.includes(search.toLowerCase())"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="block p-5 bg-white border border-slate-100 rounded-[1.5rem] shadow-sm hover:shadow-xl hover:shadow-slate-200/50 hover:border-blue-200 transition-all active:scale-[0.98] group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-black text-xl shadow-lg {{ $b['theme'] === 'teal' ? 'bg-teal-600 shadow-teal-100' : 'bg-blue-600 shadow-blue-100' }}">
                                {{ substr($b['nama_kolaborasi'], 0, 2) }}
                            </div>
                            <div>
                                <h4
                                    class="text-base font-black text-slate-800 leading-tight group-hover:text-blue-600 transition-colors">
                                    {{ $b['nama_kolaborasi'] }}</h4>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $b['kota_kolaborasi'] }}</span>
                                    <div class="w-1 h-1 rounded-full bg-slate-200"></div>
                                    <span
                                        class="text-[10px] font-bold text-blue-500 uppercase tracking-widest">{{ $b['staff_count'] }}
                                        Staff</span>
                                </div>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-slate-200 group-hover:text-slate-800 transition-all transform group-hover:translate-x-1"
                            fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </a>
            @endforeach
            {{-- State Opsional: Tampilkan pesan ini jika tidak ada cabang yang cocok dengan pencarian --}}
            <div x-cloak
                x-show="search !== '' && !Array.from($el.parentNode.children).some(el => el.tagName === 'A' && el.style.display !== 'none')"
                class="text-center py-12">
                <p class="text-sm font-bold text-slate-400">Kolaborasi atau kota tidak ditemukan.</p>
            </div>

        </div>

        <div class="fixed bottom-24 left-1/2 -translate-x-1/2 w-full max-w-[430px] pointer-events-none px-6 z-50">
            <div x-data="{ open: false }" class="flex flex-col items-end gap-3 pointer-events-auto w-full">
                {{-- Speed Dial Items (Tucked away when closed) --}}
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-90"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-90" class="flex flex-col items-end gap-3 mb-2">

                    {{-- Tambah Cabang --}}
                    <div class="flex items-center gap-3">
                        <span
                            class="bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-100 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Tambah
                            Kolaborasi</span>
                        <a href="{{ route('admin-global.cabang.create') }}"
                            class="w-12 h-12 bg-white text-teal-700 rounded-2xl flex items-center justify-center shadow-lg border border-slate-100 active:scale-95 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <button @click="open = !open"
                    :class="open ? 'bg-slate-800 shadow-slate-900/40' : 'bg-teal-900 shadow-teal-900/40'"
                    class="w-14 h-14 text-white rounded-2xl flex items-center justify-center shadow-2xl active:scale-90 transition-all duration-300 relative overflow-hidden">
                    <svg x-show="!open" x-transition:enter="transition duration-300"
                        x-transition:enter-start="opacity-0 scale-50 rotate-90"
                        x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-7 h-7" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15h2m-2 4h2" />
                    </svg>
                    <svg x-show="open" x-transition:enter="transition duration-300"
                        x-transition:enter-start="opacity-0 scale-50 -rotate-90"
                        x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-7 h-7" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <x-navigation.admin-global-navbar active="cabang" />
    </x-layouts.mobile-app>
@endsection
