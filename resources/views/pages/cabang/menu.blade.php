@extends('components.layouts.app')

@section('title', 'Manage Cabang')

@section('content')
    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen">

        {{-- 1. CONTEXT HEADER --}}
        <div class="sticky top-0 z-50 bg-white/85 backdrop-blur-xl border-b border-slate-100/80 shadow-sm">
            <div class="h-1 w-full bg-gradient-to-r from-teal-500 via-teal-700 to-emerald-500"></div>
            <div class="px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin-global.cabang') }}" class="p-2 -ml-2 text-slate-400 hover:text-teal-600 hover:bg-slate-50 rounded-xl active:scale-95 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                            {{ $kolaborasi->nama_kolaborasi }}
                        </span>
                        <h1 class="text-xs font-black text-slate-800 uppercase tracking-wider leading-none">
                            Cabang & Biaya
                        </h1>
                    </div>
                </div>
                {{-- Right Slot: Simple Decorative Brand Icon --}}
                <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center text-teal-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="px-6 py-8 space-y-8 pb-32">

            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-600 text-sm font-bold animate-in fade-in">
                    {{ session('success') }}
                </div>
            @endif

            {{-- 2. OPERATIONAL QUICK STATS (Visual Feedback) --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="p-4 bg-teal-50/50 border border-teal-100/75 rounded-2xl">
                    <p class="text-[9px] font-black text-teal-600/70 uppercase tracking-widest mb-1">Layanan Aktif</p>
                    <p class="text-xl font-black text-teal-800">{{ $layananCount }} <span class="text-xs font-bold opacity-50">Menu</span>
                    </p>
                </div>
                <div class="p-4 bg-emerald-50/50 border border-emerald-100/75 rounded-2xl">
                    <p class="text-[9px] font-black text-emerald-600/70 uppercase tracking-widest mb-1">Terapis Aktif</p>
                    <p class="text-xl font-black text-emerald-800">{{ $terapisCount }} <span
                            class="text-xs font-bold opacity-50">Orang</span></p>
                </div>
            </div>

            {{-- 3. CORE MANAGEMENT MENUS --}}
            <div class="space-y-4">
                <h3 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Konfigurasi Sistem</h3>

                <div class="grid grid-cols-1 gap-3">
                    {{-- Edit Cabang Settings --}}
                    <a href="{{ route('admin-global.cabang.edit', $kolaborasi->id) }}"
                        class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 transition-all group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Pengaturan Cabang & Biaya</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    {{-- Menu Item 1 --}}
                    <a href="{{ route('admin-global.layanan', $kolaborasi->id) }}"
                        class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 transition-all group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Kelola Layanan</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    {{-- Menu Item 2 --}}
                    <a href="{{ route('admin-global.operasional-jadwal', $kolaborasi->id) }}"
                        class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 transition-all group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Jam Operasional</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    {{-- Menu Item 4 --}}
                    <a href="{{ route('admin-global.karyawan', $kolaborasi->id) }}"
                        class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 transition-all group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Kelola Karyawan</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    {{-- Menu Item 3 --}}
                    <a href="{{ route('admin-global.therapist-list', ['id_kolaborasi' => $kolaborasi->id]) }}"
                        class="flex items-center justify-between p-5 bg-white border border-slate-100 rounded-2xl hover:border-blue-300 transition-all group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-11 h-11 bg-slate-50 text-slate-500 rounded-xl flex items-center justify-center group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <span class="text-sm font-black text-slate-700 uppercase tracking-widest">Mapping Layanan Terapis</span>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition-all group-hover:translate-x-1"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <x-navigation.admin-global-navbar active="cabang" />

    </x-layouts.mobile-app>
@endsection
