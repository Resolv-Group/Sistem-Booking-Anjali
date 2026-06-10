@extends('components.layouts.app')

@section('title', 'Katalog Layanan')

@section('content')

    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{ search: '' }">

        {{-- 1. TOPBAR --}}
<nav class="sticky top-0 z-[100] bg-white/80 backdrop-blur-xl border-b border-slate-100/80 px-6 py-4">
            <div class="flex items-center justify-between">

                {{-- Left: Navigation & Context --}}
                <div class="flex items-center gap-4">
                    {{-- Tombol Back/Menu dengan Hitbox Luas --}}
                    {{-- <a href="javascript:void(0)" onclick="window.history.back()" 
                    class="group flex items-center justify-center w-10 h-10 bg-white border border-slate-100 rounded-xl shadow-sm hover:bg-teal-50 transition-all active:scale-90">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-teal-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" />
                        </svg>
                    </a> --}}

                    <div class="flex flex-col">
                        {{-- Nama Cabang/Kolaborasi --}}
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                            {{-- {{ $sessions[0]['kolaborasi'] ?? 'Rumah Terapi Anjali' }} --}}
                            ANJALI SADINA MULYO
                        </span>
                        <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">
                            Layanan Kami
                        </h1>
                    </div>
                </div>

                {{-- Right: Profile with Status Indicator --}}
                <div class="flex items-center gap-3">
                    <div class="relative">
                        {{-- Avatar dengan Ring Status --}}
                        <div class="w-10 h-10 rounded-xl border-2 border-white shadow-md p-0.5">
                            <img src="{{ asset('images/logo_anjali.jpg') }}"
                                class="w-full h-full rounded-[10px] object-cover bg-white">
                        </div>
                    </div>
                </div>

            </div>
        </nav>

        <div class="px-6 pt-8 space-y-8">

            {{-- SUCCESS NOTIFICATION --}}
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                    x-transition:leave="transition ease-in duration-300"
                    class="bg-teal-500 text-white rounded-2xl p-4 text-xs font-bold text-center shadow-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- 2. TITLE SECTION --}}
            <div class="space-y-3 px-1">
                <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-none">Katalog<br>Layanan Klinik</h2>
                <p class="text-sm font-medium text-slate-500 leading-relaxed">
                    Atur daftar layanan kesehatan cabang Anda dengan mudah.
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
                <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.25em] ml-1">Daftar Layanan ({{ $layanans->count() }})</h3>

                @forelse($layanans as $item)
                    <a href="{{ route('admin-cabang.layanan.edit', $item->id) }}"
                        class="bg-white rounded-[1.8rem] p-5 shadow-sm border border-slate-100 flex items-center justify-between group active:scale-[0.98] transition-all"
                        x-show="'{{ strtolower($item->nama) }}'.includes(search.toLowerCase()) || search === ''">
                        <div class="flex items-center gap-4">
                            {{-- Icon Container --}}
                            <div class="w-14 h-14 rounded-xl {{ $item->status === 'Tersedia' ? 'bg-teal-50 text-teal-600' : 'bg-slate-100 text-slate-450' }} flex items-center justify-center shadow-inner">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>

                            {{-- Text Info --}}
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-sm font-black text-slate-800 leading-tight">{{ $item->nama }}</h4>
                                    @if ($item->status === 'Tersedia')
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[8px] font-black uppercase tracking-widest rounded-md border border-emerald-100 flex items-center gap-1">
                                            <div class="w-1 h-1 rounded-full bg-emerald-500"></div>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 bg-slate-50 text-slate-400 text-[8px] font-black uppercase tracking-widest rounded-md border border-slate-200 flex items-center gap-1">
                                            <div class="w-1 h-1 rounded-full bg-slate-400"></div>
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <p class="text-xs font-black text-slate-700">Rp{{ number_format($item->base_harga, 0, ',', '.') }}</p>
                                    @if ($item->diskon_persentase > 0)
                                        <span class="text-[9px] font-bold text-orange-500 bg-orange-50 px-2 py-0.5 rounded-md">-{{ $item->diskon_persentase }}%</span>
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
                    <div class="text-center py-16 space-y-3 bg-white rounded-[2rem] border border-slate-100 shadow-sm">
                        <div class="w-16 h-16 mx-auto bg-slate-100 rounded-2xl flex items-center justify-center text-slate-350">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400">Belum ada layanan.</p>
                        <p class="text-xs text-slate-300">Tambahkan layanan pertama Anda.</p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- 5. FLOATING ACTION BUTTON --}}
        <div class="fixed bottom-24 right-6 z-50">
            <a href="{{ route('admin-cabang.layanan.create') }}"
                class="w-14 h-14 bg-teal-800 text-white rounded-2xl flex items-center justify-center shadow-xl active:scale-90 transition-all duration-300">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
            </a>
        </div>

        {{-- 6. BOTTOM NAVBAR --}}
        <x-navigation.admin-cabang-navbar active="layanan" />

    </x-layouts.mobile-app>

@endsection
