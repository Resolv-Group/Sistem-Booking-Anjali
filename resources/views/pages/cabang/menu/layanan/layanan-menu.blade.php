@extends('components.layouts.app')

@section('title', 'Pengaturan Layanan Klinik')

@section('content')

<x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen" x-data="{ search: '' }">

    {{-- 1. TOPBAR --}}
    <div class="px-6 py-5 flex justify-between items-center bg-white/90 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100 shadow-sm">
        <div class="flex items-center gap-4">
            <a href="{{ url()->previous() }}" class="p-1 -ml-1 text-slate-400 hover:text-teal-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
            </a>
            <h1 class="text-sm font-bold text-teal-800 uppercase tracking-widest leading-none">Rumah Terapi Anjali</h1>
        </div>
        <div class="w-10 h-10 rounded-xl border-2 border-orange-100 p-0.5 bg-white">
            <img src="https://i.pravatar.cc/100?u=admin" class="w-full h-full rounded-lg object-cover">
        </div>
    </div>

    <div class="px-6 pt-8 pb-32 space-y-8">

        {{-- 2. TITLE SECTION --}}
        <div class="space-y-3 px-1">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none">Pengaturan <br> Layanan Klinik</h2>
            <p class="text-sm font-medium text-slate-500 leading-relaxed">
                Atur daftar layanan kesehatan Anda dengan presisi dan kemudahan.
            </p>
        </div>

        {{-- 3. SEARCH BAR --}}
        <div class="relative group">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-teal-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" x-model="search" placeholder="Cari nama layanan..." 
                class="w-full pl-12 pr-4 py-4 bg-slate-100 border-none rounded-xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:bg-white transition-all outline-none">
        </div>

        {{-- 4. SERVICE LIST --}}
        <div class="space-y-4">
            <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] ml-1">Daftar Layanan Aktif</h3>
            
            @php
                $layanan = [
                    ['nama' => 'Akupunktur', 'durasi' => '60 Menit', 'harga' => 350000, 'bg' => 'bg-cyan-100', 'text' => 'text-cyan-600', 'icon' => 'plus-circle'],
                    ['nama' => 'Mass Age', 'durasi' => '90 Menit', 'harga' => 450000, 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'flower'],
                    ['nama' => 'Cupping', 'durasi' => '45 Menit', 'harga' => 275000, 'bg' => 'bg-teal-100', 'text' => 'text-teal-600', 'icon' => 'beaker'],
                    ['nama' => 'Moksa', 'durasi' => '30 Menit', 'harga' => 180000, 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'icon' => 'flame'],
                    ['nama' => 'TDP', 'durasi' => '20 Menit', 'harga' => 120000, 'bg' => 'bg-slate-200', 'text' => 'text-slate-500', 'icon' => 'sun'],
                ];
            @endphp

            @foreach($layanan as $item)
            <a href="{{ route('admin-global.layanan.detail') }}" class="bg-white rounded-[1.8rem] p-5 shadow-sm border border-slate-100 flex items-center justify-between group active:scale-[0.98] transition-all">
                <div class="flex items-center gap-4">
                    {{-- Icon Container --}}
                    <div class="w-16 h-16 rounded-xl {{ $item['bg'] }} {{ $item['text'] }} flex items-center justify-center shadow-inner">
                        {{-- Placeholder for actual Lucide icons --}}
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /> {{-- Example generic icon --}}
                        </svg>
                    </div>

                    {{-- Text Info --}}
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <h4 class="text-base font-black text-slate-800 leading-tight">{{ $item['nama'] }}</h4>
                            <span class="px-2 py-0.5 bg-teal-50 text-teal-600 text-[8px] font-black uppercase tracking-widest rounded-md border border-teal-100 flex items-center gap-1">
                                <div class="w-1 h-1 rounded-full bg-teal-500"></div>
                                Aktif
                            </span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1 text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-[11px] font-bold uppercase tracking-tighter">{{ $item['durasi'] }}</span>
                            </div>
                            <p class="text-sm font-black text-slate-700">Rp{{ number_format($item['harga'], 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Action Menu --}}
                <button class="p-2 text-slate-300 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                    </svg>
                </button>
            </a>
            @endforeach
        </div>

    </div>

    {{-- 5. FLOATING ACTION BUTTON --}}
    <div x-data="{ open: false }" class="fixed bottom-24 right-6 z-50 flex flex-col items-end gap-3">
        {{-- Speed Dial Items (Tucked away when closed) --}}
        <div 
            x-show="open" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-90"
            class="flex flex-col items-end gap-3 mb-2"
        >

            {{-- Tambah Cabang --}}
            <div class="flex items-center gap-3">
                <span class="bg-white px-3 py-1.5 rounded-xl shadow-sm border border-slate-100 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Tambah Layanan</span>
                <a href="{{ route('admin-global.layanan.create') }}" class="w-12 h-12 bg-white text-teal-700 rounded-xl flex items-center justify-center shadow-lg border border-slate-100 active:scale-95 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </a>
            </div>
        </div>

        <button 
            @click="open = !open" 
            :class="open ? 'bg-slate-800 shadow-slate-900/40' : 'bg-teal-900 shadow-teal-900/40'"
            class="w-14 h-14 text-white rounded-xl flex items-center justify-center shadow-2xl active:scale-90 transition-all duration-300 relative overflow-hidden"
        >
            <svg x-show="!open" 
                 x-transition:enter="transition duration-300"
                 x-transition:enter-start="opacity-0 scale-50 rotate-90"
                 x-transition:enter-end="opacity-100 scale-100 rotate-0"
                 class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15h2m-2 4h2" />
            </svg>
            <svg x-show="open" 
                 x-transition:enter="transition duration-300"
                 x-transition:enter-start="opacity-0 scale-50 -rotate-90"
                 x-transition:enter-end="opacity-100 scale-100 rotate-0"
                 class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- 6. BOTTOM NAVBAR --}}
    <x-navigation.admin-global-navbar active="cabang" />

</x-layouts.mobile-app>

@endsection