@extends('components.layouts.app')

@section('title', 'Therapist Dashboard')

@section('content')

<x-layouts.mobile-app class="bg-slate-50 min-h-screen">

    {{-- 1. TOPBAR: GREETING & PROFILE --}}
    <div class="px-6 py-6 flex justify-between items-center bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div>
            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-[0.2em] mb-1">Selamat Pagi,</p>
            <h1 class="text-xl font-semibold text-teal-900 leading-none">Dr. Elena, Sp. Ak</h1>
        </div>
        <div class="relative">
            <img src="https://i.pravatar.cc/100?u=therapist" class="w-12 h-12 rounded-2xl border-2 border-orange-100 p-0.5 shadow-sm">
            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-500 border-2 border-white rounded-full"></div>
        </div>
    </div>

    <div class="px-6 pt-8 pb-32 space-y-8">

        {{-- 2. DAILY PERFORMANCE OVERVIEW --}}
        <div class="grid grid-cols-2 gap-4">
            {{-- Card: Total Janjian --}}
            <div class="bg-white p-5 rounded-[1.8rem] border border-slate-100 shadow-sm space-y-3">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold text-slate-800">12</h3>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Sesi Hari Ini</p>
                </div>
            </div>

            {{-- Card: Selesai --}}
            <div class="bg-white p-5 rounded-[1.8rem] border border-slate-100 shadow-sm space-y-3">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-semibold text-slate-800">08</h3>
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest">Sesi Selesai</p>
                </div>
            </div>
        </div>

        {{-- 3. ACTIVE SESSION (URGENT ACTION) --}}
        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest ml-1">Sesi Berjalan</h3>
            <div class="bg-teal-900 rounded-[2rem] p-6 text-white shadow-xl shadow-teal-900/20 relative overflow-hidden">
                {{-- Decorative Glow --}}
                <div class="absolute top-0 right-0 w-32 h-32 bg-teal-500/10 rounded-full -mr-16 -mt-16 blur-2xl"></div>
                
                <div class="relative z-10 flex justify-between items-start mb-6">
                    <div class="space-y-1">
                        <span class="px-2.5 py-1 bg-teal-500/20 text-teal-300 text-[10px] font-semibold uppercase tracking-widest rounded-lg border border-teal-500/30">Ongoing</span>
                        <h4 class="text-xl font-semibold pt-2">David Purnama</h4>
                        <p class="text-xs text-teal-100/60 uppercase tracking-widest font-medium">Akupunktur Medis • ID: 8829</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-semibold text-teal-400 uppercase tracking-widest">Durasi</p>
                        <p class="text-lg font-semibold tracking-tighter">00:45:12</p>
                    </div>
                </div>

                <a href="#" class="relative z-10 w-full py-4 bg-white text-teal-900 rounded-2xl text-sm font-semibold uppercase tracking-widest flex items-center justify-center gap-2 active:scale-95 transition-all">
                    Lengkapi Catatan Medis
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M13 7l5 5-5 5M6 7l5 5-5 5"/></svg>
                </a>
            </div>
        </div>

        {{-- 4. UPCOMING SCHEDULE --}}
        <div class="space-y-5">
            <div class="flex justify-between items-end px-1">
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-widest">Jadwal Berikutnya</h3>
                <a href="#" class="text-xs font-semibold text-teal-600 underline">Lihat Semua</a>
            </div>

            <div class="space-y-3">
                @php
                    $schedules = [
                        ['time' => '11:00 AM', 'name' => 'Nayesha Putri', 'type' => 'Bekam Steril', 'status' => 'paid'],
                        ['time' => '01:30 PM', 'name' => 'Alexander Thoma', 'type' => 'Fisioterapi', 'status' => 'pending'],
                    ];
                @endphp

                @foreach($schedules as $s)
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group active:bg-slate-50 transition-all">
                    <div class="flex items-center gap-4">
                        <div class="text-center pr-4 border-r border-slate-100">
                            <p class="text-xs font-semibold text-slate-800 leading-none">{{ explode(' ', $s['time'])[0] }}</p>
                            <p class="text-[9px] font-bold text-slate-400 uppercase mt-1">{{ explode(' ', $s['time'])[1] }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-slate-800 leading-none">{{ $s['name'] }}</h4>
                            <p class="text-xs text-slate-400 mt-1.5 font-medium italic">{{ $s['type'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($s['status'] == 'paid')
                            <div class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></div>
                        @else
                            <div class="w-2 h-2 rounded-full bg-amber-400"></div>
                        @endif
                        <svg class="w-5 h-5 text-slate-200 group-active:text-teal-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M9 5l7 7-7 7"/></svg>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- 5. QUICK ACTIONS --}}
        <div class="bg-slate-900 rounded-[2rem] p-8 space-y-6 text-white relative overflow-hidden shadow-2xl">
             <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-teal-500/10 rounded-full blur-3xl"></div>
             <h3 class="text-lg font-semibold tracking-tight relative z-10 text-center">Butuh bantuan operasional?</h3>
             <div class="grid grid-cols-2 gap-3 relative z-10">
                 <button class="flex flex-col items-center gap-2 p-4 bg-white/10 rounded-2xl border border-white/10 hover:bg-white/20 transition-all active:scale-95">
                     <svg class="w-6 h-6 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
                     <span class="text-[10px] font-semibold uppercase tracking-widest">Input Pasien</span>
                 </button>
                 <button class="flex flex-col items-center gap-2 p-4 bg-white/10 rounded-2xl border border-white/10 hover:bg-white/20 transition-all active:scale-95">
                     <svg class="w-6 h-6 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                     <span class="text-[10px] font-semibold uppercase tracking-widest">Riwayat Medis</span>
                 </button>
             </div>
        </div>

    </div>

    {{-- BOTTOM NAVBAR --}}
    <x-navigation.therapist-navbar active="dashboard" />

</x-layouts.mobile-app>

@endsection