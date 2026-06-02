@extends('components.layouts.app')

@section('title', 'Pengaturan Layanan Klinik')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{ search: '' }">

        {{-- 1. TOPBAR --}}
        <div
            class="px-6 py-5 flex justify-between items-center bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 shadow-sm">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin-global.cabang.menu', $kolaborasi->id) }}"
                    class="p-1 -ml-1 text-slate-400 hover:text-teal-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h1 class="text-xl font-bold text-teal-800 uppercase tracking-widest leading-none">
                    {{ $kolaborasi->nama_kolaborasi }}</h1>
            </div>
        </div>

        <div class="px-6 pt-8 pb-32 space-y-8">

            {{-- SUCCESS NOTIFICATION --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4"
                    class="bg-teal-500 text-white rounded-2xl p-4 text-sm font-bold text-center shadow-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- 2. TITLE SECTION --}}
            <div class="space-y-3 px-1">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none">Pengaturan <br> Layanan Klinik
                </h2>
                <p class="text-sm font-medium text-slate-500 leading-relaxed">
                    Atur daftar layanan kesehatan Anda dengan presisi dan kemudahan.
                </p>
            </div>

            {{-- 3. SEARCH BAR --}}
            <div class="relative group">
                <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400 group-focus-within:text-teal-500 transition-colors" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="search" placeholder="Cari nama layanan..."
                    class="w-full pl-12 pr-4 py-4 bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none">
            </div>

            {{-- 4. SERVICE LIST --}}
            <div class="space-y-4">
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] ml-1">Daftar Layanan
                    ({{ $layanans->count() }})</h3>

                @forelse($layanans as $item)
                    <a href="{{ route('admin-global.layanan.detail', [$kolaborasi->id, $item->id]) }}"
                        class="bg-white rounded-[1.8rem] p-5 shadow-sm border border-slate-100 flex items-center justify-between group active:scale-[0.98] transition-all"
                        x-show="'{{ strtolower($item->nama) }}'.includes(search.toLowerCase()) || search === ''">
                        <div class="flex items-center gap-4">
                            {{-- Icon Container --}}
                            <div
                                class="w-16 h-16 rounded-xl {{ $item->status === 'Tersedia' ? 'bg-teal-100 text-teal-600' : 'bg-slate-200 text-slate-400' }} flex items-center justify-center shadow-inner">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>

                            {{-- Text Info --}}
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-base font-black text-slate-800 leading-tight">{{ $item->nama }}</h4>
                                    @if ($item->status === 'Tersedia')
                                        <span
                                            class="px-2 py-0.5 bg-teal-50 text-teal-600 text-[8px] font-black uppercase tracking-widest rounded-md border border-teal-100 flex items-center gap-1">
                                            <div class="w-1 h-1 rounded-full bg-teal-500"></div>
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="px-2 py-0.5 bg-red-50 text-red-500 text-[8px] font-black uppercase tracking-widest rounded-md border border-red-100 flex items-center gap-1">
                                            <div class="w-1 h-1 rounded-full bg-red-400"></div>
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <p class="text-sm font-black text-slate-700">
                                        Rp{{ number_format($item->base_harga, 0, ',', '.') }}</p>
                                    @if ($item->diskon_persentase > 0)
                                        <span
                                            class="text-[10px] font-bold text-orange-500 bg-orange-50 px-2 py-0.5 rounded-md">-{{ $item->diskon_persentase }}%</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Action Arrow --}}
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-teal-600 transition-all group-hover:translate-x-1 shrink-0"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @empty
                    <div class="text-center py-16 space-y-3">
                        <div
                            class="w-16 h-16 mx-auto bg-slate-100 rounded-2xl flex items-center justify-center text-slate-300">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400">Belum ada layanan.</p>
                        <p class="text-xs text-slate-300">Tambahkan layanan pertama Anda.</p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- 5. FLOATING ACTION BUTTON --}}
        <div x-data="{ open: false }" class="fixed bottom-24 right-6 z-50 flex flex-col items-end gap-3">
            {{-- Speed Dial Items --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-4 scale-90"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-90" class="flex flex-col items-end gap-3 mb-2">
                {{-- Tambah Layanan --}}
                <div class="flex items-center gap-3">
                    <span
                        class="bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-100 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Tambah
                        Layanan</span>
                    <a href="{{ route('admin-global.layanan.create', $kolaborasi->id) }}"
                        class="w-12 h-12 bg-white text-teal-700 rounded-xl flex items-center justify-center shadow-lg border border-slate-100 active:scale-95 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </a>
                </div>
            </div>

            <button @click="open = !open"
                :class="open ? 'bg-slate-800 shadow-slate-900/40' : 'bg-teal-900 shadow-teal-900/40'"
                class="w-14 h-14 text-white rounded-xl flex items-center justify-center shadow-2xl active:scale-90 transition-all duration-300 relative overflow-hidden">
                <svg x-show="!open" x-transition:enter="transition duration-300"
                    x-transition:enter-start="opacity-0 scale-50 rotate-90"
                    x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-7 h-7" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <svg x-show="open" x-transition:enter="transition duration-300"
                    x-transition:enter-start="opacity-0 scale-50 -rotate-90"
                    x-transition:enter-end="opacity-100 scale-100 rotate-0" class="w-7 h-7" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- 6. BOTTOM NAVBAR --}}
        <x-navigation.admin-global-navbar active="cabang" />

    </x-layouts.mobile-app>

@endsection
