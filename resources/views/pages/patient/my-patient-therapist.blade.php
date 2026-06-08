@extends('components.layouts.app')

@section('title', 'Daftar Pasien Saya')

<script>
    window.patientlist = @js($patients);
    </script>

@section('content')
    <x-layouts.mobile-app class="bg-[#F8FAFB] min-h-screen pb-32" x-data="{
        search: '',
        patients: window.patientlist
    }">

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

                    <div class="flex items-center gap-3">
                        <div class="relative">
                            {{-- Avatar dengan Ring Status --}}
                            <div class="w-10 h-10 rounded-xl border-2 border-white shadow-md p-0.5">
                                <img src="{{ asset('images/logo_anjali.jpg') }}"
                                    class="w-full h-full rounded-[10px] object-cover bg-white">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col">
                        {{-- Nama Cabang/Kolaborasi --}}
                        <span class="text-[9px] font-black text-teal-600 uppercase tracking-[0.2em] leading-none mb-1">
                            {{-- {{ $sessions[0]['kolaborasi'] ?? 'Rumah Terapi Anjali' }} --}}
                            ANJALI SADINA MULYO
                        </span>
                        <h1 class="text-sm font-black text-slate-800 tracking-tight leading-none uppercase">
                            Daftar Pasien
                        </h1>
                    </div>
                </div>
            </div>
        </nav>

        <div class="px-6 pt-8 space-y-8">

            {{-- 2. TITLE SECTION --}}
            <div class="space-y-2">
                <h2 class="text-3xl font-bold text-teal-900 tracking-tight">Pasien Anda</h2>
                <p class="text-sm text-slate-500 font-medium leading-relaxed">Kelola dan pantau perkembangan medis seluruh pasien Anda.</p>
            </div>

            {{-- 3. TOTAL PASIEN CARD (New Design) --}}
            <div class="bg-teal-900 rounded-[2.2rem] p-6 text-white shadow-xl shadow-teal-900/20 relative overflow-hidden">
                <div class="relative z-10 flex items-center justify-between">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-teal-300 uppercase tracking-[0.2em]">Total Database</p>
                        <div class="flex items-baseline gap-2">
                            <h3 class="text-4xl font-black tracking-tighter" x-text="patients.length">0</h3>
                            <span class="text-sm font-bold text-teal-200 uppercase tracking-widest">Pasien</span>
                        </div>
                    </div>
                    <div class="w-14 h-14 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20">
                        <svg class="w-7 h-7 text-teal-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                </div>
                
                {{-- Decorative circles --}}
                <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-teal-800 rounded-full opacity-40"></div>
                <div class="absolute -right-2 -top-10 w-24 h-24 bg-teal-700 rounded-full opacity-20"></div>
            </div>

            {{-- 2. SEARCH BAR --}}
            <div class="relative group">
                <div class="absolute inset-y-0 left-5 flex items-center pointer-events-none">
                    {{-- Inline SVG: search icon --}}
                    <svg class="w-4 h-4 text-slate-300 group-focus-within:text-teal-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                    </svg>
                </div>
                <input type="text" x-model="search" placeholder="Cari nama atau ID pasien..."
                    class="w-full pl-12 pr-5 py-4 bg-white border border-slate-100 rounded-2xl text-xs font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 transition-all outline-none shadow-sm">
            </div>

            {{-- 3. PATIENT LIST --}}
            <div class="space-y-4">
                <template x-for="p in patients.filter(i => i.nama.toLowerCase().includes(search.toLowerCase()) || i.public_id.toLowerCase().includes(search.toLowerCase()))" :key="p.id_raw">
                    <div class="bg-white rounded-[2rem] p-5 border border-slate-100 shadow-sm flex items-center justify-between group active:scale-[0.98] transition-all duration-300">
                        <div class="flex items-center gap-4">
                            {{-- Avatar Pasien --}}
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-50 shadow-inner">
                                <img :src="p.foto" class="w-full h-full object-cover">
                            </div>

                            <div class="space-y-1">
                                <h4 class="text-sm font-black text-slate-800 leading-tight" x-text="p.nama"></h4>
                                <div class="flex items-center gap-2">
                                    {{-- <span class="text-[11px] font-bold text-teal-600 font-mono uppercase tracking-tighter" x-text="p.public_id"></span> --}}
                                    <span class="w-1 h-1 rounded-full bg-slate-200"></span>
                                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest" x-text="p.total_sesi + ' Sesi'"></span>
                                </div>
                                <p class="text-[11px] font-medium text-slate-400">Terakhir: <span class="font-bold text-slate-500" x-text="p.last_session"></span></p>
                            </div>
                        </div>

                        {{-- Action Buttons — using inline SVGs so they work inside x-for --}}
                        <div class="flex items-center gap-2">
                            {{-- Button Rekam Medis --}}
                            <a :href="'/therapist/patient/history/' + p.id_raw"
                                class="w-9 h-9 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100 hover:bg-teal-600 hover:text-white transition-all shadow-sm">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </template>

                {{-- EMPTY STATE — x-cloak prevents flash before Alpine initialises --}}
                <div x-cloak
                    x-show="patients.filter(i => i.nama.toLowerCase().includes(search.toLowerCase()) || i.public_id.toLowerCase().includes(search.toLowerCase())).length === 0"
                    class="text-center py-20 px-10 space-y-4">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto border border-slate-100">
                        <svg class="w-8 h-8 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Pasien tidak ditemukan</p>
                </div>
            </div>
        </div>

        {{-- BOTTOM NAVBAR --}}
        <x-navigation.therapist-navbar active="pasien" />
    </x-layouts.mobile-app>
@endsection