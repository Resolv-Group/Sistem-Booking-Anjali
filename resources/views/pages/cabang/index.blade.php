@extends('components.layouts.app')

@section('title', 'Direktori Cabang')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{ search: '' }">

        {{-- 1. HEADER WITH ADD ACTION --}}
        <div class="px-6 py-6 bg-white border-b border-slate-100 sticky top-0 z-50 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Cabang<span class="text-blue-600">.</span>
                    </h1>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Manajemen Lokasi Anjali</p>
                </div>

            </div>

            {{-- SEARCH BAR --}}
            <div class="relative group">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-blue-600 transition-colors"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" x-model="search" placeholder="Cari nama cabang atau kota..."
                    class="w-full pl-12 pr-5 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all outline-none shadow-inner">
            </div>
        </div>

        {{-- 2. BRANCH LIST (SCALABLE) --}}
        <div class="px-6 py-8 space-y-4 pb-32">

            @foreach ($cabangs as $b)
                <a href="{{ route('admin-global.cabang.menu', ['id_kolaborasi' => $b->id]) }}"
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
        </div>

        <div x-data="{ open: false }" class="fixed bottom-24 right-6 z-50 flex flex-col items-end gap-3">
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
                        Cabang</span>
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

        <x-navigation.admin-global-navbar active="cabang" />
    </x-layouts.mobile-app>
@endsection
